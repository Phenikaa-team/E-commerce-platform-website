<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $ordersCount = Order::where('user_id', $user->id)->count();
        $reviewsCount = Review::where('user_id', $user->id)->count();

        $statusCounts = [
            'pending' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', $user->id)->where('status', 'processing')->count(),
            'shipping' => Order::where('user_id', $user->id)->where('status', 'shipping')->count(),
            'completed' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        $recentOrders = Order::where('user_id', $user->id)->with('items.product')->latest()->take(5)->get();

        return view('profile', compact('user', 'ordersCount', 'reviewsCount', 'statusCounts', 'recentOrders'));
    }

    /**
     * Show dedicated personal info page.
     */
    public function info(): View
    {
        $user = Auth::user();

        return view('profile.info', compact('user'));
    }

    /**
     * Show dedicated shipping address book page.
     */
    public function addresses(): View
    {
        $user = Auth::user()->load('addresses');

        return view('profile.addresses', compact('user'));
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
     * Update an existing shipping address.
     */
    public function updateAddress(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'is_default' => ['nullable'],
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'address_line.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
        ]);

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update([
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address_line' => $validated['address_line'],
            'is_default' => $isDefault || $address->is_default,
        ]);

        return back()->with('success', 'Đã cập nhật địa chỉ thành công!');
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

    /**
     * Change user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Delete user account permanently.
     */
    public function destroyAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return back()->withErrors([
                'delete_account' => 'Tài khoản Quản trị viên (Admin) không thể tự xóa để đảm bảo an toàn hệ thống.',
            ]);
        }

        // Verification based on account type
        if (! $user->provider) {
            $request->validate([
                'confirm_password' => ['required', 'current_password'],
            ], [
                'confirm_password.required' => 'Vui lòng nhập mật khẩu để xác nhận xóa tài khoản.',
                'confirm_password.current_password' => 'Mật khẩu xác nhận không chính xác.',
            ]);
        } else {
            $request->validate([
                'confirm_text' => ['required', 'string', 'in:XÓA,XOA,xóa,xoa'],
            ], [
                'confirm_text.required' => 'Vui lòng nhập chữ XÓA để xác nhận.',
                'confirm_text.in' => 'Nội dung xác nhận không đúng. Vui lòng gõ chính xác chữ XÓA.',
            ]);
        }

        // Clean up store if seller
        if ($user->store) {
            $user->store->delete();
        }

        // Clean up relationships
        $user->addresses()->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')->with('success', 'Tài khoản của bạn đã được xóa thành công khỏi hệ thống ShopMart.');
    }
}
