@extends('layouts.app')

@section('title', 'Đơn Mua Của Tôi - ShopMart')

@section('content')
<div class="page-container py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Persistent User Sidebar -->
        <div class="lg:col-span-3">
            <x-user-sidebar :active="$status === 'completed' ? 'reviews' : 'orders'" />
        </div>

        <!-- Orders Main Area -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs font-medium text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-primary">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('profile') }}" class="hover:text-primary">Tài khoản</a>
                <span>/</span>
                <span class="text-gray-900 font-bold">Đơn mua</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Quản Lý Đơn Mua</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Theo dõi tiến độ đơn hàng và lịch sử mua sắm của bạn</p>
                </div>

                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-light text-primary text-xs font-bold hover:bg-rose-100 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Tiếp tục mua hàng</span>
                </a>
            </div>

            <!-- 5 Status Tabs Bar -->
            <div class="order-tabs-bar">
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
                       class="order-tab-item {{ $status === $key ? 'is-active' : '' }}">
                        <span>{{ $label }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === $key ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-600' }}">
                            {{ $counts[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            <!-- Orders Cards List -->
            @if($orders->isEmpty())
                <div class="empty-state max-w-md mx-auto my-8">
                    <div class="empty-state__icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="empty-state__title">Chưa có đơn hàng nào</h3>
                    <p class="empty-state__desc">Bạn chưa có đơn hàng nào trong trạng thái này. Khám phá hàng ngàn ưu đãi ngay!</p>
                    <div class="empty-state__action">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-sm px-6 py-2.5">
                            Khám phá ngay
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="order-card">
                            
                            <!-- Card Header: Shop & Status -->
                            <div class="order-card__header flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2 sm:gap-3 text-xs">
                                    @php
                                        $store = $order->store ?? $order->items->first()?->product?->store;
                                    @endphp
                                    @if($store)
                                        <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-500 text-white">Yêu thích+</span>
                                            <a href="{{ route('store.show', $store->slug ?? $store->id) }}" class="hover:text-primary transition-colors flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                                <span>{{ $store->name }}</span>
                                            </a>
                                            <span class="text-gray-300">|</span>
                                        </div>
                                    @endif
                                    <span class="order-card__code font-mono text-primary font-bold">{{ $order->order_code }}</span>
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
                            <div class="order-card__items">
                                @foreach($order->items as $item)
                                    <div class="order-card__item">
                                        <img src="{{ $item->product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=120&q=80' }}" class="order-card__item-img">
                                        
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('product.detail', $item->product->slug ?? '#') }}" class="text-xs sm:text-sm font-bold text-gray-900 hover:text-primary truncate block">
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
                            <div class="order-card__footer">
                                <div class="text-xs">
                                    <span class="text-gray-500">Thành tiền ({{ $order->items->sum('quantity') }} sản phẩm):</span>
                                    <span class="order-card__total-price">{{ $order->formatted_total }}</span>
                                    <span class="text-[10px] text-gray-400 block sm:inline sm:ml-2">({{ $order->payment_method_label }})</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <!-- Cancel pending order button -->
                                    @if($order->status === 'pending')
                                        <form action="{{ route('user.orders.cancel', $order->order_code) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm text-rose-600 hover:border-rose-200 hover:bg-rose-50">
                                                Hủy đơn hàng
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Re-order button -->
                                    <form action="{{ route('user.orders.reorder', $order->order_code) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm">
                                            Mua lại
                                        </button>
                                    </form>

                                    <!-- Detail button -->
                                    <a href="{{ route('user.orders.show', $order->order_code) }}" class="btn btn-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
