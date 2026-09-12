@extends('layouts.app')

@section('title', 'Đặt Hàng Thành Công - ShopMart')

@section('content')
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
            Cảm ơn bạn đã tin tưởng mua sắm tại ShopMart. Đơn hàng của bạn đã được tiếp nhận và đang chuyển tới người bán để đóng gói.
        </p>

        <!-- Order Summary Card -->
        <div class="mt-8 bg-gray-50/80 rounded-2xl p-6 border border-gray-100 text-left space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-200/80 gap-2">
                <div>
                    <span class="text-xs text-gray-400 font-medium">Mã vận đơn / Đơn hàng:</span>
                    <p class="text-lg font-black text-[#ea384c] tracking-wider">{{ $order->order_code }}</p>
                </div>
                <div class="sm:text-right">
                    <span class="text-xs text-gray-400 font-medium">Thời gian đặt hàng:</span>
                    <p class="text-xs font-bold text-gray-800">{{ $order->created_at->format('H:i - d/m/Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-gray-400 block mb-1">Phương thức thanh toán:</span>
                    <span class="font-bold text-gray-900 block">{{ $order->payment_method_label }}</span>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán trực tuyến' : 'Thanh toán khi nhận hàng' }}
                    </span>
                </div>

                <div>
                    <span class="text-gray-400 block mb-1">Dự kiến giao hàng:</span>
                    <span class="font-bold text-gray-900 block">{{ now()->addDays(2)->format('d/m') }} - {{ now()->addDays(4)->format('d/m/Y') }}</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a2 2 0 11-4 0m4 0a2 2 0 104 0m-4 0h5m-9 0H6m10 0v-5h4l2 3.5V16h-2m-4 0a2 2 0 10-4 0"/></svg>
                        <span>Giao hàng tiêu chuẩn toàn quốc</span>
                    </span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="pt-3 border-t border-gray-200/80 text-xs">
                <span class="text-gray-400 block mb-1">Địa chỉ nhận hàng:</span>
                <p class="font-bold text-gray-800">
                    {{ $order->shipping_address['name'] ?? '' }} - {{ $order->shipping_address['phone'] ?? '' }}
                </p>
                <p class="text-gray-600 mt-0.5">{{ $order->shipping_address['address'] ?? '' }}</p>
            </div>

            <!-- Items List -->
            <div class="pt-3 border-t border-gray-200/80 text-xs">
                <span class="text-gray-400 block mb-2 font-medium">Sản phẩm trong đơn ({{ $order->items->count() }}):</span>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-xs py-1">
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
            </div>

            <!-- Total -->
            <div class="pt-3 border-t border-gray-200/80 flex items-baseline justify-between text-sm">
                <span class="font-bold text-gray-900">Tổng thanh toán:</span>
                <span class="text-xl font-black text-[#ea384c]">{{ $order->formatted_total }}</span>
            </div>
        </div>

        <!-- Action CTAs -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                <a href="{{ route('user.orders.show', $order->order_code) }}" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-md">
                    Xem chi tiết đơn hàng
                </a>
            @endauth
            
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-rose-500/20">
                Tiếp tục mua sắm
            </a>
        </div>

    </div>
</div>
@endsection
