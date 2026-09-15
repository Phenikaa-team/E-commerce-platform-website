@props([
    'active' => 'profile',
])

<aside class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs sticky top-24">
    <!-- User Quick Summary Mini Card (Hidden when on 'Tài khoản của tôi' page) -->
    @if($active !== 'profile')
        <div class="flex items-center gap-3 pb-4 mb-3 border-b border-gray-100">
            <a href="{{ route('profile') }}" class="w-12 h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-rose-100 hover:ring-primary transition-all">
                <img 
                    src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=150&q=80' }}" 
                    alt="{{ auth()->user()->name }}" 
                    class="w-full h-full object-cover"
                >
            </a>
            <div class="min-w-0 flex-1">
                <a href="{{ route('profile') }}" class="text-xs font-black text-gray-900 truncate block hover:text-primary transition-colors">
                    {{ auth()->user()->username ?? auth()->user()->name }}
                </a>
                <span class="text-[10px] text-gray-400 font-medium block truncate">
                    {{ auth()->user()->email }}
                </span>
            </div>
        </div>
    @endif

    <!-- Navigation Items List -->
    <nav class="space-y-1 text-sm">
        <!-- Item 1: Tài khoản của tôi -->
        <a 
            href="{{ route('profile') }}" 
            class="user-sidebar-item {{ $active === 'profile' ? 'is-active' : '' }}"
        >
            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Tài khoản của tôi</span>
        </a>

        <!-- Item 2: Đơn mua -->
        <a 
            href="{{ route('user.orders') }}" 
            class="user-sidebar-item {{ $active === 'orders' ? 'is-active' : '' }}"
        >
            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Đơn mua</span>
        </a>

        <!-- Item 3: Đánh giá sản phẩm -->
        <a 
            href="{{ route('user.orders', ['status' => 'completed']) }}" 
            class="user-sidebar-item {{ $active === 'reviews' ? 'is-active' : '' }}"
        >
            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span>Đánh giá sản phẩm</span>
        </a>

        <!-- Item 4: Sổ địa chỉ -->
        <a 
            href="{{ route('profile.addresses') }}" 
            class="user-sidebar-item {{ $active === 'addresses' ? 'is-active' : '' }}"
        >
            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Sổ địa chỉ nhận hàng</span>
        </a>

        <!-- Item 5: Voucher của tôi -->
        <a 
            href="{{ route('vouchers.index') }}" 
            class="user-sidebar-item {{ $active === 'vouchers' ? 'is-active' : '' }}"
        >
            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <span>Voucher của tôi</span>
        </a>

        <!-- Item 6: Kênh người bán / Đăng ký mở gian hàng -->
        @if(auth()->check() && auth()->user()->isSeller())
            <a 
                href="{{ route('seller.dashboard') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all font-medium text-amber-800 hover:bg-amber-50 hover:text-amber-900"
            >
                <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Kênh Người Bán</span>
            </a>
        @else
            <a 
                href="{{ route('seller.register') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all font-medium text-amber-800 hover:bg-amber-50 hover:text-amber-900"
            >
                <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Đăng ký mở gian hàng</span>
            </a>
        @endif

        <!-- Divider -->
        <div class="border-t border-gray-100 my-2 pt-1"></div>

        <!-- Item 6: Thông tin cá nhân -->
        <a 
            href="{{ route('profile.info') }}" 
            class="user-sidebar-item {{ $active === 'info' ? 'is-active' : '' }}"
        >
            <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
            <span>Thông tin cá nhân</span>
        </a>

@php
    $authUser = auth()->user();
    $hasPassword = $authUser ? $authUser->hasCustomPassword() : true;
@endphp

        <!-- Item 7: Bảo mật tài khoản / Đổi mật khẩu -->
        <button 
            type="button" 
            onclick="openPasswordModal()" 
            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all text-left cursor-pointer"
        >
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>{{ $hasPassword ? 'Đổi mật khẩu' : 'Thiết lập mật khẩu' }}</span>
            </div>
            @if(!$hasPassword)
                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0" title="Chưa thiết lập mật khẩu"></span>
            @endif
        </button>

        <!-- Divider -->
        <div class="border-t border-gray-100 my-2 pt-1"></div>

        <!-- Item 8: Đăng xuất -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 font-semibold transition-all text-left cursor-pointer">
                <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Đăng xuất</span>
            </button>
        </form>
    </nav>
</aside>

<!-- ==================== GLOBAL PASSWORD CHANGE MODAL ==================== -->
<div id="change-password-modal" class="modal-backdrop hidden animate-fade-in" onclick="if(event.target === this) closePasswordModal()">
    <div class="modal-dialog max-w-md p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
            <div>
                <h3 class="text-lg font-black text-gray-900">
                    {{ $hasPassword ? 'Đổi mật khẩu tài khoản' : 'Thiết lập mật khẩu tài khoản' }}
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $hasPassword ? 'Để bảo mật, vui lòng không chia sẻ mật khẩu cho người khác.' : 'Tạo mật khẩu riêng để có thể đăng nhập bằng email hoặc số điện thoại.' }}
                </p>
            </div>
            <button type="button" onclick="closePasswordModal()" class="modal-close-btn" aria-label="Đóng">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if(!$hasPassword)
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-bold">Đăng nhập qua {{ ucfirst($authUser->provider ?? 'Google') }}</p>
                <p class="mt-0.5 text-amber-700 leading-relaxed">Bạn không cần nhập mật khẩu hiện tại. Hãy tạo mật khẩu mới trực tiếp để bảo vệ tài khoản và đăng nhập linh hoạt.</p>
            </div>
        </div>
        @endif

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @if($hasPassword)
            <div>
                <label class="form-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required placeholder="Nhập mật khẩu hiện tại" class="form-input">
            </div>
            @endif

            <div>
                <label class="form-label">{{ $hasPassword ? 'Mật khẩu mới' : 'Mật khẩu tạo mới' }}</label>
                <input type="password" name="password" required minlength="6" placeholder="Tối thiểu 6 ký tự" class="form-input">
            </div>

            <div>
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" required minlength="6" placeholder="Nhập lại mật khẩu mới" class="form-input">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3">
                <button type="button" onclick="closePasswordModal()" class="btn btn-outline btn-sm">
                    Hủy
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ $hasPassword ? 'Đổi mật khẩu' : 'Thiết lập mật khẩu' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() {
        const modal = document.getElementById('change-password-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }
    function closePasswordModal() {
        const modal = document.getElementById('change-password-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePasswordModal();
        }
    });
</script>
