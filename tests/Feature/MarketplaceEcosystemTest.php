<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceEcosystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test live search suggestions API.
     */
    public function test_search_suggestions_api(): void
    {
        $category = Category::create(['name' => 'Điện Thoại', 'slug' => 'dien-thoai']);
        $user = User::factory()->create();
        $store = Store::create(['user_id' => $user->id, 'name' => 'Apple Store', 'slug' => 'apple-store']);

        Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'price' => 30000000,
            'stock' => 10,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/search/suggestions?q=iPhone');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'iPhone 15 Pro Max']);
    }

    /**
     * Test coupon calculation and apply API.
     */
    public function test_apply_coupon_ajax(): void
    {
        Coupon::create([
            'code' => 'GIAM10',
            'name' => 'Giảm 10%',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_value' => 100000,
            'max_discount_amount' => 50000,
            'is_active' => true,
        ]);

        $response = $this->postJson('/checkout/apply-coupon', [
            'code' => 'GIAM10',
            'subtotal' => 400000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'coupon_code' => 'GIAM10',
                'discount_amount' => 40000,
            ]);
    }

    /**
     * Test checkout order placement with COD.
     */
    public function test_checkout_order_placement(): void
    {
        $user = User::factory()->create();
        $store = Store::create(['user_id' => $user->id, 'name' => 'Shop Test', 'slug' => 'shop-test']);
        $category = Category::create(['name' => 'Gia Dụng', 'slug' => 'gia-dung']);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nồi Cơm Điện Thông Minh',
            'slug' => 'noi-com-dien-thong-minh',
            'price' => 1200000,
            'stock' => 20,
            'status' => 'active',
        ]);

        // Create user cart with item
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'is_selected' => true,
        ]);

        $response = $this->actingAs($user)->post('/checkout/order', [
            'recipient_name' => 'Nguyễn Văn Test',
            'phone' => '0912345678',
            'address_line' => '123 Đường Test, Hà Nội',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();

        // Verify Order created
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'cod',
            'status' => 'pending',
        ]);

        // Verify stock decremented
        $this->assertEquals(18, $product->fresh()->stock);
    }

    /**
     * Test IsSeller middleware protection.
     */
    public function test_is_seller_middleware_redirects_when_no_store(): void
    {
        $user = User::factory()->create(['role' => 'buyer']);

        // Non-seller access to seller dashboard should redirect to register
        $response = $this->actingAs($user)->get('/seller/dashboard');
        $response->assertRedirect(route('seller.register'));
    }

    /**
     * Test IsAdmin middleware protection.
     */
    public function test_is_admin_middleware_forbidden_for_buyer(): void
    {
        $user = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test Admin access allowed for admin role.
     */
    public function test_admin_dashboard_accessible_by_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test Admin dedicated profile access and update.
     */
    public function test_admin_profile_access_and_update(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Original Admin']);

        $response = $this->actingAs($admin)->get('/admin/profile');
        $response->assertStatus(200);
        $response->assertSee('Hồ Sơ Quản Trị Viên');

        $updateResponse = $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Super Admin Updated',
            'email' => 'newadmin@shopmart.vn',
            'phone' => '0988889999',
        ]);

        $updateResponse->assertRedirect(route('admin.profile'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Super Admin Updated',
            'email' => 'newadmin@shopmart.vn',
        ]);
    }

    /**
     * Test Seller dedicated profile access and store settings update.
     */
    public function test_seller_profile_access_and_update(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Tech Official',
            'slug' => 'tech-official',
            'status' => 'active',
        ]);

        $response = $this->actingAs($seller)->get('/seller/profile');
        $response->assertStatus(200);
        $response->assertSee('Thiết lập Gian hàng');

        $dashResponse = $this->actingAs($seller)->get('/seller/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Thiết lập Gian hàng');

        $revenueResponse = $this->actingAs($seller)->get('/seller/revenue');
        $revenueResponse->assertStatus(200);
        $revenueResponse->assertSee('Báo Cáo Doanh Thu');

        $updateResponse = $this->actingAs($seller)->put('/seller/profile', [
            'user_name' => 'Nguyen Van Seller',
            'store_name' => 'Tech Official VIP',
            'status' => 'active',
            'phone' => '19008888',
            'address' => 'Kho Hang A, Tan Binh, HCM',
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '9988776655',
            'bank_account_name' => 'NGUYEN VAN SELLER',
        ]);

        $updateResponse->assertRedirect(route('seller.profile'));
        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => 'Tech Official VIP',
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '9988776655',
        ]);
    }

    /**
     * Test Shopee coupon apply AJAX.
     */
    public function test_shopee_coupon_apply_ajax(): void
    {
        $coupon = Coupon::create([
            'code' => 'TESTVOUCHER',
            'name' => 'Test Voucher 50K',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_value' => 100000,
            'is_active' => true,
        ]);

        $response = $this->postJson('/checkout/apply-coupon', [
            'code' => 'TESTVOUCHER',
            'subtotal' => 200000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'coupon_code' => 'TESTVOUCHER',
            'discount_amount' => 50000,
        ]);
    }

    /**
     * Test admin login redirects immediately to admin dashboard.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_test@example.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'login_id' => 'admin_test@example.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test seller login redirects immediately to seller dashboard.
     */
    public function test_seller_login_redirects_to_seller_dashboard(): void
    {
        $seller = User::factory()->create([
            'email' => 'seller_test@example.com',
            'password' => bcrypt('seller'),
            'role' => 'seller',
        ]);

        $response = $this->post('/login', [
            'login_id' => 'seller_test@example.com',
            'password' => 'seller',
        ]);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertAuthenticatedAs($seller);
    }

    /**
     * Test buyer login redirects to home.
     */
    public function test_buyer_login_redirects_to_home(): void
    {
        $buyer = User::factory()->create([
            'email' => 'buyer_test@example.com',
            'password' => bcrypt('123456'),
            'role' => 'buyer',
        ]);

        $response = $this->post('/login', [
            'login_id' => 'buyer_test@example.com',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($buyer);
    }

    /**
     * Test voucher portal page renders coupons.
     */
    public function test_voucher_portal_page_renders_successfully(): void
    {
        Coupon::create([
            'code' => 'FREESHIP100',
            'name' => 'Freeship đơn từ 0Đ',
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'min_order_value' => 0,
            'is_active' => true,
        ]);

        $response = $this->get('/vouchers');

        $response->assertStatus(200);
        $response->assertSee('Kho Voucher');
        $response->assertSee('FREESHIP100');
    }

    /**
     * Test admin can view user details json endpoint.
     */
    public function test_admin_can_view_user_details_json(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create(['name' => 'Nguyen Chi Tiet']);

        $response = $this->actingAs($admin)->getJson("/admin/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => $targetUser->id,
                    'name' => 'Nguyen Chi Tiet',
                ],
            ]);
    }

    /**
     * Test seller can view and create store coupon.
     */
    public function test_seller_can_access_coupons_and_create_store_coupon(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Shop Test Coupon',
            'slug' => 'shop-test-coupon',
        ]);

        $response = $this->actingAs($seller)->get('/seller/coupons');
        $response->assertStatus(200);

        $createResponse = $this->actingAs($seller)->post('/seller/coupons', [
            'code' => 'SHOP10K',
            'name' => 'Giảm 10K Shop',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'min_order_value' => 50000,
        ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('coupons', [
            'code' => 'SHOP10K',
            'store_id' => $store->id,
            'discount_value' => 10000,
        ]);
    }

    /**
     * Test seller can view orders page with packing slip.
     */
    public function test_seller_can_access_orders_page_with_packing_slip(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Shop Test Orders',
            'slug' => 'shop-test-orders',
        ]);

        $category = Category::first() ?? Category::create(['name' => 'Thời Trang Test', 'slug' => 'thoi-trang-test']);
        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Ao Khoac Seller Test',
            'slug' => 'ao-khoac-seller-test-'.uniqid(),
            'price' => 250000,
            'stock' => 10,
            'status' => 'active',
        ]);

        $order = Order::create([
            'user_id' => $seller->id,
            'order_code' => 'ORD-TEST-1234',
            'status' => 'pending',
            'total' => 250000,
            'shipping_address' => [
                'name' => 'Nguyen Van Buyer',
                'phone' => '0988776655',
                'address' => '123 Pham Van Dong, Ha Noi',
            ],
            'payment_method' => 'cod',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 250000,
            'subtotal' => 250000,
            'selected_variant' => 'Size L',
        ]);

        $response = $this->actingAs($seller)->get('/seller/orders');
        $response->assertStatus(200);
        $response->assertSee('In vận đơn');
        $response->assertSee('ORD-TEST-1234');
    }
}
