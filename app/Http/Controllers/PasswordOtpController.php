<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordOtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Send OTP for authenticated profile password update.
     */
    public function sendProfileOtp(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
        }

        $validated = $request->validate([
            'channel' => ['required', 'in:email,sms'],
        ]);

        $channel = $validated['channel'];
        $target = $channel === 'email' ? $user->email : $user->phone;

        if (empty($target)) {
            return response()->json([
                'success' => false,
                'message' => $channel === 'sms'
                    ? 'Tài khoản chưa cập nhật số điện thoại. Vui lòng cập nhật số điện thoại ở hồ sơ trước hoặc chọn nhận OTP qua Email.'
                    : 'Tài khoản chưa có email.',
            ], 422);
        }

        $result = $this->otpService->sendOtp($user, $target, $channel, 'change_password');

        return response()->json($result);
    }

    /**
     * Verify OTP and change password for authenticated user in Profile.
     */
    public function updateProfilePasswordWithOtp(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return back()->withErrors(['otp' => 'Vui lòng đăng nhập.']);
        }

        $validated = $request->validate([
            'channel' => ['required', 'in:email,sms'],
            'otp_code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP 6 số.',
            'otp_code.size' => 'Mã OTP gồm 6 chữ số.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $channel = $validated['channel'];
        $target = $channel === 'email' ? $user->email : $user->phone;

        if (empty($target)) {
            return back()->withErrors(['otp' => 'Không tìm thấy thông tin liên lạc tương ứng.']);
        }

        $verification = $this->otpService->verifyOtp($target, 'change_password', $validated['otp_code']);
        if (! $verification['success']) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $verification['message']], 422);
            }

            return back()->withErrors(['otp_code' => $verification['message']])->withInput();
        }

        // OTP Verified -> Update password
        $user->update([
            'password' => Hash::make($validated['password']),
            'password_set' => true,
        ]);

        $msg = 'Đổi mật khẩu thành công qua xác thực OTP '.strtoupper($channel).'!';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Show Forgot Password view.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP for Forgot Password.
     */
    public function sendForgotPasswordOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account' => ['required', 'string'],
            'channel' => ['required', 'in:email,sms'],
        ], [
            'account.required' => 'Vui lòng nhập Email hoặc Số điện thoại tài khoản.',
        ]);

        $account = trim($validated['account']);
        $channel = $validated['channel'];

        // Find user by email, phone, or username
        $user = User::where('email', $account)
            ->orWhere('phone', $account)
            ->orWhere('username', $account)
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản với thông tin đã nhập.',
            ], 404);
        }

        $target = $channel === 'email' ? $user->email : $user->phone;
        if (empty($target)) {
            return response()->json([
                'success' => false,
                'message' => $channel === 'sms'
                    ? 'Tài khoản này chưa đăng ký số điện thoại. Vui lòng chọn nhận OTP qua Email.'
                    : 'Tài khoản này chưa có địa chỉ Email.',
            ], 422);
        }

        $result = $this->otpService->sendOtp($user, $target, $channel, 'reset_password');

        // Mask target for privacy in response (e.g. t***@gmail.com, 09***678)
        $masked = $channel === 'email'
            ? preg_replace('/(?<=.{2}).(?=.*@)/u', '*', $target)
            : substr($target, 0, 3).'****'.substr($target, -3);

        $result['masked_target'] = $masked;
        $result['target'] = $target;

        return response()->json($result);
    }

    /**
     * Verify OTP for Forgot Password and return reset token.
     */
    public function verifyForgotPasswordOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target' => ['required', 'string'],
            'otp_code' => ['required', 'string', 'size:6'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP 6 số.',
            'otp_code.size' => 'Mã OTP gồm 6 chữ số.',
        ]);

        $result = $this->otpService->verifyOtp($validated['target'], 'reset_password', $validated['otp_code']);
        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác minh OTP thành công. Hãy nhập mật khẩu mới.',
            'reset_token' => $result['token'],
        ]);
    }

    /**
     * Reset password using verified token.
     */
    public function resetPasswordWithToken(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reset_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'reset_token.required' => 'Thiếu mã xác nhận đặt lại mật khẩu.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $otpRecord = $this->otpService->validateToken($validated['reset_token'], 'reset_password');
        if (! $otpRecord) {
            $msg = 'Phiên đặt lại mật khẩu đã hết hạn hoặc không hợp lệ. Vui lòng thực hiện lại.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->route('password.forgot')->with('error', $msg);
        }

        // Find user by target or user_id
        $user = $otpRecord->user_id
            ? User::find($otpRecord->user_id)
            : User::where('email', $otpRecord->target)->orWhere('phone', $otpRecord->target)->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy người dùng.'], 404);
        }

        // Update password and invalidate token
        $user->update([
            'password' => Hash::make($validated['password']),
            'password_set' => true,
        ]);
        $otpRecord->delete();

        $msg = 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay với mật khẩu mới.';
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', $msg);
    }
}
