@php
    $isFreeship = $isFreeship ?? (str_contains(strtoupper($cp->code), 'FREESHIP') || str_contains(strtolower($cp->name), 'vận chuyển'));
    $isShop = !empty($cp->store_id);
    $isMall = str_contains(strtoupper($cp->code), 'MALL');
    $isTech = str_contains(strtoupper($cp->code), 'TECH') || str_contains(strtoupper($cp->code), 'DIENTU');
    $isFashion = str_contains(strtoupper($cp->code), 'FASHION');

    $voucherType = 'platform';
    if ($isFreeship) {
        $voucherType = 'freeship';
    } elseif ($isShop) {
        $voucherType = 'shop';
    }

    $catType = 'shopmart';
    if ($isFreeship) $catType = 'freeship';
    elseif ($isShop || $isTech || $isFashion) $catType = 'category';

    $badgeColor = 'bg-rose-500';
    $leftTitle = 'ShopMart';
    if ($isFreeship) {
        $badgeColor = 'bg-teal-500';
        $leftTitle = 'Vận Chuyển';
    } elseif ($isShop) {
        $badgeColor = 'bg-orange-500';
        $leftTitle = 'Shop';
    } elseif ($isMall) {
        $badgeColor = 'bg-red-700';
        $leftTitle = 'Mall';
    } elseif ($isTech) {
        $badgeColor = 'bg-rose-600';
        $leftTitle = 'Điện Tử';
    } elseif ($isFashion) {
        $badgeColor = 'bg-pink-600';
        $leftTitle = 'Thời Trang';
    }
@endphp

<div 
    class="voucher-card relative flex bg-white rounded-lg border border-gray-200 hover:border-orange-400 shadow-2xs overflow-hidden transition-all duration-150"
    data-category="{{ $catType }}"
    data-type="{{ $voucherType }}"
    data-code="{{ $cp->code }}"
>
    <!-- Left Colored Stub -->
    <div class="w-24 sm:w-28 {{ $badgeColor }} text-white p-2.5 flex flex-col justify-between items-center text-center shrink-0 relative">
        <span class="text-[8px] font-extrabold uppercase tracking-tight bg-amber-400 text-amber-950 px-1 py-0.2 rounded-full absolute top-1.5 left-1.5 shadow-2xs">
            Có hạn
        </span>

        <div class="my-auto pt-2">
            @if($isFreeship)
                <span class="text-xs font-black tracking-tighter block leading-tight uppercase">FREE SHIP</span>
            @elseif($isMall)
                <span class="text-xs font-black tracking-tighter block leading-tight uppercase">MALL</span>
            @elseif($isShop)
                <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            @else
                <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            @endif
            <span class="text-[9px] font-bold uppercase tracking-wider block opacity-90 mt-0.5">{{ $leftTitle }}</span>
        </div>

        <span class="text-[8px] opacity-75">Tất cả hình thức</span>
    </div>

    <!-- Perforated circles cutout -->
    <div class="relative w-0 flex flex-col justify-between items-center z-10">
        <div class="w-2.5 h-2.5 bg-slate-50 rounded-full -mt-1.5 -ml-1.25 border-b border-gray-200"></div>
        <div class="h-full border-r border-dashed border-gray-300 my-0.5"></div>
        <div class="w-2.5 h-2.5 bg-slate-50 rounded-full -mb-1.5 -ml-1.25 border-t border-gray-200"></div>
    </div>

    <!-- Right Content -->
    <div class="flex-1 p-2.5 pl-3 flex flex-col justify-between min-w-0">
        <div>
            <div class="flex items-start justify-between gap-1">
                <h5 class="text-xs font-black text-gray-900 leading-snug">
                    @if($cp->discount_type === 'percent')
                        Giảm {{ (int)$cp->discount_value }}% (Tối đa ₫{{ number_format($cp->max_discount_amount ?? 50000, 0, ',', '.') }})
                    @else
                        Giảm ₫{{ number_format($cp->discount_value, 0, ',', '.') }}
                    @endif
                </h5>
                <span class="text-[9px] font-extrabold text-orange-600 bg-orange-50 px-1 py-0.2 rounded shrink-0">
                    {{ $cp->code }}
                </span>
            </div>

            <p class="text-[10px] text-gray-500 mt-0.5">
                Đơn tối thiểu ₫{{ number_format($cp->min_order_value, 0, ',', '.') }}
            </p>

            <div class="flex items-center gap-1.5 mt-1 text-[9px] text-gray-400">
                <span class="text-red-700 font-bold bg-red-50 border border-red-200 px-1 rounded">Chỉ có trên ShopMart</span>
                <span>HSD: {{ $cp->expires_at ? $cp->expires_at->format('d.m.Y') : 'Vô thời hạn' }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-gray-50">
            <div class="text-[9px] text-gray-400">
                <span>Đã dùng {{ $cp->used_count ?? 15 }}%</span>
                <div class="w-14 h-1 bg-gray-100 rounded-full mt-0.5 overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full" style="width: {{ min(100, max(15, (int)($cp->used_count ?? 25))) }}%"></div>
                </div>
            </div>

            <button 
                type="button" 
                class="btn-modal-select-coupon px-3 py-1 border border-primary text-primary hover:bg-primary hover:text-white rounded-md text-xs font-bold transition-colors cursor-pointer"
                data-code="{{ $cp->code }}"
                data-type="{{ $voucherType }}"
                onclick="selectVoucherCard('{{ $cp->code }}', '{{ $voucherType }}')"
            >
                Dùng ngay
            </button>
        </div>
    </div>
</div>
