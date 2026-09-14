<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-check" content="{{ auth()->check() ? '1' : '0' }}">
    <title>@yield('title', 'ShopMart - Sàn Thương Mại Điện Tử Đỉnh Cao')</title>
    <meta name="description" content="@yield('meta_description', 'Mua sắm trực tuyến hàng ngàn sản phẩm công nghệ, thời trang, gia dụng chính hãng với ưu đãi giảm đến 50% tại ShopMart.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased selection:bg-rose-500 selection:text-white flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="site-announcement">
        <span>Lễ Hội Mua Sắm Siêu Sale: Miễn phí vận chuyển đơn từ 500k & Voucher giảm đến 30% hôm nay!</span>
    </div>

    <!-- ==================== MAIN HEADER ==================== -->
    <header class="site-header">
        <div class="site-header-container">
            
            <!-- Brand Logo -->
            <x-logo />

            <!-- Smart Live Search Bar -->
            <div class="header-search-container" id="header-search-container">
                <form action="{{ route('catalog.search') }}" method="GET" class="relative flex items-center">
                    <input 
                        type="text" 
                        name="q"
                        value="{{ request('q', request('search')) }}"
                        id="smart-search-input"
                        autocomplete="off"
                        placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục..." 
                        class="header-search-input"
                        aria-label="Tìm kiếm sản phẩm"
                    >
                    <button 
                        type="submit" 
                        class="header-search-btn"
                        aria-label="Tìm kiếm"
                    >
                        <x-icon name="search" class="w-4 h-4" />
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
                

                <!-- Cart Button with realtime badge -->
                <a href="{{ route('cart') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors group relative py-1">
                    <div class="relative">
                        <x-icon name="cart" class="w-6 h-6 text-gray-700 group-hover:text-[#ea384c] transition-colors" />
                        <span class="cart-badge-count header-cart-badge">0</span>
                    </div>
                    <span class="hidden sm:inline">Giỏ hàng</span>
                </a>

                <!-- Multi-state User Menu -->
                <x-header-user-menu />
            </div>
        </div>
    </header>

    <!-- Global Flash Toast Alerts -->
    @if(session('success') || session('error') || session('warning') || session('info'))
        <div class="global-alerts-container">
            @if(session('success'))
                <div class="alert-box alert-box-success">
                    <x-icon name="check" class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-box alert-box-error">
                    <x-icon name="close" class="w-5 h-5 text-rose-600 shrink-0" />
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
                <x-footer-payment-shipping />
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
