@props([
    'product',
    'isFlashSale' => false,
    'class' => '',
])

<div 
    @if(!$isFlashSale) data-product-category="{{ $product->category?->slug ?? 'all' }}" @endif
    class="{{ $isFlashSale ? 'shrink-0 w-[170px] sm:w-auto snap-start' : '' }} bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-xl hover:border-rose-200 transition-all duration-300 flex flex-col justify-between group overflow-hidden relative {{ $class }}"
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
                        <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
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
