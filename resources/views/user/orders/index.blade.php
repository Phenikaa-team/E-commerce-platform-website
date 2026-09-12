@extends('layouts.app')

@section('title', 'Đơn Mua Của Tôi - ShopMart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-[#ea384c]">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('profile') }}" class="hover:text-[#ea384c]">Tài khoản</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">Đơn mua</span>
    </nav>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Quản Lý Đơn Mua</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Theo dõi tiến độ đơn hàng và lịch sử mua sắm của bạn</p>
        </div>

        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 text-[#ea384c] text-xs font-bold hover:bg-rose-100 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>Tiếp tục mua hàng</span>
        </a>
    </div>

    <!-- 5 Status Tabs Bar -->
    <div class="bg-white rounded-2xl p-1.5 border border-gray-100 shadow-xs mb-6 overflow-x-auto flex gap-1 scrollbar-none">
        @php
            $tabs = [
                'all' => 'Tất cả',
                'pending' => 'Chờ duyệt',
                'processing' => 'Chờ lấy hàng',
                'shipping' => 'Đang giao',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy',
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <a href="{{ route('user.orders', ['status' => $key]) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $status === $key ? 'bg-[#ea384c] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span>{{ $label }}</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === $key ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-600' }}">
                    {{ $counts[$key] ?? 0 }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Orders Cards List -->
    @if($orders->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-xs max-w-md mx-auto my-8">
            <div class="w-16 h-16 bg-rose-50 text-[#ea384c] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Chưa có đơn hàng nào</h3>
            <p class="text-xs text-gray-500 mb-6">Bạn chưa có đơn hàng nào trong trạng thái này. Khám phá hàng ngàn ưu đãi ngay!</p>
            <a href="{{ route('home') }}" class="px-6 py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Khám phá ngay
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden hover:border-gray-200 transition-all">
                    
                    <!-- Card Header: Shop & Status -->
                    <div class="px-6 py-3.5 bg-gray-50/70 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3 text-xs">
                            <span class="font-extrabold text-[#ea384c] tracking-wider">{{ $order->order_code }}</span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $order->status_badge }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Items List inside Card -->
                    <div class="p-6 divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center gap-4">
                                <img src="{{ $item->product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=120&q=80' }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 shrink-0">
                                
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('product.detail', $item->product->slug ?? '#') }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-[#ea384c] truncate block">
                                        {{ $item->product_name }}
                                    </a>
                                    @if($item->selected_variant)
                                        <p class="text-xs text-gray-400 mt-0.5">Phân loại: {{ $item->selected_variant }}</p>
                                    @endif
                                    <span class="text-xs text-gray-500">Số lượng: x{{ $item->quantity }}</span>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs sm:text-sm font-bold text-gray-900">{{ $item->formatted_subtotal }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Card Footer: Total & Actions -->
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-xs">
                            <span class="text-gray-500">Thành tiền ({{ $order->items->sum('quantity') }} sản phẩm):</span>
                            <span class="text-lg font-black text-[#ea384c] ml-1">{{ $order->formatted_total }}</span>
                            <span class="text-[10px] text-gray-400 block sm:inline sm:ml-2">({{ $order->payment_method_label }})</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Cancel pending order button -->
                            @if($order->status === 'pending')
                                <form action="{{ route('user.orders.cancel', $order->order_code) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-white hover:bg-rose-50 border border-gray-200 hover:border-rose-200 text-rose-600 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                        Hủy đơn hàng
                                    </button>
                                </form>
                            @endif

                            <!-- Re-order button -->
                            <form action="{{ route('user.orders.reorder', $order->order_code) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                    Mua lại
                                </button>
                            </form>

                            <!-- Detail button -->
                            <a href="{{ route('user.orders.show', $order->order_code) }}" class="px-4 py-2 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
