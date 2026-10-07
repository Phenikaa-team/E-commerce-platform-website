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
            'status' => 'approved',
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
    }

    /**
     * Test ZaloPay return callback with status 1 marks order as paid.
     */
    public function test_zalopay_return_callback_success_marks_order_as_paid(): void
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
            'shipping_address' => [
                'name' => 'Nguyen Van Test',
                'phone' => '0901234567',
                'address' => '123 Đường Test, Hà Nội',
            ],
        ]);

        $response = $this->actingAs($this->buyer)->get(route('checkout.zalopay-return', [
            'txn_ref' => 'GRP-ZALO-102',
            'status' => '1',
        ]));

        $response->assertRedirect(route('checkout.success', [
            'order_code' => 'ORD-ZALO-102',
            'group' => 'GRP-ZALO-102',
        ]));

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
    }
}
