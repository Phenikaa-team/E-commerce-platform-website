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

    public function test_dev_logins_authenticate_respective_roles(): void
    {
        $adminRes = $this->get('/admin/login');
        $adminRes->assertOk();

        // Check buyer dev login
        $buyerRes = $this->get('/dev-login/buyer');
        $buyerRes->assertRedirect(route('cart'));
        $this->assertTrue(auth()->check());
        $this->assertEquals('buyer', auth()->user()->role);

        // Check seller dev login
        $sellerRes = $this->get('/seller/dev-login');
        $sellerRes->assertRedirect(route('seller.dashboard'));
        $this->assertTrue(auth()->check());
        $this->assertEquals('seller', auth()->user()->role);

        // Check admin dev login
        $adminLoginRes = $this->get('/admin/dev-login');
        $adminLoginRes->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(auth()->check());
        $this->assertEquals('admin', auth()->user()->role);
    }

    public function test_role_permissions_guard_respective_portals(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        Store::create(['user_id' => $seller->id, 'name' => 'Seller Shop', 'slug' => 'seller-shop', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Buyer cannot access admin dashboard -> 403 Forbidden
        $this->actingAs($buyer)->get('/admin/dashboard')->assertForbidden();

        // Buyer cannot access seller dashboard -> redirected to seller.register
        $this->actingAs($buyer)->get('/seller/dashboard')->assertRedirect(route('seller.register'));

        // Seller cannot access admin dashboard -> 403 Forbidden
        $this->actingAs($seller)->get('/admin/dashboard')->assertForbidden();

        // Admin accessing seller dashboard -> redirected to admin.dashboard
        $this->actingAs($admin)->get('/seller/dashboard')->assertRedirect(route('admin.dashboard'));
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
