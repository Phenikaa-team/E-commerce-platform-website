<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Test 3rd party user sees password setup warning and can set password without current password.
     */
    public function test_third_party_user_can_set_password_without_current_password(): void
    {
        $googleUser = User::factory()->create([
            'provider' => 'google',
            'provider_id' => '123456789',
            'password_set' => false,
        ]);

        $this->assertFalse($googleUser->hasCustomPassword());

        // Profile view contains the warning alert and modal
        $response = $this->actingAs($googleUser)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Bảo vệ tài khoản: Bạn chưa thiết lập mật khẩu riêng');
        $response->assertSee('Thiết lập mật khẩu tài khoản');

        // Setting password directly without current_password succeeds
        $res = $this->actingAs($googleUser)->post('/profile/password', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $res->assertSessionHas('success');
        $googleUser->refresh();
        $this->assertTrue($googleUser->hasCustomPassword());
        $this->assertTrue(Hash::check('newpassword123', $googleUser->password));
    }

    /**
     * Test regular user with custom password requires current password to update.
     */
    public function test_regular_user_requires_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
            'provider' => null,
            'password_set' => true,
        ]);

        // Attempt without current_password fails
        $fail = $this->actingAs($user)->post('/profile/password', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $fail->assertSessionHasErrors(['current_password']);

        // With correct current_password succeeds
        $success = $this->actingAs($user)->post('/profile/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $success->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
