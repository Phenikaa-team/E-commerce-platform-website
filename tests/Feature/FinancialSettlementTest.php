<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\FinancialSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialSettlementTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_calculates_exact_financial_breakdown_and_escrow(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Công Nghệ Store',
            'slug' => 'cong-nghe-store',
            'status' => 'active',
        ]);

        $buyer = User::factory()->create(['role' => 'buyer', 'coins' => 100]);
        $product = Product::create([
            'store_id' => $store->id,
            'name' => 'Sản phẩm Test',
            'slug' => 'san-pham-test-1',
            'price' => 200000,
            'stock' => 50,
        ]);

        $order = Order::create([
            'store_id' => $store->id,
            'user_id' => $buyer->id,
            'order_code' => 'SM-TEST-001',
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 200000,
            'shipping_fee' => 30000,
            'discount_amount' => 25000, // 10k shop + 15k platform
            'total' => 205000, // 200k - 10k - 15k + 30k = 205k
            'shipping_address' => ['name' => 'Nguyen Van A', 'phone' => '0901234567', 'address' => 'Hanoi'],
        ]);

        $service = app(FinancialSettlementService::class);
        $financial = $service->recordOrderCreation($order, [
            'gross_merchandise_amount' => 200000,
            'shop_discount' => 10000,
            'platform_discount' => 15000,
            'freeship_discount' => 0,
            'shipping_fee_paid' => 30000,
            'total_buyer_paid' => 205000,
        ]);

        $this->assertEquals(200000, (float) $financial->gross_merchandise_amount);
        $this->assertEquals(10000, (float) $financial->shop_discount);
        $this->assertEquals(190000, (float) $financial->net_merchandise_amount);
        $this->assertEquals(4750, (float) $financial->payment_fee); // 2.5% of 190k
        $this->assertEquals(5700, (float) $financial->commission_fee); // 3.0% of 190k
        $this->assertEquals(179550, (float) $financial->shop_earning); // 190k - 4.75k - 5.7k
        $this->assertEquals(10450, (float) $financial->platform_gross_fee); // 4.75k + 5.7k
        $this->assertEquals(15000, (float) $financial->platform_voucher_cost);
        $this->assertEquals(-4550, (float) $financial->platform_net_earning); // Sàn tài trợ 15k, thu 10.45k => marketing spend -4.55k
        $this->assertEquals(570, (int) $financial->cashback_points); // 10% of commission
        $this->assertEquals('holding', $financial->escrow_status);

        $wallet = $store->getOrCreateWallet();
        $this->assertEquals(179550, (float) $wallet->pending_balance);
        $this->assertEquals(0, (float) $wallet->balance);
    }

    public function test_seller_completing_order_settles_escrow_to_available_balance_and_awards_cashback(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Fashion Store',
            'slug' => 'fashion-store',
            'status' => 'active',
        ]);

        $buyer = User::factory()->create(['role' => 'buyer', 'coins' => 100]);

        $order = Order::create([
            'store_id' => $store->id,
            'user_id' => $buyer->id,
            'order_code' => 'SM-TEST-002',
            'status' => 'shipping',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 200000,
            'shipping_fee' => 30000,
            'discount_amount' => 10000,
            'total' => 220000,
            'shipping_address' => ['name' => 'Test User', 'phone' => '0901234567', 'address' => 'HCM'],
        ]);

        $service = app(FinancialSettlementService::class);
        $service->recordOrderCreation($order, [
            'gross_merchandise_amount' => 200000,
            'shop_discount' => 10000,
            'platform_discount' => 0,
            'shipping_fee_paid' => 30000,
            'total_buyer_paid' => 220000,
        ]);

        $wallet = $store->getOrCreateWallet();
        $this->assertEquals(179550, (float) $wallet->pending_balance);
        $this->assertEquals(0, (float) $wallet->balance);

        // Seller updates status to completed
        $response = $this->actingAs($seller)->post(route('seller.orders.status', $order->id), [
            'status' => 'completed',
        ]);
        $response->assertSessionHas('success');

        $order->refresh();
        $financial = $order->financial;
        $wallet->refresh();
        $buyer->refresh();

        $this->assertEquals('completed', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('settled', $financial->escrow_status);
        $this->assertNotNull($financial->settled_at);

        // Wallet balance updated
        $this->assertEquals(0, (float) $wallet->pending_balance);
        $this->assertEquals(179550, (float) $wallet->balance);
        $this->assertEquals(179550, (float) $wallet->total_earned);

        // Transaction created
        $this->assertDatabaseHas('wallet_transactions', [
            'store_wallet_id' => $wallet->id,
            'order_id' => $order->id,
            'type' => 'order_settlement',
            'amount' => 179550,
        ]);

        // Buyer received loyalty cashback points (100 + 570 = 670 coins)
        $this->assertEquals(670, $buyer->coins);
    }

    public function test_buyer_confirm_receipt_completes_order_and_settles_funds(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Book Store',
            'slug' => 'book-store',
            'status' => 'active',
        ]);

        $buyer = User::factory()->create(['role' => 'buyer', 'coins' => 50]);

        $order = Order::create([
            'store_id' => $store->id,
            'user_id' => $buyer->id,
            'order_code' => 'SM-CONFIRM-123',
            'status' => 'shipping',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 100000,
            'shipping_fee' => 20000,
            'discount_amount' => 0,
            'total' => 120000,
            'shipping_address' => ['name' => 'Buyer', 'phone' => '0901234567', 'address' => 'Danang'],
        ]);

        app(FinancialSettlementService::class)->recordOrderCreation($order, [
            'gross_merchandise_amount' => 100000,
            'shop_discount' => 0,
            'platform_discount' => 0,
            'shipping_fee_paid' => 20000,
            'total_buyer_paid' => 120000,
        ]);

        // Buyer confirms receipt
        $response = $this->actingAs($buyer)->post(route('user.orders.confirm', $order->order_code));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('completed', $order->status);
        $this->assertEquals('paid', $order->payment_status);

        $wallet = $store->getOrCreateWallet();
        $this->assertEquals(94500, (float) $wallet->balance); // 100k - 2.5k - 3k = 94.5k
    }

    public function test_seller_can_withdraw_available_balance(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Beauty Store',
            'slug' => 'beauty-store',
            'status' => 'active',
        ]);

        $wallet = $store->getOrCreateWallet();
        $wallet->update([
            'balance' => 500000,
        ]);

        $response = $this->actingAs($seller)->post(route('seller.finances.withdraw'), [
            'amount' => 200000,
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'NGUYEN VAN SELLER',
        ]);

        $response->assertSessionHas('success');

        $wallet->refresh();
        $this->assertEquals(300000, (float) $wallet->balance);
        $this->assertEquals(200000, (float) $wallet->total_withdrawn);

        $this->assertDatabaseHas('wallet_transactions', [
            'store_wallet_id' => $wallet->id,
            'type' => 'withdrawal',
            'amount' => 200000,
        ]);
    }
}
