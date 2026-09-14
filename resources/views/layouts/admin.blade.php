<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Toàn Sàn - ShopMart Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-gray-800 font-sans antialiased flex min-h-screen">

    <!-- Admin Sidebar (Matching Seller Page Design) -->
    <aside class="seller-sidebar">
        <!-- Brand Header: ShopMart Red Bag Icon + Brand Text -->
        <div class="seller-brand-header">
            <div class="w-8 h-8 rounded-xl bg-[#F52245] flex items-center justify-center text-white shadow-xs">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/></svg>
            </div>
            <div>
                <span class="text-base font-bold tracking-tight text-gray-900">Shop<span class="text-[#F52245]">Mart</span></span>
            </div>
        </div>

        <!-- Navigation Links matching Seller Sidebar Reference -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto custom-scrollbar text-xs">
            <!-- Section 1: HỆ THỐNG QUẢN TRỊ -->
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Hệ thống quản trị</span>
            </div>

            <!-- Tổng quan & Hồ sơ Admin (Active pill) -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.profile') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.profile') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Tổng quan & Hồ sơ</span>
            </a>

            <!-- Danh mục ngành hàng -->
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.categories.*') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Danh mục ngành hàng</span>
            </a>

            <!-- Khách hàng & Gian hàng -->
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.users.*') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Khách hàng & Gian hàng</span>
            </a>

            <!-- Khuyến mãi toàn sàn -->
            <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.coupons.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.coupons.*') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <span>Khuyến mãi toàn sàn</span>
            </a>

            <!-- Section 2: VẬN HÀNH & BÁO CÁO -->
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 pt-3 pb-1">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Vận hành & Báo cáo</span>
            </div>

            <!-- Doanh thu sàn -->
            <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.revenue') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.revenue') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Doanh thu sàn</span>
            </a>

            <!-- Giám sát đơn hàng -->
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('admin.orders.*') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Giám sát đơn hàng</span>
            </a>

            <!-- Section 3: CÀI ĐẶT -->
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 pt-3 pb-1">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Cài đặt</span>
            </div>

            <!-- Tài khoản & bảo mật -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Tài khoản & bảo mật</span>
            </a>

            <!-- Xem sàn ShopMart -->
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-600 hover:text-emerald-600 hover:bg-emerald-50/70 transition-all">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Xem sàn ShopMart</span>
            </a>
        </nav>

        <!-- Sidebar Bottom: Admin Profile Card matching Seller Layout -->
        <div class="p-3 border-t border-[#E9ECF1]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100/80 transition-colors group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-rose-50 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                        <img 
                            src="{{ auth()->user()?->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80' }}" 
                            alt="{{ auth()->user()?->name ?? 'Admin' }}" 
                            class="w-full h-full object-cover rounded-full"
                        >
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate max-w-[120px]">{{ auth()->user()?->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">Super Admin</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-700 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-content-wrapper">
        
        <!-- Top Navbar (Matching Mockup Header) -->
        <header class="admin-topbar">
            <div class="flex items-center gap-4">
                <button type="button" class="text-gray-500 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>

                <!-- Search Input matching Mockup -->
                <div class="relative w-80 lg:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input 
                        type="text" 
                        placeholder="Tìm kiếm đơn hàng, người dùng, sản phẩm..." 
                        class="w-full pl-10 pr-4 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-all"
                    >
                </div>
            </div>

            <!-- Header Right Profile & Notification Actions -->
            <div class="flex items-center gap-5">
                <!-- Notification Bell -->
                <button type="button" class="relative p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer">
                    <x-icon name="bell" class="w-5 h-5" />
                    <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-[#ea384c] text-white text-[10px] font-bold flex items-center justify-center">
                        3
                    </span>
                </button>

                <!-- Profile Badge & Dropdown Trigger -->
                <div class="flex items-center gap-3 pl-3 border-l border-gray-100">
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 group">
                        <img 
                            src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80' }}" 
                            alt="Admin" 
                            class="w-9 h-9 rounded-full object-cover border border-gray-200 group-hover:ring-2 group-hover:ring-[#ea384c] transition-all"
                        >
                        <div class="text-left hidden sm:block">
                            <span class="text-xs font-black text-gray-900 block group-hover:text-[#ea384c] transition-colors">Admin</span>
                            <span class="text-[10px] text-gray-400 font-medium block -mt-0.5">Quản trị viên</span>
                        </div>
                    </a>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Đăng xuất" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer">
                            <x-icon name="logout" class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        @if(session('success') || session('error'))
            <div class="px-8 mt-4">
                @if(session('success'))
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                        <x-icon name="close" class="w-4 h-4 text-rose-600 shrink-0" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Main Body Area -->
        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
