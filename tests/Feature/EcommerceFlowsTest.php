<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;

class EcommerceFlowsTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleProduct(string $name = 'iPhone 15 Pro Max', int $price = 30000000, ?Category $category = null): Product
    {
        $user = User::factory()->create();
        $store = Store::create(['user_id' => $user->id, 'name' => 'Tech Store', 'slug' => 'tech-store-'.uniqid()]);
        $category = $category ?? Category::create(['name' => 'Điện Thoại', 'slug' => 'dien-thoai-'.uniqid()]);

        return Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str()->slug($name).'-'.uniqid(),
            'price' => $price,
            'stock' => 15,
            'sold_count' => 5,
            'status' => 'active',
            'main_image_url' => 'https://example.com/test.jpg',
        ]);
    }

    /**
     * Test homepage search and category filter.
     */
    public function test_homepage_search_and_category_filter(): void
    {
        $cat1 = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
        $cat2 = Category::create(['name' => 'Thời trang', 'slug' => 'thoi-trang']);

        $p1 = $this->createSampleProduct('Điện thoại iPhone 15', 25000000, $cat1);
        $p2 = $this->createSampleProduct('Áo sơ mi nam', 350000, $cat2);

        // Filter by search query
        $resSearch = $this->get('/?search=iPhone');
        $resSearch->assertStatus(200)
            ->assertSee('Điện thoại iPhone 15')
            ->assertDontSee('Áo sơ mi nam');

        // Filter by category
        $resCat = $this->get('/?category='.$cat2->id);
        $resCat->assertStatus(200)
            ->assertSee('Áo sơ mi nam')
            ->assertDontSee('Điện thoại iPhone 15');
    }

    /**
     * Test cart quantity update via PATCH and POST with action increment/decrement.
     */
    public function test_cart_quantity_update_actions(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = $this->createSampleProduct();

        // 1. Add to cart
        $addRes = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $addRes->assertStatus(200)->assertJson(['success' => true]);

        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $item = $cart->items()->firstOrFail();

        // 2. Update via PATCH with explicit quantity
        $patchRes = $this->patchJson('/cart/item/'.$item->id, [
            'quantity' => 3,
        ]);
        $patchRes->assertStatus(200)->assertJson(['success' => true, 'item_quantity' => 3]);

        // 3. Update via POST with action = 'increment' (used by frontend stepper)
        $incRes = $this->postJson('/cart/item/'.$item->id, [
            'action' => 'increment',
        ]);
        $incRes->assertStatus(200)->assertJson(['success' => true, 'item_quantity' => 4]);

        // 4. Update via POST with action = 'decrement'
        $decRes = $this->postJson('/cart/item/'.$item->id, [
            'action' => 'decrement',
        ]);
        $decRes->assertStatus(200)->assertJson(['success' => true, 'item_quantity' => 3]);
    }

    /**
     * Test remove selected items from cart.
     */
    public function test_cart_remove_selected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $p1 = $this->createSampleProduct('Product A', 100000);
        $p2 = $this->createSampleProduct('Product B', 200000);

        $this->postJson('/cart/add', ['product_id' => $p1->id, 'quantity' => 1]);
        $this->postJson('/cart/add', ['product_id' => $p2->id, 'quantity' => 1]);

        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $this->assertCount(2, $cart->items);

        // Remove selected items
        $res = $this->postJson('/cart/remove-selected');
        $res->assertStatus(200)->assertJson(['success' => true]);

        $cart->refresh();
        $this->assertCount(0, $cart->items);
    }

    /**
     * Test inline cart checkout process.
     */
    public function test_inline_cart_checkout_process(): void
    {
        $user = User::factory()->create();
        $product = $this->createSampleProduct('Smart Watch Series 9', 10000000);

        $this->actingAs($user);

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $initialStock = $product->fresh()->stock;

        $checkoutRes = $this->postJson('/checkout/process', [
            'payment_method' => 'cod',
        ]);

        $checkoutRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['order_code']);

        // Assert Order created in DB
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Assert stock decremented and sold_count incremented
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock);
        $this->assertEquals(7, $product->sold_count);
    }

    /**
     * Test product detail view renders real reviews.
     */
    public function test_product_detail_view_real_reviews(): void
    {
        $product = $this->createSampleProduct();
        $buyer = User::factory()->create(['name' => 'Lê Thị Mai']);

        Review::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Sản phẩm dùng rất thích và mượt mà!',
        ]);

        $res = $this->get('/product/'.$product->slug);
        $res->assertStatus(200)
            ->assertSee('Lê Thị Mai')
            ->assertSee('Sản phẩm dùng rất thích và mượt mà!');
    }

    /**
     * Test profile page calculates real counts.
     */
    public function test_profile_real_counts(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 2 orders for this user
        Order::create([
            'user_id' => $user->id,
            'order_code' => 'SM-TEST-001',
            'status' => 'pending',
            'total' => 500000,
            'shipping_address' => ['name' => 'Test'],
        ]);
        Order::create([
            'user_id' => $user->id,
            'order_code' => 'SM-TEST-002',
            'status' => 'completed',
            'total' => 800000,
            'shipping_address' => ['name' => 'Test'],
        ]);

        $product = $this->createSampleProduct();

        $res = $this->get('/profile');
        $res->assertStatus(200)
            ->assertSee('Đơn mua của tôi')
            ->assertSee(route('user.orders', ['status' => 'pending']), false)
            ->assertSee(route('user.orders', ['status' => 'completed']), false);
    }

    /**
     * Test public store front page renders products, search and filters.
     */
    public function test_public_store_page_renders_products_and_filters(): void
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Anker Official Flagship Store',
            'slug' => 'anker-official',
            'description' => 'Chuyên cung cấp củ sạc, cáp sạc, pin dự phòng chính hãng.',
            'status' => 'active',
        ]);

        $cat = Category::create(['name' => 'Phụ kiện', 'slug' => 'phu-kien']);

        $p1 = Product::create([
            'store_id' => $store->id,
            'category_id' => $cat->id,
            'name' => 'Củ sạc Anker 65W GaN',
            'slug' => 'cu-sac-anker-65w',
            'price' => 650000,
            'stock' => 50,
            'sold_count' => 120,
            'status' => 'active',
            'main_image_url' => 'https://example.com/anker65w.jpg',
        ]);

        $p2 = Product::create([
            'store_id' => $store->id,
            'category_id' => $cat->id,
            'name' => 'Pin dự phòng Anker 20000mAh',
            'slug' => 'pin-du-phong-anker-20000',
            'price' => 890000,
            'stock' => 30,
            'sold_count' => 45,
            'status' => 'active',
            'main_image_url' => 'https://example.com/ankerpin.jpg',
        ]);

        // 1. Visit store page
        $res = $this->get('/store/'.$store->slug);
        $res->assertStatus(200)
            ->assertSee('Anker Official Flagship Store')
            ->assertSee('Củ sạc Anker 65W GaN')
            ->assertSee('Pin dự phòng Anker 20000mAh');

        // 2. In-store search
        $resSearch = $this->get("/store/{$store->slug}?q=GaN");
        $resSearch->assertStatus(200)
            ->assertSee('Củ sạc Anker 65W GaN')
            ->assertDontSee('Pin dự phòng Anker 20000mAh');
    }

    /**
     * Test review flow end-to-end for completed order.
     */
    public function test_completed_order_review_flow_and_recalculation(): void
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $product = $this->createSampleProduct('Tai nghe Sony WH-1000XM5', 6500000);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_code' => 'SM-ORD-REVIEW-01',
            'status' => 'completed',
            'total' => 6500000,
            'shipping_address' => ['name' => 'Buyer Name', 'phone' => '0912345678', 'address' => 'Hanoi'],
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'subtotal' => $product->price,
        ]);

        // Submit review
        $response = $this->postJson(route('user.reviews.store'), [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Âm thanh cực đỉnh, chống ồn rất tốt!',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Assert review saved
        $this->assertDatabaseHas('reviews', [
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
        ]);

        // Assert product rating updated
        $product->refresh();
        $this->assertEquals(5.0, (float) $product->rating);
        $this->assertEquals(1, $product->reviews_count);

        // Assert duplicate review rejected
        $dupResponse = $this->postJson(route('user.reviews.store'), [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 4,
            'comment' => 'Thử đánh giá lại lần hai',
        ]);
        $dupResponse->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /**
     * Test pending order cannot submit review.
     */
    public function test_pending_order_cannot_be_reviewed(): void
    {
        $buyer = User::factory()->create();
        $this->actingAs($buyer);

        $product = $this->createSampleProduct('Chuột Logitech MX Master 3S', 2200000);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_code' => 'SM-ORD-PENDING-01',
            'status' => 'pending',
            'total' => 2200000,
            'shipping_address' => ['name' => 'Buyer Name'],
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'subtotal' => $product->price,
        ]);

        $res = $this->postJson(route('user.reviews.store'), [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Chưa nhận hàng mà đòi đánh giá',
        ]);

        $res->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /**
     * Test buyer password change flow.
     */
    public function test_buyer_password_change_flow(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);
        $this->actingAs($user);

        // Incorrect current password
        $resFail = $this->post(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);
        $resFail->assertSessionHasErrors(['current_password']);

        // Correct change
        $resSuccess = $this->post(route('profile.password'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);
        $resSuccess->assertRedirect()
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));
    }

    /**
     * Test social login without credentials gives informative error.
     */
    public function test_social_login_without_credentials_gives_informative_error(): void
    {
        config(['services.google.client_id' => null]);
        config(['services.google.client_secret' => null]);

        $res = $this->get(route('auth.social', 'google'));
        $res->assertRedirect(route('login'))
            ->assertSessionHasErrors(['login_id']);
    }

    /**
     * Test login page renders with empty inputs by default.
     */
    public function test_login_page_renders_with_empty_inputs(): void
    {
        $res = $this->get(route('login'));
        $res->assertStatus(200);
        $res->assertDontSee('value="example@gmail.com"', false);
        $res->assertDontSee('value="123456"', false);
    }

    /**
     * Test social login with credentials successfully authenticates user.
     */
    public function test_social_login_with_credentials_logs_in_user(): void
    {
        // Apple and Facebook are disabled
        $resApple = $this->get(route('auth.social', 'apple'));
        $resApple->assertRedirect(route('login'))
            ->assertSessionHasErrors(['login_id']);

        // Google redirect requires credentials
        config(['services.google.client_id' => '']);
        config(['services.google.client_secret' => '']);
        $this->get(route('auth.social', 'google'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['login_id']);

        // Google redirect with credentials builds Google OAuth URL
        config(['services.google.client_id' => 'dummy_google_client_id']);
        config(['services.google.client_secret' => 'dummy_google_client_secret']);
        $res = $this->get(route('auth.social', 'google'));
        $this->assertTrue(str_contains($res->getTargetUrl(), 'accounts.google.com'));

        // Mock Socialite Google User Callback
        $abstractUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google_123456');
        $abstractUser->shouldReceive('getName')->andReturn('Real User Google');
        $abstractUser->shouldReceive('getNickname')->andReturn('realuser');
        $abstractUser->shouldReceive('getEmail')->andReturn('realuser@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/photo.jpg');

        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $cbRes = $this->get(route('auth.social.callback', 'google'));
        $cbRes->assertRedirect(route('profile'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'realuser@gmail.com',
            'name' => 'Real User Google',
            'provider' => 'google',
        ]);
    }
}
