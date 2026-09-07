<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     */
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user()->load('addresses');

        return view('profile', compact('user'));
    }

    /**
     * Update user personal information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'max:50'],
            'birthday' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'username.unique' => 'Tên đăng nhập này đã có người sử dụng.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? $user->username,
            'phone' => $validated['phone'] ?? $user->phone,
            'gender' => $validated['gender'] ?? $user->gender,
            'birthday' => $validated['birthday'] ?? $user->birthday,
        ]);

        return back()->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    /**
     * Add a new shipping address.
     */
    public function addAddress(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'is_default' => ['nullable'],
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'address_line.required' => 'Vui lòng nhập địa chỉ nhận hàng chi tiết.',
        ]);

        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address_line' => $validated['address_line'],
            'is_default' => $isDefault,
        ]);

        return back()->with('success', 'Đã thêm địa chỉ nhận hàng mới!');
    }

    /**
     * Set default shipping address.
     */
    public function setDefaultAddress(int $id): RedirectResponse
    {
        $user = Auth::user();

        $address = $user->addresses()->findOrFail($id);

        $user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt làm địa chỉ mặc định!');
    }

    /**
     * Delete a shipping address.
     */
    public function deleteAddress(int $id): RedirectResponse
    {
        $user = Auth::user();

        $address = $user->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        // If default was deleted, assign another address as default if exists
        if ($wasDefault && $user->addresses()->count() > 0) {
            $user->addresses()->first()->update(['is_default' => true]);
        }

        return back()->with('success', 'Đã xóa địa chỉ nhận hàng.');
    }
}
