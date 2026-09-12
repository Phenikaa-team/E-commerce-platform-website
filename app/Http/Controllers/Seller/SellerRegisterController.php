<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
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
        if (auth()->user()->store) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.register');
    }

    /**
     * Handle store creation and grant seller role.
     */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:stores,name',
            'description' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoUrl = asset('images/placeholders/store-logo-placeholder.svg');
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('stores', 'public');
            $logoUrl = '/storage/'.$path;
        }

        $store = Store::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.rand(100, 999),
            'description' => $data['description'],
            'address' => $data['address'],
            'phone' => auth()->user()->phone ?? '+84 900 000 000',
            'logo_url' => $logoUrl,
            'banner_url' => asset('images/placeholders/store-banner-placeholder.svg'),
            'rating' => 5.0,
            'response_rate' => '100%',
            'followers' => '1',
            'is_mall' => false,
            'online_status' => 'Vừa mới online',
            'status' => 'active',
        ]);

        // Update user role to seller
        auth()->user()->update(['role' => 'seller']);

        return redirect()->route('seller.dashboard')->with('success', '🎉 Chúc mừng bạn đã mở gian hàng thành công trên ShopMart!');
    }
}
