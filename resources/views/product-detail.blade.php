@php
$images = collect([$product->main_image_url])
    ->merge($product->images->pluck('url'))
    ->filter()
    ->unique()
    ->values()
    ->all();
$imageCount = count($images);
$variants = $product->variants ?? [];
$colors = $variants['colors'] ?? [];
$options = $variants['options'] ?? [];
$specs = $product->specs ?? [];
$features = $product->features ?? [];
$faqs = $product->faqs ?? [];

$allReviews = ($product->reviews ?? collect())->filter(fn($r) => $r->status === 'approved');
$reviewsCount = $allReviews->count();
$ratingCounts = [
    5 => $allReviews->where('rating', 5)->count(),
    4 => $allReviews->where('rating', 4)->count(),
    3 => $allReviews->where('rating', 3)->count(),
    2 => $allReviews->where('rating', 2)->count(),
    1 => $allReviews->where('rating', 1)->count(),
];
$withCommentCount = $allReviews->filter(fn($r) => !empty(trim((string)$r->comment)))->count();
$withMediaCount = $allReviews->filter(fn($r) => !empty($r->images) && is_array($r->images) && count($r->images) > 0)->count();

$initialStock = $product->stock;
if ($product->productVariants && $product->productVariants->isNotEmpty()) {
$firstColor = !empty($colors) ? ($colors[0]['label'] ?? null) : null;
$firstOption = !empty($options) ? ($options[0] ?? null) : null;
$firstVariant = $product->productVariants->first(function($v) use ($firstColor, $firstOption) {
if ($firstColor && $firstOption) {
return $v->color === $firstColor && $v->option === $firstOption;
} elseif ($firstColor) {
return $v->color === $firstColor;
} elseif ($firstOption) {
return $v->option === $firstOption;
}
return true;
});
if ($firstVariant) {
$initialStock = $firstVariant->stock;
}
}
@endphp
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="project-assets-base" content="{{ project_asset_base_url() }}">
    <meta name="auth-check" content="{{ auth()->check() ? '1' : '0' }}">
    <title>{{ $product->name }} - ShopMart</title>
    <meta name="description"
        content="Mua {{ $product->name }} chính hãng giá tốt nhất tại ShopMart. Miễn phí vận chuyển, cam kết 100% chính hãng, đổi trả trong 7 ngày.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://bthtgakccwqnjexvwlii.supabase.co" crossorigin>
    <link rel="dns-prefetch" href="https://bthtgakccwqnjexvwlii.supabase.co">
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link rel="dns-prefetch" href="https://images.unsplash.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-slate-800 font-sans antialiased selection:bg-rose-500 selection:text-white pb-20 lg:pb-0">

    <!-- ==================== DESKTOP TOP HEADER ==================== -->
    <header class="site-header-desktop">
        <div class="pd-desktop-header-inner">
            <a href="/" class="flex items-center gap-2.5 shrink-0 group">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary to-rose-400 flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <span
                    class="text-2xl font-black tracking-tight text-gray-900 group-hover:text-primary transition-colors">
                    Shop<span class="text-primary">Mart</span>
                </span>
            </a>

            <div class="flex-1 max-w-2xl">
                <form action="{{ route('catalog.search') }}" method="GET" class="relative flex items-center">
                    <input type="text" name="q" value="{{ request('q', request('search')) }}"
                        placeholder="Tìm kiếm sản phẩm, thương hiệu, danh mục..." class="pd-search-input">
                    <button type="submit" class="pd-search-btn" aria-label="Tìm kiếm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ route('cart') }}"
                    class="flex items-center gap-2 text-gray-600 hover:text-primary transition-colors relative group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span
                        class="cart-badge-count absolute -top-1.5 -right-2.5 min-w-[18px] h-4.5 px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-xs">0</span>
                    <span class="text-sm font-semibold">Giỏ hàng</span>
                </a>
                <div class="pl-4 border-l border-gray-200">
                    <x-header-user-menu />
                </div>
            </div>
        </div>

        <!-- Nav Bar -->
        <div class="border-t border-gray-100 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-1.5 py-1">
                <a href="/"
                    class="px-4 py-2 text-xs font-bold text-primary rounded-lg hover:bg-rose-50/60 transition-all">Trang
                    chủ</a>
                <a href="/#flash-sale"
                    class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-primary rounded-lg hover:bg-rose-50/60 transition-all">Flash
                    Sale</a>
                <a href="/#recommended"
                    class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-primary rounded-lg hover:bg-rose-50/60 transition-all">Gợi
                    ý cho bạn</a>
                <a href="#"
                    class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-primary rounded-lg hover:bg-rose-50/60 transition-all">Bán
                    chạy</a>
                <a href="#"
                    class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-primary rounded-lg hover:bg-rose-50/60 transition-all">Thương
                    hiệu</a>
                <a href="#"
                    class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-primary rounded-lg hover:bg-rose-50/60 transition-all">Ưu
                    đãi thành viên</a>
            </div>
        </div>
    </header>

    <!-- ==================== MOBILE TOP HEADER ==================== -->
    <header class="site-header-mobile">
        <div class="flex items-center justify-between gap-3">
            <a href="/"
                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-rose-50 hover:text-primary transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <a href="/" class="flex items-center gap-1.5">
                <div class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900">Shop<span class="text-primary">Mart</span></span>
            </a>
            <div class="flex items-center gap-2">
                <a href="/" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </a>
                <a href="{{ route('cart') }}"
                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span
                        class="cart-badge-count absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center">0</span>
                </a>
            </div>
        </div>
    </header>

    <main class="pd-container" data-product-id="{{ $product->id }}">

        <!-- ==================== BREADCRUMB (Desktop Only) ==================== -->
        <nav class="pd-breadcrumb" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition-colors">Trang chủ</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            @if($product->category)
            <a href="{{ route('catalog.category', $product->category->slug) }}"
                class="hover:text-primary transition-colors">{{ $product->category->name }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            @endif
            <span class="text-gray-600 font-semibold truncate max-w-md">{{ $product->name }}</span>
        </nav>


        <!-- ==================== PRODUCT HERO SECTION (2 COLUMNS EXPANDED) ==================== -->
        <section class="lg:grid lg:grid-cols-12 lg:gap-7 items-start">

            <!-- ========== COL 1: LARGE IMAGE GALLERY (Desktop: col 1-7) ========== -->
            <div class="lg:col-span-7">
                <!-- Desktop Gallery: Card with Large Image on Top & Horizontal Thumbnails Below -->
                <div class="pd-gallery-card" id="pd-desktop-gallery">
                    <!-- Main Large Image Stage -->
                    <div class="w-full h-[440px] lg:h-[480px] relative overflow-hidden group flex flex-col justify-center items-center p-6 select-none bg-gray-50/40 rounded-xl"
                        id="pd-main-image-container">
                        <!-- Subtle ambient radial glow behind the product -->
                        <div
                            class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(244,63,94,0.025),transparent_70%)] pointer-events-none">
                        </div>

                        <!-- Desktop Prev/Next Navigation Arrows -->
                        @if($imageCount > 1)
                        <button id="pd-desktop-prev" class="pd-gallery-nav pd-gallery-nav--prev group/prev"
                            aria-label="Ảnh trước" title="Ảnh trước">
                            <svg class="w-5 h-5 group-hover/prev:-translate-x-0.5 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button id="pd-desktop-next" class="pd-gallery-nav pd-gallery-nav--next group/next"
                            aria-label="Ảnh kế tiếp" title="Ảnh kế tiếp">
                            <svg class="w-5 h-5 group-hover/next:translate-x-0.5 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        @endif

                        <img id="pd-main-image" src="{{ $images[0] ?? $product->main_image_url }}"
                            alt="{{ $product->name }}"
                            class="max-h-[390px] lg:max-h-[430px] w-auto max-w-full object-contain relative z-10 transition-transform duration-300 group-hover:scale-105">

                        <!-- Zoom / inspect tip -->
                        <div
                            class="absolute bottom-3 left-4 text-xs text-gray-400 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <span>Rê chuột để xem chi tiết</span>
                        </div>

                        <!-- Image Counter Badge -->
                        <div id="pd-main-counter"
                            class="absolute bottom-3 right-4 bg-black/60 text-white text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-md z-20 shadow-xs">
                            1 / {{ $imageCount }}
                        </div>
                    </div>

                    <!-- Horizontal Thumbnails Strip Underneath (Increased size to fill space) -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3.5 overflow-x-auto no-scrollbar py-1"
                        id="pd-thumbs-desktop">
                        @foreach($images as $idx => $img)
                        <button data-pd-thumb="{{ $idx }}" data-full-img="{{ $img }}"
                            class="w-20 h-20 sm:w-22 sm:h-22 lg:w-24 lg:h-24 rounded-2xl border-2 {{ $idx === 0 ? 'border-primary ring-2 ring-rose-400/30' : 'border-gray-200 hover:border-gray-300' }} bg-white p-2 overflow-hidden transition-all duration-200 cursor-pointer shadow-2xs hover:shadow-xs group shrink-0">
                            <img src="{{ $img }}" alt="{{ $product->name }} {{ $idx + 1 }}"
                                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Mobile Gallery (Swipeable) -->
                <div class="lg:hidden relative bg-white -mx-4 overflow-hidden select-none" id="pd-mobile-gallery">
                    <div class="flex w-full transition-transform duration-300 ease-out" id="pd-mobile-slides">
                        @foreach($images as $idx => $img)
                        <div
                            class="min-w-full w-full shrink-0 aspect-square max-h-[380px] sm:max-h-[440px] flex items-center justify-center p-4 sm:p-6 bg-white overflow-hidden">
                            <img src="{{ $img }}" alt="{{ $product->name }} {{ $idx + 1 }}"
                                class="max-h-full max-w-full w-auto h-auto object-contain">
                        </div>
                        @endforeach
                    </div>
                    <!-- Prev/Next arrows -->
                    @if($imageCount > 1)
                    <button id="pd-mobile-prev" class="pd-thumb-nav pd-thumb-nav--prev" aria-label="Ảnh trước">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="pd-mobile-next" class="pd-thumb-nav pd-thumb-nav--next" aria-label="Ảnh kế tiếp">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    @endif
                    <!-- Slide counter -->
                    <div class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full backdrop-blur-sm z-10"
                        id="pd-mobile-counter">1 / {{ $imageCount }}</div>
                    <!-- Mobile Thumbnail Strip -->
                    @if($imageCount > 1)
                    <div class="flex gap-2 px-4 py-3 bg-white border-t border-gray-100 overflow-x-auto no-scrollbar"
                        id="pd-mobile-thumbstrip">
                        @foreach($images as $idx => $img)
                        <button data-pd-mobile-thumb="{{ $idx }}"
                            class="w-12 h-12 rounded-lg border-2 {{ $idx === 0 ? 'border-primary ring-1 ring-rose-400/30' : 'border-gray-200' }} bg-white p-0.5 overflow-hidden shrink-0 cursor-pointer transition-all">
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
                            <span
                                class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $product->brand ?? 'Chính hãng' }}</span>
                            @if($product->category)
                            <span class="text-xs text-gray-300">·</span>
                            <span class="text-xs font-medium text-gray-400">{{ $product->category->name }}</span>
                            @endif
                        </div>
                        @if($product->badge_text)
                        <span
                            class="text-[11px] font-extrabold text-blue-600 bg-blue-50/80 px-2.5 py-0.5 rounded-lg border border-blue-100/60">{{ $product->badge_text }}</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-xl lg:text-2xl font-black text-gray-900 mt-2.5 leading-tight tracking-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Rating & Social Meta -->
                    <div class="flex items-center gap-3 mt-3 flex-wrap">
                        <button type="button" onclick="document.querySelector('[data-pd-tab=\'reviews\']')?.click(); document.getElementById('pd-tab-nav')?.scrollIntoView({ behavior: 'smooth' });" class="flex items-center gap-1 hover:opacity-80 transition-opacity cursor-pointer">
                            <svg class="w-4.5 h-4.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span
                                class="text-sm font-bold text-gray-900 border-b border-gray-900">{{ number_format($product->rating, 1) }}</span>
                            <span class="text-xs text-rose-600 font-semibold hover:underline">({{ $product->formatted_reviews }} đánh giá)</span>
                        </button>
                        <span class="text-xs text-gray-300">|</span>
                        <span class="text-xs text-gray-500">Đã bán <strong
                                class="text-gray-800">{{ $product->formatted_sold }}</strong></span>
                        @if($product->is_mall)
                        <span
                            class="inline-flex items-center px-2 py-0.5 bg-red-50 text-primary text-[10px] font-black rounded uppercase border border-red-100">Mall</span>
                        @endif
                        <div class="ml-auto flex items-center gap-2">
                            <button id="pd-btn-share"
                                class="w-8 h-8 rounded-full bg-gray-50 hover:bg-rose-50 flex items-center justify-center text-gray-400 hover:text-primary transition-colors cursor-pointer"
                                title="Chia sẻ" data-product-id="{{ $product->id }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Price Card -->
                    <div class="pd-price-card">
                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span class="pd-price-main" id="pd-price-display">{{ $product->formatted_price }}</span>
                            @if($product->effective_original_price)
                            <span class="text-sm text-gray-400 line-through"
                                id="pd-original-price-display">{{ $product->formatted_original_price }}</span>
                            @endif
                            @if($product->effective_discount_percent > 0)
                            <span class="px-2 py-0.5 bg-primary text-white text-xs font-bold rounded-md shadow-xs"
                                id="pd-discount-badge">-{{ $product->effective_discount_percent }}%</span>
                            @endif
                        </div>
                        @if($product->effective_original_price)
                        <div class="flex items-center gap-1.5 mt-1.5 text-xs text-gray-600" id="pd-save-container">
                            <span>Tiết kiệm:</span>
                            <span class="text-primary font-bold"
                                id="pd-save-amount">{{ number_format($product->effective_original_price - $product->effective_price, 0, ',', '.') }}₫</span>
                        </div>
                        @endif
                        <div
                            class="flex items-center gap-1.5 mt-2.5 text-xs text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-lg w-fit border border-emerald-100">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>Giá tốt nhất trong 30 ngày qua</span>
                        </div>
                    </div>

                    <!-- Color Variants -->
                    @if(!empty($colors))
                    <div class="mt-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5">Màu sắc / Phiên bản
                        </h3>
                        <div class="flex flex-wrap gap-2.5" id="pd-color-variants">
                            @foreach($colors as $cIdx => $c)
                            <button type="button" data-variant-btn data-variant-type="color"
                                data-color-name="{{ $c['label'] }}" data-variant-color="{{ Str::slug($c['label']) }}"
                                class="pd-variant-btn flex items-center gap-2.5 px-3.5 py-2 rounded-xl border-2 {{ $cIdx === 0 ? 'is-active active border-primary bg-rose-50/30 ring-2 ring-rose-400/20' : 'border-gray-200 bg-white hover:border-gray-300' }} transition-all cursor-pointer">
                                @if(!empty($c['image']))
                                <div class="w-7 h-7 rounded-lg bg-gray-100 overflow-hidden p-0.5"><img
                                        src="{{ $c['image'] }}" alt="{{ $c['label'] }}"
                                        class="w-full h-full object-contain"></div>
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
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5">Tùy chọn / Kích cỡ
                        </h3>
                        <div class="flex flex-wrap gap-2.5" id="pd-storage-variants">
                            @foreach($options as $oIdx => $opt)
                            <button type="button" data-variant-btn data-variant-type="option"
                                data-option-name="{{ $opt }}" data-variant-storage="{{ Str::slug($opt) }}"
                                data-variant-option="{{ Str::slug($opt) }}"
                                class="pd-variant-btn px-4 py-2.5 rounded-xl border-2 {{ $oIdx === 0 ? 'is-active active border-primary bg-rose-50/30 text-primary ring-2 ring-rose-400/20' : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300' }} text-xs font-bold transition-all cursor-pointer">{{ $opt }}</button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Quantity -->
                    <div class="mt-5">
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-2.5">Số lượng</h3>
                        <div class="flex items-center gap-3">
                            <div class="pd-qty-box">
                                <button id="pd-qty-minus" class="pd-qty-btn" type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                </button>
                                <input id="pd-qty-input" type="number" value="{{ $initialStock > 0 ? 1 : 0 }}"
                                    min="{{ $initialStock > 0 ? 1 : 0 }}" max="{{ max(0, $initialStock) }}"
                                    class="pd-qty-input [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button id="pd-qty-plus" class="pd-qty-btn" type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                            <span id="pd-stock-wrapper" class="text-xs text-gray-400">
                                @if($initialStock > 0)
                                <span id="pd-stock-label">Còn </span><strong id="pd-stock-display"
                                    class="text-gray-700">{{ $initialStock }}</strong><span id="pd-stock-unit"> sản
                                    phẩm</span>
                                @else
                                <span id="pd-stock-label"></span><strong id="pd-stock-display"
                                    class="text-red-600 font-bold">Hết hàng</strong><span id="pd-stock-unit"></span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Variants Data for JavaScript -->
                    <script id="pd-variants-json" type="application/json">
                        {!! $product->productVariants->map(fn ($v) => [
                            'id' => $v->id,
                            'name' => $v->name,
                            'color' => $v->color,
                            'option' => $v->option,
                            'stock' => (int) $v->stock,
                            'price' => $product->effectivePriceForVariant($v),
                            'original_price' => $product->isFlashSaleActive()
                                ? (float) $v->price
                                : (float) $v->original_price,
                            'image_url' => $v->image_url,
                        ])->values()->toJson() !!}
                    </script>

                    <!-- CTA Buttons (Desktop) -->
                    <div class="hidden lg:flex gap-3 mt-6">
                        <button data-add-to-cart data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}" class="pd-btn-add">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Thêm vào giỏ hàng</span>
                        </button>
                        <button data-buy-now data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}" class="pd-btn-buy">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Mua ngay</span>
                        </button>
                    </div>

                    <!-- Trust Badges -->
                    <div class="pd-trust-grid">
                        <div class="pd-trust-item">
                            <div class="pd-trust-icon bg-blue-50 text-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="pd-trust-text">Chính hãng 100%</span>
                        </div>
                        <div class="pd-trust-item">
                            <div class="pd-trust-icon bg-emerald-50 text-emerald-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                            </div>
                            <span class="pd-trust-text">Giao nhanh 24h</span>
                        </div>
                        <div class="pd-trust-item">
                            <div class="pd-trust-icon bg-amber-50 text-amber-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <span class="pd-trust-text">7 ngày đổi trả</span>
                        </div>
                        <div class="pd-trust-item">
                            <div class="pd-trust-icon bg-purple-50 text-purple-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <span class="pd-trust-text">Hỗ trợ 24/7</span>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- ==================== UNIFIED STORE CARD WITH HORIZONTAL DEALS ==================== -->
        <section class="mt-6 bg-white rounded-2xl border border-gray-100/90 p-5 sm:p-6 shadow-xs">
            <!-- Top Row: Store Info & Rich Store Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">

                <!-- Left Column: Store Avatar, Name, Online Status & Action Buttons (5 cols) -->
                <div class="md:col-span-5 flex flex-col justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <a href="{{ $product->store ? route('store.show', $product->store->slug) : route('home') }}"
                            class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-rose-50 to-rose-100 border border-rose-200/80 flex items-center justify-center text-primary font-black text-2xl shrink-0 shadow-2xs overflow-hidden group/store hover:opacity-90 transition-opacity">
                            @if($product->store?->logo_url && !str_contains($product->store->logo_url, 'placeholder'))
                            <img src="{{ $product->store->logo_url }}" alt="{{ $product->store->name }}"
                                class="w-full h-full object-cover">
                            @else
                            {{ mb_substr($product->store->name ?? 'S', 0, 1) }}
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ $product->store ? route('store.show', $product->store->slug) : route('home') }}"
                                    class="text-base font-extrabold text-gray-900 truncate hover:text-primary transition-colors">{{ $product->store->name ?? 'ShopMart Official Store' }}</a>
                                @if($product->store?->is_mall)
                                <span
                                    class="px-1.5 py-0.5 bg-red-50 text-primary text-[9px] font-black rounded uppercase shrink-0 border border-red-100">Mall</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span
                                    class="text-xs text-gray-400 font-medium">{{ $product->store->online_status ?? 'Online vài phút trước' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chat & View Shop CTA buttons -->
                    <div class="flex items-center gap-2.5">
                        @auth
                        <form action="{{ route('chat.start', $product->store->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="pd-store-btn-chat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>Chat ngay</span>
                            </button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="pd-store-btn-chat"><span>Đăng nhập để chat</span></a>
                        @endauth
                        <a href="{{ $product->store ? route('store.show', $product->store->slug) : route('home') }}"
                            class="pd-store-btn-view">
                            <span>Xem shop</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Rich Store Stats Grid (7 cols, separated by border) -->
                <div
                    class="md:col-span-7 md:border-l md:border-gray-100 md:pl-6 pt-3 md:pt-0 border-t border-gray-100 md:border-t-0">
                    <div class="grid grid-cols-3 gap-2.5 text-center">
                        <div class="p-2.5 bg-gray-50/80 rounded-xl">
                            <div class="text-sm font-black text-gray-900 inline-flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span>{{ number_format($product->store->rating ?? 4.9, 1) }}</span>
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Đánh giá shop</div>
                        </div>
                        <div class="p-2.5 bg-gray-50/80 rounded-xl">
                            <div class="text-sm font-black text-gray-900">{{ $product->store->response_rate ?? '99%' }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Phản hồi chat</div>
                        </div>
                        <div class="p-2.5 bg-gray-50/80 rounded-xl">
                            <div class="text-sm font-black text-gray-900">{{ $product->store->followers ?? '1.2tr' }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Người theo dõi</div>
                        </div>
                        <div class="p-2.5 bg-gray-50/80 rounded-xl">
                            <div class="text-sm font-black text-gray-900">
                                {{ ($product->store->products_count ?? $product->store?->products?->count()) ?: 185 }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Sản phẩm</div>
                        </div>
                        <div class="p-2.5 bg-gray-50/80 rounded-xl">
                            <div class="text-sm font-black text-emerald-600">Nhanh chóng</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Chuẩn bị hàng</div>
                        </div>
                        <div class="p-2.5 bg-gray-50/80 rounded-xl">
                            <div class="text-sm font-black text-gray-900">
                                {{ $product->store->created_at ? $product->store->created_at->diffForHumans(null, true) : '3 năm trước' }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Tham gia</div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== BELOW-FOLD CONTENT (100% DATABASE DRIVEN) ==================== -->
        <section class="mt-7 bg-white rounded-2xl border border-gray-100/90 shadow-xs overflow-hidden">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-100 overflow-x-auto no-scrollbar bg-gray-50/40" id="pd-tab-nav">
                <button type="button" data-pd-tab="info" onclick="(window.switchProductTab || function(t){document.querySelectorAll('#pd-tab-nav [data-pd-tab]').forEach(b=>b.classList.toggle('is-active',b.getAttribute('data-pd-tab')===t));document.querySelectorAll('#pd-tab-panels [data-pd-panel]').forEach(p=>p.classList.toggle('hidden',p.getAttribute('data-pd-panel')!==t));})('info')" class="pd-tab-btn is-active">
                    Chi tiết & Giới thiệu
                </button>
                <button type="button" data-pd-tab="qa" onclick="(window.switchProductTab || function(t){document.querySelectorAll('#pd-tab-nav [data-pd-tab]').forEach(b=>b.classList.toggle('is-active',b.getAttribute('data-pd-tab')===t));document.querySelectorAll('#pd-tab-panels [data-pd-panel]').forEach(p=>p.classList.toggle('hidden',p.getAttribute('data-pd-panel')!==t));})('qa')" class="pd-tab-btn">
                    Hỏi đáp & Thắc mắc ({{ $product->questions->count() }})
                </button>
                <button type="button" data-pd-tab="reviews" onclick="(window.switchProductTab || function(t){document.querySelectorAll('#pd-tab-nav [data-pd-tab]').forEach(b=>b.classList.toggle('is-active',b.getAttribute('data-pd-tab')===t));document.querySelectorAll('#pd-tab-panels [data-pd-panel]').forEach(p=>p.classList.toggle('hidden',p.getAttribute('data-pd-panel')!==t));})('reviews')" class="pd-tab-btn">
                    Đánh giá ({{ $product->formatted_reviews }})
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
                                    <span class="w-1.5 h-5 rounded-full bg-primary"></span>
                                    <h3 class="text-base font-extrabold text-gray-900 tracking-tight">Mô tả sản phẩm</h3>
                                </div>
                                <div class="text-sm text-gray-700 leading-relaxed space-y-3">
                                    <p class="font-bold text-gray-900 text-base">{{ $product->name }}</p>
                                    <div class="text-justify whitespace-pre-line break-words text-gray-600 font-normal">
                                        {{ $product->description ?: 'Chưa có mô tả chi tiết cho sản phẩm này.' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Video / Hero Media Banner from Database -->
                            @if($product->banner_image_url)
                            <div
                                class="rounded-2xl overflow-hidden bg-gradient-to-r from-gray-900 to-gray-800 aspect-video relative group shadow-sm border border-gray-100">
                                <img src="{{ $product->banner_image_url }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover opacity-75 group-hover:opacity-65 transition-opacity">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-6">
                                    <span
                                        class="px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-md text-[11px] font-bold text-white w-fit mb-2">Video
                                        giới thiệu</span>
                                    <h4 class="text-lg lg:text-xl font-black text-white">{{ $product->name }}</h4>
                                    <p class="text-xs text-gray-300 mt-1 line-clamp-2">{{ $product->description }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Highlight Features Cards from Database -->
                            @if(!empty($features))
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="w-1.5 h-5 rounded-full bg-primary"></span>
                                    <h3 class="text-base font-extrabold text-gray-900">Đặc điểm nổi bật</h3>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    @foreach($features as $feat)
                                    <div
                                        class="flex items-start gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100 hover:border-rose-200 transition-colors">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-white shadow-2xs border border-gray-100 flex items-center justify-center text-primary shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-900">{{ $feat['title'] }}</h4>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $feat['desc'] }}
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Col: Shopee-Style Technical Specifications & Details (col 8-12) -->
                        <div class="lg:col-span-5 mt-8 lg:mt-0 space-y-6">
                            <!-- Specs Table Card -->
                            <div class="p-5 rounded-2xl bg-gray-50/70 border border-gray-100">
                                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-5 rounded-full bg-rose-600"></span>
                                        <h3 class="text-base font-extrabold text-gray-900 tracking-tight">Chi tiết sản phẩm</h3>
                                    </div>
                                    <span class="text-[11px] font-semibold text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-100">Chính hãng</span>
                                </div>

                                <div class="divide-y divide-gray-200/60 text-xs">
                                    <!-- 1. Danh mục (Shopee Category Breadcrumbs) -->
                                    <div class="py-2.5 flex items-start justify-between gap-4">
                                        <span class="text-gray-500 font-medium w-32 shrink-0">Danh Mục</span>
                                        <div class="font-medium text-right flex flex-wrap justify-end items-center gap-1.5 text-xs">
                                            <a href="/" class="text-primary hover:underline">ShopMart</a>
                                            @if($product->category)
                                                @if($product->category->parent)
                                                    <span class="text-gray-300">›</span>
                                                    <a href="{{ route('catalog.category', $product->category->parent->slug) }}" class="text-primary hover:underline">
                                                        {{ $product->category->parent->name }}
                                                    </a>
                                                @endif
                                                <span class="text-gray-300">›</span>
                                                <a href="{{ route('catalog.category', $product->category->slug) }}" class="text-primary hover:underline">
                                                    {{ $product->category->name }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 2. Kho hàng -->
                                    <div class="py-2.5 flex items-start justify-between gap-4">
                                        <span class="text-gray-500 font-medium w-32 shrink-0">Kho</span>
                                        <span class="font-bold text-gray-900 text-right">{{ number_format($product->stock) }}</span>
                                    </div>

                                    <!-- 3. Thương hiệu -->
                                    <div class="py-2.5 flex items-start justify-between gap-4">
                                        <span class="text-gray-500 font-medium w-32 shrink-0">Thương hiệu</span>
                                        <div class="font-bold text-right">
                                            @if($product->brand)
                                                <a href="{{ route('catalog.search', ['brand' => $product->brand]) }}" class="text-primary hover:underline">
                                                    {{ $product->brand }}
                                                </a>
                                            @else
                                                <span class="text-gray-600 font-normal">No Brand</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- 4. Dynamic Technical Specs from Seller -->
                                    @if(!empty($specs) && is_array($specs))
                                        @foreach($specs as $sKey => $sVal)
                                        <div class="py-2.5 flex items-start justify-between gap-4">
                                            <span class="text-gray-500 font-medium w-32 shrink-0">{{ $sKey }}</span>
                                            <span class="font-bold text-gray-900 text-right">{{ $sVal }}</span>
                                        </div>
                                        @endforeach
                                    @endif

                                    <!-- 5. Hạn bảo hành (nếu có warranty_info) -->
                                    @if(!empty($product->warranty_info))
                                    <div class="py-2.5 flex items-start justify-between gap-4">
                                        <span class="text-gray-500 font-medium w-32 shrink-0">Hạn bảo hành</span>
                                        <span class="font-bold text-gray-900 text-right">{{ $product->warranty_info }}</span>
                                    </div>
                                    @endif

                                    <!-- 6. Gửi từ (Shop Location: Tỉnh / Thành phố) -->
                                    <div class="py-2.5 flex items-start justify-between gap-4">
                                        <span class="text-gray-500 font-medium w-32 shrink-0">Gửi từ</span>
                                        <span class="font-bold text-gray-900 text-right">{{ $product->store ? $product->store->province_city : 'Hà Nội' }}</span>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($product->warranty_info))
                            <!-- Warranty Card from Database -->
                            <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <h4 class="text-sm font-bold text-emerald-900">Chính sách bảo hành</h4>
                                </div>
                                <p class="text-xs text-emerald-800 leading-relaxed">
                                    {{ $product->warranty_info }}
                                </p>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- 2. QA TAB: DYNAMIC PRODUCT CONSULTATION Q&A -->
                <div data-pd-panel="qa" class="p-6 lg:p-8 hidden">
                    <div class="w-full space-y-6">
                        <!-- Header & Call To Action -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-base font-extrabold text-gray-900">Hỏi đáp & Tư vấn nhanh về {{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Nơi bạn có thể hỏi người bán về tính năng, tư vấn chọn phân loại hoặc chính sách.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="document.getElementById('pd-ask-form-box').scrollIntoView({ behavior: 'smooth' }); document.getElementById('pd-question-input')?.focus();" class="btn btn-primary text-xs font-bold px-4 py-2">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Đặt câu hỏi
                                </button>
                                @auth
                                <form action="{{ route('chat.start', $product->store->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline text-xs font-bold px-4 py-2 border-gray-200 hover:border-primary hover:text-primary">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Chat riêng với Shop
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('login') }}" class="btn btn-outline text-xs font-bold px-4 py-2 border-gray-200 hover:border-primary hover:text-primary">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Đăng nhập để chat
                                </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Ask Question Form Box -->
                        <div id="pd-ask-form-box" class="p-5 rounded-2xl bg-rose-50/30 border border-rose-100">
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Bạn có thắc mắc gì về sản phẩm này?</h4>
                            <p class="text-xs text-gray-500 mb-3">Người bán hoặc đội ngũ tư vấn sẽ phản hồi trong thời gian sớm nhất.</p>

                            <form action="{{ route('product.questions.store', $product->id) }}" method="POST" class="space-y-3" id="pd-ask-question-form" onsubmit="const btn = this.querySelector('button[type=submit]'); if (btn.disabled) return false; btn.disabled = true; btn.innerHTML = '<span class=\'inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5\'></span> Đang gửi...'; return true;">
                                @csrf
                                @guest
                                <div>
                                    <input type="text" name="customer_name" placeholder="Họ và tên hoặc biệt danh của bạn" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 bg-white focus:outline-rose-500">
                                </div>
                                @endguest
                                <div>
                                    <textarea id="pd-question-input" name="question" rows="3" required minlength="5" placeholder="Ví dụ: Sản phẩm còn màu xanh không shop? Bảo hành tại nhà hay gửi hãng?..." class="w-full text-xs p-3 rounded-xl border border-gray-200 bg-white focus:outline-rose-500 focus:border-rose-500 resize-none"></textarea>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] text-gray-400">Câu hỏi sẽ được hiển thị công khai để người mua khác cùng tham khảo.</span>
                                    <button type="submit" class="btn btn-primary text-xs font-bold px-4 py-2 flex items-center justify-center min-w-[110px]">
                                        Gửi câu hỏi
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Questions List -->
                        <div class="space-y-4">
                            @php
                                $isShopOwner = auth()->check() && auth()->user()->store && auth()->user()->store->id === $product->store_id;
                            @endphp

                            @forelse($product->questions as $q)
                            <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-100 {{ !$q->is_visible ? 'opacity-60 bg-gray-100/70 border-dashed border-gray-300' : 'hover:border-rose-100' }} transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-7 h-7 rounded-full bg-rose-100 text-primary font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                        Q
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900">{{ $q->customer_name ?: ($q->user?->name ?? 'Khách hàng') }}</span>
                                                <span class="text-[10px] text-gray-400">· {{ $q->created_at?->diffForHumans() ?? 'Gần đây' }}</span>
                                                @if(!$q->is_visible)
                                                <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-[10px] rounded font-semibold">Đã ẩn</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <!-- Tiếp tục trong chat riêng tư button -->
                                                @auth
                                                <form action="{{ route('chat.start', $product->store->slug) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" title="Chat riêng tư với Shop về vấn đề này" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span>Chat riêng với Shop</span>
                                                    </button>
                                                </form>
                                                @else
                                                <a href="{{ route('login') }}" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                    <span>Chat riêng</span>
                                                </a>
                                                @endauth

                                                <!-- Shop or Author Controls: Hide/Unhide & Delete question -->
                                                @php
                                                    $isQuestionAuthor = auth()->check() && $q->user_id && auth()->id() === $q->user_id;
                                                @endphp

                                                @if($isShopOwner || $isQuestionAuthor)
                                                <div class="flex items-center gap-1.5 ml-2 border-l border-gray-200 pl-2">
                                                    <form action="{{ route('questions.toggle-visibility', $q->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-[11px] font-semibold text-gray-500 hover:text-gray-800 underline cursor-pointer">
                                                            {{ $q->is_visible ? 'Ẩn' : 'Hiện lại' }}
                                                        </button>
                                                    </form>
                                                    <span class="text-gray-300">·</span>
                                                    <form action="{{ route('questions.destroy', $q->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-[11px] font-semibold text-rose-500 hover:text-rose-700 underline cursor-pointer">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <h4 class="text-xs font-semibold text-gray-800 mt-1 leading-relaxed">{{ $q->question }}</h4>

                                        <!-- Seller's Answer -->
                                        @if($q->answer)
                                        <div class="flex items-start gap-3 mt-3 pt-3 border-t border-gray-200/50">
                                            <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                                A
                                            </div>
                                            <div class="text-xs text-gray-700 leading-relaxed flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-bold text-gray-900">{{ $product->store->name ?? 'Người bán' }}</span>
                                                    <span class="px-1.5 py-0.2 bg-emerald-50 text-emerald-700 font-bold text-[9px] rounded border border-emerald-100">Shop phản hồi</span>
                                                    @if($q->answered_at)
                                                    <span class="text-[10px] text-gray-400">· {{ $q->answered_at->diffForHumans() }}</span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-800">{{ $q->answer }}</p>
                                            </div>
                                        </div>
                                        @else
                                        <!-- No answer yet -->
                                        <div class="mt-2.5 pt-2 border-t border-gray-200/40 text-[11px] text-gray-400 flex items-center justify-between">
                                            <span>Chưa có câu trả lời từ người bán.</span>
                                            @if($isShopOwner)
                                             <details class="cursor-pointer">
                                                <summary class="font-bold text-primary hover:underline">Trả lời câu hỏi này</summary>
                                                <form action="{{ route('seller.questions.answer', $q->id) }}" method="POST" class="mt-2 space-y-2" onsubmit="const btn = this.querySelector('button[type=submit]'); if (btn.disabled) return false; btn.disabled = true; btn.innerHTML = '<span class=\'inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin mr-1\'></span> Đang gửi...'; return true;">
                                                    @csrf
                                                    <textarea name="answer" rows="2" required placeholder="Nhập câu trả lời của Shop..." class="w-full text-xs p-2 rounded-lg border border-gray-200 bg-white focus:outline-rose-500"></textarea>
                                                    <button type="submit" class="btn btn-primary text-xs font-bold px-3 py-1 flex items-center justify-center min-w-[90px]">Gửi phản hồi</button>
                                                </form>
                                            </details>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-10 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-xs">Chưa có câu hỏi nào. Hãy là người đầu tiên đặt câu hỏi để nhận tư vấn từ Shop!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 3. REVIEWS TAB -->
                <div data-pd-panel="reviews" class="p-6 lg:p-8 hidden">
                    <div class="w-full space-y-6">
                        <!-- Rating Summary & Interactive Filters Header -->
                        <div class="pd-review-summary-card">
                            <!-- Left: Rating Score -->
                            <div class="pd-review-score-box">
                                <div class="pd-review-score-num">
                                    {{ number_format($product->rating, 1) }}<span class="pd-review-score-max"> / 5</span>
                                </div>
                                <div class="pd-review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    @endfor
                                </div>
                                <div class="pd-review-count-text">{{ $reviewsCount }} lượt đánh giá xác thực</div>
                            </div>

                            <!-- Right: Filter Pills -->
                            <div class="pd-review-filters-wrap" id="pd-review-filters">
                                <button type="button" data-review-filter="all" class="pd-filter-pill is-active">
                                    <span>Tất Cả</span>
                                    @if($reviewsCount > 0)
                                    <span class="pd-filter-badge">({{ $reviewsCount }})</span>
                                    @endif
                                </button>
                                <button type="button" data-review-filter="5" class="pd-filter-pill">
                                    <span>5 Sao</span>
                                    <span class="pd-filter-badge">({{ $ratingCounts[5] ?? 0 }})</span>
                                </button>
                                <button type="button" data-review-filter="4" class="pd-filter-pill">
                                    <span>4 Sao</span>
                                    <span class="pd-filter-badge">({{ $ratingCounts[4] ?? 0 }})</span>
                                </button>
                                <button type="button" data-review-filter="3" class="pd-filter-pill">
                                    <span>3 Sao</span>
                                    <span class="pd-filter-badge">({{ $ratingCounts[3] ?? 0 }})</span>
                                </button>
                                <button type="button" data-review-filter="2" class="pd-filter-pill">
                                    <span>2 Sao</span>
                                    <span class="pd-filter-badge">({{ $ratingCounts[2] ?? 0 }})</span>
                                </button>
                                <button type="button" data-review-filter="1" class="pd-filter-pill">
                                    <span>1 Sao</span>
                                    <span class="pd-filter-badge">({{ $ratingCounts[1] ?? 0 }})</span>
                                </button>
                                <button type="button" data-review-filter="comment" class="pd-filter-pill">
                                    <span>Có Bình Luận</span>
                                    <span class="pd-filter-badge">({{ $withCommentCount }})</span>
                                </button>
                                <button type="button" data-review-filter="media" class="pd-filter-pill">
                                    <span>Có Hình Ảnh / Video</span>
                                    <span class="pd-filter-badge">({{ $withMediaCount }})</span>
                                </button>
                            </div>
                        </div>

                        <!-- Customer Reviews List from Database -->
                        <div class="space-y-3.5" id="pd-reviews-list">
                            @forelse($product->reviews as $rev)
                            @php
                                $hasComment = !empty(trim((string)$rev->comment));
                                $hasMedia = !empty($rev->images) && is_array($rev->images) && count($rev->images) > 0;
                            @endphp
                            <div class="pd-review-card" data-rating="{{ $rev->rating }}" data-has-comment="{{ $hasComment ? '1' : '0' }}" data-has-media="{{ $hasMedia ? '1' : '0' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-rose-100 text-primary font-bold text-sm flex items-center justify-center shrink-0 border border-rose-200/60 shadow-2xs">
                                            {{ strtoupper(substr($rev->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-900 flex items-center gap-2">
                                                <span>{{ $rev->user?->name ?? 'Khách hàng' }}</span>
                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100/60">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Đã mua hàng
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-1 text-amber-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $rev->rating ? 'fill-amber-400 text-amber-400' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-gray-400 font-medium shrink-0">{{ $rev->created_at?->diffForHumans() ?? 'Vừa xong' }}</span>
                                </div>

                                @if($hasComment)
                                <p class="text-xs text-gray-700 leading-relaxed mt-2.5">{{ $rev->comment }}</p>
                                @endif

                                 @if($hasMedia)
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($rev->images as $img)
                                    <a href="{{ $img }}" target="_blank" rel="noopener" class="w-16 h-16 rounded-xl overflow-hidden border border-gray-200 block hover:opacity-90 transition-opacity">
                                        <img src="{{ $img }}" alt="Review media" class="w-full h-full object-cover">
                                    </a>
                                    @endforeach
                                </div>
                                @endif

                                @php
                                    $isReviewAuthor = auth()->check() && $rev->user_id && auth()->id() === $rev->user_id;
                                @endphp

                                @if($isReviewAuthor)
                                <div class="flex items-center justify-between gap-3 mt-3 pt-2.5 border-t border-gray-100 text-[11px]">
                                    <div class="flex items-center gap-2">
                                        @if($rev->status === 'hidden')
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-500 font-bold border border-gray-200">
                                            Đã ẩn (chỉ bạn nhìn thấy)
                                        </span>
                                        @else
                                        <span class="text-gray-400">Đánh giá của bạn</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('user.reviews.toggle-visibility', $rev->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="font-semibold text-gray-600 hover:text-gray-900 underline cursor-pointer">
                                                {{ $rev->status === 'hidden' ? 'Hiện lại đánh giá' : 'Ẩn đánh giá' }}
                                            </button>
                                        </form>
                                        <span class="text-gray-300">·</span>
                                        <form action="{{ route('user.reviews.destroy', $rev->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này? Điểm số sản phẩm sẽ được tự động tính toán lại.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-semibold text-rose-500 hover:text-rose-700 underline cursor-pointer">
                                                Xóa đánh giá
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-10 text-gray-400 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-xs">Chưa có đánh giá nào cho sản phẩm này.</p>
                            </div>
                            @endforelse

                            <!-- Empty Filter Message (Hidden by default, shown by JS when no reviews match filter) -->
                            <div id="pd-reviews-filter-empty" class="hidden text-center py-10 text-gray-400 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <p class="text-xs font-medium">Không có đánh giá nào phù hợp với bộ lọc đã chọn.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== RECOMMENDED PRODUCTS SECTION ==================== -->
        <section class="mt-10" id="pd-recommendations">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                <div>
                    <div
                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-rose-500/10 to-orange-500/10 text-primary rounded-full text-xs font-black uppercase tracking-wider border border-rose-200/60 mb-2">
                        <svg class="w-3.5 h-3.5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.11 2.014-1.127 3.32-.017.92.366 1.763.784 2.378.077.113.155.22.233.32-.15-.02-.303-.053-.46-.102C8.36 8.59 7.42 7.78 7.07 7.054a1 1 0 00-1.748.97c.5 1.037 1.487 2.054 2.518 2.56.096.047.195.09.297.13-.243.076-.493.125-.747.146-.66.055-1.332-.142-1.848-.527a1 1 0 00-1.282 1.528c.84.707 1.93 1.01 3.03.905.155-.015.31-.044.464-.085-.316.326-.69.6-1.12.805-1.077.51-2.28.536-3.23.07a1 1 0 00-.895 1.79c1.478.724 3.376.677 4.986-.086.58-.275 1.1-.645 1.547-1.08.31.398.69.742 1.13.993 1.343.766 2.946.726 4.316-.105a1 1 0 00-1.042-1.71c-.854.518-1.867.54-2.735.045-.486-.277-.872-.693-1.124-1.187.35-.187.67-.425.952-.705.945-.94 1.442-2.213 1.447-3.528.005-1.572-.73-3.053-1.428-4.14a17.47 17.47 0 00-1.354-1.78z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>GỢI Ý DÀNH RIÊNG CHO BẠN</span>
                    </div>
                    <h2 class="text-xl lg:text-2xl font-black text-gray-900 tracking-tight">Sản phẩm tương tự & Được yêu
                        thích</h2>
                    <p class="text-xs text-gray-400 mt-1">Dựa trên sản phẩm bạn đang xem và các sản phẩm bán chạy nhất
                        cùng danh mục</p>
                </div>

                <!-- Category quick filter badges -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                    <span
                        class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-xs whitespace-nowrap">
                        Tất cả gợi ý ({{ $recommendedProducts->count() }})
                    </span>
                    @if($product->category)
                    <a href="{{ route('catalog.category', $product->category->slug) }}"
                        class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:border-primary text-gray-700 text-xs font-bold hover:text-primary transition-colors whitespace-nowrap inline-flex items-center gap-1">
                        <span>{{ $product->category->name }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    @endif
                    @if($product->brand)
                    <a href="{{ route('catalog.brand', $product->brand) }}"
                        class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:border-primary text-gray-700 text-xs font-bold hover:text-primary transition-colors whitespace-nowrap inline-flex items-center gap-1">
                        <span>Cùng hãng {{ $product->brand }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Products Grid: 6 columns on desktop, 4 on tablet, 2 on mobile -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 sm:gap-4">
                @foreach($recommendedProducts as $rec)
                <x-product-card :product="$rec" />
                @endforeach
            </div>

            <!-- View More Button -->
            <div class="text-center mt-8">
                @if($product->category)
                <a href="{{ route('catalog.category', $product->category->slug) }}" class="pd-btn-load-more">
                    <span>Xem thêm sản phẩm cùng loại {{ $product->category->name }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                @else
                <a href="{{ route('catalog.search') }}" class="pd-btn-load-more">
                    <span>Xem thêm các sản phẩm khác trên ShopMart</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
                @endif
            </div>
        </section>

    </main>

    <!-- ==================== DESKTOP FOOTER ==================== -->
    <footer class="bg-white border-t border-gray-200 mt-14 hidden md:block text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Chăm sóc khách hàng</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-primary transition-colors">Trung tâm trợ giúp</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">ShopMart Blog</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Hướng dẫn mua hàng</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Chính sách vận chuyển</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Trả hàng & Hoàn tiền</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Về ShopMart</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-primary transition-colors">Giới thiệu về chúng tôi</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Tuyển dụng</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="{{ auth()->check() && auth()->user()->isSeller() ? route('seller.dashboard') : (auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->check() ? route('seller.register') : route('seller.login'))) }}"
                                class="hover:text-primary transition-colors">Kênh Người Bán</a></li>
                    </ul>
                </div>
                <div>
                    <x-footer-payment-shipping />
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Theo dõi chúng tôi</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-primary transition-colors">Facebook</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Instagram</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">TikTok</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">YouTube</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">Tải ứng dụng ngay</h4>
                    <p class="text-xs text-gray-500 mb-3">Mua sắm mọi lúc, mọi nơi</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-16 h-16 rounded-xl border border-gray-200 p-1 bg-white shadow-xs shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full text-gray-800" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm12-2h-3v3h3v-3zm3 0h-2v2h2v-2zm-3 5h3v2h-3v-2zm3 0h2v2h-2v-2zm-6-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-900 hover:bg-black text-white rounded-md text-[10px] font-semibold flex items-center gap-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.62-.75 1.04-1.8 0.93-2.85-.9.04-1.99.6-2.63 1.35-.57.65-1.07 1.72-.94 2.74 1.01.08 2.02-.49 2.64-1.24z" />
                                </svg>
                                <span>App Store</span>
                            </a>
                            <a href="#"
                                class="px-2.5 py-1 bg-gray-900 hover:bg-black text-white rounded-md text-[10px] font-semibold flex items-center gap-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M3 20.5v-17c0-.8.9-1.3 1.6-.9l14 8.5c.7.4.7 1.4 0 1.8l-14 8.5c-.7.4-1.6-.1-1.6-.9z" />
                                </svg>
                                <span>Google Play</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="border-t border-gray-100 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-gray-400">
                <p>© 2026 ShopMart Inc. Tất cả quyền được bảo lưu.</p>
                <div class="flex gap-4"><a href="#" class="hover:underline">Chính sách bảo mật</a><a href="#"
                        class="hover:underline">Quy chế hoạt động</a><a href="#" class="hover:underline">Giải quyết
                        khiếu nại</a></div>
            </div>
        </div>
    </footer>

    <!-- ==================== MOBILE FIXED BOTTOM CTA BAR ==================== -->
    <div class="pd-mobile-sticky-bar" id="pd-mobile-cta">
        <button data-add-to-cart data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
            class="pd-btn-add flex-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>Thêm giỏ</span>
        </button>
        <button data-buy-now data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
            class="pd-btn-buy flex-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>Mua ngay</span>
        </button>
    </div>

    <!-- ==================== FLOATING AI ASSISTANT ==================== -->
    <x-chat-ai-widget />

</body>

</html>
