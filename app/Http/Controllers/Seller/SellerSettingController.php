<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SellerSettingController extends Controller
{
    /**
     * Display the store security and management settings page.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('seller.register');
        }

        $wallet = $store->getOrCreateWallet();

        // Check conditions for closing store
        $activeOrdersCount = Order::where('store_id', $store->id)
            ->whereIn('status', ['pending', 'processing', 'shipping'])
            ->count();

        $pendingEscrow = (float) $wallet->pending_balance;
        $availableBalance = (float) $wallet->balance;

        $canCloseStore = ($activeOrdersCount === 0 && $pendingEscrow <= 0);

        return view('seller.settings.index', [
            'user' => $user,
            'store' => $store,
            'wallet' => $wallet,
            'activeOrdersCount' => $activeOrdersCount,
            'pendingEscrow' => $pendingEscrow,
            'availableBalance' => $availableBalance,
            'canCloseStore' => $canCloseStore,
        ]);
    }

    /**
     * Update store operational info.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('seller.register');
        }

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'store_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif,avif', 'max:3072'],
            'store_banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif,avif', 'max:5120'],
            'status' => ['required', 'in:active,inactive'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
        ], [
            'store_name.required' => 'Tên gian hàng không được để trống.',
            'store_logo.image' => 'Ảnh logo không hợp lệ.',
            'store_banner.image' => 'Ảnh bìa không hợp lệ.',
        ]);

        $storeLogoUrl = $store->logo_url;
        if ($request->hasFile('store_logo')) {
            $uploaded = FileUploadService::upload($request->file('store_logo'), 'stores/'.$store->id.'/logo', $store->logo_url);
            $storeLogoUrl = $uploaded['url'];
        }

        $storeBannerUrl = $store->banner_url;
        if ($request->hasFile('store_banner')) {
            $uploaded = FileUploadService::upload($request->file('store_banner'), 'stores/'.$store->id.'/banner', $store->banner_url);
            $storeBannerUrl = $uploaded['url'];
        }

        $store->update([
            'name' => $validated['store_name'],
            'description' => $validated['store_description'] ?? $store->description,
            'logo_url' => $storeLogoUrl,
            'banner_url' => $storeBannerUrl,
            'phone' => $validated['phone'] ?? $store->phone,
            'address' => $validated['address'] ?? $store->address,
            'status' => $validated['status'],
            'bank_name' => $validated['bank_name'] ?? $store->bank_name,
            'bank_account_number' => $validated['bank_account_number'] ?? $store->bank_account_number,
            'bank_account_name' => $validated['bank_account_name'] ?? $store->bank_account_name,
        ]);

        return redirect()->route('seller.settings')->with('success', 'Đã cập nhật cài đặt gian hàng thành công!');
    }

    /**
     * Close the store, stop selling, and revert user back to normal buyer account.
     */
    public function closeStore(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('home');
        }

        $request->validate([
            'password' => ['required', 'string'],
            'confirm_text' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:500'],
            'agree_terms' => ['accepted'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu để xác nhận danh tính.',
            'confirm_text.required' => 'Vui lòng gõ cụm từ xác nhận đóng gian hàng.',
            'agree_terms.accepted' => 'Bạn phải xác nhận đã đọc và đồng ý với điều lệ đóng cửa hàng.',
        ]);

        // Verify confirmation phrase
        if (trim(mb_strtoupper($request->confirm_text)) !== 'ĐÓNG CỬA HÀNG') {
            return back()->withErrors([
                'confirm_text' => 'Cụm từ xác nhận không chính xác. Vui lòng nhập đúng "ĐÓNG CỬA HÀNG".',
            ])->withInput();
        }

        // Verify user password
        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu xác nhận không chính xác.',
            ])->withInput();
        }

        // Verify prerequisite conditions
        $activeOrders = Order::where('store_id', $store->id)
            ->whereIn('status', ['pending', 'processing', 'shipping'])
            ->count();

        if ($activeOrders > 0) {
            return back()->withErrors([
                'close_store_error' => "Không thể đóng cửa hàng: Gian hàng vẫn còn {$activeOrders} đơn hàng đang trong tiến trình xử lý hoặc giao hàng. Vui lòng hoàn tất hoặc hủy trước.",
            ]);
        }

        $wallet = $store->getOrCreateWallet();
        if ((float) $wallet->pending_balance > 0) {
            return back()->withErrors([
                'close_store_error' => 'Không thể đóng cửa hàng: Vẫn còn số dư ký quỹ tạm giữ trong Escrow chưa được thanh toán/giải ngân ('.number_format((float) $wallet->pending_balance, 0, ',', '.').'₫).',
            ]);
        }

        DB::transaction(function () use ($user, $store, $request) {
            // Update store status to closed and hide all products
            $store->update([
                'status' => 'closed',
                'description' => ($store->description ? $store->description."\n" : '').'[Đã đóng cửa hàng ngày '.now()->format('d/m/Y H:i').($request->reason ? ' - Lý do: '.$request->reason : '').']',
            ]);

            // Hide/draft all products belonging to this store
            $store->products()->update([
                'status' => 'inactive',
            ]);

            // Revert user role back to regular buyer
            $user->update([
                'role' => 'buyer',
            ]);
        });

        return redirect()->route('profile.info')->with('success', 'Gian hàng của bạn đã đóng thành công. Tài khoản của bạn đã được chuyển về tài khoản Khách hàng mua sắm thông thường.');
    }

    /**
     * Reopen a closed store.
     */
    public function reopenStore(): RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store || $store->status !== 'closed') {
            return redirect()->route('seller.register');
        }

        DB::transaction(function () use ($user, $store) {
            $store->update([
                'status' => 'active',
            ]);

            $user->update([
                'role' => 'seller',
            ]);
        });

        return redirect()->route('seller.dashboard')->with('success', 'Chào mừng bạn quay trở lại! Gian hàng đã được mở lại thành công.');
    }
}
