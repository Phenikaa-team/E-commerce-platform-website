<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\FileUploadService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerRegisterController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Display seller store onboarding form.
     */
    public function showRegister(): View|RedirectResponse
    {
        $store = auth()->user()->store;

        if ($store?->status === 'active' && auth()->user()->isSeller()) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.register', [
            'store' => $store,
            'applicationStatus' => $store?->status,
        ]);
    }

    /**
     * Send OTP for seller store registration verification.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $user = auth()->user();
        $channel = $request->input('channel', 'email');
        $target = $channel === 'sms' ? $request->input('phone', $user->phone) : $user->email;

        if (empty($target)) {
            return response()->json([
                'success' => false,
                'message' => $channel === 'sms' ? 'Vui lòng nhập số điện thoại để nhận mã OTP.' : 'Không tìm thấy địa chỉ email của bạn.',
            ], 422);
        }

        $result = $this->otpService->sendOtp($user, $target, $channel, 'seller_registration');

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Verify OTP code for seller registration.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
            'channel' => 'required|string|in:email,sms',
        ]);

        $user = auth()->user();
        $channel = $request->input('channel');
        $target = $channel === 'sms' ? $request->input('phone', $user->phone) : $user->email;

        $verification = $this->otpService->verifyOtp($target, 'seller_registration', $request->input('otp_code'));

        if (! $verification['success']) {
            return response()->json($verification, 422);
        }

        // Store OTP verification state in session
        session(['seller_reg_otp_verified' => true, 'seller_reg_target' => $target]);

        return response()->json([
            'success' => true,
            'message' => 'Xác thực OTP thành công! Bạn có thể tiếp tục hoàn tất hồ sơ.',
        ]);
    }

    /**
     * Handle store creation and grant seller role.
     */
    public function register(Request $request): RedirectResponse
    {
        $store = auth()->user()->store;

        if ($store?->status === 'pending') {
            return redirect()->route('seller.register')->with('info', 'Hồ sơ mở gian hàng của bạn đang chờ quản trị viên phê duyệt.');
        }

        if ($store?->status === 'active' && auth()->user()->isSeller()) {
            return redirect()->route('seller.dashboard');
        }

        // Security check: Must have verified OTP before submitting store application
        if (! session('seller_reg_otp_verified', false) && ! $store) {
            return redirect()->route('seller.register')
                ->withInput()
                ->with('error', 'Vui lòng hoàn tất xác thực mã OTP trước khi gửi hồ sơ đăng ký.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:150|unique:stores,name'.($store ? ','.$store->id : ''),
            'description' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'business_type' => 'required|string|in:individual,business',
            'tax_code' => 'nullable|string|max:50',
            'representative_name' => 'nullable|string|max:150',
            'id_card_number' => 'nullable|string|max:50',
            'package_plan' => 'required|string|in:free,pro,enterprise',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'string|in:shopmart_pay,bank_transfer,momo,vnpay,cod',
            'shipping_partners' => 'nullable|array',
            'shipping_partners.*' => 'string|in:shopmart_logistics,ghn,ghtk,viettel,jnt,express_2h',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:150',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:3072',
            'id_card_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'business_license_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'terms_agreed' => 'accepted',
        ], [
            'name.required' => 'Vui lòng nhập tên gian hàng.',
            'name.unique' => 'Tên gian hàng này đã có người đăng ký. Vui lòng chọn tên khác.',
            'phone.required' => 'Vui lòng cung cấp số điện thoại liên hệ.',
            'address.required' => 'Vui lòng nhập địa chỉ kho hoặc cửa hàng.',
            'description.required' => 'Vui lòng giới thiệu sơ lược về gian hàng.',
            'terms_agreed.accepted' => 'Bạn cần đọc và đồng ý với điều khoản & chính sách bán hàng của ShopMart.',
        ]);

        $logoUrl = $store?->logo_url ?: project_asset('images/placeholders/store-logo-placeholder.svg');
        if ($request->hasFile('logo')) {
            $uploaded = FileUploadService::upload($request->file('logo'), 'stores/user-'.auth()->id().'/logo');
            $logoUrl = $uploaded['url'];
        }

        $idCardImageUrl = $store?->id_card_image;
        if ($request->hasFile('id_card_image')) {
            $uploadedId = FileUploadService::upload($request->file('id_card_image'), 'stores/user-'.auth()->id().'/documents');
            $idCardImageUrl = $uploadedId['url'];
        }

        $businessLicenseUrl = $store?->business_license_image;
        if ($request->hasFile('business_license_image')) {
            $uploadedLic = FileUploadService::upload($request->file('business_license_image'), 'stores/user-'.auth()->id().'/documents');
            $businessLicenseUrl = $uploadedLic['url'];
        }

        $defaultPayments = ['shopmart_pay', 'bank_transfer', 'cod'];
        $defaultShipping = ['shopmart_logistics', 'ghn', 'viettel'];

        $storeData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.rand(100, 999),
            'description' => $data['description'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'business_type' => $data['business_type'],
            'tax_code' => $data['tax_code'] ?? null,
            'representative_name' => $data['representative_name'] ?? null,
            'id_card_number' => $data['id_card_number'] ?? null,
            'package_plan' => $data['package_plan'],
            'payment_methods' => $data['payment_methods'] ?? $defaultPayments,
            'shipping_partners' => $data['shipping_partners'] ?? $defaultShipping,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'bank_account_name' => $data['bank_account_name'] ?? null,
            'logo_url' => $logoUrl,
            'id_card_image' => $idCardImageUrl,
            'business_license_image' => $businessLicenseUrl,
            'banner_url' => $store?->banner_url ?: project_asset('images/placeholders/store-banner-placeholder.svg'),
            'rating' => 5.0,
            'response_rate' => '100%',
            'followers' => '1',
            'is_mall' => ($data['business_type'] === 'business' && $data['package_plan'] === 'enterprise'),
            'online_status' => 'Vừa mới online',
            'status' => 'pending',
        ];

        if ($store) {
            $store->update($storeData);
        } else {
            Store::create(array_merge(['user_id' => auth()->id()], $storeData));
        }

        // Keep the account as a buyer until an administrator approves the application.
        $userUpdates = [];
        if (empty(auth()->user()->phone)) {
            $userUpdates['phone'] = $data['phone'];
        }
        auth()->user()->update($userUpdates);

        // Clear session flags
        session()->forget(['seller_reg_otp_verified', 'seller_reg_target']);

        return redirect()->route('seller.register')->with('success', 'Hồ sơ mở gian hàng đã được gửi thành công! Quản trị viên ShopMart sẽ thẩm định và kích hoạt trong vòng 1-2 ngày làm việc.');
    }
}
