<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    /**
     * Show the combined admin profile and dashboard page.
     */
    public function index(): View
    {
        return app(AdminDashboardController::class)->index();
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
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif,avif', 'max:3072'],
            'avatar_url' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'avatar.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 3MB.',
        ]);

        $avatarUrl = $admin->avatar_url;
        if ($request->hasFile('avatar')) {
            $uploaded = FileUploadService::upload($request->file('avatar'), 'avatars', $admin->avatar_url);
            $avatarUrl = $uploaded['url'];
        } elseif (isset($validated['avatar_url']) && ! empty($validated['avatar_url'])) {
            $avatarUrl = $validated['avatar_url'];
        }

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $admin->phone,
            'avatar_url' => $avatarUrl,
        ]);

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
