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

class MultiVendorCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private User $sellerA;

    private User $sellerB;

    private Store $storeA;

    private Store $storeB;

    private Category $category;

    private Product $productA1;

    private Product $productA2;

    private Product $productB1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $this->sellerA = User::factory()->create(['role' => 'seller']);
        $this->sellerB = User::factory()->create(['role' => 'seller']);

        $this->storeA = Store::create([
            'user_id' => $this->sellerA->id,
            'name' => 'Shop A Fashion',
            'slug' => 'shop-a-fashion',
        ]);

        $this->storeB = Store::create([
            'user_id' => $this->sellerB->id,
            'name' => 'Shop B Tech',
            'slug' => 'shop-b-tech',
        ]);

        $this->category = Category::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $this->productA1 = Product::create([
            'store_id' => $this->storeA->id,
            'category_id' => $this->category->id,
            'name' => 'Ao Thun Shop A',
            'slug' => 'ao-thun-shop-a',
            'price' => 100000,
            'stock' => 50,
            'status' => 'active',
        ]);

        $this->productA2 = Product::create([
            'store_id' => $this->storeA->id,
            'category_id' => $this->category->id,
            'name' => 'Quan Jean Shop A',
            'slug' => 'quan-jean-shop-a',
            'price' => 200000,
            'stock' => 30,
            'status' => 'active',
        ]);

        $this->productB1 = Product::create([
            'store_id' => $this->storeB->id,
            'category_id' => $this->category->id,
            'name' => 'Tai Nghe Shop B',
            'slug' => 'tai-nghe-shop-b',
            'price' => 500000,
            'stock' => 20,
            'status' => 'active',
        ]);
    }

    /**
     * Test multi-store checkout splits orders by store with shared checkout_group_id.
     */
    public function test_multi_vendor_cart_splits_into_separate_orders_per_store(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create([
            'product_id' => $this->productA1->id,
            'quantity' => 2,
            'unit_price' => $this->productA1->price,
            'is_selected' => true,
        ]);
        $cart->items()->create([
            'product_id' => $this->productA2->id,
            'quantity' => 1,
            'unit_price' => $this->productA2->price,
            'is_selected' => true,
        ]);
        $cart->items()->create([
            'product_id' => $this->productB1->id,
            'quantity' => 1,
            'unit_price' => $this->productB1->price,
            'is_selected' => true,
        ]);

        $response = $this->actingAs($this->buyer)->post('/checkout/order', [
            'recipient_name' => 'Nguyen Van Buyer',
            'phone' => '0988888888',
            'address_line' => '456 Le Duan, Da Nang',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();

        // Exactly 2 orders created
        $orders = Order::where('user_id', $this->buyer->id)->get();
        $this->assertCount(2, $orders);

        $orderA = $orders->firstWhere('store_id', $this->storeA->id);
        $orderB = $orders->firstWhere('store_id', $this->storeB->id);

        $this->assertNotNull($orderA);
        $this->assertNotNull($orderB);

        // Same checkout_group_id
        $this->assertNotEmpty($orderA->checkout_group_id);
        $this->assertEquals($orderA->checkout_group_id, $orderB->checkout_group_id);

        // Distinct order_codes
        $this->assertNotEquals($orderA->order_code, $orderB->order_code);

        // Verify items segregation
        $this->assertCount(2, $orderA->items);
        $this->assertTrue($orderA->items->pluck('product_id')->contains($this->productA1->id));
        $this->assertTrue($orderA->items->pluck('product_id')->contains($this->productA2->id));

        $this->assertCount(1, $orderB->items);
        $this->assertTrue($orderB->items->pluck('product_id')->contains($this->productB1->id));

        // Subtotals
        $this->assertEquals(400000, $orderA->subtotal); // 2*100k + 1*200k
        $this->assertEquals(500000, $orderB->subtotal); // 1*500k

        // Stock decrements
        $this->assertEquals(48, $this->productA1->fresh()->stock);
        $this->assertEquals(29, $this->productA2->fresh()->stock);
        $this->assertEquals(19, $this->productB1->fresh()->stock);
    }

    /**
     * Test Seller A cannot see or access Seller B's orders.
     */
    public function test_seller_isolation_on_orders(): void
    {
        // Place multi-store order
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create(['product_id' => $this->productA1->id, 'quantity' => 1, 'unit_price' => 100000, 'is_selected' => true]);
        $cart->items()->create(['product_id' => $this->productB1->id, 'quantity' => 1, 'unit_price' => 500000, 'is_selected' => true]);

        $this->actingAs($this->buyer)->post('/checkout/order', [
            'recipient_name' => 'Buyer Test',
            'phone' => '0988888888',
            'address_line' => 'Test Address',
            'payment_method' => 'cod',
        ]);

        $orderA = Order::where('store_id', $this->storeA->id)->firstOrFail();
        $orderB = Order::where('store_id', $this->storeB->id)->firstOrFail();

        // Seller A viewing their dashboard orders
        $response = $this->actingAs($this->sellerA)->get('/seller/orders');
        $response->assertStatus(200);
        $response->assertSee($orderA->order_code);
        $response->assertDontSee($orderB->order_code);

        // Seller A trying to update status of Seller B's order -> should 404
        $updateResponse = $this->actingAs($this->sellerA)->post("/seller/orders/{$orderB->id}/status", [
            'status' => 'shipping',
        ]);
        $updateResponse->assertStatus(404);

        // Seller A updating their own order -> success
        $validUpdate = $this->actingAs($this->sellerA)->post("/seller/orders/{$orderA->id}/status", [
            'status' => 'processing',
        ]);
        $validUpdate->assertRedirect();
        $this->assertEquals('processing', $orderA->fresh()->status);
    }

    /**
     * Test Store::orders() relationship returns only that store's orders.
     */
    public function test_store_orders_relationship(): void
    {
        $orderA = Order::create([
            'store_id' => $this->storeA->id,
            'user_id' => $this->buyer->id,
            'order_code' => 'SM-TEST-A1',
            'status' => 'completed',
            'subtotal' => 300000,
            'total' => 300000,
            'payment_method' => 'cod',
            'shipping_address' => ['name' => 'Buyer', 'phone' => '0912345678', 'address' => 'Hanoi'],
        ]);

        $orderB = Order::create([
            'store_id' => $this->storeB->id,
            'user_id' => $this->buyer->id,
            'order_code' => 'SM-TEST-B1',
            'status' => 'completed',
            'subtotal' => 500000,
            'total' => 500000,
            'payment_method' => 'cod',
            'shipping_address' => ['name' => 'Buyer', 'phone' => '0912345678', 'address' => 'Hanoi'],
        ]);

        $this->assertTrue($this->storeA->orders->contains('id', $orderA->id));
        $this->assertFalse($this->storeA->orders->contains('id', $orderB->id));
        $this->assertEquals(1, $this->storeA->orders()->count());
    }

    /**
     * Test CartWebController::processCheckout also splits orders by store.
     */
    public function test_inline_cart_checkout_splits_orders(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create(['product_id' => $this->productA1->id, 'quantity' => 1, 'unit_price' => 100000, 'is_selected' => true]);
        $cart->items()->create(['product_id' => $this->productB1->id, 'quantity' => 1, 'unit_price' => 500000, 'is_selected' => true]);

        $response = $this->actingAs($this->buyer)->postJson('/checkout/process', [
            'payment_method' => 'cod',
            'recipient_name' => 'Buyer Inline',
            'phone' => '0912345678',
            'address_line' => 'Hanoi, Vietnam',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $orders = Order::where('user_id', $this->buyer->id)->get();
        $this->assertCount(2, $orders);
        $this->assertNotNull($orders->firstWhere('store_id', $this->storeA->id));
        $this->assertNotNull($orders->firstWhere('store_id', $this->storeB->id));
    }

    /**
     * Test buy now flow creates order only for buy now product and does not touch existing cart.
     */
    public function test_buy_now_flow_creates_order_without_touching_cart(): void
    {
        // Put an item in cart
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cartItem = $cart->items()->create([
            'product_id' => $this->productB1->id,
            'quantity' => 1,
            'unit_price' => 500000,
            'is_selected' => true,
        ]);

        // Place buy-now session
        $response = $this->actingAs($this->buyer)
            ->withSession([
                'buy_now_item' => [
                    'product_id' => $this->productA1->id,
                    'quantity' => 1,
                    'unit_price' => 100000,
                    'selected_variant' => 'Size M',
                ],
            ])
            ->post('/checkout/order', [
                'buy_now' => 1,
                'recipient_name' => 'Buyer Test',
                'phone' => '0988888888',
                'address_line' => 'Test Address',
                'payment_method' => 'cod',
            ]);

        $response->assertRedirect();

        // Only 1 order created for Store A
        $orders = Order::where('user_id', $this->buyer->id)->get();
        $this->assertCount(1, $orders);
        $this->assertEquals($this->storeA->id, $orders->first()->store_id);

        // Cart item for Store B is untouched!
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'product_id' => $this->productB1->id,
        ]);
    }

    /**
     * Test shop vouchers apply strictly to the matching store order.
     */
    public function test_store_voucher_applies_strictly_to_matching_store(): void
    {
        $shopCouponA = Coupon::create([
            'store_id' => $this->storeA->id,
            'code' => 'SHOPA20K',
            'name' => 'Giảm 20k Shop A',
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'min_order_value' => 50000,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create(['product_id' => $this->productA1->id, 'quantity' => 1, 'unit_price' => 100000, 'is_selected' => true]);
        $cart->items()->create(['product_id' => $this->productB1->id, 'quantity' => 1, 'unit_price' => 500000, 'is_selected' => true]);

        $this->actingAs($this->buyer)->post('/checkout/order', [
            'recipient_name' => 'Buyer Test',
            'phone' => '0988888888',
            'address_line' => 'Test Address',
            'payment_method' => 'cod',
            'shop_voucher_codes' => [
                $this->storeA->id => 'SHOPA20K',
            ],
        ]);

        $orderA = Order::where('store_id', $this->storeA->id)->firstOrFail();
        $orderB = Order::where('store_id', $this->storeB->id)->firstOrFail();

        // Store A got discount
        $this->assertEquals(20000, $orderA->discount_amount);
        $this->assertStringContainsString('SHOPA20K', (string) $orderA->coupon_code);

        // Store B got 0 discount from Store A voucher
        $this->assertEquals(0, $orderB->discount_amount);
    }

    /**
     * Test order success page displays all orders in the checkout group.
     */
    public function test_order_success_page_renders_all_orders_in_group(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create(['product_id' => $this->productA1->id, 'quantity' => 1, 'unit_price' => 100000, 'is_selected' => true]);
        $cart->items()->create(['product_id' => $this->productB1->id, 'quantity' => 1, 'unit_price' => 500000, 'is_selected' => true]);

        $this->actingAs($this->buyer)->post('/checkout/order', [
            'recipient_name' => 'Buyer Test',
            'phone' => '0988888888',
            'address_line' => 'Test Address',
            'payment_method' => 'cod',
        ]);

        $orderA = Order::where('store_id', $this->storeA->id)->firstOrFail();
        $orderB = Order::where('store_id', $this->storeB->id)->firstOrFail();

        $response = $this->actingAs($this->buyer)->get(route('checkout.success', [
            'order_code' => $orderA->order_code,
            'group' => $orderA->checkout_group_id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($orderA->order_code);
        $response->assertSee($orderB->order_code);
        $response->assertSee('Shop A Fashion');
        $response->assertSee('Shop B Tech');
    }

    /**
     * Test buyer can reorder items without SQLite no such column error.
     */
    public function test_buyer_can_reorder_without_sqlite_error(): void
    {
        $order = Order::create([
            'store_id' => $this->storeA->id,
            'user_id' => $this->buyer->id,
            'order_code' => 'SM-REORDER-123',
            'status' => 'completed',
            'subtotal' => 100000,
            'total' => 100000,
            'payment_method' => 'cod',
            'shipping_address' => ['name' => 'Buyer', 'phone' => '0912345678', 'address' => 'Hanoi'],
        ]);

        $order->items()->create([
            'product_id' => $this->productA1->id,
            'product_name' => $this->productA1->name,
            'selected_variant' => 'Titan Tu Nhien',
            'quantity' => 1,
            'unit_price' => $this->productA1->price,
            'subtotal' => $this->productA1->price,
        ]);

        // Trigger reorder
        $response = $this->actingAs($this->buyer)->post("/user/orders/{$order->order_code}/reorder");
        $response->assertRedirect(route('cart'));

        $cart = Cart::where('user_id', $this->buyer->id)->firstOrFail();
        $this->assertCount(1, $cart->items);
        $this->assertEquals($this->productA1->id, $cart->items->first()->product_id);
        $this->assertEquals('Titan Tu Nhien', $cart->items->first()->selected_variant);
    }

    /**
     * Test checkout requires mandatory address and fails if address is missing.
     */
    public function test_checkout_fails_if_address_is_missing(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create([
            'product_id' => $this->productA1->id,
            'quantity' => 1,
            'unit_price' => $this->productA1->price,
            'is_selected' => true,
        ]);

        $response = $this->actingAs($this->buyer)->post('/checkout/order', [
            'recipient_name' => '',
            'phone' => '',
            'address_line' => '',
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors(['recipient_name', 'phone', 'address_line']);
        $this->assertEquals(0, Order::where('user_id', $this->buyer->id)->count());
    }

    /**
     * Test quick address creation saves address and sets default.
     */
    public function test_quick_add_address_saves_successfully(): void
    {
        $response = $this->actingAs($this->buyer)->postJson('/checkout/quick-address', [
            'recipient_name' => 'Tran Thi B',
            'phone' => '0912345678',
            'address_line' => '123 Nguyen Hue',
            'city_district' => 'Quan 1, TP HCM',
            'is_default' => true,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $addr = $this->buyer->addresses()->first();
        $this->assertNotNull($addr);
        $this->assertEquals('Tran Thi B', $addr->recipient_name);
        $this->assertStringContainsString('123 Nguyen Hue', $addr->address_line);
        $this->assertStringContainsString('Quan 1, TP HCM', $addr->address_line);
        $this->assertTrue($addr->is_default);
    }
}
