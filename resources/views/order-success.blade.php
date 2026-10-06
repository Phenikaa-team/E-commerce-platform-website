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
        
        <!-- Decorative Header Background Glow -->
        <div class="order-success-glow"></div>

        <!-- Animated Success Check Circle -->
        <div class="order-success-icon-wrap">
            <svg class="order-success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="order-success-title">Đặt Hàng Thành Công!</h1>
        <p class="order-success-desc">
            Cảm ơn bạn đã tin tưởng mua sắm tại ShopMart.
            @if($isMultiOrder)
                Đơn hàng của bạn gồm nhiều gian hàng và đã được tách thành <strong class="text-gray-800">{{ $ordersList->count() }} đơn hàng độc lập</strong> để các shop đóng gói và giao hàng nhanh nhất.
            @else
                Đơn hàng của bạn đã được tiếp nhận và đang chuyển tới người bán để đóng gói.
            @endif
        </p>

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
                    <span class="order-success-paid-pill {{ $firstOrder->payment_status === 'paid' ? 'order-success-paid-pill--paid' : 'order-success-paid-pill--pending' }}">
                        {{ $firstOrder->payment_status === 'paid' ? 'Đã thanh toán trực tuyến' : 'Thanh toán khi nhận hàng' }}
                    </span>
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
            
            <a href="{{ route('home') }}" class="btn btn-primary w-full sm:w-auto px-6 py-3 text-xs font-bold shadow-md shadow-rose-500/20">
                Tiếp tục mua sắm
            </a>
        </div>

    </div>
</div>
@endsection
