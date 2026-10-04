<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get(route('password.forgot'));
        $response->assertStatus(200);
        $response->assertSee('Quên Mật Khẩu?');
        $response->assertSee('Gửi Mã OTP Xác Thực');
    }

    public function test_user_can_request_and_verify_forgot_password_otp_and_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'buyer_test@example.com',
            'phone' => '0987654321',
            'password' => Hash::make('old_password_123'),
        ]);

        // 1. Request OTP via email
        $sendResponse = $this->postJson(route('password.forgot.send-otp'), [
            'account' => 'buyer_test@example.com',
            'channel' => 'email',
        ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        $otpRecord = UserOtp::where('target', 'buyer_test@example.com')->latest()->first();
        $this->assertNotNull($otpRecord);
        $this->assertNotEmpty($otpRecord->otp_code);

        // 2. Verify OTP
        $verifyResponse = $this->postJson(route('password.forgot.verify-otp'), [
            'target' => 'buyer_test@example.com',
            'otp_code' => $otpRecord->otp_code,
        ]);

        $verifyResponse->assertStatus(200);
        $verifyResponse->assertJson(['success' => true]);
        $resetToken = $verifyResponse->json('reset_token');
        $this->assertNotEmpty($resetToken);

        // 3. Reset password with verified token
        $resetResponse = $this->postJson(route('password.forgot.reset'), [
            'reset_token' => $resetToken,
            'password' => 'new_password_888',
            'password_confirmation' => 'new_password_888',
        ]);

        $resetResponse->assertStatus(200);
        $resetResponse->assertJson(['success' => true]);

        // Verify password changed
        $user->refresh();
        $this->assertTrue(Hash::check('new_password_888', $user->password));
    }

    public function test_authenticated_user_can_update_password_using_profile_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'profile_test@example.com',
            'phone' => '0912345678',
            'password' => Hash::make('initial_secret'),
        ]);

        $this->actingAs($user);

        // 1. Send OTP to profile email
        $sendResponse = $this->postJson(route('profile.password.send-otp'), [
            'channel' => 'email',
        ]);
        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        $otpRecord = UserOtp::where('target', 'profile_test@example.com')->latest()->first();
        $this->assertNotNull($otpRecord);

        // 2. Submit password update with OTP
        $updateResponse = $this->postJson(route('profile.password.otp-update'), [
            'channel' => 'email',
            'otp_code' => $otpRecord->otp_code,
            'password' => 'updated_secret_999',
            'password_confirmation' => 'updated_secret_999',
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['success' => true]);

        $user->refresh();
        $this->assertTrue(Hash::check('updated_secret_999', $user->password));
    }
}
