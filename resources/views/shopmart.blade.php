<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ShopMart - Nền tảng Mua sắm Trực tuyến Hàng đầu</title>
    <meta name="description" content="Mua sắm trực tuyến hàng ngàn sản phẩm công nghệ, thời trang, gia dụng chính hãng với ưu đãi giảm đến 50%, flash sale cực sốc và miễn phí vận chuyển tại ShopMart.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f5f5fa] text-[#1e293b] font-sans antialiased selection:bg-rose-500 selection:text-white pb-20 md:pb-0">

    <!-- ==================== DESKTOP TOP HEADER (>= 1024px) ==================== -->
    <header class="hidden lg:block bg-white border-b border-gray-100 sticky top-0 z-[100] shadow-xs">
        <!-- Main Header Row -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-8 relative z-50">
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
            <div class="flex-1 max-w-2xl">
                <form action="#" method="GET" class="relative flex items-center" onsubmit="event.preventDefault();">
                    <input 
                        type="text" 
                        placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục..." 
                        class="w-full pl-5 pr-14 py-2.5 bg-gray-100/90 hover:bg-gray-100 focus:bg-white text-sm text-gray-800 rounded-lg border border-transparent focus:border-[#ea384c] focus:outline-hidden focus:ring-2 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                    >
                    <button 
                        type="submit" 
                        class="absolute right-1 top-1 bottom-1 px-4 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-md flex items-center justify-center transition-all duration-200 active:scale-95 shadow-xs"
                        aria-label="Tìm kiếm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- User Utilities (Cart, User) -->
            <div class="flex items-center gap-6 shrink-0 text-sm font-medium">
                <!-- Cart -->
                <a href="{{ route('cart') }}" class="flex items-center gap-2 text-gray-600 hover:text-[#ea384c] transition-colors group">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-700 group-hover:text-[#ea384c] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="cart-badge-count absolute -top-2 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-[#ea384c] text-white text-[10px] font-bold flex items-center justify-center shadow-xs">3</span>
                    </div>
                    <span>Giỏ hàng</span>
                </a>

                <!-- User Account -->
                @auth
                    <div class="relative group" id="user-menu-wrapper" style="position: relative; z-index: 1000;">
                        <button type="button" id="user-menu-toggle" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors focus:outline-none cursor-pointer">
                            <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=100&q=80' }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            <div class="text-left text-xs leading-tight">
                                <span class="text-gray-400 block">Xin chào,</span>
                                <span class="font-bold text-gray-800">{{ auth()->user()->username ?? auth()->user()->name }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <!-- Dropdown Bridge & Content (zero gap hover + z-[9999]) -->
                        <div id="user-dropdown-panel" class="absolute right-0 top-full pt-1.5 w-48 hidden group-hover:block transition-all" style="position: absolute; z-index: 99999;">
                            <!-- Invisible hover bridge prevents mouseleave -->
                            <div class="absolute -top-4 left-0 right-0 h-6"></div>
                            <div class="bg-white rounded-xl shadow-2xl border border-gray-100 py-1.5 overflow-hidden">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Tài khoản của tôi
                                </a>
                                <a href="{{ route('profile') }}#orders" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Đơn mua
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 font-semibold text-left transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2.5 text-gray-700 hover:text-[#ea384c] transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center border border-gray-100">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-left text-xs leading-tight">
                            <span class="text-gray-400 block">Xin chào,</span>
                            <span class="font-bold text-gray-800">Đăng nhập</span>
                        </div>
                    </a>
                @endauth
            </div>
        </div>

        <!-- Secondary Navbar Row with DETAILED MEGA DROPDOWN MENU -->
        <div class="border-t border-gray-100 bg-white relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative">
                    <div class="flex items-center justify-between text-xs sm:text-sm font-medium">
                        <div class="flex items-center gap-6">
                            
                            <!-- TOP CATEGORY MEGA DROPDOWN BUTTON -->
                            <div id="top-mega-menu-wrapper">
                                <button id="top-mega-menu-btn" class="flex items-center gap-3 px-5 py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white font-semibold rounded-t-lg transition-colors cursor-pointer select-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                    <span>Danh mục sản phẩm</span>
                                    <svg id="top-mega-menu-chevron" class="w-3.5 h-3.5 ml-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Navigation Links -->
                            <nav class="flex items-center gap-7">
                                <a href="/" class="py-2.5 text-[#ea384c] font-bold border-b-2 border-[#ea384c] transition-colors">Trang chủ</a>
                                <a href="#flashsale" class="py-2.5 text-gray-600 hover:text-[#ea384c] transition-colors">Flash Sale</a>
                                <a href="#new" class="py-2.5 text-gray-600 hover:text-[#ea384c] transition-colors">Sản phẩm mới</a>
                                <a href="#bestseller" class="py-2.5 text-gray-600 hover:text-[#ea384c] transition-colors">Bán chạy</a>
                                <a href="#brands" class="py-2.5 text-gray-600 hover:text-[#ea384c] transition-colors">Thương hiệu</a>
                                <a href="#vip" class="py-2.5 text-gray-600 hover:text-[#ea384c] transition-colors">Ưu đãi thành viên</a>
                            </nav>
                        </div>

                        <!-- Right Utility Links -->
                        <div class="flex items-center gap-5 text-gray-500 text-xs">
                            <a href="#support" class="hover:text-gray-800 transition-colors">Hỗ trợ</a>
                            <a href="#track-order" class="hover:text-gray-800 transition-colors">Theo dõi đơn hàng</a>
                            <a href="#download-app" class="flex items-center gap-1.5 hover:text-gray-800 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Tải ứng dụng</span>
                            </a>
                        </div>
                    </div>

                    <!-- MEGA DROPDOWN MENU PANEL (Exact match to screenshot) -->
                    <div id="top-mega-dropdown" class="hidden absolute top-full left-0 right-0 bg-white rounded-b-2xl shadow-2xl border border-gray-100 p-5 z-[100] animate-dropdown" style="background-color: #ffffff;">
                            
                            <!-- Main Top Area: 10 Cards Grid (Col-9) + Right Sidebar (Col-3) -->
                            <div class="grid grid-cols-12 gap-5 items-start">
                                
                                <!-- LEFT: 10 Category Cards (2 rows x 5 columns) -->
                                <div class="col-span-9 grid grid-cols-5 gap-3">
                                    
                                    <!-- Card 1: Điện tử -->
                                    <a href="#cat-electronics" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-slate-50/70 border border-gray-100/60 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-rose-50/20 transition-colors">
                                            <img 
                                                src="/images/concept/electronics.jpg" 
                                                alt="Điện tử" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Điện tử</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Điện thoại, Laptop, Máy tính, Phụ kiện</p>
                                        </div>
                                    </a>

                                    <!-- Card 2: Thời trang -->
                                    <a href="#cat-fashion" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-amber-50/50 border border-amber-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-amber-50/80 transition-colors">
                                            <img 
                                                src="/images/concept/fashion.jpg" 
                                                alt="Thời trang" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Thời trang</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Nam, Nữ, Trẻ em, Phụ kiện thời trang</p>
                                        </div>
                                    </a>

                                    <!-- Card 3: Nhà cửa & Đời sống -->
                                    <a href="#cat-home" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-sky-50/50 border border-sky-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-sky-50/80 transition-colors">
                                            <img 
                                                src="/images/concept/furniture.jpg" 
                                                alt="Nhà cửa & Đời sống" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Nhà cửa & Đời sống</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Nội thất, Trang trí, Đồ dùng gia đình</p>
                                        </div>
                                    </a>

                                    <!-- Card 4: Làm đẹp & Sức khỏe -->
                                    <a href="#cat-beauty" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-pink-50/50 border border-pink-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-pink-50/80 transition-colors">
                                            <img 
                                                src="/images/concept/cosmetics.jpg" 
                                                alt="Làm đẹp & Sức khỏe" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Làm đẹp & Sức khỏe</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Mỹ phẩm, Chăm sóc da, Chăm sóc cá nhân</p>
                                        </div>
                                    </a>

                                    <!-- Card 5: Thực phẩm & Đồ uống -->
                                    <a href="#cat-food" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-emerald-50/50 border border-emerald-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-emerald-50/80 transition-colors">
                                            <img 
                                                src="/images/concept/groceries.jpg" 
                                                alt="Thực phẩm & Đồ uống" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Thực phẩm & Đồ uống</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Thực phẩm tươi sống, Đồ khô, Đồ uống</p>
                                        </div>
                                    </a>

                                    <!-- Card 6: Mẹ & Bé -->
                                    <a href="#cat-mom" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-orange-50/50 border border-orange-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-orange-50/80 transition-colors">
                                            <img 
                                                src="/images/concept/baby.jpg" 
                                                alt="Mẹ & Bé" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Mẹ & Bé</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Đồ dùng, Thực phẩm, Thời trang cho bé</p>
                                        </div>
                                    </a>

                                    <!-- Card 7: Thể thao & Dã ngoại -->
                                    <a href="#cat-sports" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-cyan-50/50 border border-cyan-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-cyan-50/80 transition-colors">
                                            <img 
                                                src="/images/concept/sports.jpg" 
                                                alt="Thể thao & Dã ngoại" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Thể thao & Dã ngoại</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Dụng cụ thể thao, Du lịch, Dã ngoại</p>
                                        </div>
                                    </a>

                                    <!-- Card 8: Sách & Văn phòng phẩm -->
                                    <a href="#cat-books" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-amber-50/60 border border-amber-100/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-amber-50/90 transition-colors">
                                            <img 
                                                src="/images/concept/books.jpg" 
                                                alt="Sách & Văn phòng phẩm" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Sách & Văn phòng phẩm</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Sách, Dụng cụ học tập, Văn phòng phẩm</p>
                                        </div>
                                    </a>

                                    <!-- Card 9: Ô tô, Xe máy & Phụ kiện -->
                                    <a href="#cat-auto" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-slate-100/70 border border-slate-200/50 flex items-center justify-center overflow-hidden relative mb-2 p-1.5 group-hover:bg-slate-100 transition-colors">
                                            <img 
                                                src="/images/concept/automotive.jpg" 
                                                alt="Ô tô, Xe máy & Phụ kiện" 
                                                class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Ô tô, Xe máy & Phụ kiện</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Phụ tùng, Chăm sóc xe, Phụ kiện</p>
                                        </div>
                                    </a>

                                    <!-- Card 10: Xem tất cả danh mục -->
                                    <a href="#categories" class="bg-white rounded-2xl border border-gray-100 hover:border-rose-200 hover:shadow-md transition-all p-3 flex flex-col justify-between group">
                                        <div class="w-full h-24 rounded-xl bg-gray-50 border border-gray-100/60 flex items-center justify-center overflow-hidden relative mb-2 group-hover:bg-rose-50/30 transition-colors">
                                            <div class="grid grid-cols-2 gap-2 text-gray-400 group-hover:text-[#ea384c] group-hover:scale-110 transition-all">
                                                <span class="w-4 h-4 rounded-md border-2 border-current"></span>
                                                <span class="w-4 h-4 rounded-md border-2 border-current"></span>
                                                <span class="w-4 h-4 rounded-md border-2 border-current"></span>
                                                <span class="w-4 h-4 rounded-md border-2 border-current"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#ea384c] transition-colors">Xem tất cả danh mục</h4>
                                                <svg class="w-3 h-3 text-gray-300 group-hover:text-[#ea384c] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <p class="text-[10.5px] text-gray-400 mt-0.5 line-clamp-2 leading-tight">Khám phá thêm nhiều sản phẩm</p>
                                        </div>
                                    </a>

                                </div>

                                <!-- RIGHT SIDEBAR: Thương hiệu nổi bật & Ưu đãi hôm nay (Col-3) -->
                                <div class="col-span-3 space-y-4 pl-1">
                                    
                                    <!-- Thương hiệu nổi bật (8 Brands: 2 rows x 4 cols) -->
                                    <div class="bg-white rounded-2xl border border-gray-100/90 p-3.5 shadow-xs">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-extrabold text-xs text-gray-900">Thương hiệu nổi bật</h4>
                                            <a href="#brands" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-0.5">
                                                <span>Xem tất cả</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>

                                        <div class="grid grid-cols-4 gap-2">
                                            <!-- Apple -->
                                            <a href="#brand-apple" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center text-gray-900 shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all">
                                                    <img src="/icons/brands/apple_light.svg" alt="Apple" class="w-4 h-4 object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Apple</span>
                                            </a>

                                            <!-- Samsung -->
                                            <a href="#brand-samsung" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all px-1">
                                                    <img src="/icons/brands/samsung_default.svg" alt="Samsung" class="w-6.5 h-auto object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Samsung</span>
                                            </a>

                                            <!-- Xiaomi -->
                                            <a href="#brand-xiaomi" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all">
                                                    <img src="/icons/brands/xiaomi_default.svg" alt="Xiaomi" class="w-5 h-5 rounded-[4px] object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Xiaomi</span>
                                            </a>

                                            <!-- Nike -->
                                            <a href="#brand-nike" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center text-gray-900 shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all">
                                                    <img src="/icons/brands/nike_mono.svg" alt="Nike" class="w-5 h-auto object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Nike</span>
                                            </a>

                                            <!-- Adidas -->
                                            <a href="#brand-adidas" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center text-gray-900 shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all">
                                                    <img src="/icons/brands/adidas_mono.svg" alt="Adidas" class="w-4.5 h-auto object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Adidas</span>
                                            </a>

                                            <!-- Logitech -->
                                            <a href="#brand-logitech" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all px-1">
                                                    <img src="/icons/brands/logitech_default.svg" alt="Logitech" class="w-6.5 h-auto object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Logitech</span>
                                            </a>

                                            <!-- Unilever -->
                                            <a href="#brand-unilever" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all">
                                                    <img src="/icons/brands/unilever_default.svg" alt="Unilever" class="w-4.5 h-4.5 object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Unilever</span>
                                            </a>

                                            <!-- Lego -->
                                            <a href="#brand-lego" class="flex flex-col items-center group">
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200/70 flex items-center justify-center shadow-2xs group-hover:border-[#ea384c] group-hover:scale-105 transition-all">
                                                    <img src="/icons/brands/lego_default.svg" alt="Lego" class="w-5 h-5 rounded-[3px] object-contain">
                                                </div>
                                                <span class="text-[10px] font-medium text-gray-600 mt-1 group-hover:text-[#ea384c]">Lego</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Ưu đãi hôm nay Banner (Exact matching 3D Shopping Bag concept) -->
                                    <div class="bg-gradient-to-br from-rose-50 via-pink-50 to-orange-50/70 rounded-2xl p-4 border border-rose-100 flex items-center justify-between relative overflow-hidden group shadow-xs">
                                        <div class="relative z-10 max-w-[60%]">
                                            <h4 class="font-extrabold text-sm text-[#ea384c] leading-tight">Ưu đãi hôm nay</h4>
                                            <p class="text-[11px] text-gray-600 mt-1 leading-snug">Khám phá hàng ngàn sản phẩm giá tốt</p>
                                            <a href="#flashsale" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-[11px] font-bold rounded-full mt-3 shadow-xs transition-transform active:scale-95">
                                                <span>Xem ngay →</span>
                                            </a>
                                        </div>

                                        <!-- Promo 3D Shopping Bag with Vouchers & Coins -->
                                        <div class="w-24 h-24 shrink-0 relative flex items-center justify-center">
                                            <img 
                                                src="/images/concept/promo_deal.jpg" 
                                                alt="Ưu đãi hôm nay" 
                                                class="w-full h-full object-contain mix-blend-multiply drop-shadow-md group-hover:scale-105 transition-transform duration-300"
                                            >
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- Bottom Quick Links Footer Bar (Exact match to screenshot) -->
                            <div class="border-t border-gray-100 mt-5 pt-3.5 flex items-center justify-between text-xs font-semibold text-gray-700 px-2">
                                <a href="#flashsale" class="flex items-center gap-2 hover:text-[#ea384c] transition-colors group">
                                    <span class="w-4 h-4 rounded-full bg-[#ea384c] text-white flex items-center justify-center text-[9px] font-bold shadow-xs">✓</span>
                                    <span>Flash Sale</span>
                                </a>

                                <a href="#bestseller" class="flex items-center gap-2 hover:text-[#ea384c] transition-colors group">
                                    <span class="text-amber-500 text-sm">👑</span>
                                    <span>Sản phẩm bán chạy</span>
                                </a>

                                <a href="#new" class="flex items-center gap-2 hover:text-[#ea384c] transition-colors group">
                                    <span class="px-1.5 py-0.5 bg-[#ea384c] text-white text-[9px] font-black rounded uppercase">NEW</span>
                                    <span>Sản phẩm mới</span>
                                </a>

                                <a href="#vip" class="flex items-center gap-2 hover:text-[#ea384c] transition-colors group">
                                    <span class="text-[#ea384c] text-sm">🏷️</span>
                                    <span>Ưu đãi thành viên</span>
                                </a>

                                <a href="#coupons" class="flex items-center gap-2 hover:text-[#ea384c] transition-colors group">
                                    <span class="text-[#ea384c] text-sm">🎁</span>
                                    <span>Mã giảm giá</span>
                                </a>

                                <a href="#trends" class="flex items-center gap-2 hover:text-[#ea384c] transition-colors group">
                                    <span class="text-blue-500 text-sm">⚡</span>
                                    <span>Xu hướng mua sắm</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== MOBILE TOP HEADER (< 1024px) ==================== -->
    <header class="lg:hidden bg-white sticky top-0 z-40 px-4 pt-3 pb-3 border-b border-gray-100 shadow-xs">
        <div class="flex items-center justify-between gap-3 mb-2.5">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#ea384c] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-gray-900">ShopMart</span>
            </a>

            <!-- Right Icons (Notifications + Cart) -->
            <div class="flex items-center gap-4">
                <button class="relative p-1 text-gray-700 hover:text-[#ea384c] transition-colors" aria-label="Thông báo">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-[#ea384c]"></span>
                </button>

                <a href="{{ route('cart') }}" class="relative p-1 text-gray-700 hover:text-[#ea384c] transition-colors" aria-label="Giỏ hàng">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="cart-badge-count absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-[#ea384c] text-white text-[10px] font-bold flex items-center justify-center shadow-xs">3</span>
                </a>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input 
                type="text" 
                placeholder="Tìm kiếm sản phẩm, thương hiệu..." 
                class="w-full pl-10 pr-4 py-2 bg-gray-100 focus:bg-white text-xs text-gray-800 rounded-full border border-transparent focus:border-[#ea384c] focus:outline-hidden transition-all placeholder:text-gray-400"
            >
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 pt-4 pb-12 space-y-6">

        <!-- ==================== HERO SECTION (Desktop 3 columns with SIDEBAR FLYOUT / Mobile 1 banner) ==================== -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-5 items-stretch relative">
            
            <!-- DESKTOP LEFT SIDEBAR: Categories with RICH FLYOUT EXPANSION PANEL -->
            <div id="sidebar-categories" class="hidden lg:flex lg:col-span-3 bg-white rounded-xl shadow-xs border border-gray-100 flex-col py-2 text-xs font-medium text-gray-700 relative select-none">
                
                <div data-sidebar-item="phone" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Điện thoại & Phụ kiện</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="laptop" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Laptop & Thiết bị số</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="electronics" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        <span>Điện tử & Điện lạnh</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="fashion" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Thời trang & Phụ kiện</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="home" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Nhà cửa & Đời sống</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="beauty" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Làm đẹp & Sức khỏe</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="mom" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mẹ & Bé</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="sports" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Thể thao & Du lịch</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="books" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Sách & Văn phòng phẩm</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="auto" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-2 4m0 0l-2-4m2 4V6a2 2 0 00-2-2H9a2 2 0 00-2 2v12m0 0l-2-4m2 4l2-4"/></svg>
                        <span>Ô tô, Xe máy & Phụ kiện</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="pets" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span>Thú cưng</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="global" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Hàng quốc tế</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <div data-sidebar-item="services" class="sidebar-cat-item px-4 py-2 hover:bg-rose-50/80 hover:text-[#ea384c] flex items-center justify-between transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#ea384c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <span>Dịch vụ & Thẻ cào</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

                <!-- SIDEBAR FLYOUT PANEL (Expansion over Hero Banner) -->
                <div id="sidebar-flyout-panel" class="hidden absolute left-full top-0 min-h-full h-auto w-[780px] xl:w-[840px] 2xl:w-[880px] bg-white rounded-2xl shadow-2xl border border-gray-100 p-5 z-40 animate-flyout -ml-1">
                    <div id="sidebar-flyout-content" class="h-full flex flex-col justify-between"></div>
                </div>

            </div>

            <!-- CENTER MAIN BANNER CAROUSEL (Desktop col-6, Mobile col-12) -->
            <div class="col-span-1 lg:col-span-6 relative rounded-2xl overflow-hidden shadow-xs border border-gray-100 min-h-[220px] sm:min-h-[300px] lg:min-h-[380px] group" data-carousel>
                
                <!-- Slide 1: Tech Flagship -->
                <div data-carousel-slide class="absolute inset-0 z-10 opacity-100 bg-gradient-to-r from-[#0b0e17] via-[#0e1422] to-[#131b2e] p-6 sm:p-8 flex flex-col justify-between text-white transition-opacity duration-700 overflow-hidden">
                    <div class="relative z-10 max-w-[55%] sm:max-w-[46%] flex flex-col justify-between h-full">
                        <div>
                            <span class="inline-block text-[10px] sm:text-xs font-bold tracking-wider uppercase text-cyan-300 bg-cyan-500/15 border border-cyan-500/20 px-3 py-1 rounded-full backdrop-blur-md mb-2 sm:mb-3">
                                Thương hiệu nổi bật
                            </span>
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight mb-2 sm:mb-3">
                                Công nghệ<br>mở lối tương lai
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-300 mb-4 sm:mb-6 line-clamp-2 leading-relaxed">
                                Hiệu suất vượt trội. Trải nghiệm không giới hạn với ưu đãi đến 50%.
                            </p>
                        </div>
                        <div>
                            <a href="#flashsale" class="inline-flex items-center gap-2 px-5 sm:px-6 py-2.5 bg-white text-gray-950 font-extrabold text-xs sm:text-sm rounded-full hover:bg-gray-100 hover:shadow-xl hover:scale-105 active:scale-95 transition-all">
                                <span>Mua ngay</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Slide 1: High-res Laptop + Accessories clearly displayed on the right -->
                    <div class="absolute right-0 top-0 bottom-0 w-full sm:w-[72%] lg:w-[68%] pointer-events-none overflow-hidden flex items-center justify-end"
                         style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 14%, black 32%, black 100%); mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 14%, black 32%, black 100%);">
                        <img 
                            src="/images/banners/hero_tech_laptop.jpg" 
                            alt="Laptop công nghệ cao cấp" 
                            class="w-full h-full object-cover object-right filter drop-shadow-2xl"
                            loading="eager"
                        >
                    </div>
                </div>

                <!-- Slide 2: Apple Ecosystem Event -->
                <div data-carousel-slide class="absolute inset-0 z-0 bg-gradient-to-r from-[#0d0d0f] via-[#141417] to-[#1c1c22] p-6 sm:p-8 flex flex-col justify-between text-white opacity-0 pointer-events-none transition-opacity duration-700 overflow-hidden">
                    <div class="relative z-10 max-w-[55%] sm:max-w-[46%] flex flex-col justify-between h-full">
                        <div>
                            <span class="inline-block text-[10px] sm:text-xs font-bold tracking-wider uppercase text-rose-300 bg-rose-500/15 border border-rose-500/20 px-3 py-1 rounded-full backdrop-blur-md mb-2 sm:mb-3">
                                Apple Official Store
                            </span>
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight mb-2 sm:mb-3">
                                iPhone 15 Pro &<br>MacBook Series
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-300 mb-4 sm:mb-6 line-clamp-2 leading-relaxed">
                                Thu cũ đổi mới trợ giá tới 3 triệu đồng. Trả góp 0% lãi suất.
                            </p>
                        </div>
                        <div>
                            <a href="#flashsale" class="inline-flex items-center gap-2 px-5 sm:px-6 py-2.5 bg-[#ea384c] text-white font-extrabold text-xs sm:text-sm rounded-full hover:bg-[#d3273b] hover:shadow-xl hover:scale-105 active:scale-95 transition-all">
                                <span>Khám phá ngay</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Slide 2: iPhone 15 Pro & MacBook clearly displayed on the right -->
                    <div class="absolute right-0 top-0 bottom-0 w-full sm:w-[75%] lg:w-[70%] pointer-events-none overflow-hidden flex items-center justify-end"
                         style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 12%, black 28%, black 100%); mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.15) 12%, black 28%, black 100%);">
                        <img 
                            src="/images/banners/hero_apple_devices.jpg" 
                            alt="iPhone 15 Pro & MacBook" 
                            class="w-full h-full object-cover object-right filter drop-shadow-2xl"
                            loading="lazy"
                        >
                    </div>
                </div>

                <!-- Carousel Controls -->
                <button data-carousel-prev class="hidden sm:flex absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/70 text-white items-center justify-center backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Slide trước">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button data-carousel-next class="hidden sm:flex absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/70 text-white items-center justify-center backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Slide tiếp">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                <!-- Dots Indicators -->
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-20">
                    <button data-carousel-dot class="h-2 w-6 rounded-full bg-white transition-all" aria-label="Trang 1"></button>
                    <button data-carousel-dot class="h-2 w-2 rounded-full bg-white/40 hover:bg-white/70 transition-all" aria-label="Trang 2"></button>
                </div>
            </div>

            <!-- DESKTOP RIGHT SIDEBAR: 2 Promo Banner Cards -->
            <div class="hidden lg:flex lg:col-span-3 flex-col gap-4">
                
                <!-- Promo 1: Fashion -->
                <div class="flex-1 bg-gradient-to-br from-amber-50 to-stone-100 rounded-2xl p-5 border border-amber-100/80 flex items-center justify-between relative overflow-hidden group shadow-xs">
                    <div class="relative z-10 max-w-[60%]">
                        <h3 class="font-bold text-sm text-gray-900 leading-snug mb-1">
                            Thời trang thu đông
                        </h3>
                        <p class="text-xs text-gray-500 mb-3">Phong cách mới cho ngày mới rạng ngời</p>
                        <a href="#cat-fashion" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-gray-900 text-white text-[11px] font-semibold rounded-full group-hover:bg-[#ea384c] transition-colors">
                            <span>Khám phá</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <div class="w-32 h-full absolute right-0 top-0 bottom-0 overflow-hidden pointer-events-none promo-mask-blend" style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.8) 30%, #000 100%); mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.8) 30%, #000 100%);">
                        <img 
                            src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=300&q=80" 
                            alt="Thời trang thu đông" 
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                    </div>
                </div>

                <!-- Promo 2: Home Living -->
                <div class="flex-1 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-100/80 flex items-center justify-between relative overflow-hidden group shadow-xs">
                    <div class="relative z-10 max-w-[60%]">
                        <h3 class="font-bold text-sm text-gray-900 leading-snug mb-1">
                            Nhà cửa tiện nghi
                        </h3>
                        <p class="text-xs text-emerald-700 font-semibold mb-3">Ưu đãi đến 50%</p>
                        <a href="#cat-home" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-emerald-700 text-white text-[11px] font-semibold rounded-full group-hover:bg-emerald-800 transition-colors">
                            <span>Mua ngay</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <div class="w-32 h-full absolute right-0 top-0 bottom-0 overflow-hidden pointer-events-none promo-mask-blend" style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.8) 30%, #000 100%); mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.8) 30%, #000 100%);">
                        <img 
                            src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=300&q=80" 
                            alt="Nhà cửa tiện nghi" 
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== CATEGORIES QUICK ACCESS ROW / GRID ==================== -->
        <section class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-gray-100">
            
            <!-- Mobile 10-item Grid (2 rows x 5 columns) -->
            <div class="grid grid-cols-5 gap-y-4 gap-x-2 sm:hidden text-center">
                <a href="#cat-phone" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Điện thoại</span>
                </a>
                <a href="#cat-fashion" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Thời trang</span>
                </a>
                <a href="#cat-electronics" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Điện tử</span>
                </a>
                <a href="#cat-home" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Gia dụng</span>
                </a>
                <a href="#cat-beauty" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Làm đẹp</span>
                </a>
                <a href="#cat-food" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Thực phẩm</span>
                </a>
                <a href="#cat-mom" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Mẹ & Bé</span>
                </a>
                <a href="#cat-sports" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 3a7 7 0 110 14 7 7 0 010-14z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Thể thao</span>
                </a>
                <a href="#cat-books" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Sách</span>
                </a>
                <a href="#categories" class="flex flex-col items-center group">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 mt-1.5 group-hover:text-[#ea384c]">Xem thêm</span>
                </a>
            </div>

            <!-- Desktop 12-item Row -->
            <div class="hidden sm:grid sm:grid-cols-6 lg:grid-cols-12 gap-3 text-center">
                <a href="#cat-phone" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Điện thoại</span>
                </a>
                <a href="#cat-laptop" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Laptop</span>
                </a>
                <a href="#cat-electronics" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Điện tử</span>
                </a>
                <a href="#cat-fashion" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Thời trang</span>
                </a>
                <a href="#cat-home" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Gia dụng</span>
                </a>
                <a href="#cat-beauty" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Làm đẹp</span>
                </a>
                <a href="#cat-mom" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Mẹ & Bé</span>
                </a>
                <a href="#cat-sports" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 3a7 7 0 110 14 7 7 0 010-14z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Thể thao</span>
                </a>
                <a href="#cat-books" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Sách</span>
                </a>
                <a href="#cat-food" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Thực phẩm</span>
                </a>
                <a href="#cat-pets" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Thú cưng</span>
                </a>
                <a href="#categories" class="flex flex-col items-center group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 mt-2 group-hover:text-[#ea384c] line-clamp-1">Xem thêm</span>
                </a>
            </div>
        </section>

        <!-- ==================== FLASH SALE SECTION ==================== -->
        <section id="flashsale" class="space-y-4">
            
            <!-- Section Header -->
            <div class="bg-white rounded-2xl px-4 sm:px-6 py-4 shadow-xs border border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="flex items-center gap-1.5 text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        <span class="text-[#ea384c] inline-block animate-pulse">⚡</span>
                        <span>Flash Sale</span>
                    </div>

                    <!-- Live Countdown Timer -->
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                        <span class="hidden sm:inline">Kết thúc sau</span>
                        <div class="flex items-center gap-1">
                            <span class="timer-hours px-2 py-1 bg-[#ea384c] text-white rounded-md font-bold text-xs shadow-xs">04</span>
                            <span class="text-red-500 font-bold">:</span>
                            <span class="timer-minutes px-2 py-1 bg-[#ea384c] text-white rounded-md font-bold text-xs shadow-xs">18</span>
                            <span class="text-red-500 font-bold">:</span>
                            <span class="timer-seconds px-2 py-1 bg-[#ea384c] text-white rounded-md font-bold text-xs shadow-xs">27</span>
                        </div>
                    </div>
                </div>

                <a href="#all-flash-sale" class="text-xs sm:text-sm font-semibold text-gray-500 hover:text-[#ea384c] flex items-center gap-1 transition-colors group">
                    <span>Xem tất cả</span>
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Flash Sale Main Grid / Row -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-5">
                
                <div class="lg:col-span-9 flex sm:grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3.5 overflow-x-auto no-scrollbar pb-2 sm:pb-0 scroll-smooth snap-x">
                    
                    @foreach($flashSaleProducts as $product)
                    <!-- Flash Sale Item: {{ $product->name }} -->
                    <div class="shrink-0 w-[170px] sm:w-auto snap-start bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-xl hover:border-rose-200 transition-all flex flex-col justify-between group overflow-hidden relative">
                        <!-- Top Full Bleed Image Container -->
                        <div class="relative w-full aspect-square overflow-hidden bg-gray-100">
                            @if($product->discount_percent > 0)
                            <span class="absolute top-2.5 left-2.5 z-10 px-2 py-0.5 bg-[#ea384c] text-white text-[10px] font-extrabold rounded-md shadow-xs pointer-events-none">
                                -{{ $product->discount_percent }}%
                            </span>
                            @endif

                            <a href="{{ route('product.detail', $product->slug) }}" class="block w-full h-full">
                                <img 
                                    src="{{ $product->main_image_url }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500 ease-out"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/[0.03] transition-colors pointer-events-none"></div>
                            </a>
                        </div>

                        <!-- Bottom Content Info -->
                        <div class="p-3 sm:p-3.5 flex flex-col justify-between flex-1 gap-2">
                            <a href="{{ route('product.detail', $product->slug) }}" class="block">
                                <h3 class="text-xs sm:text-sm font-semibold text-gray-800 line-clamp-2 leading-snug group-hover:text-[#ea384c] transition-colors mb-1.5 min-h-[32px]">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-baseline gap-1.5 mb-2">
                                    <span class="text-sm sm:text-base font-extrabold text-[#ea384c]">{{ $product->formatted_price }}</span>
                                    @if($product->original_price)
                                    <span class="text-[11px] text-gray-400 line-through">{{ $product->formatted_original_price }}</span>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-red-500 to-[#ea384c] h-2 rounded-full" style="width: {{ min(100, $product->flash_sale_percent ?? 65) }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-medium text-gray-400 block">Đã bán {{ $product->formatted_sold }}</span>
                                </div>
                            </a>

                            <!-- Dual Action Buttons: Thêm giỏ & Mua ngay -->
                            <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-gray-100 mt-1">
                                <button 
                                    data-add-to-cart 
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    class="py-1.5 px-1 bg-rose-50 hover:bg-[#ea384c] text-[#ea384c] hover:text-white rounded-xl text-[11px] font-bold transition-all flex items-center justify-center gap-1 active:scale-95 cursor-pointer"
                                    aria-label="Thêm {{ $product->name }} vào giỏ"
                                    title="Thêm vào giỏ"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span class="truncate">Thêm</span>
                                </button>
                                <a 
                                    href="{{ route('product.detail', $product->slug) }}"
                                    class="py-1.5 px-1 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-xl text-[11px] font-bold transition-all flex items-center justify-center gap-1 active:scale-95 shadow-xs text-center cursor-pointer"
                                    title="Mua ngay"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span class="truncate">Mua ngay</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>

                <!-- DESKTOP RIGHT SIDEBAR: Member VIP Card & Download App Card -->
                <div class="hidden lg:flex lg:col-span-3 flex-col gap-4">
                    <div class="bg-gradient-to-br from-amber-500/10 via-amber-100/50 to-orange-100/40 rounded-2xl p-5 border border-amber-200/60 shadow-xs flex flex-col justify-between relative overflow-hidden group">
                        <div class="relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/30 mb-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-sm mb-1">Thành viên ShopMart</h3>
                            <p class="text-xs text-gray-600 mb-4">Nhiều đặc quyền hơn, nhiều ưu đãi hơn mỗi ngày.</p>
                            <a href="#vip" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-full text-xs font-bold shadow-xs transition-all active:scale-95">
                                <span>Tìm hiểu ngay</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between relative">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm mb-1">Tải ứng dụng ngay</h3>
                            <p class="text-xs text-gray-500 mb-3">Mua sắm mọi lúc, mọi nơi</p>
                            
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 rounded-xl border border-gray-200 p-1 bg-white shadow-xs shrink-0 flex items-center justify-center">
                                    <svg class="w-full h-full text-gray-800" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm12-2h-3v3h3v-3zm3 0h-2v2h2v-2zm-3 5h3v2h-3v-2zm3 0h2v2h-2v-2zm-6-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zM9 9h6v6H9V9z"/>
                                    </svg>
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <a href="#" class="px-2.5 py-1 bg-gray-900 hover:bg-black text-white rounded-md text-[10px] font-semibold flex items-center gap-1.5 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.62-.75 1.04-1.8 0.93-2.85-.9.04-1.99.6-2.63 1.35-.57.65-1.07 1.72-.94 2.74 1.01.08 2.02-.49 2.64-1.24z"/></svg>
                                        <span>App Store</span>
                                    </a>
                                    <a href="#" class="px-2.5 py-1 bg-gray-900 hover:bg-black text-white rounded-md text-[10px] font-semibold flex items-center gap-1.5 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M3 20.5v-17c0-.8.9-1.3 1.6-.9l14 8.5c.7.4.7 1.4 0 1.8l-14 8.5c-.7.4-1.6-.1-1.6-.9z"/></svg>
                                        <span>Google Play</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ==================== RECOMMENDED PRODUCTS WITH SMOOTH HEIGHT TRANSITION ==================== -->
        <section class="space-y-4 pt-4">
            
            <!-- Section Title & Category Filter Tabs -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-200 pb-3">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight">
                    Sản phẩm gợi ý cho bạn
                </h2>

                <!-- Category Tabs with Sliding Pill Indicator and Micro-animations -->
                <div class="relative flex items-center p-1 bg-gray-100/90 rounded-full border border-gray-200/70 overflow-x-auto no-scrollbar shadow-inner" id="tab-nav-container">
                    <!-- Smooth Sliding Indicator Pill -->
                    <div id="tab-indicator" class="absolute top-1 bottom-1 rounded-full bg-white shadow-xs border border-rose-200/60 transition-all duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] pointer-events-none"></div>

                    <button data-filter-tab="all" class="active-tab relative z-10 px-4 py-1.5 rounded-full text-[#ea384c] font-bold text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Tất cả
                    </button>
                    <button data-filter-tab="phone" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Điện thoại
                    </button>
                    <button data-filter-tab="laptop" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Laptop
                    </button>
                    <button data-filter-tab="fashion" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Thời trang
                    </button>
                    <button data-filter-tab="home" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Gia dụng
                    </button>
                    <button data-filter-tab="beauty" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Làm đẹp
                    </button>
                    <button data-filter-tab="sports" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Thể thao
                    </button>
                    <button data-filter-tab="books" class="relative z-10 px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 font-medium text-xs transition-all duration-200 shrink-0 cursor-pointer select-none active:scale-95">
                        Sách
                    </button>
                </div>
            </div>

            <!-- SMOOTH HEIGHT WRAPPER CONTAINER -->
            <div id="recommended-products-wrapper" class="smooth-height-container">
                <div id="recommended-products-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4 transition-opacity duration-200">
                    
                    @foreach($recommendedProducts as $product)
                    <!-- Product Card: {{ $product->name }} -->
                    <div data-product-category="{{ $product->category?->slug ?? 'all' }}" class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-xl hover:border-rose-200 transition-all flex flex-col justify-between group overflow-hidden relative">
                        <!-- Top Full Bleed Image Container -->
                        <div class="relative w-full aspect-square overflow-hidden bg-gray-100">
                            @if($product->discount_percent > 0)
                            <span class="absolute top-2.5 left-2.5 z-10 px-2 py-0.5 bg-[#ea384c] text-white text-[10px] font-extrabold rounded-md shadow-xs pointer-events-none">
                                -{{ $product->discount_percent }}%
                            </span>
                            @endif

                            <a href="{{ route('product.detail', $product->slug) }}" class="block w-full h-full">
                                <img 
                                    src="{{ $product->main_image_url }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500 ease-out"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/[0.03] transition-colors pointer-events-none"></div>
                            </a>
                        </div>

                        <!-- Bottom Content Info -->
                        <div class="p-3 sm:p-3.5 flex flex-col justify-between flex-1 gap-2">
                            <a href="{{ route('product.detail', $product->slug) }}" class="block">
                                @if($product->badge_text)
                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-sm mb-1 inline-block">{{ $product->badge_text }}</span>
                                @endif
                                <h3 class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug group-hover:text-[#ea384c] transition-colors mb-1.5 min-h-[32px]">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-baseline gap-1.5 mb-1">
                                    <span class="text-sm font-extrabold text-[#ea384c]">{{ $product->formatted_price }}</span>
                                    @if($product->original_price)
                                    <span class="text-[10px] text-gray-400 line-through">{{ $product->formatted_original_price }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-gray-400">
                                    <span class="text-amber-500 font-semibold">⭐ {{ number_format($product->rating, 1) }}</span>
                                    <span>Đã bán {{ $product->formatted_sold }}</span>
                                </div>
                            </a>

                            <!-- Dual Action Buttons: Thêm giỏ & Mua ngay -->
                            <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-gray-100 mt-1">
                                <button 
                                    data-add-to-cart 
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    class="py-1.5 px-1 bg-gray-100 hover:bg-[#ea384c] hover:text-white rounded-xl text-[11px] font-bold transition-all text-gray-700 active:scale-95 flex items-center justify-center gap-1 cursor-pointer"
                                    aria-label="Thêm {{ $product->name }} vào giỏ"
                                    title="Thêm vào giỏ"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span class="truncate">Thêm</span>
                                </button>
                                <a 
                                    href="{{ route('product.detail', $product->slug) }}"
                                    class="py-1.5 px-1 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-xl text-[11px] font-bold transition-all active:scale-95 flex items-center justify-center gap-1 shadow-xs text-center cursor-pointer"
                                    title="Mua ngay"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span class="truncate">Mua ngay</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            <!-- View More Button -->
            <div class="text-center pt-4">
                <button class="px-8 py-2.5 bg-white border border-gray-200 hover:border-[#ea384c] hover:text-[#ea384c] text-gray-700 font-bold text-xs sm:text-sm rounded-full shadow-xs hover:shadow-md transition-all">
                    Xem thêm sản phẩm
                </button>
            </div>
        </section>

        <!-- ==================== TRUST FEATURES BADGES ==================== -->
        <section class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#ea384c] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">100% Chính hãng</h4>
                    <p class="text-[11px] text-gray-400">Cam kết hoàn tiền gấp đôi</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Miễn phí giao hàng</h4>
                    <p class="text-[11px] text-gray-400">Đơn từ 0Đ toàn quốc</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">30 ngày đổi trả</h4>
                    <p class="text-[11px] text-gray-400">Miễn phí tận nhà</p>
                </div>
            </div>
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Hỗ trợ 24/7</h4>
                    <p class="text-[11px] text-gray-400">Hotline 1900 8888</p>
                </div>
            </div>
        </section>

    </main>

    <!-- ==================== DESKTOP FOOTER ==================== -->
    <footer class="bg-white border-t border-gray-200 mt-12 hidden md:block text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Chăm sóc khách hàng</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">ShopMart Blog</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Hướng dẫn mua hàng</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Chính sách vận chuyển</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Trả hàng & Hoàn tiền</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Về ShopMart</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Giới thiệu về chúng tôi</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Tuyển dụng</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors">Kênh Người Bán</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Thanh toán & Vận chuyển</h4>
                    <p class="mb-3 text-[11px] text-gray-400 leading-relaxed">Hỗ trợ các phương thức thanh toán an toàn hàng đầu.</p>
                    <div class="flex flex-wrap gap-2 text-gray-700">
                        <span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">VISA</span>
                        <span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">MasterCard</span>
                        <span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">MoMo</span>
                        <span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">ZaloPay</span>
                        <span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">COD</span>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Theo dõi chúng tôi</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors flex items-center gap-2"><span>Facebook</span></a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors flex items-center gap-2"><span>Instagram</span></a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors flex items-center gap-2"><span>TikTok</span></a></li>
                        <li><a href="#" class="hover:text-[#ea384c] transition-colors flex items-center gap-2"><span>YouTube</span></a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Tải ứng dụng ShopMart</h4>
                    <p class="text-[11px] text-gray-400 mb-3">Quét mã QR để tải ngay ứng dụng mua sắm tiện lợi.</p>
                    <div class="flex items-center gap-2">
                        <div class="w-14 h-14 rounded-lg bg-gray-100 p-1 shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full text-gray-700" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm12-2h-3v3h3v-3zm3 0h-2v2h2v-2zm-3 5h3v2h-3v-2zm3 0h2v2h-2v-2zm-6-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-bold text-gray-700">App Store</span>
                            <span class="text-[10px] font-bold text-gray-700">Google Play</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-gray-400">
                <p>© 2026 ShopMart Inc. Tất cả quyền được bảo lưu.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:underline">Chính sách bảo mật</a>
                    <a href="#" class="hover:underline">Quy chế hoạt động</a>
                    <a href="#" class="hover:underline">Giải quyết khiếu nại</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== MOBILE BOTTOM APP NAVIGATION BAR ==================== -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 px-3 py-1.5 flex items-center justify-around shadow-lg">
        <a href="/" data-mobile-nav class="flex flex-col items-center text-[#ea384c] text-[10px] font-bold py-1">
            <svg class="w-5 h-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            <span>Trang chủ</span>
        </a>

        <a href="#categories" data-mobile-nav class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Danh mục</span>
        </a>

        <a href="{{ route('cart') }}" data-mobile-nav class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors relative">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge-count absolute -top-1.5 -right-2.5 min-w-[15px] h-[15px] px-1 bg-[#ea384c] text-white text-[9px] font-bold rounded-full flex items-center justify-center shadow-xs">3</span>
            </div>
            <span>Giỏ hàng</span>
        </a>

        <a href="#account" data-mobile-nav class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Tài khoản</span>
        </a>
    </nav>

</body>
</html>
