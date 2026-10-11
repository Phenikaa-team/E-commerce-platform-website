<?php

namespace Tests\Feature\Seller;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SellerSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_view_settings_page(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'password' => Hash::make('password123'),
        ]);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Shop Test Settings',
            'slug' => 'shop-test-settings',
            'status' => 'active',
        ]);

        $response = $this->actingAs($seller)->get(route('seller.settings'));

        $response->assertStatus(200);
        $response->assertSee('Tài Khoản &amp; An Toàn Gian Hàng', false);
        $response->assertSee('Đóng Cửa Hàng &amp; Chuyển Về Tài Khoản Thường', false);
    }

    public function test_seller_cannot_close_store_if_pending_orders_exist(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'password' => Hash::make('password123'),
        ]);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Shop Test With Orders',
            'slug' => 'shop-test-orders',
            'status' => 'active',
        ]);

        $buyer = User::factory()->create(['role' => 'buyer']);

        // Create an active shipping order
        Order::create([
            'store_id' => $store->id,
            'user_id' => $buyer->id,
            'order_code' => 'SM-ORD-999',
            'status' => 'shipping',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => 100000,
            'shipping_fee' => 20000,
            'total' => 120000,
            'shipping_address' => ['name' => 'Test', 'phone' => '0912345678', 'address' => 'HN'],
        ]);

        $response = $this->actingAs($seller)->post(route('seller.settings.close-store'), [
            'confirm_text' => 'ĐÓNG CỬA HÀNG',
            'password' => 'password123',
            'agree_terms' => 'on',
        ]);

        $response->assertSessionHasErrors('close_store_error');
        $this->assertEquals('active', $store->fresh()->status);
        $this->assertEquals('seller', $seller->fresh()->role);
    }

    public function test_seller_can_close_store_when_all_conditions_met(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'password' => Hash::make('password123'),
        ]);

        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Shop Ready To Close',
            'slug' => 'shop-ready-close',
            'status' => 'active',
        ]);

        $wallet = $store->getOrCreateWallet();
        $wallet->update([
            'pending_balance' => 0,
            'balance' => 0,
        ]);

        $response = $this->actingAs($seller)->post(route('seller.settings.close-store'), [
            'confirm_text' => 'ĐÓNG CỬA HÀNG',
            'password' => 'password123',
            'agree_terms' => 'on',
            'reason' => 'Tạm ngừng để chuyển đổi mặt hàng',
        ]);

        $response->assertRedirect(route('profile.info'));
        $this->assertEquals('closed', $store->fresh()->status);
        $this->assertEquals('buyer', $seller->fresh()->role);

        // Reopen store test
        $reopenResponse = $this->actingAs($seller)->post(route('seller.settings.reopen'));
        $reopenResponse->assertRedirect(route('seller.dashboard'));
        $this->assertEquals('active', $store->fresh()->status);
        $this->assertEquals('seller', $seller->fresh()->role);
    }
}
