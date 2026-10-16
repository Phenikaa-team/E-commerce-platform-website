@extends('layouts.app')

@section('title', 'Đặt Hàng Thành Công - ShopMart')

@section('content')
@php
    $ordersList = isset($orders) && $orders->isNotEmpty() ? $orders : collect([$order]);
    $isMultiOrder = $ordersList->count() > 1;
    $grandTotal = $ordersList->sum('total');
    $firstOrder = $ordersList->first();
@endphp
<div class="order-success-page">
    <div class="order-success-card">
        
@php
    $isOnlineUnpaid = $firstOrder->payment_method !== 'cod' && $firstOrder->payment_status !== 'paid';
@endphp
        <!-- Decorative Header Background Glow -->
        <div class="order-success-glow"></div>

        <!-- Animated Success / Attention Check Circle -->
        <div class="order-success-icon-wrap {{ $isOnlineUnpaid ? 'bg-amber-100 text-amber-600' : '' }}">
            @if($isOnlineUnpaid)
                <svg class="order-success-icon text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @else
                <svg class="order-success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            @endif
        </div>

        <h1 class="order-success-title">
            {{ $isOnlineUnpaid ? 'Đơn Hàng Đang Chờ Thanh Toán' : 'Đặt Hàng Thành Công!' }}
        </h1>
        <p class="order-success-desc">
            @if($isOnlineUnpaid)
                Đơn hàng của bạn đã được ghi nhận. Vui lòng hoàn tất thanh toán trực tuyến <strong>trong vòng 12 giờ</strong> để đơn hàng được người bán chuẩn bị và giao đi.
            @elseif($isMultiOrder)
                Cảm ơn bạn đã tin tưởng mua sắm tại ShopMart. Đơn hàng của bạn gồm nhiều gian hàng và đã được tách thành <strong class="text-gray-800">{{ $ordersList->count() }} đơn hàng độc lập</strong> để các shop đóng gói và giao hàng nhanh nhất.
            @else
                Cảm ơn bạn đã tin tưởng mua sắm tại ShopMart. Đơn hàng của bạn đã được tiếp nhận và đang chuyển tới người bán để đóng gói.
            @endif
        </p>

        @if($isOnlineUnpaid && $firstOrder->payment_deadline)
            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex flex-wrap items-center justify-center gap-2 max-w-md mx-auto"
                 data-order-countdown="{{ $firstOrder->payment_deadline->toIso8601String() }}"
                 data-order-code="{{ $firstOrder->order_code }}">
                <svg class="w-4 h-4 text-amber-600 shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Hạn thanh toán còn lại: <strong class="font-mono font-bold text-rose-600 timer-display">--:--:--</strong></span>
                <span class="text-amber-700 text-[11px] block w-full text-center">(Hạn chót: {{ $firstOrder->payment_deadline->format('H:i - d/m/Y') }})</span>
            </div>
        @endif

        @if($firstOrder->checkout_group_id && $isMultiOrder)
            <div class="order-success-group-code">
                <span>Mã giao dịch chung:</span>
                <span class="font-mono font-bold">{{ $firstOrder->checkout_group_id }}</span>
            </div>
        @endif

        <!-- General Order Information -->
        <div class="order-success-info-box">
            <div class="order-success-info-grid">
                <div>
                    <span class="order-success-info-label">Phương thức thanh toán:</span>
                    <span class="order-success-info-val">{{ $firstOrder->payment_method_label }}</span>
                    @if($firstOrder->payment_status === 'paid')
                        <span class="order-success-paid-pill order-success-paid-pill--paid">
                            Đã thanh toán trực tuyến
                        </span>
                    @elseif($firstOrder->payment_method === 'cod')
                        <span class="order-success-paid-pill order-success-paid-pill--pending">
                            Thanh toán khi nhận hàng (COD)
                        </span>
                    @else
                        <span class="order-success-paid-pill bg-rose-50 text-rose-700 border border-rose-200 font-bold">
                            Chưa thanh toán (Hạn 12 giờ)
                        </span>
                    @endif
                </div>

                <div>
                    <span class="order-success-info-label">Địa chỉ nhận hàng:</span>
                    <p class="order-success-info-val">
                        {{ $firstOrder->shipping_address['name'] ?? '' }} - {{ $firstOrder->shipping_address['phone'] ?? '' }}
                    </p>
                    <p class="text-gray-600 mt-0.5">{{ $firstOrder->shipping_address['address'] ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- Orders Breakdown per Store -->
        <div class="order-success-stores">
            @foreach($ordersList as $ord)
                <div class="order-success-store-card">
                    <div class="order-success-store-header">
                        <div class="flex items-center gap-2">
                            <span class="order-success-store-tag">Gian hàng</span>
                            <span class="order-success-store-name">{{ $ord->store->name ?? 'ShopMart Mall' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">Mã đơn:</span>
                            <span class="text-xs font-mono font-bold text-gray-800">{{ $ord->order_code }}</span>
                            @auth
                                <a href="{{ route('user.orders.show', $ord->order_code) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                                    Chi tiết &rarr;
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Items in this store order -->
                    <div class="order-success-items-list">
                        @foreach($ord->items as $item)
                            <div class="order-success-item-row">
                                <div class="min-w-0 flex-1 pr-4">
                                    <p class="font-bold text-gray-800 truncate">{{ $item->product_name }}</p>
                                    @if($item->selected_variant)
                                        <p class="text-[11px] text-gray-400">Phân loại: {{ $item->selected_variant }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-gray-400 text-[11px]">x{{ $item->quantity }}</span>
                                    <span class="font-bold text-gray-900 ml-3">{{ $item->formatted_subtotal }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Subtotal & Shipping for this Store -->
                    <div class="order-success-store-footer">
                        <div class="flex items-center gap-3">
                            <span>Phí vận chuyển: <strong class="text-gray-800">{{ $ord->shipping_fee > 0 ? number_format((float) $ord->shipping_fee, 0, ',', '.').'₫' : 'Miễn phí' }}</strong></span>
                            @if($ord->discount_amount > 0)
                                <span class="text-rose-600">Giảm giá: <strong>-{{ number_format((float) $ord->discount_amount, 0, ',', '.').'₫' }}</strong></span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500 text-xs">Thành tiền shop:</span>
                            <span class="text-sm font-black text-primary ml-1.5">{{ $ord->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($isMultiOrder)
            <!-- Grand Total Bar -->
            <div class="order-success-grand-total">
                <span class="text-xs font-black uppercase tracking-wider text-gray-800">Tổng cộng tất cả các đơn:</span>
                <span class="text-2xl font-black text-primary">{{ number_format($grandTotal, 0, ',', '.') }}₫</span>
            </div>
        @endif

        <!-- Action CTAs -->
        <div class="order-success-actions">
            @if($isOnlineUnpaid)
                <a href="{{ route('checkout.retry-payment', $firstOrder->checkout_group_id ?: $firstOrder->order_code) }}" 
                   data-order-pay-btn="{{ $firstOrder->order_code }}"
                   class="btn btn-primary w-full sm:w-auto px-6 py-3 text-xs font-bold shadow-md shadow-rose-500/25 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Thanh toán ngay (<span class="btn-timer-display font-mono">--:--:--</span>)</span>
                </a>
            @endif

            @auth
                @if($ordersList->count() === 1)
                    <a href="{{ route('user.orders.show', $firstOrder->order_code) }}" class="order-success-btn-secondary">
                        Xem chi tiết đơn hàng
                    </a>
                @else
                    <a href="{{ route('user.orders') }}" class="order-success-btn-secondary">
                        Quản lý danh sách đơn hàng ({{ $ordersList->count() }})
                    </a>
                @endif
            @endauth
            
            <a href="{{ route('home') }}" class="{{ $isOnlineUnpaid ? 'order-success-btn-secondary' : 'btn btn-primary w-full sm:w-auto px-6 py-3 text-xs font-bold shadow-md shadow-rose-500/20' }}">
                Tiếp tục mua sắm
            </a>
        </div>

    </div>
</div>
@endsection
