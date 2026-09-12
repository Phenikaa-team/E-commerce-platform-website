<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    /**
     * Show the admin profile and system credentials page.
     */
    public function index(): View
    {
        $admin = auth()->user();

        // System overview statistics for admin
        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_stores' => Store::count(),
            'joined_date' => $admin->created_at ? $admin->created_at->format('d/m/Y') : 'N/A',
        ];

        return view('admin.profile', compact('admin', 'stats'));
    }

    /**
     * Update admin basic profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'avatar_url.url' => 'Đường dẫn ảnh đại diện không hợp lệ.',
        ]);

        $admin->update($validated);

        return redirect()->route('admin.profile')->with('success', 'Cập nhật thông tin hồ sơ Quản trị viên thành công!');
    }

    /**
     * Update admin account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Đổi mật khẩu tài khoản Quản trị viên thành công!');
    }
}
