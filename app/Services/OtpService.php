<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\UserOtp;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    public function __construct(
        protected EsmsService $esmsService
    ) {}

    /**
     * Generate and dispatch an OTP to target (email or phone).
     *
     * @return array{success: bool, message: string, simulated?: bool, otp?: string, target?: string, channel?: string}
     */
    public function sendOtp(?User $user, string $target, string $channel, string $action): array
    {
        $channel = strtolower($channel);
        if (! in_array($channel, ['email', 'sms'])) {
            return ['success' => false, 'message' => 'Kênh gửi mã không hợp lệ.'];
        }

        // Rate limit: Không cho spam liên tục trong 15 giây
        $recent = UserOtp::where('target', $target)
            ->where('action', $action)
            ->where('created_at', '>=', Carbon::now()->subSeconds(15))
            ->latest('created_at')
            ->first();

        if ($recent) {
            $secondsLeft = max(1, (int) ceil(15 - Carbon::now()->diffInSeconds($recent->created_at, false)));

            return [
                'success' => false,
                'message' => "Vui lòng chờ {$secondsLeft} giây trước khi yêu cầu mã OTP mới.",
            ];
        }

        // Generate 6-digit numeric OTP
        $otp = sprintf('%06d', random_int(100000, 999999));
        $expiresAt = Carbon::now()->addMinutes(5);

        // Invalidate older unused OTPs for this target & action
        UserOtp::where('target', $target)
            ->where('action', $action)
            ->where('is_used', false)
            ->delete();

        // Save new OTP record
        $record = UserOtp::create([
            'user_id' => $user?->id,
            'target' => $target,
            'channel' => $channel,
            'action' => $action,
            'otp_code' => $otp,
            'attempts' => 0,
            'is_used' => false,
            'expires_at' => $expiresAt,
        ]);

        $actionTitle = match ($action) {
            'change_password' => 'Đổi mật khẩu tài khoản',
            'reset_password' => 'Đặt lại mật khẩu',
            default => 'Xác minh tài khoản',
        };

        if ($channel === 'email') {
            $maskedTarget = preg_replace('/(?<=.{2}).(?=.*@)/u', '*', $target);
            try {
                Mail::to($target)->send(new OtpMail($otp, $actionTitle, $user?->name ?? 'Quý khách'));

                return [
                    'success' => true,
                    'message' => "Mã OTP đã được gửi đến email {$maskedTarget}. Vui lòng kiểm tra hộp thư.",
                    'channel' => 'email',
                    'target' => $target,
                    'masked_target' => $maskedTarget,
                ];
            } catch (Exception $e) {
                Log::error('[OtpService Mail Error] '.$e->getMessage());

                // Fallback for local testing if mail driver is invalid/failing
                return [
                    'success' => true,
                    'message' => "Mã OTP đã được gửi đến email {$maskedTarget} (Log test: {$otp})",
                    'simulated' => true,
                    'otp' => $otp,
                    'channel' => 'email',
                    'target' => $target,
                    'masked_target' => $maskedTarget,
                ];
            }
        }

        // SMS Channel (eSMS.vn)
        $smsResult = $this->esmsService->sendOtp($target, $otp);

        return [
            'success' => $smsResult['success'],
            'message' => $smsResult['message'],
            'simulated' => $smsResult['simulated'] ?? false,
            'otp' => $smsResult['otp'] ?? null,
            'channel' => 'sms',
            'target' => $target,
        ];
    }

    /**
     * Verify submitted OTP code.
     *
     * @return array{success: bool, message: string, token?: string, otp_record?: UserOtp}
     */
    public function verifyOtp(string $target, string $action, string $otpCode): array
    {
        $record = UserOtp::where('target', $target)
            ->where('action', $action)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (! $record) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu xác thực OTP hoặc mã đã hết hiệu lực.',
            ];
        }

        if ($record->isExpired()) {
            return [
                'success' => false,
                'message' => 'Mã OTP đã hết hạn (5 phút). Vui lòng yêu cầu mã mới.',
            ];
        }

        if ($record->attempts >= 5) {
            $record->update(['is_used' => true]);

            return [
                'success' => false,
                'message' => 'Bạn đã nhập sai quá 5 lần. Mã OTP đã bị vô hiệu hóa.',
            ];
        }

        if (trim($record->otp_code) !== trim($otpCode)) {
            $record->increment('attempts');
            $remaining = 5 - $record->attempts;

            return [
                'success' => false,
                'message' => "Mã OTP không chính xác. Bạn còn {$remaining} lần thử.",
            ];
        }

        // Valid OTP! Mark as verified and generate secure single-use token
        $resetToken = Str::random(48);
        $record->update([
            'is_used' => true,
            'token' => $resetToken,
        ]);

        return [
            'success' => true,
            'message' => 'Xác thực mã OTP thành công!',
            'token' => $resetToken,
            'otp_record' => $record,
        ];
    }

    /**
     * Validate the verified token before changing password.
     */
    public function validateToken(string $token, string $action): ?UserOtp
    {
        return UserOtp::where('token', $token)
            ->where('action', $action)
            ->where('updated_at', '>=', Carbon::now()->subMinutes(15)) // Token valid for 15 minutes
            ->first();
    }
}
