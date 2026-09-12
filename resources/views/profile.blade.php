<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hồ sơ người dùng - {{ $user->username ?? $user->name }} | ShopMart</title>
    <meta name="description" content="Quản lý thông tin tài khoản, đơn mua, voucher, địa chỉ nhận hàng và tích điểm Mart Xu tại ShopMart.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f5f5fa] text-[#1e293b] font-sans antialiased min-h-screen flex flex-col selection:bg-rose-500 selection:text-white">

    <!-- ==================== HEADER (Mockup 3 Header) ==================== -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-xs">
        <!-- Main Top Row -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-6 relative z-50">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#ea384c] to-[#ff5c6c] flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-2xl font-black tracking-tight text-gray-900 group-hover:text-[#ea384c] transition-colors">
                    Shop<span class="text-[#ea384c]">Mart</span>
                </span>
            </a>

            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl hidden sm:block">
                <form action="#" method="GET" class="relative flex items-center" onsubmit="event.preventDefault();">
                    <input 
                        type="text" 
                        placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục..." 
                        class="w-full pl-5 pr-14 py-2 bg-gray-100/90 hover:bg-gray-100 focus:bg-white text-sm text-gray-800 rounded-lg border border-transparent focus:border-[#ea384c] focus:outline-none transition-all placeholder:text-gray-400"
                    >
                    <button 
                        type="submit" 
                        class="absolute right-1 top-1 bottom-1 px-4 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-md flex items-center justify-center transition-all duration-200 active:scale-95 shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Utilities: Cart, Notification, User -->
            <div class="flex items-center gap-5 shrink-0 text-sm font-medium">
                <!-- Cart -->
                <a href="{{ route('cart') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors">
                    <div class="relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-1.5 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-[#ea384c] text-white text-[10px] font-bold flex items-center justify-center">3</span>
                    </div>
                    <span class="hidden md:inline">Giỏ hàng</span>
                </a>

                <!-- Notifications -->
                <a href="#notifications" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="hidden md:inline">Thông báo</span>
                </a>

                <!-- User Dropdown Menu (Seamless Hover + Z-index) -->
                <div class="relative group" id="user-menu-dropdown" style="position: relative; z-index: 1000;">
                    <button id="user-menu-dropdown-toggle" class="flex items-center gap-2 text-gray-800 hover:text-[#ea384c] transition-colors focus:outline-none cursor-pointer">
                        <img 
                            src="{{ $user->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=100&q=80' }}" 
                            alt="{{ $user->username ?? $user->name }}" 
                            class="w-8 h-8 rounded-full object-cover border border-gray-200 shadow-xs"
                        >
                        <span class="font-bold text-sm">{{ $user->username ?? $user->name }}</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Bridge & Content (zero gap hover + z-[9999]) -->
                    <div id="profile-user-dropdown-panel" class="absolute right-0 top-full pt-1.5 w-48 hidden group-hover:block transition-all" style="position: absolute; z-index: 99999;">
                        <div class="absolute -top-4 left-0 right-0 h-6"></div>
                        <div class="bg-white rounded-xl shadow-2xl border border-gray-100 py-1.5 overflow-hidden">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Tài khoản của tôi
                            </a>
                            <a href="#orders" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Đơn mua
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors font-semibold text-left cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Navbar Row -->
        <div class="border-t border-gray-100 bg-white relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-xs sm:text-sm font-medium">
                <div class="flex items-center gap-6 overflow-x-auto py-2.5 scrollbar-none">
                    <!-- Category dropdown trigger -->
                    <a href="/#categories" class="flex items-center gap-2 text-gray-800 font-bold hover:text-[#ea384c] shrink-0 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span>Danh mục sản phẩm</span>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>

                    <a href="/" class="text-gray-600 hover:text-[#ea384c] shrink-0 transition-colors">Trang chủ</a>
                    <a href="/#flashsale" class="text-gray-600 hover:text-[#ea384c] shrink-0 transition-colors">Flash Sale</a>
                    <a href="/#new" class="text-gray-600 hover:text-[#ea384c] shrink-0 transition-colors">Sản phẩm mới</a>
                    <a href="/#bestseller" class="text-gray-600 hover:text-[#ea384c] shrink-0 transition-colors">Bán chạy</a>
                    <a href="/#brands" class="text-gray-600 hover:text-[#ea384c] shrink-0 transition-colors">Thương hiệu</a>
                    <a href="/#vip" class="text-gray-600 hover:text-[#ea384c] shrink-0 transition-colors">Ưu đãi thành viên</a>
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN PROFILE LAYOUT ==================== -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Alerts / Messages -->
        @if(session('success'))
            <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ==================== LEFT COLUMN: SIDEBAR MENU (Mockup 3) ==================== -->
            <aside class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                
                <!-- Navigation Items List -->
                <nav class="space-y-1 text-sm">
                    <!-- Item 1: Tài khoản của tôi (Active) -->
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-rose-50 text-[#ea384c] font-bold transition-all">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Tài khoản của tôi</span>
                    </a>

                    <!-- Item 2: Đơn mua -->
                    <a href="#orders" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Đơn mua</span>
                    </a>

                    <!-- Item 3: Đánh giá sản phẩm -->
                    <a href="#reviews" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span>Đánh giá sản phẩm</span>
                    </a>

                    <!-- Item 4: Sản phẩm yêu thích -->
                    <a href="#favorites" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>Sản phẩm yêu thích</span>
                    </a>

                    <!-- Item 5: Voucher của tôi -->
                    <a href="{{ route('vouchers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <span>Voucher của tôi</span>
                    </a>

                    <!-- Item 6: Mart Xu -->
                    <a href="#coins" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Mart Xu</span>
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 my-2 pt-1"></div>

                    <!-- Item 7: Thông tin cá nhân -->
                    <a href="#personal-info" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span>Thông tin cá nhân</span>
                    </a>

                    <!-- Item 8: Địa chỉ nhận hàng -->
                    <a href="#shipping-addresses" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Địa chỉ nhận hàng</span>
                    </a>

                    <!-- Item 9: Phương thức thanh toán -->
                    <a href="#payment-methods" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span>Phương thức thanh toán</span>
                    </a>

                    <!-- Item 10: Bảo mật tài khoản -->
                    <a href="#security" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Bảo mật tài khoản</span>
                    </a>

                    <!-- Item 11: Thông báo -->
                    <a href="#notifications" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Thông báo</span>
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 my-2 pt-1"></div>

                    <!-- Item 12: Trung tâm hỗ trợ -->
                    <a href="#support" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Trung tâm hỗ trợ</span>
                    </a>

                    <!-- Item 13: Đăng xuất -->
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

            <!-- ==================== RIGHT COLUMN: MAIN CONTENT (Mockup 3) ==================== -->
            <div class="lg:col-span-9 space-y-6">
                
                <!-- ==================== 1. COVER & USER PROFILE HEADER CARD ==================== -->
                <div class="relative rounded-2xl overflow-hidden shadow-sm bg-cover bg-center border border-gray-100" style="background-image: url('{{ $user->cover_url ?? 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80' }}');">
                    <!-- Dark Gradient Overlay for text clarity -->
                    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/70 to-black/50 backdrop-blur-[2px]"></div>

                    <div class="relative p-6 sm:p-8 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            
                            <!-- Avatar & Details -->
                            <div class="flex items-center gap-5">
                                <!-- Avatar with Camera Badge -->
                                <div class="relative shrink-0 group cursor-pointer" onclick="openEditModal()">
                                    <img 
                                        src="{{ $user->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=300&q=80' }}" 
                                        alt="{{ $user->username ?? $user->name }}" 
                                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover ring-4 ring-white/20 shadow-xl group-hover:ring-rose-500 transition-all"
                                    >
                                    <div class="absolute bottom-0 right-0 w-7 h-7 bg-white text-gray-800 rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-transform">
                                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- User Name & Badges -->
                                <div>
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $user->username ?? $user->name }}</h2>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-xs font-semibold text-white">
                                            <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            {{ $user->membership_tier ?? 'Thành viên Bạc' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-white/70 mt-1.5 flex items-center gap-2">
                                        <span>{{ $user->joined_date ?? 'Tham gia từ 06/2024' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Edit Profile Button -->
                            <div class="shrink-0">
                                <button 
                                    onclick="openEditModal()" 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs sm:text-sm font-semibold transition-all active:scale-95 cursor-pointer shadow-xs"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    <span>Chỉnh sửa hồ sơ</span>
                                </button>
                            </div>
                        </div>

                        <!-- Stats Counter Row (Mockup 3) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-white/15">
                            <div>
                                <div class="text-2xl font-black text-white">{{ $user->order_count ?? 12 }}</div>
                                <div class="text-xs text-white/70 mt-0.5">Đơn mua</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $user->favorite_count ?? 4 }}</div>
                                <div class="text-xs text-white/70 mt-0.5">Sản phẩm yêu thích</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $user->review_count ?? 3 }}</div>
                                <div class="text-xs text-white/70 mt-0.5">Đánh giá</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-white">{{ $user->coins ?? 120 }}</div>
                                <div class="text-xs text-white/70 mt-0.5">Mart Xu</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== 2. ĐƠN MUA CỦA TÔI (ORDER TRACKER) ==================== -->
                <div id="orders" class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-extrabold text-base text-gray-900">Đơn mua của tôi</h3>
                        <a href="javascript:void(0)" class="text-xs font-semibold text-gray-500 hover:text-[#ea384c] flex items-center gap-1 transition-colors">
                            <span>Xem tất cả</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <!-- 5 Status Steps with Badges -->
                    <div class="grid grid-cols-5 gap-2 text-center">
                        <!-- Step 1: Chờ xác nhận -->
                        <div class="flex flex-col items-center group cursor-pointer">
                            <div class="relative w-12 h-12 rounded-2xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-gray-700 group-hover:text-[#ea384c] transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[#ea384c] text-white text-[11px] font-bold flex items-center justify-center shadow-xs">1</span>
                            </div>
                            <span class="text-xs text-gray-700 mt-2 font-medium">Chờ xác nhận</span>
                        </div>

                        <!-- Step 2: Chờ lấy hàng -->
                        <div class="flex flex-col items-center group cursor-pointer">
                            <div class="relative w-12 h-12 rounded-2xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-gray-700 group-hover:text-[#ea384c] transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[#ea384c] text-white text-[11px] font-bold flex items-center justify-center shadow-xs">1</span>
                            </div>
                            <span class="text-xs text-gray-700 mt-2 font-medium">Chờ lấy hàng</span>
                        </div>

                        <!-- Step 3: Đang giao -->
                        <div class="flex flex-col items-center group cursor-pointer">
                            <div class="relative w-12 h-12 rounded-2xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-gray-700 group-hover:text-[#ea384c] transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[#ea384c] text-white text-[11px] font-bold flex items-center justify-center shadow-xs">2</span>
                            </div>
                            <span class="text-xs text-gray-700 mt-2 font-medium">Đang giao</span>
                        </div>

                        <!-- Step 4: Đã giao -->
                        <div class="flex flex-col items-center group cursor-pointer">
                            <div class="relative w-12 h-12 rounded-2xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-gray-700 group-hover:text-[#ea384c] transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-700 mt-2 font-medium">Đã giao</span>
                        </div>

                        <!-- Step 5: Trả hàng/Hoàn tiền -->
                        <div class="flex flex-col items-center group cursor-pointer">
                            <div class="relative w-12 h-12 rounded-2xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-gray-700 group-hover:text-[#ea384c] transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <span class="text-xs text-gray-700 mt-2 font-medium">Trả hàng/Hoàn tiền</span>
                        </div>
                    </div>
                </div>

                <!-- ==================== 3. 4 QUICK ACCESS CARDS ==================== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <!-- Quick Card 1 -->
                    <a href="#favorites" class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center justify-between hover:border-rose-200 hover:shadow-md transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#ea384c] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-800">Sản phẩm yêu thích</div>
                                <div class="text-[11px] text-gray-500">{{ $user->favorite_count ?? 4 }} sản phẩm</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- Quick Card 2 -->
                    <a href="{{ route('vouchers.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center justify-between hover:border-rose-200 hover:shadow-md transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#ea384c] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-800">Voucher của tôi</div>
                                <div class="text-[11px] text-gray-500">{{ $user->voucher_count ?? 3 }} voucher</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- Quick Card 3 -->
                    <a href="#coins" class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center justify-between hover:border-amber-200 hover:shadow-md transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                                $
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-800">Mart Xu</div>
                                <div class="text-[11px] text-gray-500">{{ $user->coins ?? 120 }} Xu</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- Quick Card 4 -->
                    <a href="#vip" class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs flex items-center justify-between hover:border-amber-200 hover:shadow-md transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-800">Ưu đãi thành viên</div>
                                <div class="text-[11px] text-gray-500">{{ $user->membership_tier ?? 'Thành viên Bạc' }}</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- ==================== 4. BOTTOM 2-COLUMN GRID (Mockup 3) ==================== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    
                    <!-- Left: Thông tin cá nhân -->
                    <div id="personal-info" class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <h3 class="font-extrabold text-base text-gray-900">Thông tin cá nhân</h3>
                            <button onclick="openEditModal()" class="text-xs font-semibold text-[#ea384c] hover:underline cursor-pointer">
                                Chỉnh sửa
                            </button>
                        </div>

                        <!-- Profile Field Rows -->
                        <div class="divide-y divide-gray-100 text-sm">
                            <!-- Row: Tên đăng nhập -->
                            <div class="py-3.5 flex items-center justify-between">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">Tên đăng nhập</span>
                                </div>
                                <span class="font-semibold text-gray-900 text-xs">{{ $user->username ?? 'example' }}</span>
                            </div>

                            <!-- Row: Họ và tên -->
                            <div class="py-3.5 flex items-center justify-between cursor-pointer hover:bg-gray-50/60 rounded-lg transition-colors px-1" onclick="openEditModal()">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">Họ và tên</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold {{ $user->name ? 'text-gray-900' : 'text-[#ea384c]' }}">
                                        {{ $user->name ?? 'Chưa cập nhật' }}
                                    </span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>

                            <!-- Row: Email -->
                            <div class="py-3.5 flex items-center justify-between cursor-pointer hover:bg-gray-50/60 rounded-lg transition-colors px-1" onclick="openEditModal()">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">Email</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-900">{{ $user->email }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>

                            <!-- Row: Số điện thoại -->
                            <div class="py-3.5 flex items-center justify-between cursor-pointer hover:bg-gray-50/60 rounded-lg transition-colors px-1" onclick="openEditModal()">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">Số điện thoại</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-900">{{ $user->phone ?? '+84 912 345 678' }}</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>

                            <!-- Row: Ngày sinh -->
                            <div class="py-3.5 flex items-center justify-between cursor-pointer hover:bg-gray-50/60 rounded-lg transition-colors px-1" onclick="openEditModal()">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">Ngày sinh</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold {{ $user->birthday && $user->birthday !== 'Chưa cập nhật' ? 'text-gray-900' : 'text-[#ea384c]' }}">
                                        {{ $user->birthday ?? 'Chưa cập nhật' }}
                                    </span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>

                            <!-- Row: Giới tính -->
                            <div class="py-3.5 flex items-center justify-between cursor-pointer hover:bg-gray-50/60 rounded-lg transition-colors px-1" onclick="openEditModal()">
                                <div class="flex items-center gap-3 text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600 font-medium">Giới tính</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold {{ $user->gender && $user->gender !== 'Chưa cập nhật' ? 'text-gray-900' : 'text-[#ea384c]' }}">
                                        {{ $user->gender ?? 'Chưa cập nhật' }}
                                    </span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Địa chỉ nhận hàng -->
                    <div id="shipping-addresses" class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <h3 class="font-extrabold text-base text-gray-900">Địa chỉ nhận hàng</h3>
                            <button onclick="openAddressModal()" class="text-xs font-semibold text-[#ea384c] hover:underline cursor-pointer flex items-center gap-1">
                                <span>+ Thêm địa chỉ mới</span>
                            </button>
                        </div>

                        <!-- Address Cards List -->
                        <div class="space-y-4 pt-4">
                            @forelse($user->addresses as $address)
                                <div class="p-4 rounded-xl border {{ $address->is_default ? 'border-rose-200 bg-rose-50/20' : 'border-gray-200 bg-white' }} relative group">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if($address->is_default)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold text-[#ea384c] border border-[#ea384c] bg-white">
                                                        Mặc định
                                                    </span>
                                                @endif
                                                <span class="font-bold text-xs text-gray-900">{{ $address->recipient_name }}</span>
                                                <span class="text-xs text-gray-500 font-medium">{{ $address->phone }}</span>
                                            </div>
                                            <p class="text-xs text-gray-600 leading-relaxed pt-1">
                                                {{ $address->address_line }}
                                            </p>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="shrink-0 flex items-center gap-2">
                                            @if(! $address->is_default)
                                                <form action="{{ route('profile.address.default', $address->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-[11px] text-gray-500 hover:text-[#ea384c] font-medium border border-gray-200 hover:border-[#ea384c] px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                                        Đặt mặc định
                                                    </button>
                                                </form>
                                                <form action="{{ route('profile.address.delete', $address->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 p-1 rounded-lg transition-colors cursor-pointer" title="Xóa địa chỉ">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-300">
                                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-xs text-gray-400">
                                    Chưa có địa chỉ nào. Nhấn vào "+ Thêm địa chỉ mới" để thêm.
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer Link: Xem tất cả địa chỉ -->
                        <div class="text-center pt-4 border-t border-gray-100 mt-4">
                            <button onclick="openAddressModal()" class="text-xs font-semibold text-gray-600 hover:text-[#ea384c] inline-flex items-center gap-1 cursor-pointer">
                                <span>Xem tất cả địa chỉ ({{ $user->addresses->count() }})</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- ==================== MODAL: CHỈNH SỬA THÔNG TIN CÁ NHÂN ==================== -->
    <div id="edit-profile-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <h3 class="text-lg font-black text-gray-900">Chỉnh sửa thông tin cá nhân</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 p-1 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tên đăng nhập (Username)</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Giới tính</label>
                        <select name="gender" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                            <option value="Chưa cập nhật" {{ ($user->gender ?? '') === 'Chưa cập nhật' ? 'selected' : '' }}>Chưa cập nhật</option>
                            <option value="Nam" {{ ($user->gender ?? '') === 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ ($user->gender ?? '') === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                            <option value="Khác" {{ ($user->gender ?? '') === 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ngày sinh</label>
                        <input type="text" name="birthday" value="{{ old('birthday', $user->birthday) }}" placeholder="DD/MM/YYYY" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-xs font-bold shadow-md shadow-rose-500/20">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: THÊM ĐỊA CHỈ MỚI ==================== -->
    <div id="add-address-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <h3 class="text-lg font-black text-gray-900">Thêm địa chỉ nhận hàng</h3>
                <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-700 p-1 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('profile.address.add') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Họ và tên người nhận</label>
                    <input type="text" name="recipient_name" required placeholder="Ví dụ: Nguyễn Văn A" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" name="phone" required placeholder="Ví dụ: (+84) 912 345 678" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Địa chỉ chi tiết</label>
                    <textarea name="address_line" rows="3" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none"></textarea>
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded text-[#ea384c] focus:ring-rose-500 border-gray-300 accent-[#ea384c]">
                        <span>Đặt làm địa chỉ mặc định</span>
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddressModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Hủy
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-xs font-bold shadow-md shadow-rose-500/20">
                        Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Copyright -->
    <footer class="py-6 border-t border-gray-200 bg-white text-center text-xs text-gray-400 mt-12">
        <p>&copy; 2026 ShopMart Inc. Nền tảng thương mại điện tử hàng đầu Việt Nam.</p>
    </footer>

    <script>
        function openEditModal() {
            document.getElementById('edit-profile-modal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('edit-profile-modal').classList.add('hidden');
        }
        function openAddressModal() {
            document.getElementById('add-address-modal').classList.remove('hidden');
        }
        function closeAddressModal() {
            document.getElementById('add-address-modal').classList.add('hidden');
        }
    </script>
</body>
</html>
