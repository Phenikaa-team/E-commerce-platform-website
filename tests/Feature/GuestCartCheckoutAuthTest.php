<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCartCheckoutAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleProduct(string $name = 'iPhone 15 Pro Max', int $price = 30000000): Product
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Store '.uniqid(),
            'slug' => 'store-'.uniqid(),
        ]);
        $category = Category::create([
            'name' => 'Tech '.uniqid(),
            'slug' => 'tech-'.uniqid(),
        ]);

        return Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str()->slug($name).'-'.uniqid(),
            'price' => $price,
            'stock' => 20,
            'sold_count' => 0,
            'status' => 'active',
            'main_image_url' => 'https://example.com/item.jpg',
        ]);
    }

    /**
     * Test guest cannot access checkout page directly without logging in.
     */
    public function test_guest_is_redirected_to_login_when_accessing_checkout(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/login');
        $response->assertSessionHas('warning', 'Vui lòng đăng nhập hoặc đăng ký tài khoản để tiếp tục thanh toán.');
    }

    /**
     * Test guest cannot submit inline cart checkout.
     */
    public function test_guest_cannot_process_inline_checkout(): void
    {
        $response = $this->postJson('/checkout/process', [
            'payment_method' => 'wallet',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test guest adding product to cart saves intent and returns 401 with login redirect.
     */
    public function test_guest_add_to_cart_requires_auth_and_saves_pending_action(): void
    {
        $product = $this->createSampleProduct();

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
            'variant' => 'Titan Đen - 256GB',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'requires_auth' => true,
            'redirect' => route('login'),
        ]);

        $this->assertEquals([
            'action' => 'add_to_cart',
            'product_id' => $product->id,
            'quantity' => 2,
            'variant' => 'Titan Đen - 256GB',
            'return_url' => url()->previous(),
        ], session('pending_cart_action'));
    }

    /**
     * Test guest clicking Buy Now saves buy_now intent and returns 401 with login redirect.
     */
    public function test_guest_buy_now_requires_auth_and_saves_pending_buy_now_action(): void
    {
        $product = $this->createSampleProduct();

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
            'buy_now' => true,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'requires_auth' => true,
            'redirect' => route('login'),
        ]);

        $pending = session('pending_cart_action');
        $this->assertNotNull($pending);
        $this->assertEquals('buy_now', $pending['action']);
        $this->assertEquals($product->id, $pending['product_id']);
    }

    /**
     * Test after logging in, pending add_to_cart action is automatically added to user's cart.
     */
    public function test_logging_in_executes_pending_add_to_cart_action(): void
    {
        $product = $this->createSampleProduct();
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Simulate guest adding to cart
        $this->withSession([
            'pending_cart_action' => [
                'action' => 'add_to_cart',
                'product_id' => $product->id,
                'quantity' => 3,
                'variant' => '256GB',
                'return_url' => '/san-pham/'.$product->slug,
            ],
        ]);

        $response = $this->post('/login', [
            'login_id' => 'customer@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/san-pham/'.$product->slug);
        $response->assertSessionMissing('pending_cart_action');

        // Check user cart
        $cart = $user->cart->fresh();
        $this->assertNotNull($cart);
        $this->assertCount(1, $cart->items);
        $this->assertEquals(3, $cart->items->first()->quantity);
        $this->assertEquals('256GB', $cart->items->first()->selected_variant);
    }

    /**
     * Test after logging in, pending buy_now action adds item, selects it, and redirects to checkout.
     */
    public function test_logging_in_executes_pending_buy_now_action_and_redirects_to_checkout(): void
    {
        $product = $this->createSampleProduct();
        $user = User::factory()->create([
            'email' => 'buyer@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Simulate guest clicking Buy Now
        $this->withSession([
            'pending_cart_action' => [
                'action' => 'buy_now',
                'product_id' => $product->id,
                'quantity' => 1,
                'variant' => 'Titan Tự Nhiên',
                'return_url' => '/san-pham/'.$product->slug,
            ],
        ]);

        $response = $this->post('/login', [
            'login_id' => 'buyer@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionMissing('pending_cart_action');

        $cart = $user->cart->fresh();
        $this->assertNotNull($cart);
        $this->assertCount(1, $cart->items);
        $this->assertTrue((bool) $cart->items->first()->is_selected);
    }

    /**
     * Test if guest never logs in, session action expires or remains unused without affecting anything.
     */
    public function test_guest_browsing_without_logging_in_does_not_create_database_cart(): void
    {
        $product = $this->createSampleProduct();

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Guest browses home or catalog
        $response = $this->get('/');
        $response->assertStatus(200);

        // No cart is attached to any user in DB
        $this->assertDatabaseMissing('carts', [
            'user_id' => null,
        ]);
    }
}
