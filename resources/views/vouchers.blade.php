@extends('layouts.app')

@section('title', 'Kho Voucher & Mã Giảm Giá Độc Quyền - ShopMart')
@section('meta_description', 'Săn mã giảm giá khủng, freeship 0Đ và voucher ShopMart Mall cực hot mỗi ngày tại ShopMart.')

@section('content')
<div class="voucher-page bg-[#f9fafb] min-h-screen pb-16">
    
    <!-- Hero Banner (Concept Design: Clean White Background & Soft Pink Quick Apply) -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Left Title & Intro -->
                <div class="max-w-2xl">
                    <span class="text-xs font-bold text-[#ea384c] tracking-wider uppercase">
                        SIÊU HỘI VOUCHER HÔM NAY
                    </span>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 tracking-tight mt-1.5 leading-tight">
                        Kho Voucher & Mã Giảm Giá ShopMart
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed max-w-xl">
                        Thu thập mã giảm giá vận chuyển 0Đ, ưu đãi giảm giá lên đến 500.000Đ và hàng ngàn voucher độc quyền từ ShopMart Mall.
                    </p>
                </div>

                <!-- Right Quick Apply Box -->
                <div class="bg-[#fff1f2] border border-rose-100/90 rounded-2xl p-5 w-full lg:w-[420px] shrink-0 shadow-xs">
                    <div class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <span class="w-6 h-6 rounded-md bg-[#ea384c] text-white flex items-center justify-center text-xs font-black shadow-xs shrink-0">
                            %
                        </span>
                        <span>Nhập mã voucher nhanh</span>
                    </div>
                    <div class="flex items-center mt-3 bg-white rounded-xl border border-gray-200 overflow-hidden shadow-2xs focus-within:border-[#ea384c] transition">
                        <input 
                            type="text" 
                            id="quick-voucher-input"
                            data-checkout-url="{{ route('checkout.index') }}"
                            placeholder="NHẬP MÃ VOUCHER (VD: FREESHIP)" 
                            class="w-full px-3.5 py-2.5 text-xs font-semibold text-gray-800 placeholder-gray-400 uppercase outline-none bg-transparent"
                        >
                        <button 
                            type="button" 
                            id="btn-apply-quick-voucher"
                            class="bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold px-5 py-2.5 transition active:scale-95 shrink-0"
                        >
                            Áp dụng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Voucher Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        
        <!-- Tab Navigation (Clean Underline Style) -->
        <div class="border-b border-gray-200 flex items-center gap-8 overflow-x-auto scrollbar-none my-6" id="voucher-tab-bar">
            <button 
                type="button" 
                data-category="all" 
                class="voucher-tab-btn pb-3 text-sm font-bold text-[#ea384c] border-b-2 border-[#ea384c] transition whitespace-nowrap"
            >
                Tất cả mã ({{ $coupons->count() }})
            </button>
            <button 
                type="button" 
                data-category="freeship" 
                class="voucher-tab-btn pb-3 text-sm font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent transition whitespace-nowrap"
            >
                Miễn Phí Vận Chuyển ({{ $freeshipCoupons->count() }})
            </button>
            <button 
                type="button" 
                data-category="mall" 
                class="voucher-tab-btn pb-3 text-sm font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent transition whitespace-nowrap"
            >
                ShopMart Mall ({{ $mallCoupons->count() }})
            </button>
            <button 
                type="button" 
                data-category="category" 
                class="voucher-tab-btn pb-3 text-sm font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent transition whitespace-nowrap"
            >
                Công Nghệ & Thời Trang ({{ $categoryCoupons->count() }})
            </button>
        </div>

        <!-- Voucher Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="voucher-grid">
            @forelse($coupons as $coupon)
            @php
                $isFreeship = str_contains(strtoupper($coupon->code), 'FREESHIP') || str_contains(strtolower($coupon->name), 'vận chuyển');
                $isMall = str_contains(strtoupper($coupon->code), 'MALL');
                $isCategory = str_contains(strtoupper($coupon->code), 'TECH') || str_contains(strtoupper($coupon->code), 'DIENTU') || str_contains(strtoupper($coupon->code), 'FASHION');
                
                $categoryTag = 'other';
                if ($isFreeship) $categoryTag = 'freeship';
                elseif ($isMall) $categoryTag = 'mall';
                elseif ($isCategory) $categoryTag = 'category';

                $badgeText = $isFreeship ? 'FREESHIP' : ($isMall ? 'SHOPMART MALL' : 'GIẢM GIÁ');
                $stubBg = $isFreeship ? 'bg-[#059669]' : ($isMall ? 'bg-[#7c3aed]' : 'bg-[#ea384c]');

                $discountVal = (float)($coupon->discount_value ?? $coupon->value ?? 0);
                $discountType = $coupon->discount_type ?? $coupon->type ?? 'fixed';

                if ($discountType === 'percent' && $discountVal > 0) {
                    $stubValueText = 'Giảm ' . (int)$discountVal . '%';
                } elseif ($discountVal > 0) {
                    $stubValueText = 'Giảm ' . number_format($discountVal / 1000, 0) . 'k';
                } else {
                    $stubValueText = 'Giảm 0k';
                }
            @endphp
            <div 
                class="voucher-card group bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition-all duration-200 flex overflow-hidden min-h-[142px]"
                data-category="{{ $categoryTag }}"
                data-code="{{ $coupon->code }}"
            >
                <!-- Left Ticket Stub -->
                <div class="w-32 sm:w-36 shrink-0 {{ $stubBg }} text-white p-3 flex flex-col items-center justify-center text-center relative select-none">
                    <div class="mb-1.5">
                        @if($isFreeship)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        @elseif($isMall)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        @endif
                    </div>
                    
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-white/90 leading-tight">
                        {{ $badgeText }}
                    </span>
                    <span class="text-base sm:text-lg font-black leading-tight mt-0.5">
                        {{ $stubValueText }}
                    </span>
                </div>

                <!-- Right Ticket Details -->
                <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between min-w-0 bg-white">
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900 truncate leading-snug" title="{{ $coupon->name }}">
                            {{ $coupon->name }}
                        </h3>
                        
                        <p class="text-[11.5px] text-gray-500 mt-0.5 line-clamp-1 leading-relaxed">
                            {{ $coupon->description ?? 'Áp dụng cho mọi đơn hàng hợp lệ tại ShopMart' }}
                        </p>

                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="bg-gray-100 text-gray-500 text-[10.5px] px-2 py-0.5 rounded font-medium">
                                Đơn tối thiểu {{ number_format((float)($coupon->min_order_value ?? 0), 0, ',', '.') }}đ
                            </span>
                            @if($coupon->max_discount_amount)
                            <span class="bg-gray-100 text-gray-500 text-[10.5px] px-2 py-0.5 rounded font-medium">
                                Tối đa {{ number_format((float)$coupon->max_discount_amount / 1000, 0) }}k
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Voucher Code & Copy Action -->
                    <div class="flex items-center justify-between gap-2 mt-3 pt-1">
                        <button 
                            type="button" 
                            data-copy-voucher="{{ $coupon->code }}"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-gray-200 bg-gray-50/80 hover:bg-gray-100 text-gray-700 text-xs font-mono font-bold transition group"
                            title="Sao chép mã"
                        >
                            <span>{{ $coupon->code }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                        
                        <a 
                            href="{{ route('checkout.index') }}?coupon={{ $coupon->code }}" 
                            class="inline-flex items-center justify-center px-4 py-1.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-lg transition active:scale-95 shadow-2xs whitespace-nowrap"
                        >
                            Dùng ngay
                        </a>
                    </div>

                </div>

            </div>
            @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100 shadow-xs">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-[#ea384c] mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Chưa có mã giảm giá nào</h3>
                <p class="text-xs text-gray-500 mt-1">Các voucher siêu ưu đãi sẽ sớm xuất hiện tại đây!</p>
            </div>
            @endforelse
        </div>

    </div>

</div>

<!-- Floating Toast Notification -->
<div id="voucher-toast" class="voucher-toast">
    <div class="bg-gray-900/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/10 text-xs font-bold">
        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span id="voucher-toast-msg">Đã sao chép mã thành công!</span>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/vouchers.js'])
@endpush
