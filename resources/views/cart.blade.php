<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-check" content="{{ auth()->check() ? '1' : '0' }}">
    <title>Giỏ hàng ({{ $cart->display_count }}) - ShopMart</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex flex-col justify-between pb-36 lg:pb-24">

    <!-- ==================== DESKTOP HEADER (>= 1024px) ==================== -->
    <header class="cart-header-desktop">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-6">
            <!-- Left: Logo & Title -->
            <div class="flex items-center gap-3">
                <a href="/" class="site-brand" title="Trang chủ ShopMart">
                    <div class="site-brand__icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <span class="site-brand__text">Shop<span class="site-brand__highlight">Mart</span></span>
                </a>
                <span class="text-xs font-bold text-gray-400 pl-2 border-l border-gray-200 uppercase tracking-wider">Giỏ Hàng</span>
            </div>

            <!-- Center Search -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" placeholder="Tìm kiếm sản phẩm trong giỏ hàng..." class="header-search-input pl-4 pr-10">
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Right Utilities -->
            <div class="flex items-center gap-5 text-xs font-bold text-gray-600">
                <a href="/" class="hover:text-primary flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Tiếp tục mua hàng</span>
                </a>
                <div class="relative">
                    <span class="w-9 h-9 rounded-xl bg-rose-50 text-primary flex items-center justify-center border border-rose-100 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                    <span id="header-cart-badge" class="cart-badge-count header-cart-badge">
                        {{ $cart->display_count }}
                    </span>
                </div>
                <div class="pl-3 border-l border-gray-200">
                    <x-header-user-menu />
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN WRAPPER ==================== -->
    <main class="cart-container">

        <!-- ============================================================ -->
        <!-- SCREEN 1: GIỎ HÀNG (MATCHING MOCKUP SCREEN 1) -->
        <!-- ============================================================ -->
        <div id="cart-step-1-view">
            
            <!-- Mobile Top Header Bar (Matching Mockup 9:41 Screen 1) -->
            <div class="lg:hidden flex items-center justify-between py-2 mb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <a href="/" class="p-1 -ml-1 text-gray-800 hover:text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">
                        Giỏ hàng <span class="text-gray-500 font-bold text-sm" id="cart-header-count">({{ $cart->total_items_count }})</span>
                    </h1>
                </div>
                <button id="btn-remove-selected-top" class="text-xs font-semibold text-gray-500 hover:text-primary flex items-center gap-1 transition-colors cursor-pointer p-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa đã chọn</span>
                </button>
            </div>

            <!-- Desktop Header Title Row -->
            <div class="hidden lg:flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    <span>Giỏ hàng</span>
                    <span class="text-base font-bold text-gray-400">({{ $cart->total_items_count }} sản phẩm)</span>
                </h1>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer select-none">
                        <input type="checkbox" id="select-all-desktop-top" data-select-all-checkbox class="w-4.5 h-4.5 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer" {{ $cart->items->count() > 0 && $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
                        <span>Chọn tất cả</span>
                    </label>
                    <button id="btn-remove-selected-desktop" class="text-xs font-bold text-gray-500 hover:text-primary flex items-center gap-1.5 transition-colors cursor-pointer py-1 px-3 rounded-lg hover:bg-rose-50/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Xóa đã chọn</span>
                    </button>
                </div>
            </div>

            <!-- Mobile "Chọn tất cả" Row (Right below top bar, matching Mockup) -->
            <div class="lg:hidden flex items-center justify-between py-1.5 px-1 mb-2">
                <label class="flex items-center gap-2.5 text-xs font-bold text-gray-800 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-mobile-top" data-select-all-checkbox class="w-4.5 h-4.5 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer" {{ $cart->items->count() > 0 && $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
                    <span>Chọn tất cả</span>
                </label>
                <button id="btn-remove-selected-mobile" class="text-[11px] font-semibold text-gray-400 hover:text-primary flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa đã chọn</span>
                </button>
            </div>

            <!-- STORE GROUPS & ITEMS (Shopee Full-Width Style) -->
            <div class="space-y-4">
                    
                    @if($cart->items->isEmpty())
                    <!-- Empty Cart State -->
                    <div class="bg-white rounded-2xl p-10 text-center border border-gray-100 shadow-2xs">
                        <div class="w-16 h-16 bg-rose-50 text-primary rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900">Giỏ hàng của bạn đang trống</h3>
                        <p class="text-xs text-gray-400 mt-1 mb-5">Khám phá ngay hàng ngàn sản phẩm ưu đãi tại ShopMart!</p>
                        <a href="/" class="btn btn-primary inline-flex items-center gap-2">
                            <span>Khám phá mua sắm ngay</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                    @else

                    <!-- STORE GROUPS (Apple, Samsung, etc. as in Mockup) -->
                    @foreach($groupedItems as $storeId => $items)
                    @php
                        $firstProduct = $items->first()->product;
                        $store = $firstProduct?->store;
                        $storeName = $store?->name ?? 'ShopMart Official Store';
                        $isMall = $store?->is_mall ?? true;
                        $storeAllSelected = $items->every(fn($i) => $i->is_selected);
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden" data-store-group="{{ $storeId }}">
                        
                        <!-- Store Header (Matching Mockup: [checkbox] [logo] Name [Mall] > ... Sửa) -->
                        <div class="px-4 py-3 bg-white border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <input type="checkbox" data-store-checkbox="{{ $storeId }}" class="w-4.5 h-4.5 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer" {{ $storeAllSelected ? 'checked' : '' }}>
                                
                                <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                    @if(str_contains(strtolower($storeName), 'apple'))
                                    <svg class="w-3.5 h-3.5 text-gray-900" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.62-.75 1.04-1.8 0.93-2.85-.9.04-1.99.6-2.61 1.34-.55.63-.99 1.68-.86 2.7 1 .08 2.01-.51 2.54-1.19z"/></svg>
                                    @else
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 truncate">
                                    <span class="font-bold text-xs sm:text-sm text-gray-900 truncate">{{ $storeName }}</span>
                                    @if($isMall)
                                    <span class="px-1.5 py-0.2 bg-primary text-white text-[9px] font-black rounded uppercase">Mall</span>
                                    @endif
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>

                            <button class="text-xs font-semibold text-gray-400 hover:text-gray-700">Sửa</button>
                        </div>

                        <!-- Product Items in Store -->
                        <div class="divide-y divide-gray-100">
                            @foreach($items as $item)
                            @php
                                $prod = $item->product;
                                $rawColors = $prod?->variants['colors'] ?? [];
                                $normalizedColors = [];
                                foreach($rawColors as $c) {
                                    if (is_array($c) && isset($c['label'])) {
                                        $normalizedColors[] = [
                                            'label' => $c['label'],
                                            'image' => $c['image'] ?? $prod->main_image_url,
                                        ];
                                    } elseif (is_string($c)) {
                                        $normalizedColors[] = [
                                            'label' => $c,
                                            'image' => $prod->main_image_url,
                                        ];
                                    }
                                }

                                $rawOptions = $prod?->variants['options'] ?? [];
                                $normalizedOptions = [];
                                foreach($rawOptions as $opt) {
                                    if (is_string($opt)) {
                                        $normalizedOptions[] = $opt;
                                    } elseif (is_array($opt) && isset($opt['name'])) {
                                        $normalizedOptions[] = $opt['name'];
                                    }
                                }

                                if (empty($normalizedColors) && empty($normalizedOptions)) {
                                    $normalizedOptions = ['Bản Tiêu Chuẩn', 'Bản Nâng Cấp'];
                                }

                                $currentColorImg = $prod?->main_image_url;
                                if (!empty($normalizedColors)) {
                                    foreach($normalizedColors as $nc) {
                                        if ($item->selected_variant && str_contains($item->selected_variant, $nc['label'])) {
                                            $currentColorImg = $nc['image'];
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <div class="p-3 sm:p-4 flex items-start gap-3 hover:bg-gray-50/40 transition-colors" data-cart-item-row="{{ $item->id }}">
                                
                                <!-- Checkbox -->
                                <input type="checkbox" data-item-checkbox="{{ $item->id }}" data-store-id="{{ $storeId }}" class="w-4.5 h-4.5 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer mt-7 shrink-0" {{ $item->is_selected ? 'checked' : '' }}>

                                <!-- Fixed Size 80x80 Thumbnail with Full-Bleed Image (Fill edge-to-edge) -->
                                <a href="{{ route('product.detail', $prod->slug) }}" class="cart-img-box rounded-xl bg-gray-50 border border-gray-100 shrink-0 overflow-hidden group block relative">
                                    <img src="{{ $currentColorImg }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" data-cart-item-img="{{ $item->id }}">
                                </a>

                                <!-- Details & Actions Column -->
                                <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch">
                                    
                                    <div>
                                        <!-- Product Name -->
                                        <a href="{{ route('product.detail', $prod->slug) }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-primary transition-colors line-clamp-1 leading-snug">
                                            {{ $prod->name }}
                                        </a>

                                        <!-- Interactive Variant Button opening Rich Selection Popup -->
                                        <div class="mt-1 relative inline-block max-w-full">
                                            <button 
                                                type="button" 
                                                data-open-variant-popup
                                                data-item-id="{{ $item->id }}"
                                                data-product-name="{{ $prod->name }}"
                                                data-product-price="{{ $item->formatted_unit_price }}"
                                                data-product-original-price="{{ $prod->original_price ? number_format($prod->original_price, 0, ',', '.') . '₫' : '' }}"
                                                data-product-stock="{{ $prod->stock ?? 45 }}"
                                                data-default-img="{{ $currentColorImg }}"
                                                data-selected-variant="{{ $item->selected_variant }}"
                                                data-colors='@json($normalizedColors)'
                                                data-options='@json($normalizedOptions)'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100/90 hover:bg-rose-50/70 text-gray-700 hover:text-primary text-[11px] sm:text-xs font-medium border border-gray-200/60 hover:border-rose-200 transition-all cursor-pointer group/vb shadow-3xs max-w-full"
                                                title="Nhấp để đổi phiên bản / phân loại (màu sắc, kích thước)"
                                            >
                                                <span class="text-gray-400 text-[11px]">Phân loại:</span>
                                                <span data-variant-display="{{ $item->id }}" class="text-gray-800 font-semibold text-[11px] truncate max-w-[170px] sm:max-w-[240px]">
                                                    {{ $item->selected_variant ?: 'Chọn phân loại' }}
                                                </span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover/vb:text-primary transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bottom row: Price on left, Stepper & Mobile Actions on right -->
                                    <div class="flex items-center justify-between pt-1.5 mt-1 border-t border-gray-50 gap-2">
                                        <!-- Price -->
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-xs sm:text-sm font-black text-primary">{{ $item->formatted_unit_price }}</span>
                                            @if($prod->original_price && $prod->original_price > $prod->price)
                                            <span class="text-[10px] text-gray-400 line-through">{{ number_format($prod->original_price, 0, ',', '.') }}₫</span>
                                            @endif
                                        </div>

                                        <!-- Stepper [- 1 +] -->
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-3xs">
                                                <button data-qty-btn data-action="decrement" data-item-id="{{ $item->id }}" class="w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-primary hover:bg-rose-50 transition-colors cursor-pointer" aria-label="Giảm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                                </button>
                                                <span class="w-7 sm:w-8 text-center text-xs font-bold text-gray-900 select-none">
                                                    {{ $item->quantity }}
                                                </span>
                                                <button data-qty-btn data-action="increment" data-item-id="{{ $item->id }}" class="w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-primary hover:bg-rose-50 transition-colors cursor-pointer" aria-label="Tăng">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                </button>
                                            </div>

                                            <!-- Mobile Trash Button -->
                                            <button data-remove-item data-item-id="{{ $item->id }}" class="sm:hidden w-7 h-7 flex items-center justify-center text-gray-400 hover:text-primary transition-colors cursor-pointer" title="Xóa sản phẩm">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <!-- Right Column: 2 Components (Shopee Style: Xóa & Tìm sản phẩm tương tự ▼ - Image 1789467844139) -->
                                <div class="hidden sm:flex flex-col items-end justify-between self-stretch shrink-0 pl-3 border-l border-gray-100 min-w-[125px]">
                                    <button type="button" data-remove-item data-item-id="{{ $item->id }}" class="text-xs font-medium text-gray-700 hover:text-primary transition-colors cursor-pointer pt-0.5">
                                        Xóa
                                    </button>
                                    <a href="{{ $prod->category ? route('catalog.category', $prod->category->slug) : route('catalog.search', ['q' => $prod->name]) }}" class="text-xs font-medium text-primary hover:underline flex items-center gap-0.5 transition-colors pb-1 text-right group" title="Tìm sản phẩm tương tự">
                                        <span class="leading-tight">Tìm sản phẩm tương tự</span>
                                        <svg class="w-3 h-3 text-primary shrink-0 group-hover:translate-y-0.5 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </a>
                                </div>

                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @endif

                    <!-- "Có thể bạn cũng thích" Recommendations (Matching Mockup Screen 1) -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs sm:text-sm font-extrabold text-gray-900">Có thể bạn cũng thích</h3>
                            <a href="/" class="text-xs font-bold text-primary hover:underline flex items-center gap-0.5">
                                <span>Xem thêm</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <!-- 2-Column Responsive Grid matching Homepage Card Design (Full-bleed image) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($recommendedProducts->take(4) as $rec)
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col group">
                                <!-- Top Full Bleed Image Container (Fills top of card edge-to-edge) -->
                                <div class="relative w-full aspect-square overflow-hidden bg-gray-100">
                                    @if($rec->discount_percent > 0)
                                    <span class="absolute top-2.5 left-2.5 z-10 px-2 py-0.5 bg-primary text-white text-[10px] font-extrabold rounded-md shadow-xs pointer-events-none">
                                        -{{ $rec->discount_percent }}%
                                    </span>
                                    @endif

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
                                        <h4 class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug group-hover:text-primary transition-colors mb-1.5 min-h-[32px]">
                                            {{ $rec->name }}
                                        </h4>
                                        <div class="flex items-baseline gap-1.5 mb-1">
                                            <span class="text-sm font-extrabold text-primary">{{ $rec->formatted_price }}</span>
                                            @if($rec->original_price)
                                            <span class="text-[10px] text-gray-400 line-through">{{ $rec->formatted_original_price }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between text-[11px] text-gray-400">
                                            <span class="inline-flex items-center gap-1 text-amber-500 font-semibold">
                                                <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span>{{ number_format($rec->rating ?? 5.0, 1) }}</span>
                                            </span>
                                            <span>Đã bán {{ $rec->formatted_sold ?? '1.2k' }}</span>
                                        </div>
                                    </a>

                                    <!-- Dual Action Buttons: Thêm giỏ & Mua ngay -->
                                    <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-gray-100 mt-1">
                                        <button 
                                            data-add-to-cart 
                                            data-product-id="{{ $rec->id }}"
                                            data-product-name="{{ $rec->name }}"
                                            class="py-1.5 px-1 bg-gray-100 hover:bg-primary hover:text-white rounded-xl text-[11px] font-bold transition-all text-gray-700 active:scale-95 flex items-center justify-center gap-1 cursor-pointer"
                                            aria-label="Thêm {{ $rec->name }} vào giỏ"
                                            title="Thêm vào giỏ"
                                        >
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span class="truncate">Thêm</span>
                                        </button>
                                        <a 
                                            href="{{ route('product.detail', $rec->slug) }}"
                                            class="py-1.5 px-1 bg-primary hover:bg-primary-hover text-white rounded-xl text-[11px] font-bold transition-all active:scale-95 flex items-center justify-center gap-1 shadow-xs text-center cursor-pointer"
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

                </div>

            </div>

        </div>

        <!-- ============================================================ -->
        <!-- SCREEN 2: THANH TOÁN / CHECKOUT (MATCHING MOCKUP SCREEN 2) -->
        <!-- ============================================================ -->
        <div id="cart-step-2-view" class="hidden">
            
            <!-- Top Nav & Stepper (Matching Screen 2 Mockup: < Giỏ hàng (3), 1 Giỏ hàng -> 2 Thanh toán -> 3 Hoàn tất) -->
            <div class="pb-3 mb-3 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <button id="btn-back-to-cart" class="flex items-center gap-1 text-sm font-black text-gray-900 hover:text-primary cursor-pointer">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                        <span>Giỏ hàng ({{ $cart->selected_count }})</span>
                    </button>
                    <span class="text-xs text-gray-400 font-semibold">Bước 2/3</span>
                </div>

                <!-- 3-Step Progress Stepper -->
                <div class="flex items-center justify-center max-w-sm mx-auto px-4">
                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full bg-primary text-white text-[11px] font-black flex items-center justify-center shadow-xs">1</div>
                        <span class="text-[11px] font-black text-primary mt-1">Giỏ hàng</span>
                    </div>

                    <div class="flex-1 h-0.5 bg-primary mx-2 -mt-4"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 text-gray-500 text-[11px] font-black flex items-center justify-center bg-white">2</div>
                        <span class="text-[11px] font-semibold text-gray-500 mt-1">Thanh toán</span>
                    </div>

                    <div class="flex-1 h-0.5 bg-gray-200 mx-2 -mt-4"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 text-gray-400 text-[11px] font-black flex items-center justify-center bg-white">3</div>
                        <span class="text-[11px] font-semibold text-gray-400 mt-1">Hoàn tất</span>
                    </div>
                </div>
            </div>

            <!-- Two Columns for Desktop, Stacked for Mobile -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                <!-- Left: Address, Items, Vouchers, Payment Methods -->
                <div class="lg:col-span-8 space-y-3.5">
                    
                    <!-- 1. Shipping Address Card -->
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-50 text-primary flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900">Địa chỉ nhận hàng</h3>
                                <div id="cart-display-name-phone" class="text-xs font-bold text-gray-800 mt-0.5">
                                    {{ $defaultAddress->recipient_name ?? (auth()->user()?->name ?? 'Khách Mua Hàng') }}
                                    <span class="text-gray-300 mx-1">|</span>
                                    {{ $defaultAddress->phone ?? (auth()->user()?->phone ?? 'Chưa cập nhật SĐT') }}
                                </div>
                                <p id="cart-display-address" class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">
                                    {{ $defaultAddress->address_line ?? 'Chưa có địa chỉ — hãy thêm địa chỉ giao hàng' }}
                                </p>
                            </div>
                        </div>
                        @auth
                        <button type="button" id="cart-btn-open-address-modal" class="text-xs font-bold text-blue-600 hover:text-primary shrink-0 pt-1 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @endauth
                    </div>

                    <!-- Hidden inputs for order submission -->
                    <input type="hidden" id="cart-input-name" value="{{ $defaultAddress->recipient_name ?? (auth()->user()?->name ?? '') }}">
                    <input type="hidden" id="cart-input-phone" value="{{ $defaultAddress->phone ?? (auth()->user()?->phone ?? '') }}">
                    <input type="hidden" id="cart-input-address" value="{{ $defaultAddress->address_line ?? '' }}">

                    <!-- 2. Products List (Matching Screen 2: Sản phẩm (3) ... Chỉnh sửa) -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                        <div class="px-4 py-3 bg-white border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-xs font-black text-gray-900">
                                Sản phẩm ({{ $cart->selected_count }})
                            </h3>
                            <button id="btn-edit-cart-items" class="text-xs font-bold text-blue-600 hover:underline">Chỉnh sửa</button>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($cart->selected_items as $item)
                            <div class="p-3.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="cart-img-box rounded-xl bg-gray-100 border border-gray-100 shrink-0 overflow-hidden block">
                                        <img src="{{ $item->product?->main_image_url }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 truncate">{{ $item->product?->name }}</h4>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $item->selected_variant ?? 'Titan Đen | 256GB' }}</p>
                                        <span class="text-xs font-extrabold text-primary mt-1 block">{{ $item->formatted_unit_price }}</span>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-gray-400 shrink-0">x{{ $item->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Voucher / Mã giảm giá -->
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3">
                        <!-- Voucher trigger row (opens modal) -->
                        <div
                            id="cart-btn-open-voucher"
                            onclick="openCartVoucherModal()"
                            class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-orange-50/60 to-rose-50/40 border border-orange-200/70 hover:border-primary cursor-pointer transition-all group"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center shadow-xs shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-gray-900 block">ShopMart Voucher</span>
                                    <span class="text-[11px] text-gray-500 group-hover:text-primary transition-colors" id="cart-voucher-status-text">Chọn hoặc nhập mã khuyến mãi ›</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <span id="cart-voucher-applied-pill" class="hidden px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700"></span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>

                        <!-- ShopMart Points Switch -->
                        <div class="flex items-center justify-between text-xs border-t border-gray-50 pt-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-500 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <span class="font-bold text-gray-800">Dùng 500.000 điểm</span>
                                    <span class="text-gray-400 font-semibold">(-500.000₫)</span>
                                </div>
                            </div>
                            <label class="ios-toggle">
                                <input type="checkbox" id="toggle-reward-points">
                                <span class="ios-slider"></span>
                            </label>
                        </div>

                        <!-- Hidden coupon code for order submission -->
                        <input type="hidden" id="cart-hidden-coupon-code" value="">
                    </div>

                    <!-- 4. Payment Method -->
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3">
                        <h3 class="text-xs font-black text-gray-900">Phương thức thanh toán</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="cart-payment-methods-wrapper">
                            <!-- COD -->
                            <label class="cart-payment-option is-selected cursor-pointer flex flex-col p-3 rounded-xl border-2 border-primary bg-rose-50/20 transition-all">
                                <input type="radio" name="payment_method" value="cod" checked class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <span class="cart-pay-check w-4 h-4 rounded-full border-2 border-primary bg-primary flex items-center justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-gray-900">Thanh toán khi nhận (COD)</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Kiểm tra hàng trước khi trả tiền</span>
                            </label>

                            <!-- VNPay -->
                            <label class="cart-payment-option cursor-pointer flex flex-col p-3 rounded-xl border border-gray-200 hover:border-blue-300 bg-white transition-all">
                                <input type="radio" name="payment_method" value="vnpay" class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <span class="cart-pay-check w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs font-bold text-blue-700">VNPay</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 text-blue-800">QR / Thẻ</span>
                                </div>
                                <span class="text-[11px] text-gray-500 mt-0.5">ATM nội địa, Visa/Master, VNPAY-QR</span>
                            </label>

                            <!-- MoMo -->
                            <label class="cart-payment-option cursor-pointer flex flex-col p-3 rounded-xl border border-gray-200 hover:border-pink-300 bg-white transition-all">
                                <input type="radio" name="payment_method" value="momo" class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                                    </div>
                                    <span class="cart-pay-check w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-gray-900">Ví MoMo</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Thanh toán tiện lợi qua ứng dụng MoMo</span>
                            </label>

                            <!-- ShopMart Wallet -->
                            <label class="cart-payment-option cursor-pointer flex flex-col p-3 rounded-xl border border-gray-200 hover:border-rose-300 bg-white transition-all">
                                <input type="radio" name="payment_method" value="wallet" class="sr-only">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <span class="cart-pay-check w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-gray-900">Ví ShopMart</span>
                                <span class="text-[11px] text-gray-500 mt-0.5">Số dư: 2.000.000₫</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Right: Detailed Billing Breakdown & Place Order Button (Matching Screen 2 Mockup) -->
                <div class="lg:col-span-4 sticky top-24">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 space-y-3.5">
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Tạm tính ({{ $cart->selected_count }} sản phẩm)</span>
                                <span class="font-bold text-gray-900" id="billing-subtotal">{{ $cart->formatted_selected_total }}</span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600">
                                <span>Giảm giá</span>
                                <span class="font-bold text-primary" id="billing-voucher">- 650.000₫</span>
                            </div>

                            <div class="flex items-center justify-between text-gray-600">
                                <span>Phí vận chuyển</span>
                                <span class="font-bold text-gray-900">0₫</span>
                            </div>

                            <div id="points-discount-row" class="hidden flex items-center justify-between text-amber-600 font-bold">
                                <span>Điểm ShopMart</span>
                                <span>- 500.000₫</span>
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-baseline justify-between mb-2">
                                <span class="text-sm font-black text-gray-900">Tổng thanh toán</span>
                                <span class="text-xl font-black text-primary" id="billing-grand-total">
                                    {{ number_format(max(0, $cart->selected_total - 650000), 0, ',', '.') }}₫
                                </span>
                            </div>

                            <!-- Green Savings Pill (Matching Mockup: Bạn tiết kiệm được 650.000đ) -->
                            <div class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-2 rounded-xl flex items-center gap-1.5 border border-emerald-100">
                                <span>Bạn tiết kiệm được</span>
                                <span id="billing-total-savings">650.000₫</span>
                            </div>
                        </div>

                        <!-- Big Red Button: Đặt hàng (Matching Mockup Screen 2) -->
                        <button id="btn-place-order" class="w-full py-3.5 bg-primary hover:bg-primary-hover text-white font-black text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 active:scale-98 cursor-pointer">
                            <span>Đặt hàng</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <!-- ============================================================ -->
        <!-- SCREEN 3: HOÀN TẤT / SUCCESS CONFIRMATION -->
        <!-- ============================================================ -->
        <div id="cart-step-3-view" class="hidden max-w-md mx-auto py-8 text-center">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-lg space-y-4">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto border border-emerald-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>

                <div>
                    <h2 class="text-xl font-black text-gray-900">Đặt hàng thành công!</h2>
                    <p class="text-xs text-gray-500 mt-1">Đơn hàng của bạn đã được ghi nhận vào hệ thống ShopMart.</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4 text-xs space-y-2 border border-gray-100 text-left">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Mã đơn hàng:</span>
                        <span class="font-mono font-bold text-gray-900" id="success-order-code">SM-2026-8899</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Dự kiến giao hàng:</span>
                        <span class="font-bold text-emerald-600">Ngày mai, trước 18:00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Thanh toán:</span>
                        <span class="font-semibold text-gray-700">Ví ShopMart</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <a href="/" class="w-full py-3 bg-primary hover:bg-primary-hover text-white text-xs font-black rounded-xl shadow-xs transition-all text-center">
                        Tiếp tục mua sắm
                    </a>
                    <a href="{{ route('cart') }}" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all text-center">
                        Xem lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- ==================== FIXED SHOPEE-STYLE STICKY BOTTOM BAR (DÍNH LIỀN BOTTOM BAR) ==================== -->
    <div id="shopee-bottom-wrapper" class="fixed bottom-0 left-0 right-0 z-40 bg-white/98 backdrop-blur-md border-t border-gray-200 shadow-[0_-4px_25px_rgba(0,0,0,0.08)]">
        
        <!-- ROW 0: STICKY SHOPEE VOUCHER (Đưa kho voucher xuống sticky giống Shopee) -->
        <div class="border-b border-gray-100 bg-orange-50/40 py-2 px-3.5 sm:px-6">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-md bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-gray-800 flex items-center gap-1.5">
                        <span>ShopMart Voucher</span>
                        <span class="text-[9px] bg-rose-100 text-primary font-bold px-1.5 py-0.2 rounded-full">HOT</span>
                    </span>
                </div>
                <a href="{{ route('vouchers.index') }}" class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-primary flex items-center gap-1 transition-colors">
                    <span>Chọn hoặc nhập mã</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- ROW 1: TÓM TẮT THANH TOÁN (CHECKOUT SUMMARY BAR - SHOPEE STYLE) -->
        <div id="mobile-sticky-checkout-bar" class="max-w-6xl mx-auto px-3.5 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between gap-3">
            <!-- Left: Checkbox "Chọn tất cả" & Xóa -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                <label class="flex items-center gap-2 sm:gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" id="mobile-select-all-bottom" data-select-all-checkbox class="w-4.5 h-4.5 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer" {{ $cart->items->count() > 0 && $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
                    <span class="text-xs sm:text-sm font-bold text-gray-800">
                        Chọn tất cả <span class="hidden sm:inline">(<span class="shopee-selected-count">{{ $cart->selected_count }}</span>)</span>
                    </span>
                </label>

                <button id="btn-remove-selected-sticky" class="hidden sm:flex items-center gap-1 text-xs font-semibold text-gray-500 hover:text-primary transition-colors py-1 px-2.5 rounded-lg hover:bg-rose-50 cursor-pointer">
                    <x-icon name="trash" class="w-3.5 h-3.5" />
                    <span>Xóa</span>
                </button>
            </div>

            <!-- Right: Tổng thanh toán & Nút Mua hàng / Thanh toán -->
            <div class="flex items-center gap-2.5 sm:gap-5">
                <div class="text-right min-w-0">
                    <div class="flex items-baseline justify-end gap-1">
                        <span class="text-[11px] sm:text-xs text-gray-500 font-medium">Tổng thanh toán:</span>
                        <span class="text-sm sm:text-xl font-black text-primary truncate" id="mobile-sticky-total">
                            {{ $cart->formatted_selected_total }}
                        </span>
                    </div>
                    @if($cart->savings_total > 0)
                    <div class="text-[10px] sm:text-xs text-emerald-600 font-semibold truncate" id="sticky-savings-display">
                        Tiết kiệm {{ number_format($cart->savings_total, 0, ',', '.') }}₫
                    </div>
                    @endif
                </div>

                <a href="{{ route('checkout.index') }}" id="btn-mobile-checkout-submit" class="py-2.5 sm:py-3 px-4 sm:px-8 bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-black rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95 shrink-0 flex items-center gap-1.5 cursor-pointer text-center">
                    <span>Tiến hành thanh toán</span>
                    <span id="mobile-checkout-count-badge">({{ $cart->selected_count }})</span>
                    <x-icon name="arrow-right" class="w-4 h-4 hidden sm:inline" />
                </a>
            </div>
        </div>

        <!-- ROW 2: MOBILE BOTTOM APP NAVIGATION (DÍNH LIỀN BÊN DƯỚI BẬC 2) -->
        <nav id="mobile-bottom-app-nav" class="lg:hidden border-t border-gray-100 px-3 py-1 flex items-center justify-around bg-white/90">
            <a href="/" class="flex flex-col items-center text-gray-500 hover:text-primary text-[10px] font-medium py-1 transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span>Trang chủ</span>
            </a>

            <a href="/#categories" class="flex flex-col items-center text-gray-500 hover:text-primary text-[10px] font-medium py-1 transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Danh mục</span>
            </a>

            <a href="{{ route('cart') }}" class="flex flex-col items-center text-primary text-[10px] font-bold py-1 relative">
                <div class="relative">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="cart-badge-count absolute -top-1.5 -right-2.5 min-w-[15px] h-[15px] px-1 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center shadow-xs">
                        {{ $cart->display_count }}
                    </span>
                </div>
                <span>Giỏ hàng</span>
            </a>

            <a href="/#account" class="flex flex-col items-center text-gray-500 hover:text-primary text-[10px] font-medium py-1 transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Tài khoản</span>
            </a>
        </nav>
    </div>

    <!-- ==================== RICH VARIANT SELECTION MODAL / BOTTOM SHEET ==================== -->
    <div id="variant-selection-modal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true">
        
        <!-- Backdrop click target -->
        <div id="variant-modal-backdrop" class="absolute inset-0 cursor-pointer"></div>

        <!-- Modal Dialog Box -->
        <div id="variant-modal-card" class="relative w-full sm:max-w-lg bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border border-gray-100 flex flex-col max-h-[85vh] sm:max-h-[80vh] overflow-hidden transform translate-y-full sm:translate-y-0 sm:scale-95 transition-all duration-300 z-10">
            
            <!-- Modal Header (Product Preview & Close button) -->
            <div class="p-4 sm:p-5 border-b border-gray-100 relative bg-gradient-to-b from-gray-50/80 to-white">
                <!-- Close Button -->
                <button id="close-variant-modal-btn" type="button" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-gray-100 hover:bg-rose-50 hover:text-primary text-gray-500 flex items-center justify-center transition-colors cursor-pointer" aria-label="Đóng">
                    <x-icon name="close" class="w-4 h-4" />
                </button>

                <!-- Mobile Pull Handle -->
                <div class="sm:hidden w-12 h-1 bg-gray-300 rounded-full mx-auto -mt-1 mb-3"></div>

                <!-- Product Summary with Live Image Preview -->
                <div class="flex items-center gap-3.5 pr-8">
                    <!-- Image Preview that changes dynamically on color selection -->
                    <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-gray-100 border border-gray-200/80 shrink-0 overflow-hidden relative shadow-xs">
                        <img id="variant-modal-img" src="" alt="Ảnh sản phẩm" class="w-full h-full object-cover transition-transform duration-300">
                    </div>

                    <div class="flex-1 min-w-0 space-y-1">
                        <h3 id="variant-modal-title" class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-1">Tên sản phẩm</h3>
                        
                        <!-- Price & Stock -->
                        <div class="flex items-baseline gap-2">
                            <span id="variant-modal-price" class="text-base sm:text-lg font-black text-primary">0₫</span>
                            <span id="variant-modal-original-price" class="text-xs text-gray-400 line-through hidden">0₫</span>
                        </div>

                        <!-- Current Selected Combination Display -->
                        <div class="text-[11px] text-gray-500 flex items-center gap-1.5 truncate">
                            <span class="text-gray-400 shrink-0">Đang chọn:</span>
                            <span id="variant-modal-selected-text" class="font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded-md text-[11px] truncate">Mặc định</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Body: Interactive Variant Pickers -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-5 flex-1 divide-y divide-gray-100">
                
                <!-- Section 1: Colors (Màu sắc) -->
                <div id="variant-modal-colors-section" class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <span>Màu sắc:</span>
                            <span id="variant-modal-active-color-label" class="text-primary font-black ml-1"></span>
                        </label>
                        <span class="text-[10px] text-gray-400">Chọn 1 màu</span>
                    </div>

                    <!-- Colors Grid -->
                    <div id="variant-modal-colors-list" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <!-- Dynamically filled by JS -->
                    </div>
                </div>

                <!-- Section 2: Options/Sizes/Storage (Kích thước / Phiên bản) -->
                <div id="variant-modal-options-section" class="pt-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>Phiên bản / Kích thước:</span>
                            <span id="variant-modal-active-option-label" class="text-blue-600 font-black ml-1"></span>
                        </label>
                        <span class="text-[10px] text-gray-400">Chọn 1 phiên bản</span>
                    </div>

                    <!-- Options Flex List -->
                    <div id="variant-modal-options-list" class="flex flex-wrap gap-2">
                        <!-- Dynamically filled by JS -->
                    </div>
                </div>

            </div>

            <!-- Modal Footer (Actions) -->
            <div class="p-3.5 sm:p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                <button id="btn-cancel-variant-modal" type="button" class="py-2.5 px-4 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-bold transition-all cursor-pointer">
                    Hủy
                </button>

                <button id="btn-confirm-variant-modal" type="button" class="flex-1 py-2.5 px-5 bg-gradient-to-r from-primary to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-xs font-black rounded-xl shadow-md transition-all active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Xác nhận thay đổi</span>
                </button>
            </div>

        </div>
    </div>

<!-- ==================== CART STEP 2: ADDRESS MODAL (SHOPEE STYLE) ==================== -->
<div id="cart-address-modal" class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden">
        <div class="h-14 px-5 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Địa chỉ nhận hàng</h3>
                    <p class="text-[11px] text-gray-400">Chọn địa chỉ giao hàng có sẵn hoặc thêm mới</p>
                </div>
            </div>
            <button type="button" id="cart-btn-close-address-modal" class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center text-lg font-bold cursor-pointer transition-colors">&times;</button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 bg-gray-50/50">
            @if(isset($addresses) && $addresses->isNotEmpty())
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Địa chỉ đã lưu ({{ $addresses->count() }})</h4>
                    @foreach($addresses as $addr)
                        <div class="p-3.5 bg-white rounded-xl border-2 {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'border-primary ring-2 ring-primary/20' : 'border-gray-200 hover:border-gray-300' }} cursor-pointer transition-all cart-address-option group shadow-3xs"
                             data-name="{{ $addr->recipient_name }}"
                             data-phone="{{ $addr->phone }}"
                             data-address="{{ $addr->address_line }}"
                             data-default="{{ $addr->is_default ? '1' : '0' }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <div class="w-4 h-4 rounded-full border-2 {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? 'border-primary bg-primary' : 'border-gray-300 group-hover:border-primary' }} flex items-center justify-center shrink-0 mt-0.5 cart-addr-dot">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white {{ ($defaultAddress && $defaultAddress->id === $addr->id) ? '' : 'hidden' }}"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-xs sm:text-sm font-bold text-gray-900">{{ $addr->recipient_name }}</span>
                                            <span class="text-gray-300">|</span>
                                            <span class="text-xs font-semibold text-gray-600 font-mono">{{ $addr->phone }}</span>
                                            @if($addr->is_default)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-200">Mặc định</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Địa chỉ nhận hàng</span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1.5 flex items-start gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="leading-relaxed">{{ $addr->address_line }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="text-xs font-bold text-blue-600 hover:text-primary transition-colors shrink-0 pt-0.5">
                                    Chọn
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="bg-white rounded-xl p-4 sm:p-5 border border-gray-200 shadow-3xs space-y-3.5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span>+ Thêm địa chỉ nhận hàng mới</span>
                    </h4>
                    <span class="text-[10px] text-gray-400">Đầy đủ thông tin để giao đúng</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Họ và tên người nhận <span class="text-rose-500">*</span></label>
                            <input type="text" id="cart-modal-name-input" placeholder="VD: Nguyễn Văn A" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white rounded-xl text-xs transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Số điện thoại <span class="text-rose-500">*</span></label>
                            <input type="tel" id="cart-modal-phone-input" placeholder="VD: 0912345678" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white rounded-xl text-xs transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Tỉnh / Thành phố, Quận / Huyện, Phường / Xã</label>
                        <input type="text" id="cart-modal-city-input" placeholder="VD: Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white rounded-xl text-xs transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Địa chỉ chi tiết (Tòa nhà, số nhà, tên đường...) <span class="text-rose-500">*</span></label>
                        <input type="text" id="cart-modal-address-input" placeholder="VD: Số 123 Đường Nguyễn Huệ, Tòa nhà Bitexco" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 focus:border-primary focus:bg-white rounded-xl text-xs transition-colors">
                    </div>
                    <div class="pt-1 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-gray-500 font-medium">Loại địa chỉ:</span>
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-gray-800">
                                <input type="radio" name="cart_modal_address_type" value="home" checked class="accent-primary cursor-pointer">
                                <span>🏠 Nhà riêng</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-gray-800">
                                <input type="radio" name="cart_modal_address_type" value="office" class="accent-primary cursor-pointer">
                                <span>🏢 Văn phòng</span>
                            </label>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="cart-modal-default-check" checked class="w-4 h-4 rounded text-primary focus:ring-rose-400 border-gray-300 accent-primary cursor-pointer">
                            <span class="text-xs text-gray-700 font-semibold">Đặt làm mặc định</span>
                        </label>
                    </div>

                    <button type="button" id="cart-btn-save-address" class="w-full mt-2 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl shadow-xs transition-all cursor-pointer text-xs flex items-center justify-center gap-2 active:scale-98">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Lưu &amp; Sử dụng địa chỉ này</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-3.5 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
            <span class="text-xs text-gray-400">Địa chỉ sẽ được dùng để tạo vận đơn và giao hàng</span>
            <button type="button" onclick="document.getElementById('cart-address-modal')?.classList.add('hidden')" class="px-5 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-xs font-bold transition-all cursor-pointer">
                Đóng
            </button>
        </div>
    </div>
</div>

<!-- ==================== CART STEP 2: SHOPEE VOUCHER MODAL ==================== -->
<div id="cart-voucher-modal" class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden">

        <!-- Modal Header -->
        <div class="h-14 px-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeCartVoucherModal()" class="text-gray-500 hover:text-gray-800 p-1.5 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h3 class="text-base font-bold text-gray-900">Mã Giảm Giá</h3>
            </div>
            <a href="javascript:void(0)" class="text-xs font-semibold text-gray-500 hover:text-primary">Lịch sử</a>
        </div>

        <!-- Voucher Input -->
        <div class="p-3.5 bg-gray-50/80 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    id="cart-modal-voucher-input"
                    placeholder="Nhập mã voucher"
                    class="flex-1 px-3.5 py-2.5 bg-white border-2 border-orange-500 focus:border-primary rounded-xl text-xs font-bold uppercase tracking-wider text-gray-900 focus:outline-hidden transition-all placeholder:normal-case placeholder:font-normal"
                >
                <button
                    type="button"
                    id="cart-btn-modal-apply"
                    onclick="cartSubmitCoupon(document.getElementById('cart-modal-voucher-input').value.trim(), true)"
                    class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer shrink-0 active:scale-95"
                >
                    Áp dụng
                </button>
            </div>
            <p id="cart-modal-coupon-message" class="text-xs mt-2 px-1 hidden font-semibold"></p>
        </div>

        <!-- Category Tabs -->
        <div class="flex items-center border-b border-gray-100 px-2 bg-white text-xs shrink-0 overflow-x-auto scrollbar-none">
            <button type="button" class="cart-voucher-tab-btn py-3 px-3.5 font-bold text-primary border-b-2 border-primary shrink-0" data-cat="all">Tất cả</button>
            <button type="button" class="cart-voucher-tab-btn py-3 px-3.5 font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent shrink-0" data-cat="freeship">Mã Vận Chuyển</button>
            <button type="button" class="cart-voucher-tab-btn py-3 px-3.5 font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent shrink-0" data-cat="shopmart">ShopMart Mall</button>
            <button type="button" class="cart-voucher-tab-btn py-3 px-3.5 font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent shrink-0" data-cat="category">Danh mục &amp; Shop</button>
        </div>

        <!-- Scrollable Voucher List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50" id="cart-modal-vouchers-list">
            @if(isset($availableCoupons) && $availableCoupons->isNotEmpty())
                @foreach($availableCoupons as $cp)
                    @php
                        $isFreeship = str_contains(strtoupper($cp->code), 'FREESHIP') || str_contains(strtolower($cp->name ?? ''), 'vận chuyển');
                        $isMall = str_contains(strtoupper($cp->code), 'MALL');
                        $isTech = str_contains(strtoupper($cp->code), 'TECH') || str_contains(strtoupper($cp->code), 'DIENTU');
                        $isFashion = str_contains(strtoupper($cp->code), 'FASHION');
                        $catType = 'shopmart';
                        if ($isFreeship) $catType = 'freeship';
                        elseif ($isTech || $isFashion) $catType = 'category';
                        $badgeColor = $isFreeship ? 'bg-teal-500' : ($isMall ? 'bg-red-700' : 'bg-rose-500');
                        $leftTitle = $isFreeship ? 'Mã vận chuyển' : ($isMall ? 'Mall' : 'ShopMart');
                    @endphp
                    <div
                        class="voucher-card relative bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden flex transition-all hover:border-orange-200"
                        data-code="{{ $cp->code }}"
                        data-cat="{{ $catType }}"
                    >
                        <!-- Left colored strip -->
                        <div class="{{ $badgeColor }} w-14 flex flex-col items-center justify-center p-2 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            <span class="text-white text-[9px] font-extrabold text-center leading-tight mt-1">{{ $leftTitle }}</span>
                        </div>
                        <!-- Perforated edge -->
                        <div class="w-3 bg-slate-50 flex flex-col items-center justify-around py-1 shrink-0">
                            @for ($d = 0; $d < 5; $d++)
                                <div class="w-2.5 h-2.5 rounded-full bg-white border border-gray-100"></div>
                            @endfor
                        </div>
                        <!-- Content -->
                        <div class="flex-1 p-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-xs font-black text-gray-900">{{ $cp->name ?? $cp->code }}</span>
                                    <div class="mt-0.5">
                                        @if($cp->discount_type === 'percent')
                                            <span class="text-sm font-black text-primary">Giảm {{ $cp->discount_value }}%</span>
                                        @else
                                            <span class="text-sm font-black text-primary">Giảm {{ number_format($cp->discount_value, 0, ',', '.') }}₫</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg font-mono">{{ $cp->code }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Đơn tối thiểu ₫{{ number_format($cp->min_order_value, 0, ',', '.') }}</p>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                                <span class="text-[9px] text-gray-400">HSD: {{ $cp->expires_at ? $cp->expires_at->format('d.m.Y') : 'Vô thời hạn' }}</span>
                                <button
                                    type="button"
                                    class="btn-cart-modal-select-coupon px-3 py-1.5 border border-primary hover:bg-primary text-primary hover:text-white rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                    data-code="{{ $cp->code }}"
                                    onclick="cartSubmitCoupon('{{ $cp->code }}', true)"
                                >
                                    Dùng ngay
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center text-gray-400 text-xs">Hiện chưa có mã giảm giá nào đang khả dụng.</div>
            @endif
        </div>

        <!-- Modal Footer -->
        <div class="p-3.5 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <span class="text-xs text-gray-500 block">Voucher đã chọn:</span>
                <span class="text-xs font-black text-gray-900" id="cart-modal-selected-code-display">Chưa chọn voucher</span>
            </div>
            <button
                type="button"
                onclick="closeCartVoucherModal()"
                class="px-6 py-2.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl shadow-md shadow-orange-500/20 transition-all cursor-pointer"
            >
                Đồng ý
            </button>
        </div>
    </div>
</div>

<script>
// ===== Cart Step 2: Voucher Modal & Payment method switcher =====
window.openCartVoucherModal = function() {
    document.getElementById('cart-voucher-modal')?.classList.remove('hidden');
};
window.closeCartVoucherModal = function() {
    document.getElementById('cart-voucher-modal')?.classList.add('hidden');
};

// Close on backdrop click
document.getElementById('cart-voucher-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCartVoucherModal();
});

// Voucher category tab filter
document.querySelectorAll('.cart-voucher-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.cart-voucher-tab-btn').forEach(b => {
            b.classList.remove('text-primary', 'border-primary');
            b.classList.add('text-gray-500', 'border-transparent');
        });
        btn.classList.add('text-primary', 'border-primary');
        btn.classList.remove('text-gray-500', 'border-transparent');

        const cat = btn.getAttribute('data-cat');
        document.querySelectorAll('#cart-modal-vouchers-list .voucher-card').forEach(card => {
            card.style.display = (cat === 'all' || card.getAttribute('data-cat') === cat) ? '' : 'none';
        });
    });
});

// Cart coupon AJAX apply
window.cartSubmitCoupon = async function(code, isFromModal) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const subtotal = {{ (float) $cart->selected_total }};

    const activeBtn = isFromModal
        ? document.getElementById('cart-btn-modal-apply')
        : null;

    if (!code) {
        const msgEl = isFromModal ? document.getElementById('cart-modal-coupon-message') : null;
        if (msgEl) { msgEl.className = 'text-xs mt-2 text-rose-600 font-bold'; msgEl.textContent = 'Vui lòng nhập mã giảm giá.'; msgEl.classList.remove('hidden'); }
        return;
    }

    if (activeBtn) { activeBtn.disabled = true; activeBtn.textContent = '...'; }

    try {
        const res = await fetch('{{ route("checkout.apply-coupon") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ code, subtotal })
        });
        const data = await res.json();

        if (data.success) {
            // Update billing row
            const billingVoucher = document.getElementById('billing-voucher');
            const billingGrandTotal = document.getElementById('billing-grand-total');
            const billingTotalSavings = document.getElementById('billing-total-savings');
            if (billingVoucher) billingVoucher.textContent = '- ' + data.formatted_discount;
            if (billingGrandTotal) billingGrandTotal.textContent = data.formatted_new_total;
            if (billingTotalSavings) billingTotalSavings.textContent = data.formatted_discount;

            // Update hidden input
            const hiddenCode = document.getElementById('cart-hidden-coupon-code');
            if (hiddenCode) hiddenCode.value = data.coupon_code;

            // Update trigger row status
            const statusText = document.getElementById('cart-voucher-status-text');
            const appliedPill = document.getElementById('cart-voucher-applied-pill');
            if (statusText) { statusText.textContent = `Đã áp dụng: ${data.coupon_code} (${data.formatted_discount})`; statusText.classList.add('text-emerald-700', 'font-bold'); }
            if (appliedPill) { appliedPill.textContent = data.coupon_code; appliedPill.classList.remove('hidden'); }

            // Update modal footer
            const modalDisplay = document.getElementById('cart-modal-selected-code-display');
            if (modalDisplay) modalDisplay.textContent = `${data.coupon_code} (${data.formatted_discount})`;

            // Highlight selected card
            document.querySelectorAll('#cart-modal-vouchers-list .voucher-card').forEach(card => {
                const btn = card.querySelector('.btn-cart-modal-select-coupon');
                if (card.getAttribute('data-code') === data.coupon_code) {
                    card.classList.add('border-orange-400', 'ring-2', 'ring-orange-300/40');
                    if (btn) { btn.textContent = 'Đã chọn'; btn.className = 'btn-cart-modal-select-coupon px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-bold cursor-pointer'; }
                } else {
                    card.classList.remove('border-orange-400', 'ring-2', 'ring-orange-300/40');
                    if (btn) { btn.textContent = 'Dùng ngay'; btn.className = 'btn-cart-modal-select-coupon px-3 py-1.5 border border-primary hover:bg-primary text-primary hover:text-white rounded-lg text-xs font-bold transition-colors cursor-pointer'; }
                }
            });

            if (isFromModal) setTimeout(closeCartVoucherModal, 400);
        } else {
            const msgEl = isFromModal ? document.getElementById('cart-modal-coupon-message') : null;
            if (msgEl) { msgEl.className = 'text-xs mt-2 text-rose-600 font-bold'; msgEl.textContent = data.message || 'Mã không hợp lệ'; msgEl.classList.remove('hidden'); }
        }
    } catch(e) {
        console.error(e);
    } finally {
        if (activeBtn) { activeBtn.disabled = false; activeBtn.textContent = 'Áp dụng'; }
    }
};

