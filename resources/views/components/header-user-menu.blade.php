@auth
    <div class="relative group z-50" id="user-menu-wrapper">
        <!-- Trigger Button -->
        <button 
            type="button" 
            id="user-menu-dropdown-toggle"
            class="flex items-center gap-2.5 text-gray-700 hover:text-primary transition-colors focus:outline-none cursor-pointer py-1"
            aria-expanded="false"
            aria-haspopup="true"
        >
            <img 
                src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80' }}" 
                alt="{{ auth()->user()->name }}" 
                class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0"
            >
            <div class="text-left text-xs leading-tight hidden sm:block">
                <span class="text-gray-400 font-medium block text-[11px]">Tài khoản</span>
                <span class="font-bold text-gray-800 truncate max-w-[120px] block">{{ auth()->user()->name }}</span>
            </div>
            <x-icon name="chevron-down" class="w-3.5 h-3.5 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180" />
        </button>

        <!-- Dropdown Panel (Exact Design from Screenshot) -->
        <div 
            id="user-dropdown-panel" 
            class="absolute right-0 top-full pt-1.5 w-60 hidden group-hover:block transition-all z-50 animate-in fade-in slide-in-from-top-1 duration-150"
        >
            <!-- Invisible hover bridge -->
            <div class="absolute -top-3 left-0 right-0 h-4"></div>

            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 overflow-hidden">
                <!-- User Brief -->
                <div class="px-4 py-2.5">
                    <p class="text-sm font-black text-gray-900 truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                    <div class="mt-2">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (auth()->user()->isSeller() ? 'bg-amber-100 text-amber-800' : 'bg-rose-50 text-primary') }}">
                            {{ auth()->user()->isAdmin() ? 'Quản Trị Viên' : (auth()->user()->isSeller() ? 'Người Bán' : 'Khách Hàng') }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100 my-1"></div>

                <!-- Core Links -->
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-rose-50/60 hover:text-primary transition-colors">
                    <x-icon name="user" class="w-4 h-4 text-gray-400 shrink-0" />
                    <span>Tài khoản của tôi</span>
                </a>
                <a href="{{ route('user.orders') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-rose-50/60 hover:text-primary transition-colors">
                    <x-icon name="clipboard" class="w-4 h-4 text-gray-400 shrink-0" />
                    <span>Đơn mua hàng</span>
                </a>
                <a href="{{ route('vouchers.index') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-rose-50/60 hover:text-primary transition-colors">
                    <x-icon name="ticket" class="w-4 h-4 text-primary shrink-0" />
                    <span>Kho voucher ưu đãi</span>
                </a>

                <div class="border-t border-gray-100 my-1"></div>

                <!-- Seller or Store Registration -->
                @if(auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-amber-800 hover:bg-amber-50 transition-colors">
                        <x-icon name="store" class="w-4 h-4 text-amber-700 shrink-0" />
                        <span>Kênh người bán</span>
                    </a>
                @else
                    <a href="{{ route('seller.register') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50/80 transition-colors">
                        <x-icon name="plus" class="w-4 h-4 text-amber-700 shrink-0" />
                        <span>Đăng ký mở gian hàng</span>
                    </a>
                @endif

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-purple-700 hover:bg-purple-50 transition-colors">
                        <x-icon name="shield" class="w-4 h-4 text-purple-600 shrink-0" />
                        <span>Trang quản trị</span>
                    </a>
                @endif

                <div class="border-t border-gray-100 my-1"></div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-xs font-bold text-primary hover:bg-rose-50 text-left transition-colors cursor-pointer">
                        <x-icon name="logout" class="w-4 h-4 text-primary shrink-0" />
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="flex items-center gap-2">
        <a href="{{ route('login') }}" class="px-3 py-1.5 text-gray-700 hover:text-primary font-medium transition-colors text-xs">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn btn-primary px-3 py-1.5 text-xs">Đăng ký</a>
    </div>
@endauth
