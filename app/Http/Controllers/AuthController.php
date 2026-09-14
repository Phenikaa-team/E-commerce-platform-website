<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

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
     * Redirect to the Social OAuth provider (Google).
     */
    public function socialRedirect(string $provider): RedirectResponse
    {
        if (in_array($provider, ['apple', 'facebook'])) {
            return redirect()->route('login')->withErrors([
                'login_id' => 'Phương thức đăng nhập qua '.ucfirst($provider).' tạm thời chưa hỗ trợ (đang bảo trì). Vui lòng sử dụng Google hoặc tài khoản thông thường.',
            ]);
        }

        if ($provider !== 'google') {
            return redirect()->route('login')->withErrors(['login_id' => 'Cổng đăng nhập mạng xã hội không hỗ trợ.']);
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'login_id' => 'Cổng đăng nhập Google chưa được cấu hình Client ID & Secret trong tệp .env (GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET).',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Social OAuth provider callback (Google).
     */
    public function socialCallback(string $provider): RedirectResponse
    {
        if (in_array($provider, ['apple', 'facebook'])) {
            return redirect()->route('login')->withErrors([
                'login_id' => 'Phương thức đăng nhập qua '.ucfirst($provider).' tạm thời chưa hỗ trợ (đang bảo trì). Vui lòng sử dụng Google hoặc tài khoản thông thường.',
            ]);
        }

        if ($provider !== 'google') {
            return redirect()->route('login')->withErrors([
                'login_id' => 'Phương thức đăng nhập qua '.ucfirst($provider).' không được hỗ trợ.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'login_id' => 'Đăng nhập bằng Google không thành công hoặc phiên xác thực đã hết hạn. Vui lòng thử lại.',
            ]);
        }

        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?? $googleUser->getNickname() ?? 'Người dùng Google';
        $avatar = $googleUser->getAvatar();
        $googleId = $googleUser->getId();

        if (empty($email)) {
            return redirect()->route('login')->withErrors([
                'login_id' => 'Không thể lấy thông tin email từ tài khoản Google của bạn.',
            ]);
        }

        // Find existing user by provider_id or email
        $user = User::where('provider', 'google')
            ->where('provider_id', $googleId)
            ->first();

        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $baseUsername = Str::slug($name, '_');
            if (empty($baseUsername)) {
                $baseUsername = 'google_user';
            }
            $username = $baseUsername.'_'.Str::lower(Str::random(4));

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'phone' => null,
                'password' => Hash::make(Str::random(24)),
                'avatar_url' => $avatar,
                'role' => 'buyer',
                'provider' => 'google',
                'provider_id' => $googleId,
                'joined_date' => 'Tham gia từ '.now()->format('m/Y'),
                'membership_tier' => 'Thành viên Bạc',
                'coins' => 0,
                'voucher_count' => 0,
                'favorite_count' => 0,
                'order_count' => 0,
                'review_count' => 0,
            ]);
        } else {
            $updates = [];
            if (empty($user->provider)) {
                $updates['provider'] = 'google';
                $updates['provider_id'] = $googleId;
            }
            if (empty($user->avatar_url) && ! empty($avatar)) {
                $updates['avatar_url'] = $avatar;
            }
            if (! empty($updates)) {
                $user->update($updates);
            }
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('profile'))
            ->with('success', "Đăng nhập thành công bằng tài khoản Google ({$user->name})!");
    }

    /**
     * Backwards-compatible alias for socialRedirect.
     */
    public function socialLogin(string $provider): RedirectResponse
    {
        return $this->socialRedirect($provider);
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