// Cart Step 2 payment method switcher
document.querySelectorAll('.cart-payment-option').forEach(opt => {
    opt.addEventListener('click', () => {
        document.querySelectorAll('.cart-payment-option').forEach(o => {
            o.classList.remove('is-selected', 'border-primary', 'bg-rose-50/20');
            o.classList.remove('border-2');
            o.classList.add('border', 'border-gray-200');
            const dot = o.querySelector('.cart-pay-check span');
            const circle = o.querySelector('.cart-pay-check');
            if (dot) dot.classList.add('hidden');
            if (circle) { circle.classList.remove('border-primary', 'bg-primary'); circle.classList.add('border-gray-300'); }
        });
        opt.classList.add('is-selected', 'border-primary', 'bg-rose-50/20', 'border-2');
        opt.classList.remove('border', 'border-gray-200');
        const radio = opt.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
        const dot = opt.querySelector('.cart-pay-check span');
        const circle = opt.querySelector('.cart-pay-check');
        if (dot) dot.classList.remove('hidden');
        if (circle) { circle.classList.add('border-primary', 'bg-primary'); circle.classList.remove('border-gray-300'); }
    });
});

// Address modal
document.getElementById('cart-btn-open-address-modal')?.addEventListener('click', () => {
    document.getElementById('cart-address-modal')?.classList.remove('hidden');
});
document.getElementById('cart-btn-close-address-modal')?.addEventListener('click', () => {
    document.getElementById('cart-address-modal')?.classList.add('hidden');
});

