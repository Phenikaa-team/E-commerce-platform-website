<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerRegisterController extends Controller
{
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

        $data = $request->validate([
            'name' => 'required|string|max:150|unique:stores,name'.($store ? ','.$store->id : ''),
            'description' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:3072',
        ]);

        $logoUrl = asset('images/placeholders/store-logo-placeholder.svg');
        if ($request->hasFile('logo')) {
            $uploaded = FileUploadService::upload($request->file('logo'), 'stores/logos');
            $logoUrl = $uploaded['url'];
        }

        $storeData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.rand(100, 999),
            'description' => $data['description'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'logo_url' => $logoUrl,
            'banner_url' => asset('images/placeholders/store-banner-placeholder.svg'),
            'rating' => 5.0,
            'response_rate' => '100%',
            'followers' => '1',
            'is_mall' => false,
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

        return redirect()->route('seller.register')->with('success', 'Đã gửi hồ sơ mở gian hàng. Vui lòng chờ quản trị viên phê duyệt.');
    }
}
