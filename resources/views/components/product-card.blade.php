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
    <div class="product-card__image-box">
        <!-- Badges -->
        <div class="product-card__badges">
            @if($product->is_mall)
                <span class="product-card__badge-mall">Mall</span>
            @endif
            @if($product->discount_percent > 0)
                <span class="product-card__badge-discount">-{{ $product->discount_percent }}%</span>
            @endif
        </div>

        <a href="{{ route('product.detail', $product->slug) }}" class="block w-full h-full">
            <img 
                src="{{ $product->main_image_url }}" 
                alt="{{ $product->name }}" 
                class="product-card__image"
                loading="lazy"
            >
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/[0.03] transition-colors pointer-events-none"></div>
        </a>
    </div>

    <!-- Bottom Content Info -->
    <div class="product-card__body">
        <a href="{{ route('product.detail', $product->slug) }}" class="block">
            <div class="flex items-center gap-1.5 mb-1 overflow-hidden">
                @if(!$isFlashSale && $product->badge_text)
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-sm shrink-0">
                        {{ $product->badge_text }}
                    </span>
                @endif
                @if($product->store)
                    <span class="text-[10px] text-gray-400 font-medium truncate">
                        {{ $product->store->name }}
                    </span>
                @endif
            </div>

            <h3 class="product-card__title">
                {{ $product->name }}
            </h3>

            <div class="product-card__price-row">
                <span class="product-card__price">{{ $product->formatted_price }}</span>
                @if($product->original_price)
                    <span class="product-card__price-original">{{ $product->formatted_original_price }}</span>
                @endif
            </div>

            @if($isFlashSale)
                <div class="space-y-1">
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-primary h-2 rounded-full" style="width: {{ min(100, $product->flash_sale_percent ?? 65) }}%"></div>
                    </div>
                    <span class="text-[10px] font-medium text-gray-400 block">Đã bán {{ $product->formatted_sold }}</span>
                </div>
            @else
                <div class="product-card__footer">
                    <span class="product-card__rating">
                        <x-icon name="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                        <span>{{ number_format((float)$product->rating, 1) }}</span>
                    </span>
                    <span class="product-card__sold">Đã bán {{ $product->formatted_sold }}</span>
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
                class="product-card__btn-add"
                aria-label="Thêm {{ $product->name }} vào giỏ"
                title="Thêm vào giỏ"
            >
                <x-icon name="plus" class="w-3.5 h-3.5 shrink-0" />
                <span class="truncate">Thêm</span>
            </button>
            <a 
                href="{{ route('product.detail', $product->slug) }}"
                class="product-card__btn-buy"
                title="Mua ngay"
            >
                <x-icon name="bolt" class="w-3.5 h-3.5 shrink-0" />
                <span class="truncate">Mua ngay</span>
            </a>
        </div>
    </div>
</div>
