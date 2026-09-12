@props([
    'product',
    'isFlashSale' => false,
    'class' => '',
])

<div 
    @if(!$isFlashSale) data-product-category="{{ $product->category?->slug ?? 'all' }}" @endif
    class="product-card {{ $isFlashSale ? 'shrink-0 w-[170px] sm:w-auto snap-start' : '' }} group {{ $class }}"
>
    <!-- Top Full Bleed Image Container -->
    <div class="relative w-full aspect-square overflow-hidden bg-gray-100">
        <!-- Badges -->
        <div class="absolute top-2.5 left-2.5 z-10 flex flex-col gap-1 pointer-events-none">
            @if($product->is_mall)
                <span class="px-2 py-0.5 bg-[#ea384c] text-white text-[10px] font-black rounded-md shadow-xs uppercase tracking-wider">
                    Mall
                </span>
            @endif
            @if($product->discount_percent > 0)
                <span class="px-2 py-0.5 bg-[#ea384c] text-white text-[10px] font-extrabold rounded-md shadow-xs">
                    -{{ $product->discount_percent }}%
                </span>
            @endif
        </div>

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
            @if(!$isFlashSale && $product->badge_text)
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-sm mb-1 inline-block">
                    {{ $product->badge_text }}
                </span>
            @endif

            <h3 class="text-xs sm:text-sm font-semibold text-gray-800 line-clamp-2 leading-snug group-hover:text-[#ea384c] transition-colors mb-1.5 min-h-[32px]">
                {{ $product->name }}
            </h3>

            <div class="flex items-baseline gap-1.5 mb-1">
                <span class="text-sm sm:text-base font-extrabold text-[#ea384c]">{{ $product->formatted_price }}</span>
                @if($product->original_price)
                    <span class="text-[10px] sm:text-[11px] text-gray-400 line-through">{{ $product->formatted_original_price }}</span>
                @endif
            </div>

            @if($isFlashSale)
                <div class="space-y-1">
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-[#ea384c] h-2 rounded-full" style="width: {{ min(100, $product->flash_sale_percent ?? 65) }}%"></div>
                    </div>
                    <span class="text-[10px] font-medium text-gray-400 block">Đã bán {{ $product->formatted_sold }}</span>
                </div>
            @else
                <div class="flex items-center justify-between text-[11px] text-gray-400">
                    <span class="inline-flex items-center gap-1 text-amber-500 font-semibold">
                        <x-icon name="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                        <span>{{ number_format((float)$product->rating, 1) }}</span>
                    </span>
                    <span>Đã bán {{ $product->formatted_sold }}</span>
                </div>
            @endif
        </a>

        <!-- Dual Action Buttons: Thêm giỏ & Mua ngay -->
        <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-gray-100 mt-1">
            <button 
                type="button"
                data-add-to-cart 
                data-product-id="{{ $product->id }}"
                data-product-name="{{ $product->name }}"
                class="py-1.5 px-1 bg-gray-100 hover:bg-[#ea384c] hover:text-white text-gray-700 rounded-xl text-[11px] font-bold transition-all flex items-center justify-center gap-1 active:scale-95 cursor-pointer"
                aria-label="Thêm {{ $product->name }} vào giỏ"
                title="Thêm vào giỏ"
            >
                <x-icon name="plus" class="w-3.5 h-3.5 shrink-0" />
                <span class="truncate">Thêm</span>
            </button>
            <a 
                href="{{ route('product.detail', $product->slug) }}"
                class="py-1.5 px-1 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-xl text-[11px] font-bold transition-all flex items-center justify-center gap-1 active:scale-95 shadow-xs text-center cursor-pointer"
                title="Mua ngay"
            >
                <x-icon name="bolt" class="w-3.5 h-3.5 shrink-0" />
                <span class="truncate">Mua ngay</span>
            </a>
        </div>
    </div>
</div>
