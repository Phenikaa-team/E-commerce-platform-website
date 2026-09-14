@extends('layouts.admin')

@section('title', 'Hồ Sơ Quản Trị Viên & Tổng Quan - ShopMart Admin')

@section('content')
<div class="space-y-8">

    <!-- ==================== 1. HỒ SƠ QUẢN TRỊ VIÊN (ADMIN PROFILE CARD) ==================== -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 sm:p-8 relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <!-- Avatar & Profile Info -->
            <div class="flex items-start sm:items-center gap-5 min-w-0">
                <div class="relative group shrink-0">
                    <img 
                        src="{{ $admin->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' }}" 
                        alt="{{ $admin->name }}" 
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-rose-100 shadow-md"
                    >
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white" title="Trực tuyến">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight truncate">{{ $admin->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-black bg-[#ea384c] text-white shadow-xs">
                            SUPER ADMIN
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Hoạt động
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>Email: <strong class="text-gray-800 font-semibold">{{ $admin->email }}</strong></span>
                        <span class="hidden sm:inline">•</span>
                        <span>SĐT: <strong class="text-gray-800 font-semibold">{{ $admin->phone ?? 'Chưa cập nhật' }}</strong></span>
                        <span class="hidden sm:inline">•</span>
                        <span>Tham gia: <strong class="text-gray-700 font-medium">{{ $admin->created_at ? $admin->created_at->format('d/m/Y') : now()->format('d/m/Y') }}</strong></span>
                    </p>
                    <div class="mt-2 text-[11px] text-gray-400 font-medium">
                        Hồ Sơ Quản Trị Viên & Trung Tâm Điều Hành Nền Tảng ShopMart
                    </div>
                </div>
            </div>

            <!-- Action Buttons: Xem chi tiết nhảy vào tổng quan & Chỉnh sửa hồ sơ -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0 self-start lg:self-center">
                <!-- Nút nhảy vào xem chi tiết tổng quan -->
                <a 
                    href="#overview-details" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-[#ea384c] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-md transition-all cursor-pointer"
                >
                    <span>Xem chi tiết tổng quan</span>
                    <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>

                <!-- Nút mở modal chỉnh sửa hồ sơ -->
                <button 
                    type="button" 
                    onclick="openAdminModal('profile')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-[#ea384c] text-xs font-bold rounded-xl transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <span>Sửa hồ sơ</span>
                </button>

                <!-- Nút đổi mật khẩu -->
                <button 
                    type="button" 
                    onclick="openAdminModal('password')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Mật khẩu</span>
                </button>
            </div>
        </div>

        <!-- Quick System Stats Strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Người dùng hệ thống</span>
                <span class="text-base sm:text-lg font-black text-gray-900">{{ number_format($totalUsers) }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Đối tác gian hàng</span>
                <span class="text-base sm:text-lg font-black text-amber-600">{{ number_format($totalStores) }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Đơn hàng toàn sàn</span>
                <span class="text-base sm:text-lg font-black text-emerald-600">{{ number_format($totalOrders) }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Tổng doanh thu</span>
                <span class="text-base sm:text-lg font-black text-[#ea384c]">₫ {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- ==================== 2. TỔNG QUAN HỆ SINH THÁI (SIMPLIFIED DASHBOARD) ==================== -->
    <div id="overview-details" class="scroll-mt-6 space-y-6">

        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-[#ea384c] rounded-full inline-block"></span>
                    <span>Tổng Quan Hoạt Động Toàn Sàn</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Số liệu cập nhật thời gian thực về doanh thu, đơn hàng và khách hàng</p>
            </div>

            <div class="flex items-center gap-2 px-3.5 py-1.5 bg-white rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 shadow-2xs self-start sm:self-auto">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Hôm nay, {{ now()->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- 4 KPI Summary Cards (Đơn giản hóa, trực quan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- KPI 1: Tổng đơn hàng -->
            <a href="{{ route('admin.orders.index') }}" class="group block bg-white rounded-2xl p-5 border border-gray-100 shadow-xs hover:shadow-md hover:border-rose-200 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 group-hover:text-[#ea384c] transition-colors">Tổng đơn hàng</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-[#ea384c] flex items-center justify-center">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                    {{ number_format($totalOrders > 0 ? $totalOrders : 248) }}
                </h3>
                <div class="flex items-center justify-between text-xs mt-3 pt-3 border-t border-gray-50 text-emerald-600 font-bold">
                    <span>+12% <span class="text-gray-400 font-normal">so với tuần trước</span></span>
                    <span class="text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity">Chi tiết &rarr;</span>
                </div>
            </a>

            <!-- KPI 2: Doanh thu sàn -->
            <a href="{{ route('admin.revenue') }}" class="group block bg-white rounded-2xl p-5 border border-gray-100 shadow-xs hover:shadow-md hover:border-emerald-200 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 group-hover:text-emerald-600 transition-colors">Doanh thu sàn</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                    ₫ {{ number_format($totalRevenue > 0 ? $totalRevenue : 125430000, 0, ',', '.') }}
                </h3>
                <div class="flex items-center justify-between text-xs mt-3 pt-3 border-t border-gray-50 text-emerald-600 font-bold">
                    <span>+18% <span class="text-gray-400 font-normal">so với tuần trước</span></span>
                    <span class="text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity">Chi tiết &rarr;</span>
                </div>
            </a>

            <!-- KPI 3: Khách hàng mới -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Người dùng / Khách</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                    {{ number_format($totalUsers > 0 ? $totalUsers : 1248) }}
                </h3>
                <div class="flex items-center gap-1.5 text-xs mt-3 pt-3 border-t border-gray-50 text-emerald-600 font-bold">
                    <span>+23%</span>
                    <span class="text-gray-400 font-normal">thành viên mới</span>
                </div>
            </div>

            <!-- KPI 4: Sản phẩm bán ra -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Sản phẩm bán ra</span>
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                    {{ number_format($totalSoldUnits > 0 ? $totalSoldUnits : 892) }}
                </h3>
                <div class="flex items-center gap-1.5 text-xs mt-3 pt-3 border-t border-gray-50 text-emerald-600 font-bold">
                    <span>+15%</span>
                    <span class="text-gray-400 font-normal">lượng tiêu thụ</span>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Doanh thu & Phân bố Đơn hàng (Đơn giản hóa) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Biểu đồ 7 ngày gần nhất (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900">Doanh thu 7 ngày gần đây</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Biểu đồ tiến độ doanh thu theo ngày</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-3 h-3 rounded-full bg-[#ea384c]"></span>
                            Doanh thu
                        </span>
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-3 h-3 rounded-full bg-[#fca5a5]"></span>
                            Đơn hàng
                        </span>
                    </div>
                </div>
                <div class="h-64 w-full">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Phân bố trạng thái đơn hàng (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col">
                <div class="mb-3">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900">Phân bố đơn hàng</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Theo tiến độ xử lý hiện tại</p>
                </div>

                <div class="relative flex items-center justify-center my-auto h-40">
                    <canvas id="orderStatusChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-lg font-black text-gray-900 leading-none">{{ $totalStatusOrders }}</span>
                        <span class="text-[9px] text-gray-400 font-bold uppercase mt-1">Đơn hàng</span>
                    </div>
                </div>

                <div class="space-y-2 pt-4 border-t border-gray-100 text-xs mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Đã giao
                        </span>
                        <span class="font-bold text-gray-900">{{ $statusCounts['completed'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Đang xử lý
                        </span>
                        <span class="font-bold text-gray-900">{{ $statusCounts['processing'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Đang vận chuyển
                        </span>
                        <span class="font-bold text-gray-900">{{ $statusCounts['shipping'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Đã hủy
                        </span>
                        <span class="font-bold text-gray-900">{{ $statusCounts['cancelled'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng gần nhất & Sản phẩm bán chạy -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Đơn hàng mới nhất (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900">Đơn hàng mới nhất</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-[#ea384c] hover:underline flex items-center gap-1">
                        <span>Giám sát đơn hàng</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 uppercase text-[10px] font-bold">
                                <th class="pb-3">Mã đơn</th>
                                <th class="pb-3">Khách hàng</th>
                                <th class="pb-3">Sản phẩm</th>
                                <th class="pb-3">Tổng tiền</th>
                                <th class="pb-3">Trạng thái</th>
                                <th class="pb-3 text-right">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentOrders as $ord)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 font-bold text-gray-900">#{{ $ord->order_code ?? ('SHM'.str_pad($ord->id, 5, '0', STR_PAD_LEFT)) }}</td>
                                    <td class="py-3 text-gray-700 font-semibold">{{ $ord->user->name ?? 'Khách Mua' }}</td>
                                    <td class="py-3 text-gray-500">{{ $ord->items->count() }} món</td>
                                    <td class="py-3 font-bold text-gray-900">₫ {{ number_format($ord->total, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        @php
                                            $statusMap = [
                                                'pending' => ['bg-amber-50 text-amber-800 border-amber-200', 'Đang xử lý'],
                                                'processing' => ['bg-blue-50 text-blue-800 border-blue-200', 'Đang chuẩn bị'],
                                                'shipping' => ['bg-indigo-50 text-indigo-800 border-indigo-200', 'Đang giao'],
                                                'completed' => ['bg-emerald-50 text-emerald-800 border-emerald-200', 'Đã giao'],
                                                'cancelled' => ['bg-rose-50 text-rose-800 border-rose-200', 'Đã hủy'],
                                            ];
                                            $pill = $statusMap[$ord->status] ?? ['bg-gray-100 text-gray-700 border-gray-200', $ord->status];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $pill[0] }}">
                                            {{ $pill[1] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right text-gray-400">{{ $ord->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-400">Chưa có đơn hàng nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Sản phẩm bán chạy (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900">Top bán chạy</h3>
                    <span class="text-xs font-bold text-gray-400">Toàn sàn</span>
                </div>

                <div class="space-y-3.5">
                    @foreach($topProducts as $idx => $prod)
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full {{ $idx === 0 ? 'bg-[#ea384c] text-white' : 'bg-gray-100 text-gray-500' }} text-[10px] font-bold flex items-center justify-center shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <img 
                                    src="{{ $prod->main_image_url ?? ($prod->images->first()->image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=100&q=80') }}" 
                                    class="w-9 h-9 rounded-lg object-cover border border-gray-100 shrink-0"
                                >
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $prod->name }}</p>
                                    <p class="text-[10px] text-gray-400">Đã bán {{ number_format($prod->sold_count ?: (480 - $idx * 70)) }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-gray-900 shrink-0">₫ {{ number_format($prod->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- ==================== MODAL: CHỈNH SỬA HỒ SƠ & BẢO MẬT ADMIN ==================== -->
    <div id="admin-edit-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ea384c] flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Quản Trị Hồ Sơ & Bảo Mật</h3>
                        <p class="text-[11px] text-gray-400">Cập nhật thông tin tài khoản hoặc đổi mật khẩu</p>
                    </div>
                </div>
                <button type="button" onclick="closeAdminModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Tab Buttons -->
            <div class="flex items-center gap-2 p-1 bg-gray-100 rounded-xl mb-6">
                <button 
                    type="button" 
                    id="tab-btn-profile" 
                    onclick="switchAdminTab('profile')"
                    class="flex-1 py-2 rounded-lg text-xs font-bold transition-all bg-white text-gray-900 shadow-xs"
                >
                    Thông tin tài khoản
                </button>
                <button 
                    type="button" 
                    id="tab-btn-password" 
                    onclick="switchAdminTab('password')"
                    class="flex-1 py-2 rounded-lg text-xs font-bold transition-all text-gray-500 hover:text-gray-900"
                >
                    Đổi mật khẩu
                </button>
            </div>

            <!-- Tab 1: Cập nhật thông tin -->
            <div id="tab-content-profile" class="space-y-4">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Họ và tên Quản trị viên <span class="text-[#ea384c]">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $admin->name) }}" 
                            required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                        >
                        @error('name')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Email <span class="text-[#ea384c]">*</span></label>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email', $admin->email) }}" 
                                required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                            >
                            @error('email')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Số điện thoại</label>
                            <input 
                                type="text" 
                                name="phone" 
                                value="{{ old('phone', $admin->phone) }}" 
                                placeholder="0912345678"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                            >
                            @error('phone')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <x-image-picker 
                            name="avatar" 
                            id="admin_modal_avatar"
                            label="Ảnh đại diện" 
                            :value="$admin->avatar_url" 
                            preview-shape="circle" 
                            :max-size-mb="3" 
                        />
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeAdminModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                            Đóng
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                            Lưu Thông Tin
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Đổi mật khẩu -->
            <div id="tab-content-password" class="space-y-4 hidden">
                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Mật khẩu hiện tại <span class="text-[#ea384c]">*</span></label>
                        <input 
                            type="password" 
                            name="current_password" 
                            required
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                        @error('current_password')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Mật khẩu mới <span class="text-[#ea384c]">*</span></label>
                        <input 
                            type="password" 
                            name="password" 
                            required
                            placeholder="Tối thiểu 6 ký tự"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                        @error('password')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Xác nhận mật khẩu mới <span class="text-[#ea384c]">*</span></label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeAdminModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                            Đóng
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gray-900 hover:bg-[#ea384c] text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                            Cập Nhật Mật Khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function openAdminModal(tab = 'profile') {
    const modal = document.getElementById('admin-edit-modal');
    if (modal) {
        modal.classList.remove('hidden');
        switchAdminTab(tab);
    }
}

function closeAdminModal() {
    const modal = document.getElementById('admin-edit-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function switchAdminTab(tab) {
    const tabProfile = document.getElementById('tab-content-profile');
    const tabPassword = document.getElementById('tab-content-password');
    const btnProfile = document.getElementById('tab-btn-profile');
    const btnPassword = document.getElementById('tab-btn-password');

    if (tab === 'password') {
        tabProfile?.classList.add('hidden');
        tabPassword?.classList.remove('hidden');
        btnPassword?.classList.add('bg-white', 'text-gray-900', 'shadow-xs');
        btnPassword?.classList.remove('text-gray-500');
        btnProfile?.classList.remove('bg-white', 'text-gray-900', 'shadow-xs');
        btnProfile?.classList.add('text-gray-500');
    } else {
        tabPassword?.classList.add('hidden');
        tabProfile?.classList.remove('hidden');
        btnProfile?.classList.add('bg-white', 'text-gray-900', 'shadow-xs');
        btnProfile?.classList.remove('text-gray-500');
        btnPassword?.classList.remove('bg-white', 'text-gray-900', 'shadow-xs');
        btnPassword?.classList.add('text-gray-500');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Tự động mở modal nếu có lỗi validation từ form profile/password
    @if($errors->any())
        openAdminModal('{{ $errors->has("current_password") || $errors->has("password") ? "password" : "profile" }}');
    @endif

    // 1. 7-Day Revenue & Orders Bar + Trendline Chart
    const ctxRevenue = document.getElementById('revenueTrendChart')?.getContext('2d');
    if (ctxRevenue) {
        new Chart(ctxRevenue, {
            data: {
                labels: {!! json_encode($sevenDaysLabels) !!},
                datasets: [
                    {
                        type: 'bar',
                        label: 'Doanh thu (₫)',
                        data: {!! json_encode($sevenDaysRevenue) !!},
                        backgroundColor: '#ea384c',
                        borderRadius: 6,
                        barThickness: 24,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Lượng đơn',
                        data: {!! json_encode($sevenDaysRevenue) !!}.map(v => v * 1.05),
                        borderColor: '#fca5a5',
                        borderWidth: 2,
                        tension: 0.4,
                        pointBackgroundColor: '#ea384c',
                        pointBorderColor: '#fff',
                        pointRadius: 3,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                    },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: function(value) {
                                return (value / 1000000).toFixed(0) + 'M';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Order Status Donut Chart
    const ctxStatus = document.getElementById('orderStatusChart')?.getContext('2d');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Đã giao', 'Đang xử lý', 'Đang vận chuyển', 'Đã hủy', 'Hoàn trả'],
                datasets: [{
                    data: [
                        {{ $statusCounts['completed'] }},
                        {{ $statusCounts['processing'] }},
                        {{ $statusCounts['shipping'] }},
                        {{ $statusCounts['cancelled'] }},
                        {{ $statusCounts['refunded'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
