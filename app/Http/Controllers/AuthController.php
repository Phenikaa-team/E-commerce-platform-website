<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Store;
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

        // Check if there was a pending cart action
        if ($pendingRedirect = $this->handlePendingCartAction($request, $user)) {
            return $pendingRedirect;
        }

        // Clear unpermitted intended URLs from session if the user does not have permission
        $intendedUrl = $request->session()->get('url.intended');
        if ($intendedUrl) {
            if (str_contains($intendedUrl, '/admin') && ! $user->isAdmin()) {
                $request->session()->forget('url.intended');
            } elseif (str_contains($intendedUrl, '/seller') && ! $user->isSeller()) {
                $request->session()->forget('url.intended');
            }
        }

        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Xin chào Quản trị viên, '.$user->name.'!');
        }

        if ($user->isSeller()) {
            return redirect()->intended(route('seller.dashboard'))
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

        // Check if there was a pending cart action
        if ($pendingRedirect = $this->handlePendingCartAction($request, $user)) {
            return $pendingRedirect;
        }

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

        // Check if there was a pending cart action
        if ($pendingRedirect = $this->handlePendingCartAction(request(), $user)) {
            return $pendingRedirect;
        }

        // Clear unpermitted intended URLs from session if the user does not have permission
        $intendedUrl = request()->session()->get('url.intended');
        if ($intendedUrl) {
            if (str_contains($intendedUrl, '/admin') && ! $user->isAdmin()) {
                request()->session()->forget('url.intended');
            } elseif (str_contains($intendedUrl, '/seller') && ! $user->isSeller()) {
                request()->session()->forget('url.intended');
            }
        }

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Xin chào Quản trị viên, {$user->name}!");
        }

        if ($user->isSeller()) {
            return redirect()->intended(route('seller.dashboard'))
                ->with('success', "Chào mừng trở lại Kênh Người Bán, {$user->name}!");
        }

        return redirect()->intended(route('home'))
            ->with('success', "Đăng nhập thành công bằng tài khoản Google ({$user->name})!");
    }

    /**
     * Handle any pending cart action (add-to-cart or buy-now) stored before login/registration.
     */
    protected function handlePendingCartAction(Request $request, User $user): ?RedirectResponse
    {
        if (! $request->session()->has('pending_cart_action')) {
            return null;
        }

        $pending = $request->session()->pull('pending_cart_action');
        $productId = $pending['product_id'] ?? null;
        if (! $productId) {
            return null;
        }

        $product = Product::find($productId);
        if (! $product) {
            return null;
        }

        // Get or create user's cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $variant = $pending['variant'] ?? null;
        $qty = max(1, (int) ($pending['quantity'] ?? 1));

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('selected_variant', $variant)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $qty;
            $cartItem->is_selected = true;
            $cartItem->save();
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $product->price,
                'selected_variant' => $variant ?? ($product->brand ? $product->brand.' Chính hãng' : null),
                'is_selected' => true,
            ]);
        }

        if (($pending['action'] ?? '') === 'buy_now') {
            $cart->items()->where('id', '!=', $cartItem->id)->update(['is_selected' => false]);
            $cartItem->is_selected = true;
            $cartItem->save();

            return redirect()->route('checkout.index')
                ->with('success', 'Đã thêm "'.$product->name.'" vào giỏ hàng. Vui lòng hoàn tất đơn hàng!');
        }

        $returnUrl = $pending['return_url'] ?? route('product.detail', $product->slug);

        return redirect($returnUrl)
            ->with('success', 'Đã thêm sản phẩm "'.$product->name.'" vào giỏ hàng thành công!');
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
        $redirectUrl = $request->input('redirect');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($redirectUrl && Str::startsWith($redirectUrl, ['/', url('/')])) {
            return redirect($redirectUrl)->with('success', 'Bạn đã đăng xuất tài khoản.');
        }

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất thành công.');
    }

    /**
     * Show the Admin login interface.
     */
    public function showAdminLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $tab = 'login';
        $portal = 'admin';

        return view('auth', compact('tab', 'portal'));
    }

    /**
     * Handle Admin login.
     */
    public function adminLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        $loginId = trim($validated['login_id']);
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        $user = User::where('email', $loginId)
            ->orWhere('username', $loginId)
            ->orWhere('phone', $loginId)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['login_id' => 'Tài khoản hoặc mật khẩu không chính xác.'])
                ->withInput($request->only('login_id', 'remember'));
        }

        if (! $user->isAdmin()) {
            return back()
                ->withErrors(['login_id' => 'Tài khoản này không có quyền Quản trị viên (Admin).'])
                ->withInput($request->only('login_id', 'remember'));
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Xin chào Quản trị viên, '.$user->name.'!');
    }

    /**
     * Log out of Admin portal.
     */
    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất khỏi cổng Quản trị viên.');
    }

    /**
     * 1-Click Dev login for Admin testing.
     */
    public function adminDevLogin(Request $request): RedirectResponse
    {
        $admin = User::where('role', 'admin')->first()
            ?? User::where('email', 'admin@gmail.com')->first();

        if ($admin && $admin->role !== 'admin') {
            $admin->forceFill(['role' => 'admin', 'status' => 'active'])->save();
        }

        if (! $admin) {
            $admin = User::create([
                'name' => 'ShopMart Administrator',
                'username' => 'admin_shopmart',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'status' => 'active',
            ]);
        }

        Auth::login($admin, true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Đã đăng nhập nhanh Quản trị viên: '.$admin->name);
    }

    /**
     * Show the Seller login interface.
     */
    public function showSellerLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isSeller() && Auth::user()->store?->status === 'active') {
            return redirect()->route('seller.dashboard');
        }

        $tab = 'login';
        $portal = 'seller';

        return view('auth', compact('tab', 'portal'));
    }

    /**
     * Handle Seller login.
     */
    public function sellerLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable'],
        ]);

        $loginId = trim($validated['login_id']);
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        $user = User::where('email', $loginId)
            ->orWhere('username', $loginId)
            ->orWhere('phone', $loginId)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['login_id' => 'Tài khoản hoặc mật khẩu không chính xác.'])
                ->withInput($request->only('login_id', 'remember'));
        }

        if (! $user->isSeller()) {
            return back()
                ->withErrors(['login_id' => 'Tài khoản này chưa đăng ký Kênh Người Bán. Vui lòng đăng nhập tại cổng Khách hàng hoặc Đăng ký mở gian hàng.'])
                ->withInput($request->only('login_id', 'remember'));
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('seller.dashboard'))
            ->with('success', 'Chào mừng trở lại Kênh Người Bán, '.$user->name.'!');
    }

    /**
     * Log out of Seller portal.
     */
    public function sellerLogout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login')->with('success', 'Đã đăng xuất khỏi Kênh Người Bán.');
    }

    /**
     * 1-Click Dev login for Seller testing.
     */
    public function sellerDevLogin(Request $request): RedirectResponse
    {
        $seller = User::where('email', 'samsung@gmail.com')->first()
            ?? User::where('role', 'seller')->whereHas('store')->first()
            ?? User::where('role', 'seller')->first();

        if (! $seller) {
            $seller = User::where('email', 'techzone@gmail.com')->first();
        }

        if (! $seller) {
            $seller = User::create([
                'name' => 'Samsung Electronics VN',
                'username' => 'samsung_seller',
                'email' => 'samsung@gmail.com',
                'password' => Hash::make('seller'),
                'role' => 'seller',
                'status' => 'active',
            ]);
        }

        if (! $seller->store) {
            Store::create([
                'user_id' => $seller->id,
                'name' => 'Samsung Flagship Store',
                'slug' => 'samsung-flagship-store-'.uniqid(),
                'status' => 'active',
                'rating' => 5.0,
                'response_rate' => '100%',
                'is_mall' => true,
            ]);
            $seller->load('store');
        }

        Auth::login($seller, true);
        $request->session()->regenerate();

        return redirect()->route('seller.dashboard')
            ->with('success', 'Đã đăng nhập nhanh Kênh Người Bán: '.$seller->name);
    }

    /**
     * 1-Click Dev login for Buyer testing.
     */
    public function buyerDevLogin(Request $request): RedirectResponse
    {
        $buyer = User::where(function ($q) {
            $q->where('role', 'buyer')->orWhereNull('role')->orWhere('role', 'user');
        })->where('email', '!=', 'admin@gmail.com')->where('email', '!=', 'seller@gmail.com')->first();

        if (! $buyer) {
            $buyer = User::create([
                'name' => 'Nguyễn Văn Mua',
                'username' => 'buyer_test',
                'email' => 'buyer@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'buyer',
                'status' => 'active',
            ]);
        }

        Auth::login($buyer, true);
        $request->session()->regenerate();

        return redirect()->route('cart')
            ->with('success', 'Đã đăng nhập nhanh Khách hàng: '.$buyer->name);
    }
}
