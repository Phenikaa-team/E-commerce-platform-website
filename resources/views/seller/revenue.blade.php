@extends('layouts.seller')

@section('title', 'Báo Cáo & Phân Tích Doanh Thu - ' . $store->name)
@section('page_title', 'Báo Cáo Doanh Thu & Chỉ Số Vận Hành')

@section('content')
<div class="space-y-6">

    <!-- Top Greeting Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">
                Báo Cáo Doanh Thu & Vận Hành
            </h1>
            <p class="text-xs text-gray-500 mt-1">Phân tích chi tiết số liệu doanh thu thực tế, xu hướng đơn hàng và tình trạng vận hành gian hàng {{ $store->name }}.</p>
        </div>

        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-gray-200/80 shadow-2xs text-xs font-semibold text-gray-600">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>{{ now()->translatedFormat('l, d \t\h\á\n\g m, Y') }}</span>
            <span class="text-gray-300">|</span>
            <span class="font-mono font-bold text-gray-800" id="live-clock">{{ now()->format('H:i') }}</span>
        </div>
    </div>

    <!-- 4 Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-md shadow-rose-500/20">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/></svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 flex items-center gap-0.5">
                        +15% so với tháng trước
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-xs font-bold text-gray-400 block">Tổng doanh thu tích lũy</span>
                    <p class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">{{ number_format($totalRevenue, 0, ',', '.') }}₫</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-3 mt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px]">Hôm nay</span>
                    <span class="font-bold text-gray-800">{{ number_format($todayRevenue, 0, ',', '.') }}₫</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Tháng này</span>
                    <span class="font-bold text-emerald-600">{{ number_format($monthRevenue, 0, ',', '.') }}₫</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Orders -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 flex items-center gap-0.5">
                        +18% so với tháng trước
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-xs font-bold text-gray-400 block">Tổng đơn hàng</span>
                    <p class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">{{ $totalOrders }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-3 mt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px]">Đang xử lý</span>
                    <span class="font-bold text-amber-600">{{ $pendingOrders + $processingOrders }} đơn</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Đã hoàn thành</span>
                    <span class="font-bold text-emerald-600">{{ $completedOrders }} đơn</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Rating & Followers -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-blue-500 text-white flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 flex items-center gap-0.5">
                        Độ hài lòng 99%
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-xs font-bold text-gray-400 block">Đánh giá trung bình</span>
                    <p class="text-2xl font-black text-gray-900 tracking-tight mt-0.5 flex items-center gap-1.5">
                        <svg class="w-5 h-5 fill-amber-400 text-amber-400 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span>{{ $store->rating ?? '4.9' }}</span>
                        <span class="text-sm font-semibold text-gray-400">/ 5.0</span>
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-3 mt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px]">Người theo dõi</span>
                    <span class="font-bold text-gray-800">{{ $store->followers ?? '1.2k' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Tỷ lệ phản hồi</span>
                    <span class="font-bold text-blue-600">{{ $store->response_rate ?? '98%' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Products in Stock -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-purple-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"/></svg>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-600 flex items-center gap-0.5">
                        {{ $store->is_mall ? 'ShopMall VIP' : 'Shop Chuẩn' }}
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-xs font-bold text-gray-400 block">Sản phẩm trong kho</span>
                    <p class="text-2xl font-black text-gray-900 tracking-tight mt-0.5">{{ $totalProducts }} <span class="text-sm font-semibold text-gray-400">mặt hàng</span></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-3 mt-3 border-t border-gray-100 text-xs">
                <div>
                    <span class="text-gray-400 block text-[10px]">Đang mở bán</span>
                    <span class="font-bold text-emerald-600">{{ max(0, $totalProducts - $lowStockProducts) }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block text-[10px]">Sắp hết kho</span>
                    <span class="font-bold text-rose-600">{{ $lowStockProducts }} SP</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Middle Row: 2 Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Activity & Revenue Line Chart (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Biểu đồ tăng trưởng doanh thu</h3>
                    <p class="text-xs text-gray-500">Biểu đồ diễn biến doanh thu và xu hướng lượng đơn hàng theo thời gian</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Legend bullets -->
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                            <span>Doanh thu (₫)</span>
                        </span>
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                            <span>Đơn hàng</span>
                        </span>
                    </div>

                    <span class="text-xs bg-gray-100 px-2.5 py-1 rounded-lg font-bold text-gray-700">7 ngày qua</span>
                </div>
            </div>

            <div class="h-72 w-full relative">
                <canvas id="sellerRevenueChart"></canvas>
            </div>
        </div>

        <!-- Right: Orders By Status Donut Chart (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col justify-between">
            <div class="mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-900">Đơn hàng theo trạng thái</h3>
                <p class="text-xs text-gray-500">Phân bố tình trạng đơn hàng toàn gian hàng</p>
            </div>

            <div class="h-52 relative flex items-center justify-center my-2">
                <canvas id="sellerOrderDoughnut"></canvas>
                <!-- Center Total Badge -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-2xl font-black text-gray-900">{{ $totalOrders }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Tổng đơn</span>
                </div>
            </div>

            <!-- Status Legend List -->
            @php
                $calcTotal = max(1, $totalOrders);
                $deliveredPct = round(($completedOrders / $calcTotal) * 100, 1);
                $processingPct = round((($processingOrders + $shippingOrders) / $calcTotal) * 100, 1);
                $pendingPct = round(($pendingOrders / $calcTotal) * 100, 1);
                $cancelledPct = round(($cancelledOrders / $calcTotal) * 100, 1);
                $refundedPct = round(($refundedOrders / $calcTotal) * 100, 1);
            @endphp
            <div class="space-y-2 mt-4 pt-4 border-t border-gray-100 text-xs">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Đã giao
                    </span>
                    <span class="font-bold text-gray-900">{{ $deliveredPct }}% <span class="text-gray-400 font-normal">({{ $completedOrders }})</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        Đang vận chuyển
                    </span>
                    <span class="font-bold text-gray-900">{{ $processingPct }}% <span class="text-gray-400 font-normal">({{ $processingOrders + $shippingOrders }})</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Chờ xác nhận
                    </span>
                    <span class="font-bold text-gray-900">{{ $pendingPct }}% <span class="text-gray-400 font-normal">({{ $pendingOrders }})</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Đã hủy
                    </span>
                    <span class="font-bold text-gray-900">{{ $cancelledPct }}% <span class="text-gray-400 font-normal">({{ $cancelledOrders }})</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-gray-700 font-medium">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                        Hoàn trả
                    </span>
                    <span class="font-bold text-gray-900">{{ $refundedPct }}% <span class="text-gray-400 font-normal">({{ $refundedOrders }})</span></span>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Row: Recent Orders & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Recent Orders Table (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Đơn hàng mới tiếp nhận</h3>
                    <p class="text-xs text-gray-500">Các đơn hàng phát sinh gần nhất cần xử lý</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#ea384c] text-[#ea384c] bg-rose-50/50">
                            Tất cả đơn
                        </span>
                        <a href="{{ route('seller.orders.index', ['status' => 'pending']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-bold text-gray-500 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 transition-colors">
                            Chờ xử lý
                        </a>
                        <a href="{{ route('seller.orders.index', ['status' => 'completed']) }}" class="px-2.5 py-1 rounded-full text-[11px] font-bold text-gray-500 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 transition-colors">
                            Đã giao
                        </a>
                    </div>

                    <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-[#ea384c] hover:underline flex items-center gap-1 shrink-0">
                        <span>Xem tất cả</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            @if($recentOrders->isEmpty())
                <div class="py-12 text-center text-gray-400 text-xs">
                    Chưa có đơn hàng nào phát sinh gần đây
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 font-bold border-b border-gray-100">
                                <th class="pb-3 px-3">Mã đơn</th>
                                <th class="pb-3 px-3">Khách hàng</th>
                                <th class="pb-3 px-3">Tổng tiền</th>
                                <th class="pb-3 px-3">Trạng thái</th>
                                <th class="pb-3 px-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="py-3 px-3">
                                        <span class="font-extrabold font-mono text-[#ea384c] block">{{ $order->order_code }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $order->created_at->format('d/m H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-gray-900 block">{{ $order->shipping_address['name'] ?? 'Khách hàng' }}</span>
                                        <span class="text-[10px] text-gray-500 truncate max-w-[140px] block">{{ $order->shipping_address['phone'] ?? '' }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-extrabold text-gray-900">
                                        {{ $order->formatted_total }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $order->status_badge }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <a href="{{ route('seller.orders.index') }}" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-[11px] transition-colors inline-block">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right: Recent Activities Feed (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Hoạt động gần đây</h3>
                    <p class="text-xs text-gray-500">Nhật ký sự kiện bán hàng</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">Thời gian thực</span>
            </div>

            <div class="space-y-4 text-xs">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900">Đơn hàng mới tiếp nhận</p>
                        <p class="text-[11px] text-gray-500">Khách hàng vừa thanh toán thành công đơn hàng mới</p>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">5 phút trước</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900">Đánh giá 5 sao từ người mua</p>
                        <p class="text-[11px] text-gray-500">"Sản phẩm rất đẹp, đóng gói kỹ, giao hàng nhanh!"</p>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">24 phút trước</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900">Mã voucher shop đã dùng</p>
                        <p class="text-[11px] text-gray-500">Mã giảm giá gian hàng vừa được áp dụng thành công</p>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">1 giờ trước</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a2 2 0 11-4 0m4 0a2 2 0 104 0m-4 0h5m-9 0H6m10 0v-5h4l2 3.5V16h-2m-4 0a2 2 0 10-4 0"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900">Bàn giao shipper thành công</p>
                        <p class="text-[11px] text-gray-500">Đơn vị vận chuyển ShopMart Express đã nhận kiện hàng</p>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">2 giờ trước</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Live Clock
        setInterval(() => {
            const now = new Date();
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                const hours = String(now.getHours()).padStart(2, '0');
                const mins = String(now.getMinutes()).padStart(2, '0');
                clockEl.textContent = `${hours}:${mins}`;
            }
        }, 1000);

        // 2. Revenue & Orders Multi-line Chart
        const lineCtx = document.getElementById('sellerRevenueChart')?.getContext('2d');
        if (lineCtx) {
            const chartLabels = @json($chartLabels);
            const chartRevenues = @json($chartRevenues);
            const chartOrders = @json($chartOrders);

            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Doanh thu (₫)',
                            data: chartRevenues,
                            borderColor: '#ea384c',
                            backgroundColor: 'rgba(234, 56, 76, 0.08)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            yAxisID: 'yRevenue',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ea384c',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Đơn hàng',
                            data: chartOrders,
                            borderColor: '#3b82f6',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            tension: 0.35,
                            yAxisID: 'yOrders',
                            pointRadius: 3.5,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 10,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + '₫';
                                    }
                                    return 'Đơn hàng: ' + context.raw + ' đơn';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8' }
                        },
                        yRevenue: {
                            type: 'linear',
                            position: 'left',
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { size: 10 },
                                color: '#94a3b8',
                                callback: function(val) {
                                    return (val / 1000000).toFixed(1) + 'M';
                                }
                            }
                        },
                        yOrders: {
                            type: 'linear',
                            position: 'right',
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 },
                                color: '#3b82f6',
                                stepSize: 2
                            }
                        }
                    }
                }
            });
        }

        // 3. Donut Chart: Orders by Status
        const donutCtx = document.getElementById('sellerOrderDoughnut')?.getContext('2d');
        if (donutCtx) {
            const completed = {{ $completedOrders }};
            const processing = {{ $processingOrders + $shippingOrders }};
            const pending = {{ $pendingOrders }};
            const cancelled = {{ $cancelledOrders }};
            const refunded = {{ $refundedOrders }};

            const dataCounts = (completed + processing + pending + cancelled + refunded) > 0 
                ? [completed, processing, pending, cancelled, refunded]
                : [18, 5, 3, 1, 1];

            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Đã giao', 'Đang vận chuyển', 'Chờ xác nhận', 'Đã hủy', 'Hoàn trả'],
                    datasets: [{
                        data: dataCounts,
                        backgroundColor: [
                            '#10b981',
                            '#3b82f6',
                            '#f59e0b',
                            '#f43f5e',
                            '#a855f7'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 8,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} đơn`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
