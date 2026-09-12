<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopMart - Sàn Thương Mại Điện Tử Đỉnh Cao')</title>
    <meta name="description" content="@yield('meta_description', 'Mua sắm trực tuyến hàng ngàn sản phẩm công nghệ, thời trang, gia dụng chính hãng với ưu đãi giảm đến 50% tại ShopMart.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f5f5fa] text-[#1e293b] font-sans antialiased selection:bg-rose-500 selection:text-white flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="site-announcement">
        <span>Lễ Hội Mua Sắm Siêu Sale: Miễn phí vận chuyển đơn từ 500k & Voucher giảm đến 30% hôm nay!</span>
    </div>

    <!-- ==================== MAIN HEADER ==================== -->
    <header class="site-header">
        <div class="site-header-container">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#ea384c] to-[#ff5c6c] flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-2xl font-black tracking-tight text-gray-900 group-hover:text-[#ea384c] transition-colors">
                    Shop<span class="text-[#ea384c]">Mart</span>
                </span>
            </a>

            <!-- Smart Live Search Bar -->
            <div class="header-search-container" id="header-search-container">
                <form action="{{ route('home') }}" method="GET" class="relative flex items-center">
                    <input 
                        type="text" 
                        name="search"
                        id="smart-search-input"
                        autocomplete="off"
                        placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục..." 
                        class="header-search-input"
                    >
                    <button 
                        type="submit" 
                        class="header-search-btn"
                        aria-label="Tìm kiếm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>

                <!-- Live Search Results Dropdown -->
                <div id="smart-search-dropdown" class="header-search-dropdown hidden">
                    <div id="search-loading" class="text-center py-4 text-xs text-gray-400 hidden">
                        <svg class="animate-spin h-5 w-5 mx-auto text-[#ea384c] mb-1" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang tìm kiếm...
                    </div>
                    <div id="search-results-content" class="space-y-3"></div>
                </div>
            </div>

            <!-- User Actions & Cart -->
            <div class="header-actions">
                
                <!-- Voucher Shortcut -->
                <a href="{{ route('vouchers.index') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50/80 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all hover:scale-102">
                    <svg class="w-4 h-4 text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span>Mã Giảm Giá</span>
                </a>

                <!-- Kênh người bán shortcut -->
                <a href="{{ auth()->check() && auth()->user()->isSeller() ? route('seller.dashboard') : route('seller.register') }}" class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-200 bg-amber-50/70 hover:bg-amber-100/70 text-amber-800 text-xs font-semibold transition-colors">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ auth()->check() && auth()->user()->isSeller() ? 'Kênh Người Bán' : 'Bán hàng cùng ShopMart' }}</span>
                </a>

                <!-- Cart Button with realtime badge -->
                <a href="{{ route('cart') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors group relative py-1">
                    <div class="relative">
                        <svg class="w-6 h-6 text-gray-700 group-hover:text-[#ea384c] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-badge-count header-cart-badge">0</span>
                    </div>
                    <span class="hidden sm:inline">Giỏ hàng</span>
                </a>

                <!-- Multi-state User Menu -->
                @guest
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-gray-700 hover:text-[#ea384c] font-medium transition-colors text-xs">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="px-3 py-1.5 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-lg text-xs font-semibold transition-all shadow-xs">Đăng ký</a>
                    </div>
                @else
                    <div class="relative group" id="user-menu-wrapper">
                        <button type="button" class="flex items-center gap-2.5 text-gray-700 hover:text-[#ea384c] transition-colors focus:outline-none cursor-pointer">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80' }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            <div class="text-left text-xs leading-tight hidden sm:block">
                                <span class="text-gray-400 block">Tài khoản</span>
                                <span class="font-bold text-gray-800 truncate max-w-[120px] block">{{ auth()->user()->name }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 top-full pt-2 w-56 hidden group-hover:block transition-all z-50">
                            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 overflow-hidden">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : (auth()->user()->isSeller() ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-700') }}">
                                        {{ auth()->user()->role === 'admin' ? 'Quản Trị Viên' : (auth()->user()->isSeller() ? 'Người Bán' : 'Khách Hàng') }}
                                    </span>
                                </div>

                                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Tài khoản của tôi
                                </a>
                                <a href="{{ route('user.orders') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Đơn mua hàng
                                </a>
                                <a href="{{ route('vouchers.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] transition-colors">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    Kho voucher ưu đãi
                                </a>
                                <a href="{{ route('user.wishlist') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    Danh sách yêu thích
                                </a>

                                <!-- Role Portals -->
                                <div class="border-t border-gray-100 my-1"></div>
                                @if(auth()->user()->isSeller())
                                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-amber-700 hover:bg-amber-50 font-semibold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        Kênh Người Bán
                                    </a>
                                @else
                                    <a href="{{ route('seller.register') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-amber-700 hover:bg-amber-50 font-semibold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Đăng ký mở gian hàng
                                    </a>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-purple-700 hover:bg-purple-50 font-semibold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Quản Trị Toàn Sàn
                                    </a>
                                @endif

                                <div class="border-t border-gray-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 font-semibold transition-colors cursor-pointer text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <!-- Global Flash Toast Alerts -->
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="global-alerts-container">
            @if(session('success'))
                <div class="alert-box alert-box-success">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-box alert-box-error">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert-box alert-box-warning">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
            @if(session('info'))
                <div class="alert-box alert-box-info">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer-container">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-[#ea384c] to-[#ff5c6c] flex items-center justify-center text-white font-bold">SM</div>
                    <span class="text-lg font-black text-gray-900">Shop<span class="text-[#ea384c]">Mart</span></span>
                </div>
                <p class="text-gray-500 leading-relaxed mb-4">Sàn thương mại điện tử hàng đầu với hàng triệu sản phẩm chất lượng, bảo vệ người mua 100% và giao hàng hỏa tốc toàn quốc.</p>
                <p class="text-gray-400">© 2026 ShopMart Inc. All rights reserved.</p>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-3 text-sm">Chăm sóc khách hàng</h4>
                <ul class="space-y-2 text-gray-500">
                    <li><a href="#" class="hover:text-[#ea384c]">Trung tâm trợ giúp</a></li>
                    <li><a href="#" class="hover:text-[#ea384c]">Hướng dẫn mua hàng & hoàn tiền</a></li>
                    <li><a href="#" class="hover:text-[#ea384c]">Chính sách bảo hành 12 tháng</a></li>
                    <li><a href="#" class="hover:text-[#ea384c]">Vận chuyển & Giao nhận</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-3 text-sm">Phương thức thanh toán</h4>
                <div class="grid grid-cols-3 gap-2">
                    <div class="p-2 border border-gray-200 rounded-lg text-center font-bold text-[10px] text-blue-700 bg-blue-50">VNPay</div>
                    <div class="p-2 border border-gray-200 rounded-lg text-center font-bold text-[10px] text-pink-700 bg-pink-50">MoMo</div>
                    <div class="p-2 border border-gray-200 rounded-lg text-center font-bold text-[10px] text-emerald-700 bg-emerald-50">COD</div>
                    <div class="p-2 border border-gray-200 rounded-lg text-center font-bold text-[10px] text-indigo-700 bg-indigo-50">Visa/Master</div>
                    <div class="p-2 border border-gray-200 rounded-lg text-center font-bold text-[10px] text-amber-700 bg-amber-50">Napas</div>
                    <div class="p-2 border border-gray-200 rounded-lg text-center font-bold text-[10px] text-gray-700 bg-gray-50">Ví ShopMart</div>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-3 text-sm">Kết nối với chúng tôi</h4>
                <p class="text-gray-500 mb-3">Hotline: 1900 8888 (8:00 - 21:00 hàng ngày)</p>
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">f</span>
                    <span class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold">yt</span>
                    <span class="w-8 h-8 rounded-full bg-sky-400 text-white flex items-center justify-center font-bold">in</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
