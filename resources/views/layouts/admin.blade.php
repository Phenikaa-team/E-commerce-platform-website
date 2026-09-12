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

    <!-- Admin Sidebar (Pure Modern Light Theme) -->
    <aside class="w-64 bg-white text-gray-700 flex flex-col shrink-0 border-r border-gray-100 shadow-xs select-none z-30">
        <!-- Logo -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-100 bg-white">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#ea384c] to-[#ff5c6c] flex items-center justify-center text-white shadow-md shadow-rose-500/20">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/></svg>
            </div>
            <div>
                <span class="text-xl font-black tracking-tight text-gray-900">Shop<span class="text-[#ea384c]">Mart</span></span>
                <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#ea384c] block -mt-1">Admin Portal</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">
            <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider px-3 mb-2">Tổng quan</div>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#ea384c] text-white shadow-md shadow-rose-500/20' : 'text-gray-600 hover:text-[#ea384c] hover:bg-rose-50/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Tổng quan</span>
            </a>

            <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider px-3 pt-5 mb-2">Quản lý cửa hàng & sàn</div>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-[#ea384c] text-white shadow-md shadow-rose-500/20' : 'text-gray-600 hover:text-[#ea384c] hover:bg-rose-50/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Danh mục ngành hàng</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#ea384c] text-white shadow-md shadow-rose-500/20' : 'text-gray-600 hover:text-[#ea384c] hover:bg-rose-50/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Khách hàng & Gian hàng</span>
            </a>

            <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.coupons.*') ? 'bg-[#ea384c] text-white shadow-md shadow-rose-500/20' : 'text-gray-600 hover:text-[#ea384c] hover:bg-rose-50/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <span>Khuyến mãi toàn sàn</span>
            </a>

            <div class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider px-3 pt-5 mb-2">Hệ thống & Cài đặt</div>

            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.profile') ? 'bg-[#ea384c] text-white shadow-md shadow-rose-500/20' : 'text-gray-600 hover:text-[#ea384c] hover:bg-rose-50/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Hồ sơ Admin</span>
            </a>

            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:text-emerald-600 hover:bg-emerald-50/70 transition-all">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Xem sàn ShopMart</span>
            </a>
        </nav>

        <!-- Admin App Version Badge -->
        <div class="p-4 border-t border-gray-100">
            <div class="bg-rose-50/50 rounded-xl p-3 flex items-center gap-3 border border-rose-100/60">
                <div class="w-8 h-8 rounded-lg bg-[#ea384c] text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-900">ShopMart Admin</p>
                    <p class="text-[10px] text-gray-500 font-medium">Phiên bản 2.5.0 Pro</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#f8f9fd]">
        
        <!-- Top Navbar (Matching Mockup Header) -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8 z-20">
            <div class="flex items-center gap-4">
                <button type="button" class="text-gray-500 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>

                <!-- Search Input matching Mockup -->
                <div class="relative w-80 lg:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
