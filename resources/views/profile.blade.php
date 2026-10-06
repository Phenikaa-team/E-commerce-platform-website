@extends('layouts.app')

@section('title', 'Hồ sơ người dùng - ' . ($user->username ?? $user->name) . ' | ShopMart')
@section('meta_description', 'Quản lý thông tin tài khoản, đơn mua, voucher và địa chỉ nhận hàng tại ShopMart.')

@section('content')
<div class="page-container py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Persistent User Sidebar -->
        <div class="lg:col-span-3">
            <x-user-sidebar active="profile" />
        </div>

        <!-- Right Column: Main Content -->
        <div class="lg:col-span-9 space-y-6">
            <x-third-party-password-alert />
                
            <!-- ==================== 1. COVER & USER PROFILE HEADER CARD ==================== -->
            <div class="profile-cover-card" style="background-image: url('{{ $user->cover_url ?? 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80' }}');">
                <!-- Dark Gradient Overlay for text clarity -->
                <div class="profile-cover-overlay"></div>

                <div class="relative p-6 sm:p-8 text-white">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        
                        <!-- Avatar & Details -->
                        <div class="flex items-center gap-5">
                            <!-- Avatar with Camera Badge -->
                            <a href="{{ route('profile.info') }}" class="relative shrink-0 group cursor-pointer block" title="Chỉnh sửa thông tin cá nhân">
                                <img 
                                    src="{{ $user->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=300&q=80' }}" 
                                    alt="{{ $user->username ?? $user->name }}" 
                                    class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover ring-4 ring-white/20 shadow-xl group-hover:ring-primary transition-all"
                                >
                                <div class="absolute bottom-0 right-0 w-7 h-7 bg-white text-gray-800 rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                            </a>

                            <!-- User Name & Badges -->
                            <div>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $user->username ?? $user->name }}</h1>
                                </div>
                                <p class="text-xs text-white/70 mt-1.5 flex items-center gap-2">
                                    <span>{{ $user->joined_date ?? 'Tham gia từ 06/2024' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Edit Profile Button -->
                        <div class="shrink-0">
                            <a 
                                href="{{ route('profile.info') }}" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs sm:text-sm font-semibold transition-all active:scale-95 cursor-pointer shadow-xs"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                <span>Chỉnh sửa hồ sơ</span>
                            </a>
                        </div>
                    </div>

                    <!-- Stats Counter Row (Real Database Counts) -->
                    <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-white/15 text-left">
                        <div>
                            <div class="text-2xl font-black text-white">{{ $ordersCount ?? $user->order_count ?? 0 }}</div>
                            <div class="text-xs text-white/70 mt-0.5">Đơn mua</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white">{{ $reviewsCount ?? $user->review_count ?? 0 }}</div>
                            <div class="text-xs text-white/70 mt-0.5">Đánh giá</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-white">{{ $user->voucher_count ?? 3 }}</div>
                            <div class="text-xs text-white/70 mt-0.5">Voucher</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== 2. ĐƠN MUA CỦA TÔI (ORDER TRACKER) ==================== -->
            <div id="orders" class="profile-tracker-card">
                <div class="profile-tracker-header">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-6 bg-primary rounded-full inline-block"></span>
                        <h2 class="text-base font-bold text-gray-900">Đơn mua của tôi</h2>
                    </div>
                    <a href="{{ route('user.orders') }}" class="text-xs font-semibold text-gray-500 hover:text-primary flex items-center gap-1 group transition-colors">
                        <span>Xem tất cả</span>
                        <x-icon name="chevron-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                    </a>
                </div>

                <!-- 5 Status Steps Grid -->
                <div class="profile-status-grid">
                    <!-- Status 1: Chờ xác nhận -->
                    <a href="{{ route('user.orders', ['status' => 'pending']) }}" class="profile-status-item group">
                        <div class="profile-status-icon">
                            <svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            @if(($statusCounts['pending'] ?? 0) > 0)
                                <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 bg-primary text-white text-[11px] font-bold rounded-full flex items-center justify-center shadow-xs">
                                    {{ $statusCounts['pending'] }}
                                </span>
                            @endif
                        </div>
                        <span class="profile-status-label">Chờ xác nhận</span>
                    </a>

                    <!-- Status 2: Chờ lấy hàng -->
                    <a href="{{ route('user.orders', ['status' => 'confirmed']) }}" class="profile-status-item group">
                        <div class="profile-status-icon">
                            <svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            @if(($statusCounts['confirmed'] ?? 0) > 0)
                                <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 bg-primary text-white text-[11px] font-bold rounded-full flex items-center justify-center shadow-xs">
                                    {{ $statusCounts['confirmed'] }}
                                </span>
                            @endif
                        </div>
                        <span class="profile-status-label">Chờ lấy hàng</span>
                    </a>

                    <!-- Status 3: Đang giao -->
                    <a href="{{ route('user.orders', ['status' => 'shipping']) }}" class="profile-status-item group">
                        <div class="profile-status-icon">
                            <svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            @if(($statusCounts['shipping'] ?? 0) > 0)
                                <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 bg-primary text-white text-[11px] font-bold rounded-full flex items-center justify-center shadow-xs">
                                    {{ $statusCounts['shipping'] }}
                                </span>
                            @endif
                        </div>
                        <span class="profile-status-label">Đang giao</span>
                    </a>

                    <!-- Status 4: Đã giao -->
                    <a href="{{ route('user.orders', ['status' => 'delivered']) }}" class="flex flex-col items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                        <div class="relative w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-50 transition-all">
                            <svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 mt-2.5 group-hover:text-primary transition-colors">Đã giao</span>
                    </a>

                    <!-- Status 5: Trả hàng / Hoàn tiền -->
                    <a href="{{ route('user.orders', ['status' => 'cancelled']) }}" class="flex flex-col items-center p-3 rounded-2xl hover:bg-gray-50 transition-all group">
                        <div class="relative w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-50 transition-all">
                            <svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 mt-2.5 group-hover:text-primary transition-colors">Trả hàng/Hoàn tiền</span>
                    </a>
                </div>
            </div>

            <!-- ==================== 3. KHỐI TIỆN ÍCH & HOẠT ĐỘNG (2 CỘT CÂN ĐỐI 50-50) ==================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                
                <!-- Cột trái: Voucher & Sổ địa chỉ -->
                <div class="space-y-5 flex flex-col justify-between">
                    <!-- Voucher của tôi Card -->
                    <div class="profile-subcard">
                        <div class="profile-subcard-header">
                            <div class="flex items-center gap-2">
                                <div class="profile-icon-badge bg-rose-50 text-primary">
                                    <x-icon name="ticket" class="w-3.5 h-3.5 text-primary" />
                                </div>
                                <h3 class="font-extrabold text-xs sm:text-sm text-gray-900">Voucher của tôi</h3>
                            </div>
                            <a href="{{ route('vouchers.index') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-0.5">
                                <span>Kho voucher</span>
                                <x-icon name="chevron-right" class="w-3 h-3" />
                            </a>
                        </div>
                        <div class="profile-banner-voucher">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-gray-900">Mã giảm giá khả dụng</div>
                                <div class="text-[11px] text-gray-500 mt-0.5 truncate">Bạn có <span class="font-bold text-primary">{{ $user->voucher_count ?? 3 }} voucher</span> có sẵn</div>
                            </div>
                            <a href="{{ route('vouchers.index') }}" class="px-3 py-1 bg-primary text-white text-xs font-bold rounded-lg shadow-xs hover:bg-rose-600 transition-colors shrink-0">
                                Dùng ngay
                            </a>
                        </div>
                    </div>

                    <!-- Địa chỉ nhận hàng Card -->
                    <div class="profile-subcard flex-1 flex flex-col justify-between">
                        <div>
                            <div class="profile-subcard-header">
                                <div class="flex items-center gap-2">
                                    <div class="profile-icon-badge bg-blue-50 text-blue-600">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-extrabold text-xs sm:text-sm text-gray-900">Địa chỉ nhận hàng</h3>
                                </div>
                                <a href="{{ route('profile.addresses') }}" class="text-xs font-semibold text-primary hover:underline">
                                    + Thêm mới
                                </a>
                            </div>
                            @php
                                $defaultAddress = $user->addresses->where('is_default', true)->first() ?? $user->addresses->first();
                            @endphp
                            @if($defaultAddress)
                                <div class="profile-address-box space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 text-xs">{{ $defaultAddress->name }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-gray-600 font-medium text-[11px]">{{ $defaultAddress->phone }}</span>
                                        @if($defaultAddress->is_default)
                                            <span class="px-1.5 py-0.2 bg-primary text-white text-[9px] font-bold rounded ml-auto">Mặc định</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 leading-relaxed text-[11px] line-clamp-1">{{ $defaultAddress->address_line }}</p>
                                    @if($defaultAddress->ward || $defaultAddress->district || $defaultAddress->city)
                                        <p class="text-gray-400 text-[10px] truncate">{{ implode(', ', array_filter([$defaultAddress->ward, $defaultAddress->district, $defaultAddress->city])) }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-4 text-xs text-gray-400">
                                    <p class="text-[11px]">Chưa có địa chỉ nhận hàng.</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-gray-100 text-xs">
                            <span class="text-gray-400 text-[11px]">{{ $user->addresses->count() }} địa chỉ đã lưu</span>
                            <a href="{{ route('profile.addresses') }}" class="text-primary font-semibold hover:underline flex items-center gap-0.5 text-xs">
                                <span>Quản lý</span>
                                <x-icon name="chevron-right" class="w-3 h-3" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Đơn hàng gần đây (Gọn gàng, cân xứng hoàn hảo) -->
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gray-50 text-gray-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-xs sm:text-sm text-gray-900">Đơn hàng gần đây</h3>
                                </div>
                            </div>
                            <a href="{{ route('user.orders') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-0.5">
                                <span>Xem tất cả ({{ $ordersCount ?? 0 }})</span>
                                <x-icon name="chevron-right" class="w-3 h-3" />
                            </a>
                        </div>

                        @if(isset($recentOrders) && $recentOrders->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach($recentOrders->take(2) as $order)
                                    <div class="py-3 flex items-center justify-between gap-3">
                                        <div class="space-y-0.5 min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-xs text-gray-900">#{{ $order->order_code ?? $order->id }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $order->created_at->format('d/m/Y') }}</span>
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        'shipping' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'Chờ xác nhận',
                                                        'processing' => 'Đang chuẩn bị',
                                                        'shipping' => 'Đang giao',
                                                        'completed' => 'Đã giao',
                                                        'cancelled' => 'Đã hủy',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold border {{ $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600">
                                                Tổng: <span class="font-bold text-primary">{{ number_format($order->total_amount ?? 0, 0, ',', '.') }}₫</span>
                                                @if($order->items->count() > 0)
                                                    <span class="text-gray-400 text-[10px] ml-1">({{ $order->items->count() }} món)</span>
                                                @endif
                                            </p>
                                        </div>

                                        <div class="shrink-0">
                                            <a href="{{ route('user.orders.show', $order->order_code ?? $order->id) }}" class="px-2.5 py-1 rounded-lg border border-gray-200 hover:border-primary hover:text-primary text-[11px] font-semibold text-gray-700 transition-colors">
                                                Chi tiết
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="w-10 h-10 rounded-full bg-rose-50 text-primary mx-auto flex items-center justify-center mb-1.5">
                                    <x-icon name="cart" class="w-5 h-5 text-primary" />
                                </div>
                                <h4 class="text-xs font-bold text-gray-800">Chưa có đơn hàng nào</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5">Bạn chưa đặt đơn hàng nào gần đây.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-2 text-xs py-1 px-3">
                                    Mua sắm ngay
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
