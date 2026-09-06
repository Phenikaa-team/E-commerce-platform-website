<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giỏ hàng ({{ $cart->display_count }}) - ShopMart</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f7f7fa;
        }
        .cart-img-box {
            width: 76px;
            height: 76px;
            min-width: 76px;
            min-height: 76px;
            max-width: 76px;
            max-height: 76px;
        }
        .cart-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        /* Custom iOS Toggle */
        .ios-toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        .ios-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .ios-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #e2e8f0;
            transition: .25s;
            border-radius: 24px;
        }
        .ios-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .25s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .ios-toggle input:checked + .ios-slider {
            background-color: #ea384c;
        }
        .ios-toggle input:checked + .ios-slider:before {
            transform: translateX(20px);
        }
    </style>
</head>
<body class="text-[#1f2937] antialiased min-h-screen flex flex-col justify-between pb-28 lg:pb-12">

    <!-- ==================== DESKTOP HEADER (>= 1024px) ==================== -->
    <header class="hidden lg:block bg-white border-b border-gray-100 sticky top-0 z-40 shadow-2xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-6">
            <!-- Left: Logo & Title -->
            <div class="flex items-center gap-3">
                <a href="/" class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#ea384c] to-[#ff5c6c] flex items-center justify-center text-white shadow-xs hover:scale-105 transition-transform" title="Trang chủ ShopMart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </a>
                <a href="/" class="flex items-baseline gap-2">
                    <span class="text-xl font-black tracking-tight text-gray-900">Shop<span class="text-[#ea384c]">Mart</span></span>
                    <span class="text-xs font-bold text-gray-400 pl-2 border-l border-gray-200 uppercase tracking-wider">Giỏ Hàng</span>
                </a>
            </div>

            <!-- Center Search -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" placeholder="Tìm kiếm sản phẩm trong giỏ hàng..." class="w-full bg-gray-100/90 border border-transparent rounded-full py-2 pl-4 pr-10 text-xs focus:bg-white focus:outline-hidden focus:border-[#ea384c] transition-all">
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#ea384c]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Right Utilities -->
            <div class="flex items-center gap-5 text-xs font-bold text-gray-600">
                <a href="/" class="hover:text-[#ea384c] flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Tiếp tục mua hàng</span>
                </a>
                <div class="relative">
                    <span class="w-9 h-9 rounded-xl bg-rose-50 text-[#ea384c] flex items-center justify-center border border-rose-100 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                    <span id="header-cart-badge" class="cart-badge-count absolute -top-1.5 -right-1.5 bg-[#ea384c] text-white text-[10px] font-black px-1.5 min-w-[18px] h-[18px] rounded-full flex items-center justify-center border-2 border-white shadow-xs">
                        {{ $cart->display_count }}
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN WRAPPER ==================== -->
    <main class="max-w-6xl mx-auto px-3 sm:px-6 py-3 sm:py-6 flex-1 w-full">

        <!-- ============================================================ -->
        <!-- SCREEN 1: GIỎ HÀNG (MATCHING MOCKUP SCREEN 1) -->
        <!-- ============================================================ -->
        <div id="cart-step-1-view">
            
            <!-- Mobile Top Header Bar (Matching Mockup 9:41 Screen 1) -->
            <div class="lg:hidden flex items-center justify-between py-2 mb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <a href="/" class="p-1 -ml-1 text-gray-800 hover:text-[#ea384c]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">
                        Giỏ hàng <span class="text-gray-500 font-bold text-sm" id="cart-header-count">({{ $cart->total_items_count }})</span>
                    </h1>
                </div>
                <button id="btn-remove-selected-top" class="text-xs font-semibold text-gray-500 hover:text-[#ea384c] flex items-center gap-1 transition-colors cursor-pointer p-1">
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
                        <input type="checkbox" id="select-all-desktop-top" class="w-4.5 h-4.5 rounded text-[#ea384c] focus:ring-rose-400 border-gray-300 accent-[#ea384c] cursor-pointer" {{ $cart->items->count() > 0 && $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
                        <span>Chọn tất cả</span>
                    </label>
                    <button id="btn-remove-selected-desktop" class="text-xs font-bold text-gray-500 hover:text-[#ea384c] flex items-center gap-1.5 transition-colors cursor-pointer py-1 px-3 rounded-lg hover:bg-rose-50/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Xóa đã chọn</span>
                    </button>
                </div>
            </div>

            <!-- Mobile "Chọn tất cả" Row (Right below top bar, matching Mockup) -->
            <div class="lg:hidden flex items-center justify-between py-1.5 px-1 mb-2">
                <label class="flex items-center gap-2.5 text-xs font-bold text-gray-800 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-mobile-top" class="w-4.5 h-4.5 rounded text-[#ea384c] focus:ring-rose-400 border-gray-300 accent-[#ea384c] cursor-pointer" {{ $cart->items->count() > 0 && $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
                    <span>Chọn tất cả</span>
                </label>
                <button id="btn-remove-selected-mobile" class="text-[11px] font-semibold text-gray-400 hover:text-[#ea384c] flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Xóa đã chọn</span>
                </button>
            </div>

            <!-- Grid Layout: Left Items Column (8 cols) / Right Summary Column (4 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                <!-- LEFT COLUMN: STORE GROUPS & ITEMS -->
                <div class="lg:col-span-8 space-y-3.5">
                    
                    @if($cart->items->isEmpty())
                    <!-- Empty Cart State -->
                    <div class="bg-white rounded-2xl p-10 text-center border border-gray-100 shadow-2xs">
                        <div class="w-16 h-16 bg-rose-50 text-[#ea384c] rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900">Giỏ hàng của bạn đang trống</h3>
                        <p class="text-xs text-gray-400 mt-1 mb-5">Khám phá ngay hàng ngàn sản phẩm ưu đãi tại ShopMart!</p>
                        <a href="/" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl shadow-xs transition-all active:scale-95">
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
                                <input type="checkbox" data-store-checkbox="{{ $storeId }}" class="w-4.5 h-4.5 rounded text-[#ea384c] focus:ring-rose-400 border-gray-300 accent-[#ea384c] cursor-pointer" {{ $storeAllSelected ? 'checked' : '' }}>
                                
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
                                    <span class="px-1.5 py-0.2 bg-[#ea384c] text-white text-[9px] font-black rounded uppercase">Mall</span>
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
                            <div class="p-3 sm:p-4 flex items-start gap-3" data-cart-item-row="{{ $item->id }}">
                                
                                <!-- Checkbox -->
                                <input type="checkbox" data-item-checkbox="{{ $item->id }}" data-store-id="{{ $storeId }}" class="w-4.5 h-4.5 rounded text-[#ea384c] focus:ring-rose-400 border-gray-300 accent-[#ea384c] cursor-pointer mt-5 shrink-0" {{ $item->is_selected ? 'checked' : '' }}>

                                <!-- Fixed Size 76x76 Thumbnail with Full-Bleed Image (Fill edge-to-edge) -->
                                <a href="{{ route('product.detail', $prod->slug) }}" class="cart-img-box rounded-xl bg-gray-100 border border-gray-100 shrink-0 overflow-hidden group block relative">
                                    <img src="{{ $currentColorImg }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-300" loading="lazy">
                                </a>

                                <!-- Details & Counter -->
                                <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch">
                                    
                                    <div>
                                        <!-- Product Name -->
                                        <a href="{{ route('product.detail', $prod->slug) }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-[#ea384c] transition-colors line-clamp-1 leading-snug">
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
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200/90 text-gray-700 text-[11px] sm:text-xs font-medium border border-transparent hover:border-gray-200 transition-all cursor-pointer group/vb shadow-3xs max-w-full"
                                                title="Nhấp để đổi phiên bản / phân loại (màu sắc, kích thước)"
                                            >
                                                <span class="text-gray-400 text-[11px]">Phân loại:</span>
                                                <span data-variant-display="{{ $item->id }}" class="text-gray-800 font-semibold text-[11px] truncate max-w-[170px] sm:max-w-[240px]">
                                                    {{ $item->selected_variant ?: 'Chọn phân loại' }}
                                                </span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover/vb:text-[#ea384c] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                        <!-- Price -->
                                        <div class="flex items-baseline gap-2 mt-1">
                                            <span class="text-xs sm:text-sm font-black text-[#ea384c]">{{ $item->formatted_unit_price }}</span>
                                            @if($prod->original_price && $prod->original_price > $prod->price)
                                            <span class="text-[10px] text-gray-400 line-through">{{ number_format($prod->original_price, 0, ',', '.') }}₫</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Bottom row: Stepper [- 1 +] & Trash icon (Matching Mockup) -->
                                    <div class="flex items-center justify-between pt-1.5 mt-1 border-t border-gray-50">
                                        <!-- Quantity Stepper -->
                                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-3xs">
                                            <button data-qty-btn data-action="decrement" data-item-id="{{ $item->id }}" class="w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-[#ea384c] hover:bg-rose-50 transition-colors cursor-pointer" aria-label="Giảm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                            </button>
                                            <span class="w-7 sm:w-8 text-center text-xs font-bold text-gray-900 select-none">
                                                {{ $item->quantity }}
                                            </span>
                                            <button data-qty-btn data-action="increment" data-item-id="{{ $item->id }}" class="w-6 h-6 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-[#ea384c] hover:bg-rose-50 transition-colors cursor-pointer" aria-label="Tăng">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            </button>
                                        </div>

                                        <!-- Trash Icon Button -->
                                        <button data-remove-item data-item-id="{{ $item->id }}" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-[#ea384c] transition-colors cursor-pointer" title="Xóa sản phẩm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @endif

                    <!-- Red Promo Voucher Banner (Matching Mockup: Bạn có mã giảm giá?) -->
                    <div class="bg-white rounded-2xl p-4 border border-rose-100/80 shadow-2xs flex items-center justify-between transition-all hover:border-rose-300 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 text-[#ea384c] flex items-center justify-center shrink-0 border border-rose-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-extrabold text-[#ea384c]">Bạn có mã giảm giá?</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5">Chọn hoặc nhập mã ở bước thanh toán</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>

                    <!-- "Có thể bạn cũng thích" Recommendations (Matching Mockup Screen 1) -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs sm:text-sm font-extrabold text-gray-900">Có thể bạn cũng thích</h3>
                            <a href="/" class="text-xs font-bold text-[#ea384c] hover:underline flex items-center gap-0.5">
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
                                    <span class="absolute top-2.5 left-2.5 z-10 px-2 py-0.5 bg-[#ea384c] text-white text-[10px] font-extrabold rounded-md shadow-xs pointer-events-none">
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
                                        <h4 class="text-xs font-semibold text-gray-800 line-clamp-2 leading-snug group-hover:text-[#ea384c] transition-colors mb-1.5 min-h-[32px]">
                                            {{ $rec->name }}
                                        </h4>
                                        <div class="flex items-baseline gap-1.5 mb-1">
                                            <span class="text-sm font-extrabold text-[#ea384c]">{{ $rec->formatted_price }}</span>
                                            @if($rec->original_price)
                                            <span class="text-[10px] text-gray-400 line-through">{{ $rec->formatted_original_price }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between text-[11px] text-gray-400">
                                            <span class="text-amber-500 font-semibold">⭐ {{ number_format($rec->rating ?? 5.0, 1) }}</span>
                                            <span>Đã bán {{ $rec->formatted_sold ?? '1.2k' }}</span>
                                        </div>
                                    </a>

                                    <!-- Dual Action Buttons: Thêm giỏ & Mua ngay -->
                                    <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-gray-100 mt-1">
                                        <button 
                                            data-add-to-cart 
                                            data-product-id="{{ $rec->id }}"
                                            data-product-name="{{ $rec->name }}"
                                            class="py-1.5 px-1 bg-gray-100 hover:bg-[#ea384c] hover:text-white rounded-xl text-[11px] font-bold transition-all text-gray-700 active:scale-95 flex items-center justify-center gap-1 cursor-pointer"
                                            aria-label="Thêm {{ $rec->name }} vào giỏ"
                                            title="Thêm vào giỏ"
                                        >
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span class="truncate">Thêm</span>
                                        </button>
                                        <a 
                                            href="{{ route('product.detail', $rec->slug) }}"
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

                </div>

                <!-- RIGHT COLUMN: DESKTOP ORDER SUMMARY CARD -->
                <div class="hidden lg:block lg:col-span-4 sticky top-24">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h3 class="text-sm font-black text-gray-900 border-b border-gray-100 pb-3 uppercase tracking-wider">Tóm tắt đơn hàng</h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between text-gray-500">
                                <span>Tạm tính (<span id="desktop-summary-qty">{{ $cart->selected_count }}</span> sản phẩm):</span>
                                <span class="font-bold text-gray-900" id="desktop-summary-subtotal">{{ $cart->formatted_selected_total }}</span>
                            </div>

                            @if($cart->savings_total > 0)
                            <div class="flex items-center justify-between text-emerald-600 font-semibold">
                                <span>Giảm giá tiết kiệm:</span>
                                <span id="desktop-summary-savings">-{{ number_format($cart->savings_total, 0, ',', '.') }}₫</span>
                            </div>
                            @endif

                            <div class="flex items-center justify-between text-gray-500">
                                <span>Phí vận chuyển:</span>
                                <span class="text-emerald-600 font-bold">Miễn phí</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-baseline justify-between mb-1">
                                <span class="text-xs font-bold text-gray-700">Tổng thanh toán:</span>
                                <span class="text-xl font-black text-[#ea384c]" id="desktop-summary-total">{{ $cart->formatted_selected_total }}</span>
                            </div>
                            @if($cart->original_selected_total > $cart->selected_total)
                            <div class="text-right text-[11px] text-gray-400">
                                <span class="line-through">{{ $cart->formatted_original_selected_total }}</span>
                                <span class="ml-1 text-[#ea384c] font-bold">-{{ $cart->savings_percent }}%</span>
                            </div>
                            @endif
                        </div>

                        <button id="btn-proceed-checkout-desktop" class="w-full py-3 bg-[#ea384c] hover:bg-[#d3273b] text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 active:scale-98 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" {{ $cart->selected_count == 0 ? 'disabled' : '' }}>
                            <span>Thanh toán</span>
                            <span id="desktop-checkout-count-badge">({{ $cart->selected_count }})</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>

                        <div class="flex items-center justify-center gap-3 pt-1 text-[11px] text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Chính hãng 100%
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Miễn phí đổi trả
                            </span>
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
                    <button id="btn-back-to-cart" class="flex items-center gap-1 text-sm font-black text-gray-900 hover:text-[#ea384c] cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Giỏ hàng ({{ $cart->selected_count }})</span>
                    </button>
                    <span class="text-xs text-gray-400 font-semibold">Bước 2/3</span>
                </div>

                <!-- 3-Step Progress Stepper -->
                <div class="flex items-center justify-center max-w-sm mx-auto px-4">
                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full bg-[#ea384c] text-white text-[11px] font-black flex items-center justify-center shadow-xs">1</div>
                        <span class="text-[11px] font-black text-[#ea384c] mt-1">Giỏ hàng</span>
                    </div>

                    <div class="flex-1 h-0.5 bg-[#ea384c] mx-2 -mt-4"></div>

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
                    
                    <!-- 1. Shipping Address Card (Matching Screen 2 Mockup) -->
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-rose-50 text-[#ea384c] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-900">Địa chỉ nhận hàng</h3>
                                <div class="text-xs font-bold text-gray-800 mt-0.5">
                                    Nguyễn Văn A <span class="text-gray-300 mx-1">|</span> 0123 456 789
                                </div>
                                <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">
                                    Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh
                                </p>
                            </div>
                        </div>
                        <button class="text-xs font-bold text-gray-400 hover:text-[#ea384c] shrink-0 pt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

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
                                        <span class="text-xs font-extrabold text-[#ea384c] mt-1 block">{{ $item->formatted_unit_price }}</span>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-gray-400 shrink-0">x{{ $item->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Voucher Options (Matching Screen 2 Mockup) -->
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3">
                        <!-- Voucher Shop -->
                        <div class="flex items-center justify-between text-xs py-0.5 hover:text-[#ea384c] transition-colors cursor-pointer">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-md bg-rose-50 text-[#ea384c] flex items-center justify-center text-xs">🎟️</span>
                                <span class="font-bold text-gray-800">Voucher của Shop</span>
                            </div>
                            <span class="text-xs font-bold text-blue-600 flex items-center gap-0.5">
                                <span>Chọn mã</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>

                        <!-- Voucher Sàn -->
                        <div class="flex items-center justify-between text-xs py-0.5 hover:text-[#ea384c] transition-colors cursor-pointer border-t border-gray-50 pt-2.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-md bg-orange-50 text-orange-500 flex items-center justify-center text-xs">🧧</span>
                                <span class="font-bold text-gray-800">Voucher toàn sàn</span>
                            </div>
                            <span class="text-xs font-bold text-blue-600 flex items-center gap-0.5">
                                <span>Chọn mã</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>

                        <!-- ShopMart Points Switch (Matching Screen 2 Mockup: Dùng 500.000 điểm (-500.000đ)) -->
                        <div class="flex items-center justify-between text-xs border-t border-gray-50 pt-2.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-500 flex items-center justify-center text-xs">🪙</span>
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

                        <!-- Promo Code Input (Matching Screen 2: Nhập mã giảm giá >) -->
                        <div class="flex items-center justify-between text-xs border-t border-gray-50 pt-2.5 cursor-pointer">
                            <div class="flex items-center gap-2.5 text-gray-700 font-bold">
                                <span class="w-5 h-5 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center text-xs">🏷️</span>
                                <span>Nhập mã giảm giá</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>

                    <!-- 4. Payment Method (Matching Screen 2 Mockup) -->
                    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black text-gray-900">Phương thức thanh toán</h3>
                            <span class="text-xs font-bold text-blue-600 cursor-pointer">Xem tất cả ></span>
                        </div>

                        <div class="space-y-2">
                            <!-- ShopMart Wallet -->
                            <label class="flex items-center justify-between p-3 rounded-xl border-2 border-[#ea384c] bg-rose-50/20 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#ea384c] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-gray-900">Ví ShopMart</div>
                                        <div class="text-[11px] text-gray-400">Số dư: 2.000.000₫</div>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="wallet" checked class="w-4.5 h-4.5 accent-[#ea384c]">
                            </label>

                            <!-- Bank Card -->
                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-gray-300 bg-white cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-gray-900">Thẻ ngân hàng</div>
                                        <div class="text-[11px] text-gray-400">Visa, Mastercard, JCB</div>
                                    </div>
                                </div>
                                <input type="radio" name="payment_method" value="card" class="w-4.5 h-4.5 accent-[#ea384c]">
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
                                <span class="font-bold text-[#ea384c]" id="billing-voucher">- 650.000₫</span>
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
                                <span class="text-xl font-black text-[#ea384c]" id="billing-grand-total">
                                    {{ number_format(max(0, $cart->selected_total - 650000), 0, ',', '.') }}₫
                                </span>
                            </div>

                            <!-- Green Savings Pill (Matching Mockup: Bạn tiết kiệm được 650.000đ) -->
                            <div class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-2 rounded-xl flex items-center gap-1.5 border border-emerald-100">
                                <span>🌱 Bạn tiết kiệm được</span>
                                <span id="billing-total-savings">650.000₫</span>
                            </div>
                        </div>

                        <!-- Big Red Button: Đặt hàng (Matching Mockup Screen 2) -->
                        <button id="btn-place-order" class="w-full py-3.5 bg-[#ea384c] hover:bg-[#d3273b] text-white font-black text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 active:scale-98 cursor-pointer">
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
                    <a href="/" class="w-full py-3 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-black rounded-xl shadow-xs transition-all text-center">
                        Tiếp tục mua sắm
                    </a>
                    <a href="{{ route('cart') }}" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all text-center">
                        Xem lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- ==================== FIXED MOBILE BOTTOM CHECKOUT BAR (SCREEN 1 MOCKUP) ==================== -->
    <div id="mobile-sticky-checkout-bar" class="lg:hidden fixed bottom-14 left-0 right-0 z-40 bg-white border-t border-gray-200 px-3.5 py-2.5 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] flex items-center justify-between gap-3">
        <label class="flex items-center gap-2 cursor-pointer select-none shrink-0">
            <input type="checkbox" id="mobile-select-all-bottom" class="w-4.5 h-4.5 rounded text-[#ea384c] focus:ring-rose-400 border-gray-300 accent-[#ea384c]" {{ $cart->items->count() > 0 && $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
            <span class="text-xs font-bold text-gray-800">Chọn tất cả ({{ $cart->selected_count }})</span>
        </label>

        <div class="text-right min-w-0">
            <div class="text-[10px] text-gray-400">Tổng tiền:</div>
            <div class="text-xs sm:text-sm font-black text-[#ea384c] truncate" id="mobile-sticky-total">
                {{ $cart->formatted_selected_total }}
            </div>
        </div>

        <button id="btn-mobile-checkout-submit" class="py-2.5 px-5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-black rounded-xl shadow-md transition-all active:scale-95 shrink-0 disabled:opacity-50 disabled:cursor-not-allowed" {{ $cart->selected_count == 0 ? 'disabled' : '' }}>
            Thanh toán ({{ $cart->selected_count }})
        </button>
    </div>

    <!-- ==================== MOBILE BOTTOM APP NAVIGATION (MATCHING SCREEN 1 MOCKUP) ==================== -->
    <nav id="mobile-bottom-app-nav" class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 px-3 py-1.5 flex items-center justify-around shadow-lg">
        <a href="/" class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors">
            <svg class="w-5 h-5 mb-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            <span>Trang chủ</span>
        </a>

        <a href="/#categories" class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Danh mục</span>
        </a>

        <a href="{{ route('cart') }}" class="flex flex-col items-center text-[#ea384c] text-[10px] font-bold py-1 relative">
            <div class="relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge-count absolute -top-1.5 -right-2.5 min-w-[15px] h-[15px] px-1 bg-[#ea384c] text-white text-[9px] font-bold rounded-full flex items-center justify-center shadow-xs">
                    {{ $cart->display_count }}
                </span>
            </div>
            <span>Giỏ hàng</span>
        </a>

        <a href="/#wishlist" class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors relative">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span>Yêu thích</span>
        </a>

        <a href="/#account" class="flex flex-col items-center text-gray-500 hover:text-[#ea384c] text-[10px] font-medium py-1 transition-colors">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Tài khoản</span>
        </a>
    </nav>

</body>
</html>
