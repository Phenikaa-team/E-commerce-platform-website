<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SandboxPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private Store $store;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create();
        $seller = User::factory()->create(['role' => 'seller']);
        $this->store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Demo Store',
            'slug' => 'demo-store',
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
        ]);
        $this->product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $category->id,
            'name' => 'Sản phẩm Test ZaloPay',
            'slug' => 'san-pham-test-zalopay',
            'price' => 150000,
            'stock' => 10,
            'status' => 'active',
        ]);
    }

    /**
     * Test checkout with ZaloPay creates pending order and redirects to ZaloPay order_url.
     */
    public function test_checkout_order_with_zalopay_redirects_to_zalopay_gateway(): void
    {
        Http::fake([
            'https://sb-openapi.zalopay.vn/*' => Http::response([
                'return_code' => 1,
                'return_message' => 'Giao dịch thành công',
                'order_url' => 'https://qcgateway.zalopay.vn/openinapp?order=test12345',
            ], 200),
        ]);

        $cart = Cart::create(['user_id' => $this->buyer->id]);
        $cart->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 150000,
            'is_selected' => true,
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.order'), [
            'recipient_name' => 'Nguyen Van Test',
            'phone' => '0901234567',
            'address_line' => '123 Đường Test, Hà Nội',
            'payment_method' => 'zalopay',
        ]);

        $response->assertRedirect('https://qcgateway.zalopay.vn/openinapp?order=test12345');

        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('zalopay', $order->payment_method);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertNotEmpty($order->zalopay_app_trans_id);
    }

    /**
     * Test a signed ZaloPay server callback marks the order as paid.
     */
    public function test_zalopay_callback_success_marks_order_as_paid(): void
    {
        $order = Order::create([
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'order_code' => 'ORD-ZALO-102',
            'checkout_group_id' => 'GRP-ZALO-102',
            'total' => 300000,
            'subtotal' => 300000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'zalopay',
            'payment_status' => 'pending',
            'zalopay_app_trans_id' => '261016_2553_test-order',
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test, Hà Nội',
            ],
        ]);

        $callbackData = json_encode([
            'app_id' => (int) config('services.zalopay.app_id'),
            'app_trans_id' => $order->zalopay_app_trans_id,
            'amount' => 300000,
            'zp_trans_id' => 987654321,
        ], JSON_THROW_ON_ERROR);
        $mac = hash_hmac('sha256', $callbackData, (string) config('services.zalopay.key2'));

        $response = $this->postJson(route('checkout.zalopay-callback'), [
            'data' => $callbackData,
            'mac' => $mac,
            'type' => 1,
        ]);

        $response->assertOk()->assertJson(['return_code' => 1]);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertEquals('987654321', $order->zalopay_trans_id);
    }

    /**
     * A browser redirect alone must not be trusted as proof of payment.
     */
    public function test_zalopay_return_does_not_mark_order_paid_from_bare_status(): void
    {
        $order = Order::create([
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'order_code' => 'ORD-ZALO-103',
            'checkout_group_id' => 'GRP-ZALO-103',
            'total' => 300000,
            'subtotal' => 300000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'zalopay',
            'payment_status' => 'pending',
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test, Hà Nội',
            ],
        ]);

        $response = $this->actingAs($this->buyer)->get(route('checkout.zalopay-return', [
            'txn_ref' => 'GRP-ZALO-103',
            'status' => '1',
        ]));

        $response->assertRedirect(route('checkout.success', [
            'order_code' => 'ORD-ZALO-103',
            'group' => 'GRP-ZALO-103',
        ]));
        $this->assertEquals('pending', $order->refresh()->payment_status);
    }

    /**
     * Test ZaloPay return callback with failed or cancelled status marks payment as failed.
     */
    public function test_zalopay_return_callback_failure_marks_payment_failed(): void
    {
        $order = Order::create([
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'order_code' => 'ORD-ZALO-FAIL',
            'checkout_group_id' => 'GRP-ZALO-FAIL',
            'total' => 300000,
            'subtotal' => 300000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'zalopay',
            'payment_status' => 'pending',
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test, Hà Nội',
            ],
        ]);

        $response = $this->actingAs($this->buyer)->get(route('checkout.zalopay-return', [
            'txn_ref' => 'GRP-ZALO-FAIL',
            'status' => '0', // Failed / cancelled code
        ]));

        $response->assertRedirect(route('checkout.success', [
            'order_code' => 'ORD-ZALO-FAIL',
            'group' => 'GRP-ZALO-FAIL',
        ]));
        $response->assertSessionHas('warning');

        $order->refresh();
        $this->assertEquals('failed', $order->payment_status);
        $this->assertEquals('pending', $order->status);
        $this->assertTrue($order->isAwaitingPayment());
    }

    /**
     * Test buyer cancels pending order restores stock and updates status.
     */
    public function test_buyer_cancels_order_restores_stock(): void
    {
        $initialStock = $this->product->stock;

        $order = Order::create([
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'order_code' => 'ORD-CANCEL-1',
            'checkout_group_id' => 'GRP-CANCEL-1',
            'total' => 150000,
            'subtotal' => 150000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test',
            ],
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 2,
            'unit_price' => 150000,
            'subtotal' => 300000,
        ]);

        // Giả sử tồn kho đã bị trừ 2 trước đó
        $this->product->decrement('stock', 2);

        $response = $this->actingAs($this->buyer)->post(route('user.orders.cancel', $order->order_code));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);
    }

    /**
     * Test retrying online payment initiates a fresh ZaloPay transaction within 12 hours.
     */
    public function test_buyer_can_retry_payment_within_twelve_hours(): void
    {
        Http::fake([
            'https://sb-openapi.zalopay.vn/*' => Http::response([
                'return_code' => 1,
                'return_message' => 'Tạo đơn mới thành công',
                'order_url' => 'https://qcgateway.zalopay.vn/openinapp?order=retry12345',
            ], 200),
        ]);

        $order = Order::create([
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'order_code' => 'ORD-RETRY-1',
            'checkout_group_id' => 'GRP-RETRY-1',
            'total' => 200000,
            'subtotal' => 200000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'zalopay',
            'payment_status' => 'failed',
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test',
            ],
            'created_at' => now()->subHours(2), // 2 hours ago (well within 12h)
        ]);

        $response = $this->actingAs($this->buyer)->get(route('checkout.retry-payment', 'GRP-RETRY-1'));
        $response->assertRedirect('https://qcgateway.zalopay.vn/openinapp?order=retry12345');
    }

    /**
     * Test retrying online payment is blocked if the 12-hour grace period has expired.
     */
    public function test_buyer_cannot_retry_payment_after_twelve_hours(): void
    {
        $order = Order::create([
            'user_id' => $this->buyer->id,
            'store_id' => $this->store->id,
            'order_code' => 'ORD-EXPIRED-1',
            'checkout_group_id' => 'GRP-EXPIRED-1',
            'total' => 200000,
            'subtotal' => 200000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'zalopay',
            'payment_status' => 'failed',
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test',
            ],
            'created_at' => now()->subHours(13), // 13 hours ago (expired)
        ]);

        $response = $this->actingAs($this->buyer)->get(route('checkout.retry-payment', 'ORD-EXPIRED-1'));
        $response->assertRedirect(route('user.orders.show', 'ORD-EXPIRED-1'));
        $response->assertSessionHas('error');
    }
}
