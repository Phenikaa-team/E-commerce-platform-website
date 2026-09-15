@extends('layouts.app')

@section('title', 'Kho Voucher & Mã Giảm Giá Độc Quyền - ShopMart')
@section('meta_description', 'Săn mã giảm giá khủng, freeship 0Đ và voucher ShopMart Mall cực hot mỗi ngày tại ShopMart.')

@section('content')
<div class="voucher-page">
    
    <!-- Hero Banner (Shopee Aesthetic) -->
    <div class="voucher-hero">
        <!-- Background decorative rings -->
        <div class="voucher-hero__decor-1"></div>
        <div class="voucher-hero__decor-2"></div>

        <div class="voucher-hero__inner">
            <div class="voucher-hero__content">
                <div>
                    <div class="voucher-hero__badge">
                        <span>SIÊU HỘI VOUCHER HÔM NAY</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-ping"></span>
                    </div>
                    <h1 class="voucher-hero__title">
                        Kho Voucher & Mã Giảm Giá ShopMart
                    </h1>
                    <p class="voucher-hero__desc">
                        Thu thập mã giảm giá vận chuyển 0Đ, ưu đãi giảm giá lên đến 500.000đ và hàng ngàn voucher độc quyền từ ShopMart Mall.
                    </p>
                </div>

                <!-- Fast Coupon Apply / Search Card -->
                <div class="voucher-quick-box">
                    <p class="voucher-quick-box__title">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Nhập mã voucher nhanh
                    </p>
                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            id="quick-voucher-input"
                            placeholder="Nhập mã voucher (VD: FREESHIP)" 
                            class="voucher-quick-input"
                        >
                        <button 
                            type="button" 
                            onclick="applyQuickVoucher()"
                            class="voucher-quick-btn"
                        >
                            Áp dụng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Voucher Container -->
    <div class="page-container mt-6">
        
        <!-- Tab Navigation (Shopee style sticky category pills) -->
        <div class="voucher-tabs" id="voucher-tab-bar">
            <button 
                type="button" 
                onclick="filterVoucherTab('all', this)" 
                class="tab-btn voucher-tab-btn is-active"
            >
                Tất cả mã ({{ $coupons->count() }})
            </button>
            <button 
                type="button" 
                onclick="filterVoucherTab('freeship', this)" 
                class="tab-btn voucher-tab-btn"
            >
                Miễn Phí Vận Chuyển ({{ $freeshipCoupons->count() }})
            </button>
            <button 
                type="button" 
                onclick="filterVoucherTab('mall', this)" 
                class="tab-btn voucher-tab-btn"
            >
                ShopMart Mall ({{ $mallCoupons->count() }})
            </button>
            <button 
                type="button" 
                onclick="filterVoucherTab('category', this)" 
                class="tab-btn voucher-tab-btn"
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

                $badgeText = $isFreeship ? 'FREESHIP' : ($isMall ? 'MALL' : 'GIẢM GIÁ');
            @endphp
            <div 
                class="voucher-card group"
                data-category="{{ $categoryTag }}"
                data-code="{{ $coupon->code }}"
            >
                <!-- Shopee Serrated Ticket Body -->
                <div class="voucher-card__content">
                    
                    <!-- Left Ticket Stub -->
                    <div class="voucher-card__stub {{ $isFreeship ? 'is-freeship' : ($isMall ? 'is-mall' : 'is-default') }}">
                        <!-- Left Icon -->
                        <div class="voucher-card__stub-icon">
                            @if($isFreeship)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            @elseif($isMall)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            @endif
                        </div>
                        
                        <span class="voucher-card__stub-badge">{{ $badgeText }}</span>
                        <div class="voucher-card__stub-value">
                            @if($coupon->type === 'percent')
                                Giảm {{ $coupon->value }}%
                            @else
                                Giảm {{ number_format($coupon->value / 1000, 0) }}k
                            @endif
                        </div>

                        <!-- Ticket Notch Decorators -->
                        <div class="voucher-card__notches">
                            <div class="voucher-card__notch voucher-card__notch--top"></div>
                            <div class="voucher-card__notch voucher-card__notch--bottom"></div>
                        </div>
                    </div>

                    <!-- Right Ticket Details -->
                    <div class="voucher-card__body">
                        <div>
                            <h3 class="voucher-card__title">
                                {{ $coupon->name }}
                            </h3>
                            
                            <p class="voucher-card__desc">
                                {{ $coupon->description ?? 'Áp dụng cho mọi đơn hàng hợp lệ tại ShopMart' }}
                            </p>

                            <div class="voucher-card__meta">
                                <span class="badge badge-neutral badge-xs">
                                    Đơn tối thiểu 0đ
                                </span>
                                @if($coupon->max_discount_amount)
                                <span class="voucher-card__meta-max">
                                    Tối đa {{ number_format($coupon->max_discount_amount / 1000, 0) }}k
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Voucher Code & Copy Action -->
                        <div class="voucher-card__footer">
                            <span class="voucher-card__code">{{ $coupon->code }}</span>

                            <div class="voucher-card__actions">
                                <button 
                                    type="button" 
                                    onclick="copyVoucherCode('{{ $coupon->code }}', this)"
                                    class="voucher-card__btn-copy"
                                    title="Sao chép mã"
                                >
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span>Lưu mã</span>
                                </button>
                                
                                <a 
                                    href="{{ route('checkout.index') }}?coupon={{ $coupon->code }}" 
                                    class="btn btn-primary btn-sm"
                                >
                                    <span>Dùng ngay</span>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
            @empty
            <div class="col-span-full empty-state">
                <div class="empty-state__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <h3 class="empty-state__title">Chưa có mã giảm giá nào</h3>
                <p class="empty-state__desc">Các voucher siêu ưu đãi sẽ sớm xuất hiện tại đây!</p>
            </div>
            @endforelse
        </div>

        <!-- How to Use Vouchers Guide (Shopee Style Explainer) -->
        <div class="voucher-guide">
            <h2 class="voucher-guide__title">
                <span class="voucher-guide__title-bar"></span>
                Hướng dẫn thu thập và sử dụng mã giảm giá ShopMart
            </h2>
            <div class="voucher-guide__grid">
                <div class="voucher-guide__step">
                    <div class="voucher-guide__step-num">1</div>
                    <div>
                        <h4 class="voucher-guide__step-title">Thu thập mã giảm giá</h4>
                        <p class="voucher-guide__step-desc">Bấm "Lưu mã" để copy trực tiếp mã vào khay nhớ tạm hoặc chọn voucher phù hợp với đơn hàng của bạn.</p>
                    </div>
                </div>
                <div class="voucher-guide__step">
                    <div class="voucher-guide__step-num">2</div>
                    <div>
                        <h4 class="voucher-guide__step-title">Chọn hoặc nhập tại thanh toán</h4>
                        <p class="voucher-guide__step-desc">Tại bước thanh toán, nhấp vào "Chọn Voucher" để mở danh sách voucher Shopee hoặc dán mã vào ô nhập.</p>
                    </div>
                </div>
                <div class="voucher-guide__step">
                    <div class="voucher-guide__step-num">3</div>
                    <div>
                        <h4 class="voucher-guide__step-title">Hưởng trọn ưu đãi</h4>
                        <p class="voucher-guide__step-desc">Tiền giảm giá sẽ được trừ trực tiếp vào tổng đơn hàng ngay lập tức trước khi bạn đặt hàng.</p>
                    </div>
                </div>
            </div>
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
<script>
    // Tab switching for voucher categories - State-based refactor
    function filterVoucherTab(category, btn) {
        const tabs = document.querySelectorAll('#voucher-tab-bar .tab-btn');
        tabs.forEach(t => t.classList.toggle('is-active', t === btn));

        const cards = document.querySelectorAll('.voucher-card');
        cards.forEach(card => {
            const cardCat = card.dataset.category;
            const shouldShow = (category === 'all' || cardCat === category);
            card.classList.toggle('is-hidden', !shouldShow);
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
        toast.classList.add('is-visible');

        setTimeout(() => {
            toast.classList.remove('is-visible');
        }, 3200);
    }
</script>
@endpush
