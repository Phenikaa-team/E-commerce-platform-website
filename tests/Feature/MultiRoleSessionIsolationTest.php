<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleSessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_respective_login_portal(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('admin.login'));

        $sellerResponse = $this->get('/seller/dashboard');
        $sellerResponse->assertRedirect(route('seller.login'));

        $buyerResponse = $this->get('/checkout');
        $buyerResponse->assertRedirect(route('login'));
    }

    public function test_scope_session_sets_isolated_cookie_names(): void
    {
        $adminRes = $this->get('/admin/login');
        $adminRes->assertOk();
        $this->assertEquals(config('session.cookie'), config('session.cookie'));

        // Check buyer dev login
        $buyerRes = $this->get('/dev-login/buyer');
        $buyerRes->assertRedirect(route('cart'));
        $buyerRes->assertCookie(config('session.base_cookie'));

        // Check seller dev login
        $sellerRes = $this->get('/seller/dev-login');
        $sellerRes->assertRedirect(route('seller.dashboard'));
        $sellerRes->assertCookie(config('session.base_cookie').'_seller');

        // Check admin dev login
        $adminLoginRes = $this->get('/admin/dev-login');
        $adminLoginRes->assertRedirect(route('admin.dashboard'));
        $adminLoginRes->assertCookie(config('session.base_cookie').'_admin');
    }

    public function test_admin_and_seller_and_buyer_can_logout_from_their_portals(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@shopmart.com',
        ]);

        $seller = User::factory()->create([
            'role' => 'seller',
            'email' => 'seller@shopmart.com',
        ]);
        Store::create(['user_id' => $seller->id, 'name' => 'Seller Shop', 'slug' => 'seller-shop', 'status' => 'active']);

        // Admin logout
        $this->actingAs($admin)
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        // Seller logout
        $this->actingAs($seller)
            ->post('/seller/logout')
            ->assertRedirect(route('seller.login'));

        // Buyer logout
        $buyer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($buyer)
            ->post('/logout')
            ->assertRedirect(route('login'));
    }
}
