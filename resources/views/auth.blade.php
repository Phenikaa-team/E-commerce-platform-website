<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập & Đăng ký tài khoản - ShopMart</title>
    <meta name="description" content="Đăng nhập hoặc tạo tài khoản ShopMart để nhận ngay voucher giảm giá độc quyền, tích điểm Mart Xu và theo dõi đơn hàng tiện lợi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .active-tab-line {
            position: relative;
        }
        .active-tab-line::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background-color: #ee4d2d;
            border-radius: 3px 3px 0 0;
        }
    </style>
</head>
<body class="bg-[#f8f9fc] text-[#1e293b] font-sans antialiased min-h-screen flex flex-col selection:bg-red-500 selection:text-white">

    <!-- ==================== DESKTOP TOP HEADER (Mockup 2 Header) ==================== -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-xs">
        <div class="w-full px-6 sm:px-10 lg:px-14 py-3.5 flex items-center justify-between gap-6">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#ee4d2d] to-[#ff6347] flex items-center justify-center text-white shadow-md shadow-red-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-2xl font-black tracking-tight text-gray-900 group-hover:text-[#ee4d2d] transition-colors">
                    Shop<span class="text-[#ee4d2d]">Mart</span>
                </span>
            </a>

            <!-- Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
                <a href="/#categories" class="hover:text-[#ee4d2d] transition-colors">Danh mục sản phẩm</a>
                <a href="/#flashsale" class="hover:text-[#ee4d2d] transition-colors">Flash Sale</a>
                <a href="/#new" class="hover:text-[#ee4d2d] transition-colors">Sản phẩm mới</a>
                <a href="/#brands" class="hover:text-[#ee4d2d] transition-colors">Thương hiệu</a>
                <a href="/#vip" class="hover:text-[#ee4d2d] transition-colors">Ưu đãi thành viên</a>
            </nav>

            <!-- Search Bar & Utilities -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:block relative w-60 lg:w-80">
                    <input 
                        type="text" 
                        placeholder="Tìm kiếm sản phẩm..." 
                        class="w-full pl-4 pr-10 py-2 bg-gray-100 hover:bg-gray-50 focus:bg-white text-xs sm:text-sm text-gray-800 rounded-full border border-transparent focus:border-[#ee4d2d] focus:outline-none transition-all placeholder:text-gray-400"
                    >
                    <button class="absolute right-3 top-2.5 text-gray-400 hover:text-[#ee4d2d] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>

                <!-- Cart Button -->
                <a href="{{ route('cart') }}" class="relative p-2 text-gray-600 hover:text-[#ee4d2d] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#ee4d2d] text-white text-[10px] font-bold flex items-center justify-center">0</span>
                </a>

                <!-- Header Action Button -->
                <button onclick="switchTab('login')" class="hidden sm:inline-flex items-center px-4 py-2 border border-gray-200 hover:border-[#ee4d2d] text-gray-700 hover:text-[#ee4d2d] text-sm font-semibold rounded-lg transition-all cursor-pointer">
                    Đăng nhập
                </button>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN AUTHENTICATION CONTAINER ==================== -->
    <main class="flex-1 w-full min-h-[calc(100vh-65px)] flex flex-col lg:flex-row">
        
        <!-- Alerts / Messages -->
        @if(session('success'))
            <div class="fixed top-20 right-4 z-50 bg-emerald-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="fixed top-20 right-4 z-50 bg-rose-500 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium">{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- ==================== LEFT COLUMN: HERO SHOWCASE (Full Bleed Background) ==================== -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-[52%] relative flex-col justify-between border-r border-rose-100/70 overflow-hidden select-none bg-[#faecea]">
            
            <!-- Full Height Background Image (Fills 100% of left half, including the head area) -->
            <img 
                src="{{ asset('images/auth-hero-concept.png') }}" 
                alt="ShopMart 3D Concept Showcase" 
                class="absolute inset-0 w-full h-full object-cover object-left pointer-events-none z-0"
            >

            <!-- Subtle top gradient overlay to ensure text contrast -->
            <div class="absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-[#fdf6f5]/80 via-[#fdf6f5]/40 to-transparent pointer-events-none z-[1]"></div>

            <!-- Headline & Badges Top Section -->
            <div class="relative z-10 pt-10 xl:pt-14 px-8 sm:px-12 lg:px-14 xl:px-18 space-y-4">
                <div class="space-y-3 max-w-lg">
                    <h1 class="text-3xl xl:text-4xl 2xl:text-5xl font-black text-gray-900 tracking-tight leading-[1.15]">
                        Mua sắm dễ dàng<br>
                        <span class="text-[#ee4d2d]">Cuộc sống tốt hơn</span>
                    </h1>
                    <p class="text-gray-600 font-medium text-sm xl:text-base leading-relaxed">
                        Hàng triệu sản phẩm chính hãng, giá tốt mỗi ngày chỉ có tại ShopMart.
                    </p>
                </div>

                <!-- 3 Feature Highlight Badges -->
                <div class="flex items-center gap-4 xl:gap-6 pt-2">
                    <!-- Feature 1 -->
                    <div class="flex items-center gap-2.5 bg-white/85 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/60 shadow-xs">
                        <div class="w-8 h-8 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-[#ee4d2d] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-800 leading-tight">Giao hàng nhanh</div>
                            <div class="text-[10px] text-gray-500 font-medium">Toàn quốc</div>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-center gap-2.5 bg-white/85 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/60 shadow-xs">
                        <div class="w-8 h-8 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-[#ee4d2d] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-800 leading-tight">Chính hãng 100%</div>
                            <div class="text-[10px] text-gray-500 font-medium">Đảm bảo</div>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-center gap-2.5 bg-white/85 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/60 shadow-xs">
                        <div class="w-8 h-8 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-[#ee4d2d] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-800 leading-tight">Đổi trả dễ dàng</div>
                            <div class="text-[10px] text-gray-500 font-medium">Trong 7 ngày</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Social Proof Badge (Bottom-Left) -->
            <div class="relative z-10 pb-8 px-8 sm:px-12 lg:px-14 xl:px-18 mt-auto">
                <div class="inline-flex items-center gap-3.5 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl border border-gray-100/90">
                    <div class="flex -space-x-2 overflow-hidden">
                        <img class="inline-block h-7 w-7 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Customer 1">
                        <img class="inline-block h-7 w-7 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Customer 2">
                        <img class="inline-block h-7 w-7 rounded-full ring-2 ring-white object-cover shadow-xs" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Customer 3">
                    </div>
                    <div class="text-xs">
                        <span class="font-bold text-gray-900 block leading-tight">Hơn 1.000.000+</span>
                        <span class="text-gray-500 text-[11px]">khách hàng đã tin tưởng</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- ==================== RIGHT COLUMN: AUTH CARD (Centered in Right Half) ==================== -->
        <div class="w-full lg:w-1/2 xl:w-[48%] flex items-center justify-center p-6 sm:p-10 lg:p-12 xl:p-16 bg-[#f8f9fc]">
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 sm:p-9 xl:p-10 border border-gray-100 max-w-[480px] w-full">
                    
                    <!-- Tabs Header -->
                    <div class="flex border-b border-gray-100 mb-6">
                        <button 
                            type="button" 
                            id="tab-btn-login"
                            onclick="switchTab('login')" 
                            class="flex-1 py-3 text-center font-bold text-base transition-colors {{ ($tab ?? 'login') === 'login' ? 'text-[#ee4d2d] active-tab-line' : 'text-gray-400 hover:text-gray-700' }}"
                        >
                            Đăng nhập
                        </button>
                        <button 
                            type="button" 
                            id="tab-btn-register"
                            onclick="switchTab('register')" 
                            class="flex-1 py-3 text-center font-bold text-base transition-colors {{ ($tab ?? 'login') === 'register' ? 'text-[#ee4d2d] active-tab-line' : 'text-gray-400 hover:text-gray-700' }}"
                        >
                            Đăng ký
                        </button>
                    </div>

                    <!-- ==================== TAB 1: LOGIN ==================== -->
                    <div id="tab-content-login" class="{{ ($tab ?? 'login') === 'login' ? 'block' : 'hidden' }}">
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Chào mừng trở lại!</h2>
                            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                                Đăng nhập để tiếp tục mua sắm và trải nghiệm nhiều ưu đãi hấp dẫn.
                            </p>
                        </div>

                        <!-- Login Form -->
                        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Email / Phone / Username -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="login_id" 
                                        value="{{ old('login_id', 'pigku@gmail.com') }}" 
                                        required 
                                        placeholder="Email hoặc số điện thoại" 
                                        class="w-full pl-11 pr-4 py-3 bg-gray-50/80 hover:bg-gray-50 focus:bg-white text-sm text-gray-900 rounded-xl border border-gray-200 focus:border-[#ee4d2d] focus:outline-none focus:ring-3 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                                    >
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="login-password"
                                        name="password" 
                                        value="123456" 
                                        required 
                                        placeholder="Mật khẩu" 
                                        class="w-full pl-11 pr-11 py-3 bg-gray-50/80 hover:bg-gray-50 focus:bg-white text-sm text-gray-900 rounded-xl border border-gray-200 focus:border-[#ee4d2d] focus:outline-none focus:ring-3 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePasswordVisibility('login-password', 'login-eye-icon')" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                    >
                                        <svg id="login-eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between text-xs pt-1">
                                <label class="flex items-center gap-2 cursor-pointer select-none text-gray-600">
                                    <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-[#ee4d2d] focus:ring-rose-500 border-gray-300 accent-[#ee4d2d]">
                                    <span>Ghi nhớ đăng nhập</span>
                                </label>
                                <a href="javascript:void(0)" onclick="alert('Vui lòng liên hệ hotline 1900 6868 hoặc đăng nhập bằng tài khoản pigku@gmail.com mật khẩu 123456.')" class="font-medium text-[#ee4d2d] hover:underline">
                                    Quên mật khẩu?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                class="w-full py-3.5 px-4 bg-gradient-to-r from-[#ee4d2d] to-[#ff5842] hover:from-[#d3273b] hover:to-[#ee4d2d] text-white font-bold text-sm rounded-xl shadow-lg shadow-red-500/25 active:scale-[0.99] transition-all cursor-pointer"
                            >
                                Đăng nhập
                            </button>
                        </form>

                        <!-- Social Divider -->
                        <div class="relative my-6 text-center">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                            <span class="relative bg-white px-3 text-[11px] font-medium text-gray-400 uppercase tracking-wider">Hoặc đăng nhập bằng</span>
                        </div>

                        <!-- 3 Social Buttons (Mockup 1 & 2) -->
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Google Button -->
                            <a 
                                href="{{ route('auth.social', 'google') }}" 
                                class="flex items-center justify-center gap-2 py-2.5 px-3 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 rounded-xl text-xs font-semibold text-gray-700 shadow-xs transition-all active:scale-95 group"
                                title="Đăng nhập với Google"
                            >
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/>
                                    <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                                    <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.8s.2-2.1.4-2.8L1.9 6.3C.7 8.7 0 10.8 0 12s.7 3.3 1.9 5.7l3.7-2.9z"/>
                                    <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                                </svg>
                                <span>Google</span>
                            </a>

                            <!-- Apple Button -->
                            <a 
                                href="{{ route('auth.social', 'apple') }}" 
                                class="flex items-center justify-center gap-2 py-2.5 px-3 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 rounded-xl text-xs font-semibold text-gray-700 shadow-xs transition-all active:scale-95 group"
                                title="Đăng nhập với Apple"
                            >
                                <svg class="w-4 h-4 shrink-0 text-black fill-current" viewBox="0 0 170 170">
                                    <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.7-3.04-7.7-7.85-12.01-14.42-5.46-8.36-9.74-17.6-12.85-27.71-3.11-10.11-4.67-19.98-4.67-29.61 0-13.06 3.28-24.16 9.84-33.3 6.56-9.14 14.88-13.79 24.96-13.95 4.89 0 10.49 1.34 16.8 4.02 6.31 2.68 10.37 4.08 12.18 4.2 1.45 0 5.68-1.55 12.69-4.65 7.01-3.1 13.05-4.51 18.13-4.22 13.79.69 24.57 5.75 32.34 15.19-12.09 7.33-18.01 17.38-17.76 30.15.26 10.11 4.14 18.59 11.64 25.43 7.5 6.84 16.32 10.66 26.46 11.45-2.22 6.6-4.99 13.3-8.31 20.09zM119.22 33.09c0-7.39 2.67-14.34 8.01-20.85 5.34-6.51 11.83-10.74 19.47-12.24.13 1.08.2 1.95.2 2.61 0 7.34-2.82 14.46-8.46 21.36-5.64 6.9-12.29 11.05-19.95 12.44-.39-1.07-.59-2.18-.59-3.32z"/>
                                </svg>
                                <span>Apple</span>
                            </a>

                            <!-- Facebook Button -->
                            <a 
                                href="{{ route('auth.social', 'facebook') }}" 
                                class="flex items-center justify-center gap-2 py-2.5 px-3 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 rounded-xl text-xs font-semibold text-gray-700 shadow-xs transition-all active:scale-95 group"
                                title="Đăng nhập với Facebook"
                            >
                                <svg class="w-4 h-4 shrink-0 text-[#1877F2] fill-current" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </a>
                        </div>

                        <!-- Footer switch to Register -->
                        <div class="text-center mt-6 text-xs text-gray-500">
                            Chưa có tài khoản? 
                            <button type="button" onclick="switchTab('register')" class="font-bold text-[#ee4d2d] hover:underline ml-1">
                                Đăng ký ngay
                            </button>
                        </div>
                    </div>

                    <!-- ==================== TAB 2: REGISTER ==================== -->
                    <div id="tab-content-register" class="{{ ($tab ?? 'login') === 'register' ? 'block' : 'hidden' }}">
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Tạo tài khoản</h2>
                            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                                Chỉ vài bước đơn giản để bắt đầu mua sắm cùng ShopMart.
                            </p>
                        </div>

                        <!-- Register Form -->
                        <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                            @csrf

                            <!-- Full Name -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        placeholder="Họ và tên" 
                                        class="w-full pl-11 pr-4 py-2.5 bg-gray-50/80 hover:bg-gray-50 focus:bg-white text-sm text-gray-900 rounded-xl border border-gray-200 focus:border-[#ee4d2d] focus:outline-none focus:ring-3 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                                    >
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        placeholder="Email" 
                                        class="w-full pl-11 pr-4 py-2.5 bg-gray-50/80 hover:bg-gray-50 focus:bg-white text-sm text-gray-900 rounded-xl border border-gray-200 focus:border-[#ee4d2d] focus:outline-none focus:ring-3 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                                    >
                                </div>
                            </div>

                            <!-- Phone (Optional) -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="tel" 
                                        name="phone" 
                                        value="{{ old('phone') }}" 
                                        placeholder="Số điện thoại (tùy chọn)" 
                                        class="w-full pl-11 pr-4 py-2.5 bg-gray-50/80 hover:bg-gray-50 focus:bg-white text-sm text-gray-900 rounded-xl border border-gray-200 focus:border-[#ee4d2d] focus:outline-none focus:ring-3 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                                    >
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="register-password"
                                        name="password" 
                                        required 
                                        placeholder="Mật khẩu (ít nhất 6 ký tự)" 
                                        class="w-full pl-11 pr-11 py-2.5 bg-gray-50/80 hover:bg-gray-50 focus:bg-white text-sm text-gray-900 rounded-xl border border-gray-200 focus:border-[#ee4d2d] focus:outline-none focus:ring-3 focus:ring-rose-500/10 transition-all placeholder:text-gray-400"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="togglePasswordVisibility('register-password', 'reg-eye-icon')" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                    >
                                        <svg id="reg-eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="pt-1">
                                <label class="flex items-start gap-2.5 cursor-pointer text-[11px] text-gray-600 leading-snug">
                                    <input type="checkbox" name="terms" required checked class="w-4 h-4 mt-0.5 rounded text-[#ee4d2d] focus:ring-rose-500 border-gray-300 accent-[#ee4d2d]">
                                    <span>Tôi đồng ý với <a href="javascript:void(0)" class="text-[#ee4d2d] font-semibold hover:underline">Điều khoản dịch vụ</a> và <a href="javascript:void(0)" class="text-[#ee4d2d] font-semibold hover:underline">Chính sách bảo mật</a> của ShopMart</span>
                                </label>
                            </div>

                            <!-- Register Button -->
                            <button 
                                type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-[#ee4d2d] to-[#ff5842] hover:from-[#d3273b] hover:to-[#ee4d2d] text-white font-bold text-sm rounded-xl shadow-lg shadow-red-500/25 active:scale-[0.99] transition-all cursor-pointer"
                            >
                                Tạo tài khoản
                            </button>
                        </form>

                        <!-- Social Divider -->
                        <div class="relative my-5 text-center">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                            <span class="relative bg-white px-3 text-[11px] font-medium text-gray-400 uppercase tracking-wider">Hoặc đăng ký bằng</span>
                        </div>

                        <!-- 3 Social Buttons -->
                        <div class="grid grid-cols-3 gap-3">
                            <a href="{{ route('auth.social', 'google') }}" class="flex items-center justify-center gap-2 py-2 px-3 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 shadow-xs transition-all active:scale-95">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/>
                                    <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                                    <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.8s.2-2.1.4-2.8L1.9 6.3C.7 8.7 0 10.8 0 12s.7 3.3 1.9 5.7l3.7-2.9z"/>
                                    <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                                </svg>
                                <span>Google</span>
                            </a>
                            <a href="{{ route('auth.social', 'apple') }}" class="flex items-center justify-center gap-2 py-2 px-3 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 shadow-xs transition-all active:scale-95">
                                <svg class="w-4 h-4 shrink-0 text-black fill-current" viewBox="0 0 170 170">
                                    <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.7-3.04-7.7-7.85-12.01-14.42-5.46-8.36-9.74-17.6-12.85-27.71-3.11-10.11-4.67-19.98-4.67-29.61 0-13.06 3.28-24.16 9.84-33.3 6.56-9.14 14.88-13.79 24.96-13.95 4.89 0 10.49 1.34 16.8 4.02 6.31 2.68 10.37 4.08 12.18 4.2 1.45 0 5.68-1.55 12.69-4.65 7.01-3.1 13.05-4.51 18.13-4.22 13.79.69 24.57 5.75 32.34 15.19-12.09 7.33-18.01 17.38-17.76 30.15.26 10.11 4.14 18.59 11.64 25.43 7.5 6.84 16.32 10.66 26.46 11.45-2.22 6.6-4.99 13.3-8.31 20.09zM119.22 33.09c0-7.39 2.67-14.34 8.01-20.85 5.34-6.51 11.83-10.74 19.47-12.24.13 1.08.2 1.95.2 2.61 0 7.34-2.82 14.46-8.46 21.36-5.64 6.9-12.29 11.05-19.95 12.44-.39-1.07-.59-2.18-.59-3.32z"/>
                                </svg>
                                <span>Apple</span>
                            </a>
                            <a href="{{ route('auth.social', 'facebook') }}" class="flex items-center justify-center gap-2 py-2 px-3 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 shadow-xs transition-all active:scale-95">
                                <svg class="w-4 h-4 shrink-0 text-[#1877F2] fill-current" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </a>
                        </div>

                        <!-- Footer switch to Login -->
                        <div class="text-center mt-5 text-xs text-gray-500">
                            Đã có tài khoản? 
                            <button type="button" onclick="switchTab('login')" class="font-bold text-[#ee4d2d] hover:underline ml-1">
                                Đăng nhập
                            </button>
                        </div>
                    </div>

                </div>
            </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="py-6 border-t border-gray-200 bg-white text-center text-xs text-gray-400">
        <p>&copy; 2026 ShopMart Inc. Nền tảng thương mại điện tử hàng đầu Việt Nam.</p>
    </footer>

    <!-- Tab & Password Toggle Scripts -->
    <script>
        function switchTab(tab) {
            const loginBtn = document.getElementById('tab-btn-login');
            const registerBtn = document.getElementById('tab-btn-register');
            const loginContent = document.getElementById('tab-content-login');
            const registerContent = document.getElementById('tab-content-register');

            if (tab === 'login') {
                loginBtn.classList.add('text-[#ee4d2d]', 'active-tab-line');
                loginBtn.classList.remove('text-gray-400');
                registerBtn.classList.remove('text-[#ee4d2d]', 'active-tab-line');
                registerBtn.classList.add('text-gray-400');

                loginContent.classList.remove('hidden');
                registerContent.classList.add('hidden');
            } else {
                registerBtn.classList.add('text-[#ee4d2d]', 'active-tab-line');
                registerBtn.classList.remove('text-gray-400');
                loginBtn.classList.remove('text-[#ee4d2d]', 'active-tab-line');
                loginBtn.classList.add('text-gray-400');

                registerContent.classList.remove('hidden');
                loginContent.classList.add('hidden');
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>
</body>
</html>