// Pick existing address in cart address modal
document.querySelectorAll('.cart-address-option').forEach(opt => {
    opt.addEventListener('click', () => {
        const name = opt.getAttribute('data-name');
        const phone = opt.getAttribute('data-phone');
        const address = opt.getAttribute('data-address');
        document.getElementById('cart-input-name').value = name;
        document.getElementById('cart-input-phone').value = phone;
        document.getElementById('cart-input-address').value = address;
        document.getElementById('cart-display-name-phone').textContent = `${name} | ${phone}`;
        document.getElementById('cart-display-address').textContent = address;

        // Update radio UI in modal
        document.querySelectorAll('.cart-address-option').forEach(o => {
            o.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
            o.classList.add('border-gray-200');
            const dot = o.querySelector('.cart-addr-dot');
            const inner = dot?.querySelector('span');
            if (dot) { dot.classList.remove('border-primary', 'bg-primary'); dot.classList.add('border-gray-300'); }
            if (inner) inner.classList.add('hidden');
        });
        opt.classList.add('border-primary', 'ring-2', 'ring-primary/20');
        opt.classList.remove('border-gray-200');
        const activeDot = opt.querySelector('.cart-addr-dot');
        const activeInner = activeDot?.querySelector('span');
        if (activeDot) { activeDot.classList.add('border-primary', 'bg-primary'); activeDot.classList.remove('border-gray-300'); }
        if (activeInner) activeInner.classList.remove('hidden');

        document.getElementById('cart-address-modal')?.classList.add('hidden');
    });
});

