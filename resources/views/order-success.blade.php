@extends('layouts.app')

@section('title', 'Đặt Hàng Thành Công - ShopMart')

@section('content')
@php
    $ordersList = isset($orders) && $orders->isNotEmpty() ? $orders : collect([$order]);
    $isMultiOrder = $ordersList->count() > 1;
    $grandTotal = $ordersList->sum('total');
    $firstOrder = $ordersList->first();
@endphp
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-gray-100 shadow-xl text-center relative overflow-hidden">
        
        <!-- Decorative Header Background Glow -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500"></div>

        <!-- Animated Success Check Circle -->
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20 ring-8 ring-emerald-50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Đặt Hàng Thành Công!</h1>
        <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
            Cảm ơn bạn đã tin tưởng mua sắm tại ShopMart.
            @if($isMultiOrder)
                Đơn hàng của bạn gồm nhiều gian hàng và đã được tách thành <strong class="text-gray-800">{{ $ordersList->count() }} đơn hàng độc lập</strong> để các shop đóng gói và giao hàng nhanh nhất.
            @else
                Đơn hàng của bạn đã được tiếp nhận và đang chuyển tới người bán để đóng gói.
            @endif
        </p>

        @if($firstOrder->checkout_group_id && $isMultiOrder)
            <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold">
                <span>Mã giao dịch chung:</span>
                <span class="font-mono font-bold">{{ $firstOrder->checkout_group_id }}</span>
            </div>
        @endif

        <!-- General Order Information -->
        <div class="mt-6 bg-gray-50/80 rounded-2xl p-5 border border-gray-100 text-left text-xs space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-400 block mb-1">Phương thức thanh toán:</span>
                    <span class="font-bold text-gray-900 block">{{ $firstOrder->payment_method_label }}</span>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $firstOrder->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $firstOrder->payment_status === 'paid' ? 'Đã thanh toán trực tuyến' : 'Thanh toán khi nhận hàng' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-400 block mb-1">Địa chỉ nhận hàng:</span>
                    <p class="font-bold text-gray-800">
                        {{ $firstOrder->shipping_address['name'] ?? '' }} - {{ $firstOrder->shipping_address['phone'] ?? '' }}
                    </p>
                    <p class="text-gray-600 mt-0.5">{{ $firstOrder->shipping_address['address'] ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- Orders Breakdown per Store -->
        <div class="mt-6 space-y-4 text-left">
            @foreach($ordersList as $ord)
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary/10 text-primary">Gian hàng</span>
                            <span class="text-xs font-black text-gray-900">{{ $ord->store->name ?? 'ShopMart Mall' }}</span>
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
                    <div class="divide-y divide-gray-50">
                        @foreach($ord->items as $item)
                            <div class="flex items-center justify-between text-xs py-2">
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
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
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
            <div class="mt-6 p-4.5 rounded-2xl bg-white border-2 border-rose-100 shadow-xs flex items-center justify-between">
                <span class="text-xs font-black uppercase tracking-wider text-gray-800">Tổng cộng tất cả các đơn:</span>
                <span class="text-2xl font-black text-primary">{{ number_format($grandTotal, 0, ',', '.') }}₫</span>
            </div>
        @endif

        <!-- Action CTAs -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                @if($ordersList->count() === 1)
                    <a href="{{ route('user.orders.show', $firstOrder->order_code) }}" class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 text-xs font-bold rounded-xl transition-all shadow-xs">
                        Xem chi tiết đơn hàng
                    </a>
                @else
                    <a href="{{ route('user.orders') }}" class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 text-xs font-bold rounded-xl transition-all shadow-xs">
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
