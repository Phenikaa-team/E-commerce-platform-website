<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the Login & Registration interface.
     */
    public function showAuth(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isSeller()) {
                return redirect()->route('seller.dashboard');
            }

            return redirect()->route('profile');
        }

        $tab = $request->query('tab', 'login');
        if (! in_array($tab, ['login', 'register'])) {
            $tab = 'login';
        }

        return view('auth', compact('tab'));
    }

    /**
     * Handle standard user login.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ], [
            'login_id.required' => 'Vui lòng nhập Email, Tên đăng nhập hoặc Số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $loginId = trim($validated['login_id']);
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        // Look up by email, username, or phone
        $user = User::where('email', $loginId)
            ->orWhere('username', $loginId)
            ->orWhere('phone', $loginId)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['login_id' => 'Email/Tài khoản hoặc mật khẩu không chính xác.'])
                ->withInput($request->only('login_id', 'remember'));
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Xin chào Quản trị viên, '.$user->name.'!');
        }

        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard')
                ->with('success', 'Chào mừng trở lại Kênh Người Bán, '.$user->name.'!');
        }

        return redirect()->intended(route('home'))
            ->with('success', 'Chào mừng bạn trở lại, '.($user->name ?? $user->username).'!');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Email này đã được đăng ký tài khoản.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'terms.accepted' => 'Bạn cần đồng ý với Điều khoản dịch vụ và Chính sách bảo mật.',
        ]);

        // Generate base username from email prefix
        $baseUsername = Str::slug(explode('@', $validated['email'])[0], '');
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.$counter;
            $counter++;
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'avatar_url' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=400&q=80',
            'cover_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80',
            'membership_tier' => 'Thành viên Bạc',
            'joined_date' => 'Tham gia từ '.now()->format('m/Y'),
            'gender' => 'Chưa cập nhật',
            'birthday' => 'Chưa cập nhật',
            'coins' => 120,
            'voucher_count' => 3,
            'favorite_count' => 0,
            'order_count' => 0,
            'review_count' => 0,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('profile')
            ->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với ShopMart.');
    }

    /**
     * Handle one-click Social Login (Google, Apple, Facebook).
     */
    public function socialLogin(string $provider): RedirectResponse
    {
        $allowedProviders = [
            'google' => [
                'name' => 'Google Member',
                'email' => 'google.user@gmail.com',
                'username' => 'google_user',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=400&q=80',
                'label' => 'Google',
            ],
            'apple' => [
                'name' => 'Apple Member',
                'email' => 'apple.user@icloud.com',
                'username' => 'apple_user',
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=400&q=80',
                'label' => 'Apple',
            ],
            'facebook' => [
                'name' => 'Facebook Member',
                'email' => 'facebook.user@facebook.com',
                'username' => 'facebook_user',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
                'label' => 'Facebook',
            ],
        ];

        if (! array_key_exists($provider, $allowedProviders)) {
            return redirect()->route('login')->withErrors(['login_id' => 'Cổng đăng nhập mạng xã hội không hỗ trợ.']);
        }

        $providerData = $allowedProviders[$provider];

        // Find or create social account
        $user = User::where('provider', $provider)
            ->orWhere('email', $providerData['email'])
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $providerData['name'],
                'username' => $providerData['username'],
                'email' => $providerData['email'],
                'phone' => '+84 988 777 666',
                'password' => Hash::make(Str::random(16)),
                'avatar_url' => $providerData['avatar'],
                'cover_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80',
                'membership_tier' => 'Thành viên Bạc',
                'joined_date' => 'Tham gia từ '.now()->format('m/Y'),
                'gender' => 'Chưa cập nhật',
                'birthday' => 'Chưa cập nhật',
                'coins' => 120,
                'voucher_count' => 3,
                'favorite_count' => 2,
                'order_count' => 5,
                'review_count' => 2,
                'provider' => $provider,
                'provider_id' => $provider.'_'.uniqid(),
            ]);

            // Add demo default address
            $user->addresses()->create([
                'recipient_name' => $providerData['name'],
                'phone' => '(+84) 988 777 666',
                'address_line' => 'Toà nhà Landmark 81, 720A Điện Biên Phủ, Quận Bình Thạnh, TP. Hồ Chí Minh',
                'is_default' => true,
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->route('profile')
            ->with('success', "Đăng nhập thành công bằng tài khoản {$providerData['label']}!");
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