// Save new address in cart address modal via AJAX
document.getElementById('cart-btn-save-address')?.addEventListener('click', async () => {
    const name = document.getElementById('cart-modal-name-input')?.value.trim();
    const phone = document.getElementById('cart-modal-phone-input')?.value.trim();
    const city = document.getElementById('cart-modal-city-input')?.value.trim() || '';
    const address = document.getElementById('cart-modal-address-input')?.value.trim();
    const isDefault = document.getElementById('cart-modal-default-check')?.checked ?? true;

    if (!name || !phone || !address) { 
        alert('Vui lòng điền đầy đủ họ và tên, số điện thoại và địa chỉ chi tiết.'); 
        return; 
    }

    const saveBtn = document.getElementById('cart-btn-save-address');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Đang lưu địa chỉ...'; }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('{{ route("checkout.quick-address") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                recipient_name: name,
                phone: phone,
                address_line: address,
                city_district: city,
                is_default: isDefault
            })
        });
        const data = await res.json();
        if (data.success) {
            const finalAddr = data.address?.address_line || (city ? `${address}, ${city}` : address);
            document.getElementById('cart-input-name').value = name;
            document.getElementById('cart-input-phone').value = phone;
            document.getElementById('cart-input-address').value = finalAddr;
            document.getElementById('cart-display-name-phone').textContent = `${name} | ${phone}`;
            document.getElementById('cart-display-address').textContent = finalAddr;
            document.getElementById('cart-address-modal')?.classList.add('hidden');
        } else {
            alert(data.message || 'Lỗi khi lưu địa chỉ');
        }
    } catch (e) {
        console.error(e);
        const finalAddr = city ? `${address}, ${city}` : address;
        document.getElementById('cart-input-name').value = name;
        document.getElementById('cart-input-phone').value = phone;
        document.getElementById('cart-input-address').value = finalAddr;
        document.getElementById('cart-display-name-phone').textContent = `${name} | ${phone}`;
        document.getElementById('cart-display-address').textContent = finalAddr;
        document.getElementById('cart-address-modal')?.classList.add('hidden');
    } finally {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Lưu &amp; Sử dụng địa chỉ này</span>';
        }
    }
});
</script>

</body>
</html>
