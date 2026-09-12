<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerProfileController extends Controller
{
    /**
     * Display seller and store profile management page matching reference specification.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            if ($user->isSeller()) {
                $store = Store::create([
                    'user_id' => $user->id,
                    'name' => $user->name.' Store',
                    'slug' => Str::slug($user->name.'-store-'.uniqid()),
                    'description' => 'Gian hàng chính hãng trên ShopMart',
                    'status' => 'active',
                    'is_mall' => false,
                    'rating' => 5.0,
                    'followers' => '1.250',
                    'response_rate' => '100%',
                    'online_status' => 'Đang hoạt động',
                ]);
            } else {
                return redirect()->route('seller.register');
            }
        }

        $overview = $store->getPerformanceOverview(30);

        return view('seller.dashboard', array_merge([
            'user' => $user,
            'store' => $store,
        ], $overview));
    }

    /**
     * Update seller personal details and store settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        $validated = $request->validate([
            // Seller account info
            'user_name' => ['required', 'string', 'max:255'],
            'user_phone' => ['nullable', 'string', 'max:20'],
            'user_avatar_url' => ['nullable', 'url', 'max:500'],

            // Store information
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'store_logo_url' => ['nullable', 'url', 'max:500'],
            'store_banner_url' => ['nullable', 'url', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],

            // Bank payout details
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
        ], [
            'user_name.required' => 'Họ và tên người đại diện không được để trống.',
            'store_name.required' => 'Tên gian hàng không được để trống.',
            'store_logo_url.url' => 'Đường dẫn ảnh logo gian hàng không hợp lệ.',
            'store_banner_url.url' => 'Đường dẫn ảnh bìa gian hàng không hợp lệ.',
        ]);

        // Update user account details
        $user->update([
            'name' => $validated['user_name'],
            'phone' => $validated['user_phone'] ?? $user->phone,
            'avatar_url' => $validated['user_avatar_url'] ?? $user->avatar_url,
        ]);

        // Update store details
        if ($store) {
            $store->update([
                'name' => $validated['store_name'],
                'description' => $validated['store_description'] ?? $store->description,
                'logo_url' => $validated['store_logo_url'] ?? $store->logo_url,
                'banner_url' => $validated['store_banner_url'] ?? $store->banner_url,
                'phone' => $validated['phone'] ?? $store->phone,
                'address' => $validated['address'] ?? $store->address,
                'status' => $validated['status'],
                'bank_name' => $validated['bank_name'] ?? $store->bank_name,
                'bank_account_number' => $validated['bank_account_number'] ?? $store->bank_account_number,
                'bank_account_name' => $validated['bank_account_name'] ?? $store->bank_account_name,
            ]);
        }

        return redirect()->route('seller.profile')->with('success', 'Cập nhật hồ sơ Kênh Người Bán và Gian hàng thành công!');
    }
}
