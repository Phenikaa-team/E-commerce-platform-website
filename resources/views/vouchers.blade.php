@extends('layouts.app')

@section('title', 'Kho Voucher & Mã Giảm Giá Độc Quyền - ShopMart')
@section('meta_description', 'Săn mã giảm giá khủng, freeship 0Đ và voucher ShopMart Mall cực hot mỗi ngày tại ShopMart.')

@section('content')
<div class="min-h-screen bg-[#f8f9fd] pb-16">
    
    <!-- Hero Banner (Shopee Aesthetic) -->
    <div class="relative overflow-hidden bg-gradient-to-r from-[#ea384c] via-[#f24e5e] to-[#ff6b6b] text-white py-10 px-4 sm:px-6 lg:px-8 shadow-md">
        <!-- Background decorative rings -->
        <div class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-64 h-64 rounded-full bg-amber-400/20 blur-2xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold mb-3">
                        <span>SIÊU HỘI VOUCHER HÔM NAY</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-ping"></span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-black tracking-tight drop-shadow-xs">
                        Kho Voucher & Mã Giảm Giá ShopMart
                    </h1>
                    <p class="text-white/90 text-xs sm:text-sm mt-2 max-w-xl leading-relaxed">
                        Thu thập mã giảm giá vận chuyển 0Đ, ưu đãi giảm giá lên đến 500.000đ và hàng ngàn voucher độc quyền từ ShopMart Mall.
                    </p>
                </div>

                <!-- Fast Coupon Apply / Search Card -->
                <div class="w-full md:w-auto bg-white/10 backdrop-blur-md p-3.5 sm:p-4 rounded-2xl border border-white/20 shadow-lg shrink-0">
                    <p class="text-xs font-bold text-white mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Nhập mã voucher nhanh
                    </p>
                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            id="quick-voucher-input"
                            placeholder="Nhập mã voucher (VD: FREESHIP)" 
                            class="uppercase text-xs font-bold bg-white text-gray-800 px-3.5 py-2.5 rounded-xl border-none focus:outline-none focus:ring-2 focus:ring-amber-300 placeholder:text-gray-400 placeholder:normal-case w-56 sm:w-64"
                        >
                        <button 
                            type="button" 
                            onclick="applyQuickVoucher()"
                            class="px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-gray-900 text-xs font-black rounded-xl transition-all shadow-md active:scale-95 cursor-pointer whitespace-nowrap"
                        >
                            Áp dụng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Voucher Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        
        <!-- Tab Navigation (Shopee style sticky category pills) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-3 scrollbar-none border-b border-gray-200/80 mb-6" id="voucher-tab-bar">
            <button 
                type="button" 
                onclick="filterVoucherTab('all', this)" 
                class="tab-btn active-tab px-4 py-2 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer bg-[#ea384c] text-white shadow-xs"
            >
                Tất cả mã ({{ $coupons->count() }})
            </button>
            <button 
                type="button" 
                onclick="filterVoucherTab('freeship', this)" 
                class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-white text-gray-600 hover:bg-gray-100 border border-gray-200"
            >
                Miễn Phí Vận Chuyển ({{ $freeshipCoupons->count() }})
            </button>
            <button 
                type="button" 
                onclick="filterVoucherTab('mall', this)" 
                class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-white text-gray-600 hover:bg-gray-100 border border-gray-200"
            >
                ShopMart Mall ({{ $mallCoupons->count() }})
            </button>
            <button 
                type="button" 
                onclick="filterVoucherTab('category', this)" 
                class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-white text-gray-600 hover:bg-gray-100 border border-gray-200"
            >
                Công Nghệ & Thời Trang ({{ $categoryCoupons->count() }})
            </button>
        </div>

        <!-- Voucher Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="voucher-grid">
            @forelse($coupons as $coupon)
            @php
                $isFreeship = str_contains(strtoupper($coupon->code), 'FREESHIP') || str_contains(strtolower($coupon->name), 'vận chuyển');
                $isMall = str_contains(strtoupper($coupon->code), 'MALL');
                $isCategory = str_contains(strtoupper($coupon->code), 'TECH') || str_contains(strtoupper($coupon->code), 'DIENTU') || str_contains(strtoupper($coupon->code), 'FASHION');
                
                $categoryTag = 'other';
                if ($isFreeship) $categoryTag = 'freeship';
                elseif ($isMall) $categoryTag = 'mall';
                elseif ($isCategory) $categoryTag = 'category';

                $themeColor = $isFreeship ? 'emerald' : ($isMall ? 'purple' : 'rose');
                $badgeText = $isFreeship ? 'FREESHIP' : ($isMall ? 'MALL' : 'GIẢM GIÁ');
            @endphp
            <div 
                class="voucher-card bg-white rounded-2xl border border-gray-200/90 shadow-2xs hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col justify-between group relative"
                data-category="{{ $categoryTag }}"
                data-code="{{ $coupon->code }}"
            >
                <!-- Shopee Serrated Ticket Body -->
                <div class="flex items-stretch flex-1">
                    
                    <!-- Left Ticket Stub -->
                    <div class="w-28 sm:w-32 bg-gradient-to-br {{ $isFreeship ? 'from-emerald-500 to-teal-600' : ($isMall ? 'from-purple-600 to-indigo-600' : 'from-[#ea384c] to-rose-600') }} text-white p-3 flex flex-col items-center justify-center text-center relative shrink-0">
                        <!-- Left Icon -->
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mb-1.5">
                            @if($isFreeship)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            @elseif($isMall)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            @endif
                        </div>
                        
                        <span class="text-[10px] font-black uppercase tracking-wider text-white/90">{{ $badgeText }}</span>
                        <div class="font-black text-sm sm:text-base leading-tight mt-0.5">
                            @if($coupon->type === 'percent')
                                Giảm {{ $coupon->value }}%
                            @else
                                Giảm {{ number_format($coupon->value / 1000, 0) }}k
                            @endif
                        </div>

                        <!-- Ticket Notch Decorators -->
                        <div class="absolute -right-2 top-0 bottom-0 flex flex-col justify-between py-1 z-10">
                            <div class="w-3.5 h-3.5 rounded-full bg-[#f8f9fd] -mr-1.5 -mt-1.5"></div>
                            <div class="w-3.5 h-3.5 rounded-full bg-[#f8f9fd] -mr-1.5 -mb-1.5"></div>
                        </div>
                    </div>

                    <!-- Right Ticket Details -->
                    <div class="p-3.5 flex-1 flex flex-col justify-between relative pl-4">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-black text-xs sm:text-sm text-gray-900 line-clamp-1 group-hover:text-[#ea384c] transition-colors">
                                    {{ $coupon->name }}
                                </h3>
                            </div>
                            
                            <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                                {{ $coupon->description ?? 'Áp dụng cho mọi đơn hàng hợp lệ tại ShopMart' }}
                            </p>

                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold rounded-md">
                                    Đơn tối thiểu 0đ
                                </span>
                                @if($coupon->max_discount_amount)
                                <span class="text-[10px] text-gray-400">
                                    Tối đa {{ number_format($coupon->max_discount_amount / 1000, 0) }}k
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Voucher Code & Copy Action -->
                        <div class="mt-3 pt-2.5 border-t border-dashed border-gray-200 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 bg-gray-50 px-2.5 py-1 rounded-lg border border-gray-200">
                                <span class="text-[11px] font-mono font-black text-gray-800 tracking-wider select-all">{{ $coupon->code }}</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button 
                                    type="button" 
                                    onclick="copyVoucherCode('{{ $coupon->code }}', this)"
                                    class="px-2.5 py-1 text-[11px] font-bold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors cursor-pointer flex items-center gap-1"
                                    title="Sao chép mã"
                                >
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Lưu mã</span>
                                </button>
                                
                                <a 
                                    href="{{ route('checkout.index') }}?coupon={{ $coupon->code }}" 
                                    class="px-3 py-1 text-[11px] font-black text-white bg-[#ea384c] hover:bg-[#d3273b] rounded-lg shadow-2xs transition-all active:scale-95 cursor-pointer flex items-center gap-1"
                                >
                                    <span>Dùng ngay</span>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
            @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-200">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <h3 class="font-bold text-gray-800 text-sm">Chưa có mã giảm giá nào</h3>
                <p class="text-xs text-gray-400 mt-1">Các voucher siêu ưu đãi sẽ sớm xuất hiện tại đây!</p>
            </div>
            @endforelse
        </div>

        <!-- How to Use Vouchers Guide (Shopee Style Explainer) -->
        <div class="mt-12 bg-white rounded-2xl border border-gray-100 p-6 shadow-2xs">
            <h2 class="text-sm sm:text-base font-black text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-2 h-5 rounded-full bg-[#ea384c]"></span>
                Hướng dẫn thu thập và sử dụng mã giảm giá ShopMart
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs text-gray-600">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ea384c] font-black flex items-center justify-center shrink-0">1</div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Thu thập mã giảm giá</h4>
                        <p class="leading-relaxed text-gray-500">Bấm "Lưu mã" để copy trực tiếp mã vào khay nhớ tạm hoặc chọn voucher phù hợp với đơn hàng của bạn.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ea384c] font-black flex items-center justify-center shrink-0">2</div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Chọn hoặc nhập tại thanh toán</h4>
                        <p class="leading-relaxed text-gray-500">Tại bước thanh toán, nhấp vào "Chọn Voucher" để mở danh sách voucher Shopee hoặc dán mã vào ô nhập.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ea384c] font-black flex items-center justify-center shrink-0">3</div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-1">Hưởng trọn ưu đãi</h4>
                        <p class="leading-relaxed text-gray-500">Tiền giảm giá sẽ được trừ trực tiếp vào tổng đơn hàng ngay lập tức trước khi bạn đặt hàng.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Floating Toast Notification -->
