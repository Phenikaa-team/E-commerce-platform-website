<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/profile')->assertRedirect('/login');
        $this->get('/profile/info')->assertRedirect('/login');
        $this->get('/profile/addresses')->assertRedirect('/login');
    }

    /**
     * Test user can view profile overview dashboard without personal info & address form modals.
     */
    public function test_authenticated_user_can_view_profile_overview(): void
    {
        $user = User::factory()->create(['name' => 'Nguyen Van A', 'username' => 'nguyenvana']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Tài khoản của tôi');
        $response->assertSee('Đơn mua của tôi');
        $response->assertSee('Sổ địa chỉ nhận hàng');
    }

    /**
     * Test user can view dedicated personal info page.
     */
    public function test_authenticated_user_can_view_personal_info_page(): void
    {
        $user = User::factory()->create([
            'name' => 'Tran Thi B',
            'username' => 'tranthib',
            'phone' => '0987654321',
        ]);

        $response = $this->actingAs($user)->get('/profile/info');

        $response->assertStatus(200);
        $response->assertSee('Hồ Sơ Của Tôi');
        $response->assertSee('tranthib');
        $response->assertSee('0987654321');
    }

    /**
     * Test user can update personal info.
     */
    public function test_authenticated_user_can_update_personal_info(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'username' => 'oldusername',
        ]);

        $response = $this->actingAs($user)->post('/profile/update', [
            'name' => 'New Name Updated',
            'username' => 'newusername',
            'phone' => '0911223344',
            'gender' => 'Nam',
            'birthday' => '01/01/1995',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name Updated',
            'username' => 'newusername',
            'phone' => '0911223344',
            'gender' => 'Nam',
            'birthday' => '01/01/1995',
        ]);
    }

    /**
     * Test user can view dedicated addresses page.
     */
    public function test_authenticated_user_can_view_addresses_page(): void
    {
        $user = User::factory()->create();
        UserAddress::create([
            'user_id' => $user->id,
            'recipient_name' => 'Nguyen Van A',
            'phone' => '0901234567',
            'address_line' => '123 Pho Hue, Hai Ba Trung, Ha Noi',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->get('/profile/addresses');

        $response->assertStatus(200);
        $response->assertSee('Địa chỉ của tôi');
        $response->assertSee('123 Pho Hue, Hai Ba Trung, Ha Noi');
        $response->assertSee('Mặc định');
    }

    /**
     * Test adding, updating, and setting default address.
     */
    public function test_address_management_lifecycle(): void
    {
        $user = User::factory()->create();

        // 1. Add new address
        $resAdd = $this->actingAs($user)->post('/profile/address', [
            'recipient_name' => 'Le Van C',
            'phone' => '0933445566',
            'address_line' => '456 Le Duan, Da Nang',
            'is_default' => 1,
        ]);
        $resAdd->assertSessionHas('success');

        $address = UserAddress::where('user_id', $user->id)->first();
        $this->assertNotNull($address);
        $this->assertEquals('456 Le Duan, Da Nang', $address->address_line);
        $this->assertTrue((bool) $address->is_default);

        // 2. Update address
        $resUpdate = $this->actingAs($user)->put('/profile/address/'.$address->id, [
            'recipient_name' => 'Le Van C (Updated)',
            'phone' => '0933445577',
            'address_line' => '789 Nguyen Van Linh, Da Nang',
            'is_default' => 1,
        ]);
        $resUpdate->assertSessionHas('success');

        $address->refresh();
        $this->assertEquals('789 Nguyen Van Linh, Da Nang', $address->address_line);
        $this->assertEquals('Le Van C (Updated)', $address->recipient_name);

        // 3. Add second address
        $this->actingAs($user)->post('/profile/address', [
            'recipient_name' => 'Nguoi Nhan 2',
            'phone' => '0944556677',
            'address_line' => '101 Tran Phu, Ha Noi',
        ]);

        $address2 = UserAddress::where('user_id', $user->id)->where('id', '!=', $address->id)->first();
        $this->assertNotNull($address2);
        $this->assertFalse((bool) $address2->is_default);

        // 4. Set second address as default
        $this->actingAs($user)->post('/profile/address/'.$address2->id.'/default');
        $address->refresh();
        $address2->refresh();
        $this->assertFalse((bool) $address->is_default);
        $this->assertTrue((bool) $address2->is_default);

        // 5. Delete first address
        $this->actingAs($user)->delete('/profile/address/'.$address->id);
        $this->assertDatabaseMissing('user_addresses', ['id' => $address->id]);
    }

    /**
     * Test regular user can delete account with password verification.
     */
    public function test_user_can_delete_account_with_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        // Wrong password fails
        $failResponse = $this->actingAs($user)->delete('/profile/account', [
            'confirm_password' => 'wrongpassword',
        ]);
        $failResponse->assertSessionHasErrors(['confirm_password']);
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // Correct password succeeds
        $response = $this->actingAs($user)->delete('/profile/account', [
            'confirm_password' => 'secret123',
        ]);
        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertGuest();
    }

    /**
     * Test admin user cannot self-delete account.
     */
    public function test_admin_cannot_delete_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->actingAs($admin)->delete('/profile/account', [
            'confirm_password' => 'admin123',
        ]);
        $response->assertSessionHasErrors(['delete_account']);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
