@php
    $images = $product->images->isNotEmpty() ? $product->images->pluck('url')->toArray() : [$product->main_image_url];
    $imageCount = count($images);
    $variants = $product->variants ?? [];
    $colors = $variants['colors'] ?? [];
    $options = $variants['options'] ?? [];
    $specs = $product->specs ?? [];
    $features = $product->features ?? [];
    $faqs = $product->faqs ?? [];
@endphp
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - ShopMart</title>
    <meta name="description" content="Mua {{ $product->name }} chính hãng giá tốt nhất tại ShopMart. Miễn phí vận chuyển, cam kết 100% chính hãng, đổi trả trong 7 ngày.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f5f5fa] text-[#1e293b] font-sans antialiased selection:bg-rose-500 selection:text-white pb-20 lg:pb-0">

    <!-- ==================== DESKTOP TOP HEADER ==================== -->
    <header class="hidden lg:block bg-white border-b border-gray-100 sticky top-0 z-[100] shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-8 relative z-50">
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

            <div class="flex-1 max-w-2xl">
                <form action="#" method="GET" class="relative flex items-center" onsubmit="event.preventDefault();">
                    <input type="text" placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục..."
                        class="w-full pl-5 pr-14 py-2.5 bg-gray-100/90 hover:bg-gray-100 focus:bg-white text-sm text-gray-800 rounded-lg border border-transparent focus:border-[#ea384c] focus:outline-hidden focus:ring-2 focus:ring-rose-500/10 transition-all placeholder:text-gray-400">
                    <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-md flex items-center justify-center transition-all duration-200 active:scale-95 shadow-xs cursor-pointer" aria-label="Tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-6">
                <a href="#" class="flex items-center gap-2 text-gray-600 hover:text-[#ea384c] transition-colors group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="text-sm font-semibold">Yêu thích</span>
                </a>
                <a href="{{ route('cart') }}" class="flex items-center gap-2 text-gray-600 hover:text-[#ea384c] transition-colors relative group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="cart-badge-count absolute -top-1.5 -right-2.5 min-w-[18px] h-4.5 px-1 bg-[#ea384c] text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-xs">0</span>
                    <span class="text-sm font-semibold">Giỏ hàng</span>
                </a>
                <div class="pl-4 border-l border-gray-200">
                    @auth
                        <div class="relative group" style="position: relative; z-index: 1000;">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors">
                                <img src="{{ auth()->user()->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=100&q=80' }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                <div class="text-xs">
                                    <span class="text-gray-400 block">Xin chào,</span>
                                    <span class="font-bold text-gray-900">{{ auth()->user()->username ?? auth()->user()->name }}</span>
                                </div>
                            </a>
                            <div class="absolute right-0 top-full pt-1.5 w-48 hidden group-hover:block transition-all" style="position: absolute; z-index: 99999;">
                                <div class="absolute -top-4 left-0 right-0 h-6"></div>
                                <div class="bg-white rounded-xl shadow-2xl border border-gray-100 py-1.5 overflow-hidden">
                                    <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-700 hover:bg-rose-50 hover:text-[#ea384c] font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Hồ sơ cá nhân
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
                        <a href="{{ route('login') }}" class="flex items-center gap-2 text-gray-700 hover:text-[#ea384c] transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="text-xs"><span class="text-gray-400 block">Xin chào,</span><span class="font-bold text-gray-900">Đăng nhập</span></div>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Nav Bar -->
        <div class="border-t border-gray-100 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-1.5 py-1">
                <a href="/" class="px-4 py-2 text-xs font-bold text-[#ea384c] rounded-lg hover:bg-rose-50/60 transition-all">Trang chủ</a>
                <a href="/#flash-sale" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-[#ea384c] rounded-lg hover:bg-rose-50/60 transition-all">Flash Sale</a>
                <a href="/#recommended" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-[#ea384c] rounded-lg hover:bg-rose-50/60 transition-all">Gợi ý cho bạn</a>
                <a href="#" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-[#ea384c] rounded-lg hover:bg-rose-50/60 transition-all">Bán chạy</a>
                <a href="#" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-[#ea384c] rounded-lg hover:bg-rose-50/60 transition-all">Thương hiệu</a>
                <a href="#" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-[#ea384c] rounded-lg hover:bg-rose-50/60 transition-all">Ưu đãi thành viên</a>
            </div>
        </div>
    </header>

    <!-- ==================== MOBILE TOP HEADER ==================== -->
    <header class="lg:hidden bg-white sticky top-0 z-[100] px-4 py-3 border-b border-gray-100 shadow-xs">
        <div class="flex items-center justify-between gap-3">
            <a href="/" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-rose-50 hover:text-[#ea384c] transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="/" class="flex items-center gap-1.5">
                <div class="w-7 h-7 rounded-lg bg-[#ea384c] flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <span class="text-lg font-bold text-gray-900">Shop<span class="text-[#ea384c]">Mart</span></span>
            </a>
            <div class="flex items-center gap-2">
                <a href="/" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </a>
                <a href="{{ route('cart') }}" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="cart-badge-count absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 bg-[#ea384c] text-white text-[9px] font-bold rounded-full flex items-center justify-center">0</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 lg:py-4">

        <!-- ==================== BREADCRUMB (Desktop Only) ==================== -->
        <nav class="hidden lg:flex items-center gap-1.5 pb-3 text-xs text-gray-400" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#ea384c] transition-colors">Trang chủ</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if($product->category)
            <a href="/#recommended" class="hover:text-[#ea384c] transition-colors">{{ $product->category->name }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            @if($product->brand)
            <span class="hover:text-[#ea384c] transition-colors cursor-default">{{ $product->brand }}</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-gray-600 font-semibold truncate max-w-md">{{ $product->name }}</span>
        </nav>

        <!-- ==================== PRODUCT HERO SECTION (2 COLUMNS EXPANDED) ==================== -->
        <section class="lg:grid lg:grid-cols-12 lg:gap-7 items-start">

            <!-- ========== COL 1: LARGE IMAGE GALLERY (Desktop: col 1-7) ========== -->
            <div class="lg:col-span-7">
                <!-- Desktop Gallery -->
                <div class="hidden lg:flex gap-4 sticky top-28">
                    <!-- Vertical Thumbnails -->
                    <div class="flex flex-col gap-2.5 shrink-0 max-h-[580px] overflow-y-auto no-scrollbar py-1" id="pd-thumbs-desktop">
                        @foreach($images as $idx => $img)
                        <button data-pd-thumb="{{ $idx }}" data-full-img="{{ $img }}" class="w-18 h-18 rounded-xl border-2 {{ $idx === 0 ? 'border-[#ea384c] ring-2 ring-rose-400/30' : 'border-gray-200 hover:border-gray-300' }} bg-white p-1.5 overflow-hidden transition-all duration-200 cursor-pointer shadow-2xs group shrink-0">
                            <img src="{{ $img }}" alt="{{ $product->name }} {{ $idx + 1 }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                        </button>
                        @endforeach
                    </div>

                    <!-- Main Large Image Stage -->
                    <div class="flex-1 bg-white rounded-2xl border border-gray-100/90 shadow-sm relative overflow-hidden group flex flex-col justify-center items-center min-h-[520px] lg:min-h-[580px] p-6 lg:p-8 select-none" id="pd-main-image-container">
                        <!-- Subtle ambient radial glow behind the product -->
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(244,63,94,0.025),transparent_70%)] pointer-events-none"></div>

                        <!-- Desktop Prev/Next Navigation Arrows -->
                        @if($imageCount > 1)
                        <button id="pd-desktop-prev" class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white text-gray-700 hover:text-[#ea384c] shadow-md hover:shadow-xl border border-gray-100/90 flex items-center justify-center transition-all z-30 cursor-pointer active:scale-95 group/prev" aria-label="Ảnh trước" title="Ảnh trước">
                            <svg class="w-5 h-5 group-hover/prev:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button id="pd-desktop-next" class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/90 hover:bg-white text-gray-700 hover:text-[#ea384c] shadow-md hover:shadow-xl border border-gray-100/90 flex items-center justify-center transition-all z-30 cursor-pointer active:scale-95 group/next" aria-label="Ảnh kế tiếp" title="Ảnh kế tiếp">
                            <svg class="w-5 h-5 group-hover/next:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @endif

                        <img id="pd-main-image" src="{{ $images[0] ?? $product->main_image_url }}" alt="{{ $product->name }}" class="max-h-[460px] lg:max-h-[500px] w-auto max-w-full object-contain relative z-10 transition-transform duration-300 group-hover:scale-105">

                        <!-- Zoom / inspect tip -->
                        <div class="absolute bottom-4 left-5 text-xs text-gray-400 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            <span>Rê chuột để xem chi tiết</span>
                        </div>

                        <!-- Image Counter Badge -->
                        <div id="pd-main-counter" class="absolute bottom-4 right-5 bg-black/60 text-white text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-md z-20 shadow-xs">
                            1 / {{ $imageCount }}
                        </div>
                    </div>
                </div>

                <!-- Mobile Gallery (Swipeable) -->
                <div class="lg:hidden relative bg-white -mx-4 overflow-hidden select-none" id="pd-mobile-gallery">
                    <div class="flex w-full transition-transform duration-300 ease-out" id="pd-mobile-slides">
                        @foreach($images as $idx => $img)
                        <div class="min-w-full w-full shrink-0 aspect-square max-h-[380px] sm:max-h-[440px] flex items-center justify-center p-4 sm:p-6 bg-white overflow-hidden">
                            <img src="{{ $img }}" alt="{{ $product->name }} {{ $idx + 1 }}" class="max-h-full max-w-full w-auto h-auto object-contain">
                        </div>
                        @endforeach
                    </div>
                    <!-- Prev/Next arrows -->
                    @if($imageCount > 1)
                    <button id="pd-mobile-prev" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow-md border border-gray-100 flex items-center justify-center text-gray-700 hover:text-[#ea384c] z-10 transition-colors cursor-pointer active:scale-95" aria-label="Ảnh trước">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button id="pd-mobile-next" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow-md border border-gray-100 flex items-center justify-center text-gray-700 hover:text-[#ea384c] z-10 transition-colors cursor-pointer active:scale-95" aria-label="Ảnh kế tiếp">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    @endif
                    <!-- Slide counter -->
                    <div class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full backdrop-blur-sm z-10" id="pd-mobile-counter">1 / {{ $imageCount }}</div>
                    <!-- Mobile Thumbnail Strip -->
                    @if($imageCount > 1)
                    <div class="flex gap-2 px-4 py-3 bg-white border-t border-gray-100 overflow-x-auto no-scrollbar" id="pd-mobile-thumbstrip">
                        @foreach($images as $idx => $img)
                        <button data-pd-mobile-thumb="{{ $idx }}" class="w-12 h-12 rounded-lg border-2 {{ $idx === 0 ? 'border-[#ea384c] ring-1 ring-rose-400/30' : 'border-gray-200' }} bg-white p-0.5 overflow-hidden shrink-0 cursor-pointer transition-all">
                            <img src="{{ $img }}" alt="" class="w-full h-full object-contain">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- ========== COL 2: PRODUCT PURCHASE INFO (Desktop: col 8-12) ========== -->
            <div class="lg:col-span-5 mt-5 lg:mt-0">
                <div class="bg-white rounded-2xl border border-gray-100/90 p-6 lg:p-7 shadow-xs">
                    <!-- Brand & Badges -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $product->brand ?? 'Chính hãng' }}</span>
                            @if($product->category)
                            <span class="text-xs text-gray-300">·</span>
                            <span class="text-xs font-medium text-gray-400">{{ $product->category->name }}</span>
                            @endif
                        </div>
                        @if($product->badge_text)
                        <span class="text-[11px] font-extrabold text-blue-600 bg-blue-50/80 px-2.5 py-0.5 rounded-lg border border-blue-100/60">{{ $product->badge_text }}</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-xl lg:text-2xl font-black text-gray-900 mt-2.5 leading-tight tracking-tight">{{ $product->name }}</h1>

                    <!-- Rating & Social Meta -->
                    <div class="flex items-center gap-3 mt-3 flex-wrap">
                        <div class="flex items-center gap-1">
                            <svg class="w-4.5 h-4.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($product->rating, 1) }}</span>
                            <span class="text-xs text-gray-400">({{ $product->formatted_reviews }} đánh giá)</span>
                        </div>
                        <span class="text-xs text-gray-300">|</span>
                        <span class="text-xs text-gray-500">Đã bán <strong class="text-gray-800">{{ $product->formatted_sold }}</strong></span>
                        @if($product->is_mall)
                        <span class="inline-flex items-center px-2 py-0.5 bg-red-50 text-[#ea384c] text-[10px] font-black rounded uppercase border border-red-100">Mall</span>
                        @endif
                        <div class="ml-auto flex items-center gap-2">
                            <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-rose-50 flex items-center justify-center text-gray-400 hover:text-[#ea384c] transition-colors cursor-pointer" title="Chia sẻ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-gray-50 hover:bg-rose-50 flex items-center justify-center text-gray-400 hover:text-[#ea384c] transition-colors cursor-pointer" title="Yêu thích">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Price Card -->
                    <div class="mt-4 p-4 bg-gradient-to-r from-rose-50/70 to-orange-50/40 rounded-xl border border-rose-100/70">
                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span class="text-3xl lg:text-[32px] font-black text-[#ea384c]">{{ $product->formatted_price }}</span>
                            @if($product->original_price)
                            <span class="text-sm text-gray-400 line-through">{{ $product->formatted_original_price }}</span>
                            @endif
                            @if($product->discount_percent > 0)
                            <span class="px-2 py-0.5 bg-[#ea384c] text-white text-xs font-bold rounded-md shadow-xs">-{{ $product->discount_percent }}%</span>
                            @endif
                        </div>
                        @if($product->original_price && $product->original_price > $product->price)
                        <div class="flex items-center gap-1.5 mt-1.5 text-xs text-gray-600">
                            <span>Tiết kiệm:</span>
                            <span class="text-[#ea384c] font-bold">{{ number_format($product->original_price - $product->price, 0, ',', '.') }}₫</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-1.5 mt-2.5 text-xs text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-lg w-fit border border-emerald-100">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>Giá tốt nhất trong 30 ngày qua</span>
                        </div>
                    </div>

                    <!-- Color Variants -->
                    @if(!empty($colors))
                    <div class="mt-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5">Màu sắc / Phiên bản</h3>
                        <div class="flex flex-wrap gap-2.5" id="pd-color-variants">
                            @foreach($colors as $cIdx => $c)
                            <button data-variant-color="{{ Str::slug($c['label']) }}" class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl border-2 {{ $cIdx === 0 ? 'border-[#ea384c] bg-rose-50/30 ring-2 ring-rose-400/20' : 'border-gray-200 bg-white hover:border-gray-300' }} transition-all cursor-pointer">
                                @if(!empty($c['image']))
                                <div class="w-7 h-7 rounded-lg bg-gray-100 overflow-hidden p-0.5"><img src="{{ $c['image'] }}" alt="{{ $c['label'] }}" class="w-full h-full object-contain"></div>
                                @endif
                                <span class="text-xs font-bold text-gray-800">{{ $c['label'] }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Storage / Option Variants -->
                    @if(!empty($options))
                    <div class="mt-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5">Tùy chọn / Kích cỡ</h3>
                        <div class="flex flex-wrap gap-2.5" id="pd-storage-variants">
                            @foreach($options as $oIdx => $opt)
                            <button data-variant-storage="{{ Str::slug($opt) }}" class="px-4 py-2.5 rounded-xl border-2 {{ $oIdx === 0 ? 'border-[#ea384c] bg-rose-50/30 text-[#ea384c] ring-2 ring-rose-400/20' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300' }} text-xs font-bold transition-all cursor-pointer">{{ $opt }}</button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Quantity -->
                    <div class="mt-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5">Số lượng</h3>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-white">
                                <button id="pd-qty-minus" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-[#ea384c] hover:bg-rose-50 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input id="pd-qty-input" type="number" value="1" min="1" max="{{ max(1, $product->stock) }}" class="w-12 h-10 text-center text-sm font-bold text-gray-900 border-x border-gray-200 focus:outline-hidden [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button id="pd-qty-plus" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-[#ea384c] hover:bg-rose-50 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </button>
                            </div>
                            <span class="text-xs text-gray-400">Còn <strong class="text-gray-700">{{ $product->stock }}</strong> sản phẩm</span>
                        </div>
                    </div>

                    <!-- CTA Buttons (Desktop) -->
                    <div class="hidden lg:flex gap-3 mt-6">
                        <button 
                            data-add-to-cart 
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}" 
                            class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 border-2 border-[#ea384c] text-[#ea384c] font-bold text-sm rounded-xl hover:bg-rose-50 active:scale-[0.98] transition-all cursor-pointer shadow-xs"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Thêm vào giỏ hàng</span>
                        </button>
                        <button 
                            class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 bg-gradient-to-r from-[#ea384c] to-[#ff4757] hover:from-[#d3273b] hover:to-[#ea384c] text-white font-bold text-sm rounded-xl shadow-lg shadow-rose-500/25 active:scale-[0.98] transition-all cursor-pointer"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Mua ngay</span>
                        </button>
                    </div>

                    <!-- Trust Badges -->
                    <div class="grid grid-cols-4 gap-2.5 mt-5 pt-5 border-t border-gray-100">
                        <div class="flex flex-col items-center text-center gap-1">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-600 leading-tight">Chính hãng 100%</span>
                        </div>
                        <div class="flex flex-col items-center text-center gap-1">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-600 leading-tight">Giao nhanh 24h</span>
                        </div>
                        <div class="flex flex-col items-center text-center gap-1">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-600 leading-tight">7 ngày đổi trả</span>
                        </div>
                        <div class="flex flex-col items-center text-center gap-1">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-600 leading-tight">Hỗ trợ 24/7</span>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- ==================== HORIZONTAL ROW: EXTENDED STORE INFO & DEALS (SHOPEE STYLE) ==================== -->
        <section class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- CARD 1: EXTENDED STORE INFO (TAKES 2 COLUMNS) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100/90 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                    
                    <!-- Left Column: Store Avatar, Name, Online Status & Action Buttons (5 cols) -->
                    <div class="md:col-span-5 flex flex-col justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-50 to-rose-100 border border-rose-200/80 flex items-center justify-center text-[#ea384c] font-black text-2xl shrink-0 shadow-2xs">
                                {{ mb_substr($product->store->name ?? 'S', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-base font-extrabold text-gray-900 truncate">{{ $product->store->name ?? 'ShopMart Official Store' }}</span>
                                    @if($product->store?->is_mall)
                                    <span class="px-1.5 py-0.5 bg-red-50 text-[#ea384c] text-[9px] font-black rounded uppercase shrink-0 border border-red-100">Mall</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-xs text-gray-400 font-medium">{{ $product->store->online_status ?? 'Online vài phút trước' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chat & View Shop CTA buttons -->
                        <div class="flex items-center gap-2.5">
                            <a href="#" class="flex-1 flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100/80 text-[#ea384c] text-xs font-bold rounded-xl transition-all shadow-3xs hover:scale-102">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Chat ngay</span>
                            </a>
                            <a href="#" class="flex-1 flex items-center justify-center gap-1 px-3.5 py-2.5 border border-gray-200 hover:border-[#ea384c] hover:text-[#ea384c] text-gray-700 text-xs font-bold rounded-xl transition-all shadow-3xs hover:scale-102">
                                <span>Xem shop</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Rich Store Stats Grid (7 cols, separated by border) -->
                    <div class="md:col-span-7 md:border-l md:border-gray-100 md:pl-5 pt-3 md:pt-0 border-t border-gray-100 md:border-t-0">
                        <div class="grid grid-cols-3 gap-2.5 text-center">
                            <div class="p-2.5 bg-gray-50/80 rounded-xl">
                                <div class="text-sm font-black text-gray-900">{{ number_format($product->store->rating ?? 4.9, 1) }} ★</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Đánh giá shop</div>
                            </div>
                            <div class="p-2.5 bg-gray-50/80 rounded-xl">
                                <div class="text-sm font-black text-gray-900">{{ $product->store->response_rate ?? '99%' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Phản hồi chat</div>
                            </div>
                            <div class="p-2.5 bg-gray-50/80 rounded-xl">
                                <div class="text-sm font-black text-gray-900">{{ $product->store->followers ?? '1.2tr' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Người theo dõi</div>
                            </div>
                            <div class="p-2.5 bg-gray-50/80 rounded-xl">
                                <div class="text-sm font-black text-gray-900">185</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Sản phẩm</div>
                            </div>
                            <div class="p-2.5 bg-gray-50/80 rounded-xl">
                                <div class="text-sm font-black text-emerald-600">Nhanh chóng</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Chuẩn bị hàng</div>
                            </div>
                            <div class="p-2.5 bg-gray-50/80 rounded-xl">
                                <div class="text-sm font-black text-gray-900">3 năm trước</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">Tham gia</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- CARD 2: DEALS & BENEFITS (TAKES 1 COLUMN) -->
            <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100/90 p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-[#ea384c] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900">Ưu đãi & Đặc quyền</h3>
                    </div>

                    <div class="space-y-2.5">
                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-rose-50/40 border border-rose-100/50">
                            <div class="w-6 h-6 rounded-lg bg-[#ea384c] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">%</div>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Giảm thêm 500.000₫</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">Khi thanh toán qua thẻ tín dụng hoặc ví ShopMart Pay.</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-blue-50/40 border border-blue-100/50">
                            <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">0%</div>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Trả góp 0% lãi suất</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">Kỳ hạn linh hoạt 3 - 12 tháng, duyệt hồ sơ nhanh trong 5 phút.</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-emerald-50/40 border border-emerald-100/50">
                            <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">♻</div>
                            <div>
                                <div class="text-xs font-bold text-gray-900">Thu cũ đổi mới trợ giá 2Tr</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">Định giá máy cũ nhanh chóng, hỗ trợ lên đời tiết kiệm nhất.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- ==================== BELOW-FOLD CONTENT (100% DATABASE DRIVEN) ==================== -->
        <section class="mt-7 bg-white rounded-2xl border border-gray-100/90 shadow-xs overflow-hidden">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-100 overflow-x-auto no-scrollbar bg-gray-50/40" id="pd-tab-nav">
                <button data-pd-tab="info" class="px-7 py-4 text-sm font-bold text-[#ea384c] border-b-2 border-[#ea384c] whitespace-nowrap transition-all cursor-pointer">
                    Chi tiết & Giới thiệu
                </button>
                <button data-pd-tab="qa" class="px-7 py-4 text-sm font-semibold text-gray-500 hover:text-[#ea384c] border-b-2 border-transparent whitespace-nowrap transition-all cursor-pointer">
                    Hỏi đáp & Thắc mắc ({{ count($faqs) }})
                </button>
                <button data-pd-tab="policy" class="px-7 py-4 text-sm font-semibold text-gray-500 hover:text-[#ea384c] border-b-2 border-transparent whitespace-nowrap transition-all cursor-pointer">
                    Chính sách & Bảo hành
                </button>
                <button data-pd-tab="reviews" class="px-7 py-4 text-sm font-semibold text-gray-500 hover:text-[#ea384c] border-b-2 border-transparent whitespace-nowrap transition-all cursor-pointer">
                    Đánh giá ({{ $product->formatted_reviews }})
                </button>
                <button data-pd-tab="related" class="px-7 py-4 text-sm font-semibold text-gray-500 hover:text-[#ea384c] border-b-2 border-transparent whitespace-nowrap transition-all cursor-pointer">
                    Sản phẩm tương tự ({{ $relatedProducts->count() }})
                </button>
            </div>

            <!-- Tab Content Panels -->
            <div id="pd-tab-panels">

                <!-- 1. INFO TAB: FULL DESCRIPTION, HIGHLIGHT FEATURES & DATABASE SPECS -->
                <div data-pd-panel="info" class="p-6 lg:p-8">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-start">

                        <!-- Left Col: Description & Highlight Features (col 1-7) -->
                        <div class="lg:col-span-7 space-y-6">
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-1.5 h-5 rounded-full bg-[#ea384c]"></span>
                                    <h3 class="text-base font-extrabold text-gray-900">Mô tả sản phẩm</h3>
                                </div>
                                <div class="text-sm text-gray-600 leading-relaxed space-y-3">
                                    <p class="font-bold text-gray-800 text-base">{{ $product->name }}</p>
                                    <p class="text-justify">{{ $product->description }}</p>
                                </div>
                            </div>

                            <!-- Video / Hero Media Banner from Database -->
                            @if($product->banner_image_url)
                            <div class="rounded-2xl overflow-hidden bg-gradient-to-r from-gray-900 to-gray-800 aspect-video relative group shadow-sm border border-gray-100">
                                <img src="{{ $product->banner_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover opacity-75 group-hover:opacity-65 transition-opacity">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-6">
                                    <span class="px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-md text-[11px] font-bold text-white w-fit mb-2">Video giới thiệu</span>
                                    <h4 class="text-lg lg:text-xl font-black text-white">{{ $product->name }}</h4>
                                    <p class="text-xs text-gray-300 mt-1 line-clamp-2">{{ $product->description }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Highlight Features Cards from Database -->
                            @if(!empty($features))
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="w-1.5 h-5 rounded-full bg-[#ea384c]"></span>
                                    <h3 class="text-base font-extrabold text-gray-900">Đặc điểm nổi bật</h3>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    @foreach($features as $feat)
                                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100 hover:border-rose-200 transition-colors">
                                        <div class="w-9 h-9 rounded-xl bg-white shadow-2xs border border-gray-100 flex items-center justify-center text-[#ea384c] shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-900">{{ $feat['title'] }}</h4>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $feat['desc'] }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Col: Technical Specifications from Database (col 8-12) -->
                        <div class="lg:col-span-5 mt-8 lg:mt-0 space-y-6">
                            <!-- Specs Table Card -->
                            <div class="p-5 rounded-2xl bg-gray-50/60 border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-5 rounded-full bg-blue-600"></span>
                                        <h3 class="text-base font-extrabold text-gray-900">Thông số kỹ thuật</h3>
                                    </div>
                                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Chi tiết</span>
                                </div>

                                @if(!empty($specs))
                                <div class="divide-y divide-gray-200/60 text-xs">
                                    @foreach($specs as $sKey => $sVal)
                                    <div class="py-2.5 flex items-start justify-between gap-4">
                                        <span class="text-gray-500 font-medium w-36 shrink-0">{{ $sKey }}</span>
                                        <span class="font-bold text-gray-900 text-right">{{ $sVal }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-xs text-gray-400 py-3">Thông số kỹ thuật đang được cập nhật thêm.</p>
                                @endif
                            </div>

                            <!-- Warranty Card from Database -->
                            <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <h4 class="text-sm font-bold text-emerald-900">Thông tin bảo hành</h4>
                                </div>
                                <p class="text-xs text-emerald-800 leading-relaxed">
                                    {{ $product->warranty_info ?? 'Bảo hành chính hãng 12 tháng tại các trung tâm dịch vụ ủy quyền trên toàn quốc.' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 2. QA TAB: DATABASE-DRIVEN FAQS -->
                <div data-pd-panel="qa" class="p-6 lg:p-8 hidden">
                    <div class="max-w-4xl mx-auto space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-base font-extrabold text-gray-900">Câu hỏi thường gặp về {{ $product->name }}</h3>
                                <p class="text-xs text-gray-400 mt-1">Các giải đáp nhanh từ người bán và cộng đồng ShopMart</p>
                            </div>
                            <button class="px-4 py-2 bg-[#ea384c] text-white text-xs font-bold rounded-xl hover:bg-[#d3273b] transition-colors cursor-pointer">
                                Đặt câu hỏi mới
                            </button>
                        </div>

                        @if(!empty($faqs))
                        <div class="space-y-4">
                            @foreach($faqs as $faqIdx => $faq)
                            <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-100 hover:border-rose-100 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 rounded-full bg-rose-100 text-[#ea384c] font-black text-xs flex items-center justify-center shrink-0 mt-0.5">Q</div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-gray-900">{{ $faq['question'] }}</h4>
                                        <div class="flex items-start gap-3 mt-2.5 pt-2.5 border-t border-gray-200/50">
                                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">A</div>
                                            <div class="text-xs text-gray-700 leading-relaxed">
                                                <p>{{ $faq['answer'] }}</p>
                                                <div class="flex items-center gap-2 mt-1.5 text-[10px] text-gray-400">
                                                    <span class="font-bold text-gray-600">{{ $product->store->name ?? 'ShopMart' }}</span>
                                                    <span>· Phản hồi chính thức</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-10 text-gray-400">
                            <p class="text-xs">Chưa có câu hỏi nào. Hãy là người đầu tiên đặt câu hỏi!</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- 3. POLICY TAB: DATABASE-DRIVEN POLICY & WARRANTY -->
                <div data-pd-panel="policy" class="p-6 lg:p-8 hidden">
                    <div class="max-w-4xl mx-auto space-y-6">
                        <div class="p-6 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-gray-900">Chính sách đổi trả & Hoàn tiền</h3>
                                    <p class="text-xs text-gray-400">Áp dụng cho tất cả đơn hàng tại ShopMart</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                {{ $product->policy ?? 'Đổi trả miễn phí trong 7 ngày nếu không hài lòng hoặc sản phẩm có lỗi từ nhà sản xuất. Hỗ trợ kiểm tra hàng đồng kiểm cùng shipper trước khi nhận.' }}
                            </p>
                        </div>

                        <div class="p-6 rounded-2xl bg-blue-50/50 border border-blue-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-gray-900">Chính sách bảo hành sản phẩm</h3>
                                    <p class="text-xs text-gray-400">Bảo hành chính hãng uy tín</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                {{ $product->warranty_info ?? 'Bảo hành chính hãng 12 tháng. Hỗ trợ kỹ thuật và kiểm tra miễn phí trong suốt quá trình sử dụng.' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <div class="p-4 rounded-xl border border-gray-100 bg-white">
                                <div class="text-xs font-bold text-gray-900 mb-1">100% Hàng chính hãng</div>
                                <div class="text-[11px] text-gray-500">Đền tiền 200% nếu phát hiện hàng giả, hàng nhái.</div>
                            </div>
                            <div class="p-4 rounded-xl border border-gray-100 bg-white">
                                <div class="text-xs font-bold text-gray-900 mb-1">Đồng kiểm khi nhận</div>
                                <div class="text-[11px] text-gray-500">Được mở bọc kiểm tra sản phẩm cùng shipper.</div>
                            </div>
                            <div class="p-4 rounded-xl border border-gray-100 bg-white">
                                <div class="text-xs font-bold text-gray-900 mb-1">Hỗ trợ thu hồi tại nhà</div>
                                <div class="text-[11px] text-gray-500">ShopMart cử nhân viên đến tận nơi nhận hàng đổi trả.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. REVIEWS TAB -->
                <div data-pd-panel="reviews" class="p-6 lg:p-8 hidden">
                    <div class="max-w-4xl mx-auto space-y-6">
                        <!-- Rating Summary Header -->
                        <div class="p-6 rounded-2xl bg-gradient-to-r from-rose-50/40 via-amber-50/30 to-transparent border border-rose-100/50 flex flex-col md:flex-row items-center gap-8">
                            <div class="text-center md:text-left shrink-0">
                                <div class="text-4xl font-black text-gray-900">{{ number_format($product->rating, 1) }}<span class="text-xl text-gray-400 font-medium"> / 5</span></div>
                                <div class="flex items-center justify-center md:justify-start gap-1 my-1.5 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                                <div class="text-xs text-gray-500 font-semibold">{{ $product->formatted_reviews }} lượt đánh giá xác thực</div>
                            </div>
                            <!-- Star filter tags -->
                            <div class="flex flex-wrap gap-2">
                                <button class="px-4 py-1.5 rounded-xl border border-[#ea384c] bg-rose-50 text-[#ea384c] text-xs font-bold">Tất cả</button>
                                <button class="px-4 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:border-gray-300">5 Sao (480)</button>
                                <button class="px-4 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:border-gray-300">4 Sao (45)</button>
                                <button class="px-4 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-xs font-semibold hover:border-gray-300">Có hình ảnh (182)</button>
                            </div>
                        </div>

                        <!-- Sample Customer Reviews -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl border border-gray-100 bg-white space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-rose-100 text-[#ea384c] font-bold text-xs flex items-center justify-center">N</div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-900">Nguyễn Văn Anh</div>
                                            <div class="flex items-center gap-1 text-amber-400 text-[10px]">★★★★★</div>
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-gray-400">2 ngày trước</span>
                                </div>
                                <p class="text-xs text-gray-700">Sản phẩm tuyệt vời, đúng mô tả, đóng gói rất kỹ càng. Giao hàng nhanh chỉ 1 ngày là tới nơi. Rất hài lòng!</p>
                            </div>
                            <div class="p-4 rounded-xl border border-gray-100 bg-white space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold text-xs flex items-center justify-center">T</div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-900">Trần Minh Hoàng</div>
                                            <div class="flex items-center gap-1 text-amber-400 text-[10px]">★★★★★</div>
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-gray-400">1 tuần trước</span>
                                </div>
                                <p class="text-xs text-gray-700">Chất lượng hoàn thiện rất tốt, dùng mượt mà, nhân viên shop tư vấn nhiệt tình. 10/10 sẽ ủng hộ tiếp!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. RELATED PRODUCTS TAB -->
                <div data-pd-panel="related" class="p-6 lg:p-8 hidden">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($relatedProducts as $rel)
                        <div class="bg-white rounded-2xl border border-gray-100/90 p-3.5 hover:shadow-md hover:border-rose-200 transition-all group flex flex-col justify-between">
                            <a href="{{ route('product.detail', $rel->slug) }}" class="block">
                                <div class="w-full aspect-square bg-white rounded-xl overflow-hidden mb-3 flex items-center justify-center p-3 border border-gray-50">
                                    <img src="{{ $rel->main_image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                                </div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $rel->brand ?? 'Chính hãng' }}</span>
                                <h4 class="text-xs font-bold text-gray-800 line-clamp-2 min-h-[32px] mt-0.5 group-hover:text-[#ea384c] transition-colors">{{ $rel->name }}</h4>
                                <div class="flex items-baseline gap-1.5 mt-2">
                                    <span class="text-sm font-black text-[#ea384c]">{{ $rel->formatted_price }}</span>
                                    @if($rel->original_price)
                                    <span class="text-[10px] text-gray-400 line-through">{{ $rel->formatted_original_price }}</span>
                                    @endif
                                </div>
                            </a>
                            <div class="mt-3 pt-2.5 border-t border-gray-50 flex gap-2">
                                <a href="{{ route('product.detail', $rel->slug) }}" class="w-full text-center py-2 bg-rose-50 hover:bg-[#ea384c] text-[#ea384c] hover:text-white rounded-xl text-xs font-bold transition-colors">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== RECOMMENDED PRODUCTS SECTION ==================== -->
        <section class="mt-10" id="pd-recommendations">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-rose-500/10 to-orange-500/10 text-[#ea384c] rounded-full text-xs font-black uppercase tracking-wider border border-rose-200/60 mb-2">
                        <svg class="w-3.5 h-3.5 text-[#ea384c]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.11 2.014-1.127 3.32-.017.92.366 1.763.784 2.378.077.113.155.22.233.32-.15-.02-.303-.053-.46-.102C8.36 8.59 7.42 7.78 7.07 7.054a1 1 0 00-1.748.97c.5 1.037 1.487 2.054 2.518 2.56.096.047.195.09.297.13-.243.076-.493.125-.747.146-.66.055-1.332-.142-1.848-.527a1 1 0 00-1.282 1.528c.84.707 1.93 1.01 3.03.905.155-.015.31-.044.464-.085-.316.326-.69.6-1.12.805-1.077.51-2.28.536-3.23.07a1 1 0 00-.895 1.79c1.478.724 3.376.677 4.986-.086.58-.275 1.1-.645 1.547-1.08.31.398.69.742 1.13.993 1.343.766 2.946.726 4.316-.105a1 1 0 00-1.042-1.71c-.854.518-1.867.54-2.735.045-.486-.277-.872-.693-1.124-1.187.35-.187.67-.425.952-.705.945-.94 1.442-2.213 1.447-3.528.005-1.572-.73-3.053-1.428-4.14a17.47 17.47 0 00-1.354-1.78z" clip-rule="evenodd"/></svg>
                        <span>GỢI Ý DÀNH RIÊNG CHO BẠN</span>
                    </div>
                    <h2 class="text-xl lg:text-2xl font-black text-gray-900 tracking-tight">Sản phẩm tương tự & Được yêu thích</h2>
                    <p class="text-xs text-gray-400 mt-1">Dựa trên sản phẩm bạn đang xem và các sản phẩm bán chạy nhất cùng danh mục</p>
                </div>

                <!-- Category quick filter badges -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                    <button class="px-4 py-2 rounded-xl bg-[#ea384c] text-white text-xs font-bold shadow-xs whitespace-nowrap">
                        Tất cả gợi ý ({{ $recommendedProducts->count() }})
                    </button>
                    @if($product->category)
                    <button class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:border-[#ea384c] text-gray-700 text-xs font-bold hover:text-[#ea384c] transition-colors whitespace-nowrap">
                        {{ $product->category->name }}
                    </button>
                    @endif
                    @if($product->brand)
                    <button class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:border-[#ea384c] text-gray-700 text-xs font-bold hover:text-[#ea384c] transition-colors whitespace-nowrap">
                        Cùng hãng {{ $product->brand }}
                    </button>
                    @endif
                </div>
            </div>

            <!-- Products Grid: 6 columns on desktop, 4 on tablet, 2 on mobile -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 sm:gap-4">
                @foreach($recommendedProducts as $rec)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-xl hover:border-rose-200 transition-all duration-300 flex flex-col justify-between group overflow-hidden relative">
                    <!-- Top Full Bleed Image Container -->
                    <div class="relative w-full aspect-square overflow-hidden bg-gray-100">
                        <!-- Badges -->
                        <div class="absolute top-2.5 left-2.5 z-10 flex flex-col gap-1 pointer-events-none">
                            @if($rec->discount_percent > 0)
                            <span class="px-2 py-0.5 bg-[#ea384c] text-white text-[10px] font-extrabold rounded-md shadow-xs">
                                -{{ $rec->discount_percent }}%
                            </span>
                            @endif
                            @if($rec->is_mall)
                            <span class="px-1.5 py-0.5 bg-white/95 text-[#ea384c] text-[9px] font-black rounded uppercase border border-red-100 shadow-2xs">
                                MALL
                            </span>
                            @endif
                        </div>

                        <!-- Wishlist button -->
                        <button data-toggle-wishlist class="absolute top-2.5 right-2.5 z-10 w-7 h-7 rounded-full bg-white/85 hover:bg-white text-gray-400 hover:text-rose-500 flex items-center justify-center shadow-xs backdrop-blur-xs transition-all active:scale-90 cursor-pointer" aria-label="Yêu thích">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>

                        <a href="{{ route('product.detail', $rec->slug) }}" class="block w-full h-full">
                            <img 
                                src="{{ $rec->main_image_url }}" 
                                alt="{{ $rec->name }}" 
                                class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500 ease-out"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/[0.03] transition-colors pointer-events-none"></div>
                        </a>
                    </div>

                    <!-- Bottom Content Info -->
                    <div class="p-3 sm:p-3.5 flex flex-col justify-between flex-1 gap-2">
                        <a href="{{ route('product.detail', $rec->slug) }}" class="block">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block truncate mb-0.5">{{ $rec->brand ?? 'Chính hãng' }}</span>
                            <h3 class="text-xs font-bold text-gray-800 line-clamp-2 leading-snug group-hover:text-[#ea384c] transition-colors min-h-[32px]">
                                {{ $rec->name }}
                            </h3>

                            <!-- Price -->
                            <div class="flex items-baseline gap-1.5 mt-1.5">
                                <span class="text-sm font-black text-[#ea384c]">{{ $rec->formatted_price }}</span>
                                @if($rec->original_price)
                                <span class="text-[10px] text-gray-400 line-through">{{ $rec->formatted_original_price }}</span>
                                @endif
                            </div>

                            <!-- Rating & Sold count -->
                            <div class="flex items-center gap-1 mt-1.5 text-[11px] text-gray-500">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="font-bold text-gray-700">{{ number_format($rec->rating, 1) }}</span>
                                <span class="text-gray-300">·</span>
                                <span class="text-gray-400 text-[10px]">Đã bán {{ $rec->formatted_sold }}</span>
                            </div>
                        </a>

                        <!-- Dual Action Buttons: Thêm giỏ & Mua ngay -->
                        <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-gray-100 mt-1">
                            <button 
                                data-add-to-cart 
                                data-product-id="{{ $rec->id }}"
                                data-product-name="{{ $rec->name }}"
                                class="py-1.5 px-1 bg-rose-50 hover:bg-[#ea384c] text-[#ea384c] hover:text-white rounded-xl text-[11px] font-bold transition-all flex items-center justify-center gap-1 active:scale-95 cursor-pointer"
                                aria-label="Thêm {{ $rec->name }} vào giỏ"
                                title="Thêm vào giỏ"
                            >
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span class="truncate">Thêm</span>
                            </button>
                            <a 
                                href="{{ route('product.detail', $rec->slug) }}"
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

            <!-- View More Button -->
            <div class="text-center mt-8">
                <a href="/" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white border-2 border-gray-200 hover:border-[#ea384c] hover:text-[#ea384c] text-gray-700 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all hover:shadow-md active:scale-98">
                    <span>Xem thêm các sản phẩm khác trên ShopMart</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </section>

    </main>

    <!-- ==================== DESKTOP FOOTER ==================== -->
    <footer class="bg-white border-t border-gray-200 mt-14 hidden md:block text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div><h4 class="font-bold text-gray-900 text-sm mb-3">Chăm sóc khách hàng</h4><ul class="space-y-2"><li><a href="#" class="hover:text-[#ea384c] transition-colors">Trung tâm trợ giúp</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">ShopMart Blog</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Hướng dẫn mua hàng</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Chính sách vận chuyển</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Trả hàng & Hoàn tiền</a></li></ul></div>
                <div><h4 class="font-bold text-gray-900 text-sm mb-3">Về ShopMart</h4><ul class="space-y-2"><li><a href="#" class="hover:text-[#ea384c] transition-colors">Giới thiệu về chúng tôi</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Tuyển dụng</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Điều khoản dịch vụ</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Chính sách bảo mật</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Kênh Người Bán</a></li></ul></div>
                <div><h4 class="font-bold text-gray-900 text-sm mb-3">Thanh toán & Vận chuyển</h4><p class="mb-3 text-[11px] text-gray-400 leading-relaxed">Hỗ trợ các phương thức thanh toán an toàn hàng đầu.</p><div class="flex flex-wrap gap-2 text-gray-700"><span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">VISA</span><span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">MasterCard</span><span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">MoMo</span><span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">ZaloPay</span><span class="px-2 py-1 bg-gray-100 rounded font-semibold text-[10px]">COD</span></div></div>
                <div><h4 class="font-bold text-gray-900 text-sm mb-3">Theo dõi chúng tôi</h4><ul class="space-y-2"><li><a href="#" class="hover:text-[#ea384c] transition-colors">Facebook</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">Instagram</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">TikTok</a></li><li><a href="#" class="hover:text-[#ea384c] transition-colors">YouTube</a></li></ul></div>
                <div><h4 class="font-bold text-gray-900 text-sm mb-3">Tải ứng dụng ShopMart</h4><p class="text-[11px] text-gray-400 mb-3">Quét mã QR để tải ngay ứng dụng ShopMart trên iOS & Android.</p></div>
            </div>
            <div class="border-t border-gray-100 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-gray-400">
                <p>© 2026 ShopMart Inc. Tất cả quyền được bảo lưu.</p>
                <div class="flex gap-4"><a href="#" class="hover:underline">Chính sách bảo mật</a><a href="#" class="hover:underline">Quy chế hoạt động</a><a href="#" class="hover:underline">Giải quyết khiếu nại</a></div>
            </div>
        </div>
    </footer>

    <!-- ==================== MOBILE FIXED BOTTOM CTA BAR ==================== -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 px-4 py-3 flex items-center gap-3 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]" id="pd-mobile-cta">
        <button 
            data-add-to-cart 
            data-product-id="{{ $product->id }}"
            data-product-name="{{ $product->name }}" 
            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-2 border-[#ea384c] text-[#ea384c] font-bold text-sm rounded-xl active:scale-[0.98] transition-all cursor-pointer"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span>Thêm giỏ</span>
        </button>
        <button class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-[#ea384c] hover:bg-[#d3273b] text-white font-bold text-sm rounded-xl shadow-lg shadow-rose-500/20 active:scale-[0.98] transition-all cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Mua ngay</span>
        </button>
    </div>

</body>
</html>