<div id="voucher-toast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div class="bg-gray-900/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/10 text-xs font-bold">
        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span id="voucher-toast-msg">Đã sao chép mã thành công!</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching for voucher categories
    function filterVoucherTab(category, btn) {
        const tabs = document.querySelectorAll('#voucher-tab-bar .tab-btn');
        tabs.forEach(t => {
            t.className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-white text-gray-600 hover:bg-gray-100 border border-gray-200';
        });

        btn.className = 'tab-btn active-tab px-4 py-2 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer bg-[#ea384c] text-white shadow-xs';

        const cards = document.querySelectorAll('.voucher-card');
        cards.forEach(card => {
            const cardCat = card.dataset.category;
            if (category === 'all' || cardCat === category) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Quick Voucher input from banner
    function applyQuickVoucher() {
        const input = document.getElementById('quick-voucher-input');
        const code = (input.value || '').trim().toUpperCase();
        if (!code) {
            showToast('Vui lòng nhập mã voucher!');
            input.focus();
            return;
        }

        // Redirect to checkout with this coupon
        window.location.href = "{{ route('checkout.index') }}?coupon=" + encodeURIComponent(code);
    }

    // Copy Voucher Code to Clipboard with Toast
    function copyVoucherCode(code, btn) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(() => {
                showToast(`Đã sao chép mã ${code}! Dùng ngay khi thanh toán nhé.`);
                if (btn) {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = `<svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span class="text-emerald-700">Đã chép</span>`;
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                    }, 2000);
                }
            }).catch(() => {
                fallbackCopy(code);
            });
        } else {
            fallbackCopy(code);
        }
    }

    function fallbackCopy(code) {
        const temp = document.createElement('input');
        temp.value = code;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        showToast(`Đã sao chép mã ${code}!`);
    }

    function showToast(msg) {
        const toast = document.getElementById('voucher-toast');
        const toastMsg = document.getElementById('voucher-toast-msg');
        if (!toast || !toastMsg) return;

        toastMsg.textContent = msg;
        toast.classList.remove('translate-y-20', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3200);
    }
</script>
@endpush
