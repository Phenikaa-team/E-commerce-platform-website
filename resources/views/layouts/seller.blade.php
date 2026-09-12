<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kênh Người Bán - ShopMart Seller Center')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#F7F8FA] text-gray-800 font-sans antialiased flex min-h-screen">

    @php
        $currentStore = auth()->user()?->store ?? \App\Models\Store::first();
        $pendingOrdersCount = $currentStore ? \App\Models\Order::whereHas('items.product', fn($q) => $q->where('store_id', $currentStore->id))->where('status', 'pending')->count() : 0;
    @endphp

    <!-- Seller Sidebar matching Reference Design -->
    <aside class="seller-sidebar">
        <!-- Brand Header: ShopMart Red Bag Icon + Bold Brand Text -->
        <div class="seller-brand-header">
            <div class="w-8 h-8 rounded-xl bg-[#F52245] flex items-center justify-center text-white shadow-xs">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/></svg>
            </div>
            <div>
                <span class="text-base font-bold tracking-tight text-gray-900">Shop<span class="text-[#F52245]">Mart</span></span>
            </div>
        </div>

        <!-- Navigation Links matching Reference -->
        <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto custom-scrollbar text-xs">
            <!-- Section 1: GIAN HÀNG CỦA TÔI -->
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 pt-1 pb-1">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Gian hàng của tôi</span>
            </div>

            <!-- Hồ sơ gian hàng (Active state matching reference) -->
            <a href="{{ route('seller.profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('seller.profile') || request()->routeIs('seller.dashboard') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('seller.profile') || request()->routeIs('seller.dashboard') ? 'text-[#F52245]' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Hồ sơ gian hàng</span>
            </a>

            <!-- Sản phẩm -->
            <a href="{{ route('seller.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('seller.products.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Sản phẩm</span>
            </a>

            <!-- Đơn hàng -->
            <a href="{{ route('seller.orders.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('seller.orders.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Đơn hàng</span>
                </div>
                @if($pendingOrdersCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full bg-[#F52245] text-white text-[10px] font-extrabold leading-none">{{ $pendingOrdersCount }}</span>
                @endif
            </a>

            <!-- Khuyến mãi -->
            <a href="{{ route('seller.coupons.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('seller.coupons.*') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <span>Khuyến mãi</span>
            </a>

            <!-- Doanh thu -->
            <a href="{{ route('seller.revenue') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all {{ request()->routeIs('seller.revenue') ? 'bg-[#FFF0F2] text-[#F52245] font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 font-medium' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Doanh thu</span>
            </a>

            <!-- Section 2: TÀI CHÍNH -->
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 pt-3 pb-1">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Tài chính</span>
            </div>

            <!-- Ví của tôi -->
            <a href="#wallet" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Ví của tôi</span>
            </a>

            <!-- Lịch sử giao dịch -->
            <a href="#transactions" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Lịch sử giao dịch</span>
            </a>

            <!-- Section 3: CÀI ĐẶT -->
            <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider px-3 pt-3 pb-1">
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Cài đặt</span>
            </div>

            <!-- Tài khoản & bảo mật -->
            <a href="#security" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Tài khoản & bảo mật</span>
            </a>

            <!-- Thông báo -->
            <a href="#notifications" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Thông báo</span>
            </a>

            <!-- Trợ giúp -->
            <a href="#help" class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-all pt-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Trợ giúp</span>
            </a>
        </nav>

        <!-- Sidebar Bottom: Store Profile Card matching Reference -->
        <div class="p-3 border-t border-[#E9ECF1]">
            <a href="{{ route('seller.profile') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100/80 transition-colors group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                        <img src="{{ $currentStore?->logo_url ?? asset('images/placeholders/store-logo-placeholder.svg') }}" alt="{{ $currentStore?->name ?? 'Store' }}" class="w-full h-full object-cover rounded-full">
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate max-w-[120px]">{{ $currentStore->name ?? 'Gian hàng' }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">Seller</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-700 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="seller-content-wrapper">
        <!-- Top header bar matching Reference -->
        <header class="seller-topbar">
            <div class="flex items-center gap-4">
                <!-- Search Input matching Reference -->
                <div class="relative w-80 lg:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        type="text" 
                        placeholder="Tìm kiếm sản phẩm, đơn hàng, khách hàng..." 
                        class="w-full pl-10 pr-4 py-2 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-all"
                    >
                </div>
            </div>

            <!-- Header Right: Notification + Profile Badge matching Reference -->
            <div class="flex items-center gap-4">
                <!-- Notification Bell with red badge 1 -->
                <button type="button" class="relative p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-[#F52245] text-white text-[10px] font-bold flex items-center justify-center">1</span>
                </button>

                <!-- Profile Badge matching Mockup -->
                <div class="flex items-center gap-3 pl-3 border-l border-[#E9ECF1]">
                    <a href="{{ route('seller.profile') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                            <img src="{{ $currentStore?->logo_url ?? asset('images/placeholders/store-logo-placeholder.svg') }}" alt="{{ $currentStore?->name ?? 'Store' }}" class="w-full h-full object-cover rounded-lg">
                        </div>
                        <div class="text-left hidden sm:block">
                            <span class="text-xs font-bold text-gray-900 block group-hover:text-[#F52245] transition-colors truncate max-w-[140px]">
                                {{ $currentStore->name ?? 'Gian hàng' }}
                            </span>
                            <span class="text-[10px] text-gray-400 font-medium block -mt-0.5">Quản trị viên cửa hàng</span>
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

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto px-4 sm:px-5 lg:px-6 pt-2 pb-6">
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
