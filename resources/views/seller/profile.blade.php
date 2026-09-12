@extends('layouts.seller')

@section('title', 'Hồ Sơ Gian Hàng - ' . ($store->name ?? 'Gian hàng'))
@section('page_title', 'Hồ Sơ Gian Hàng')

@section('content')
<div class="space-y-2.5 max-w-[1850px] w-full mx-auto">

    <!-- Back / Breadcrumb matching Reference -->
    <div class="-mb-0.5">
        <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 hover:text-[#F52245] transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Quản lý gian hàng</span>
        </a>
    </div>

    <!-- 1. Store Hero Card matching Reference Image -->
    <div class="bg-white rounded-[20px] border border-[#E9ECF1] shadow-2xs overflow-hidden">
        <!-- Store Banner: Pure image element with sleek compact height (fills entire container width) -->
        <div class="h-36 sm:h-40 lg:h-44 w-full relative overflow-hidden bg-gray-100">
            <img 
                src="{{ $store->banner_url }}" 
                alt="Banner {{ $store->name }}" 
                class="w-full h-full object-cover object-center block"
            >
        </div>

        <!-- Store Info Row with Overlapping Logo -->
        <div class="px-6 sm:px-8 pb-5 pt-0 relative">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left: Avatar + Store Info -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 relative z-10">
                    <!-- Circular Avatar with Blue Verified Checkmark (overlaps banner) -->
                    <div class="-mt-10 sm:-mt-12 shrink-0 relative">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-white shadow-md bg-white overflow-hidden flex items-center justify-center select-none">
                            <img 
                                src="{{ $store->logo_url }}" 
                                alt="{{ $store->name }}" 
                                class="w-full h-full object-cover rounded-full"
                            >
                        </div>
                        <!-- Blue verified checkmark badge -->
                        <span class="absolute bottom-0.5 right-0.5 w-5 h-5 rounded-full bg-[#1877F2] text-white flex items-center justify-center border-2 border-white shadow-xs" title="Gian hàng đã xác minh">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                    </div>

                    <!-- Details: Clean spacing below banner, never overlaps text -->
                    <div class="min-w-0 pt-2 sm:pt-2.5 pb-0.5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $store->name ?? 'Gian hàng' }}</h1>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E8F8F0] text-[#10B981]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                {{ $store->status === 'active' ? 'Đang hoạt động' : 'Tạm dừng' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 max-w-2xl line-clamp-1">
                            {{ $store->description ?? 'Cửa hàng chính hãng trên ShopMart' }}
                        </p>
                        
                        <!-- Meta row with separator dots -->
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 mt-2.5">
                            <span class="flex items-center gap-1 text-amber-500 font-semibold">
                                <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ $stats['rating'] ?? '4.9' }}</span>
                                <span class="text-gray-400 font-normal">({{ $stats['followers'] ?? '2.458' }} đánh giá)</span>
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1.5 text-gray-500">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Đã tham gia: {{ $store->created_at ? $store->created_at->diffForHumans(null, true) : '12 tháng' }}</span>
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1.5 text-gray-500">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $store->address ? \Illuminate\Support\Str::limit($store->address, 30) : 'TP. Hồ Chí Minh' }}</span>
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1.5 text-gray-600">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Chính hãng 100%</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Two Action Buttons matching Reference -->
                <div class="flex items-center gap-3 shrink-0 pt-2 lg:pt-0">
                    <button 
                        type="button" 
                        onclick="document.getElementById('edit-profile-modal').classList.remove('hidden')"
                        class="px-4 py-2.5 bg-white hover:bg-gray-50 border border-[#E9ECF1] text-gray-800 text-xs font-semibold rounded-xl shadow-2xs transition-colors flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Chỉnh sửa thông tin</span>
                    </button>

                    <a 
                        href="{{ route('home') }}" 
                        target="_blank"
                        class="px-4 py-2.5 bg-[#F52245] hover:bg-[#d8193a] text-white text-xs font-semibold rounded-xl shadow-xs transition-colors flex items-center gap-2 cursor-pointer"
                    >
                        <span>Xem gian hàng</span>
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Exactly 4 KPI Cards matching Reference Image -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tổng sản phẩm (Soft Blue Box) -->
        <div class="bg-white rounded-[18px] p-4 border border-[#E9ECF1] shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#EFF6FF] text-[#3B82F6] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium">Tổng sản phẩm</span>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ $stats['total_products'] ?? 248 }}
                </h3>
                <span class="text-[11px] font-semibold text-[#10B981] flex items-center gap-0.5 mt-0.5">
                    ↑ 12% <span class="text-gray-400 font-normal">so với tháng trước</span>
                </span>
            </div>
        </div>

        <!-- Card 2: Đơn hàng (30 ngày) (Soft Purple Cart) -->
        <div class="bg-white rounded-[18px] p-4 border border-[#E9ECF1] shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F5F3FF] text-[#8B5CF6] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium">Đơn hàng (30 ngày)</span>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ number_format($stats['total_orders'] ?? 1248, 0, ',', '.') }}
                </h3>
                <span class="text-[11px] font-semibold text-[#10B981] flex items-center gap-0.5 mt-0.5">
                    ↑ 18% <span class="text-gray-400 font-normal">so với tháng trước</span>
                </span>
            </div>
        </div>

        <!-- Card 3: Tỷ lệ phản hồi (Soft Emerald Clock) -->
        <div class="bg-white rounded-[18px] p-4 border border-[#E9ECF1] shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#ECFDF5] text-[#10B981] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium">Tỷ lệ phản hồi</span>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ $store->response_rate ?? '98%' }}
                </h3>
                <span class="text-[11px] font-semibold text-[#10B981] flex items-center gap-0.5 mt-0.5">
                    ↑ 5% <span class="text-gray-400 font-normal">so với tháng trước</span>
                </span>
            </div>
        </div>

        <!-- Card 4: Đánh giá trung bình (Soft Amber Star) -->
        <div class="bg-white rounded-[18px] p-4 border border-[#E9ECF1] shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#FFFBEB] text-[#F59E0B] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium">Đánh giá trung bình</span>
                <h3 class="text-xl font-bold text-gray-900 mt-0.5">
                    {{ $stats['rating'] ?? '4.9' }} <span class="text-xs font-normal text-gray-400">/ 5</span>
                </h3>
                <span class="text-[11px] font-semibold text-[#10B981] flex items-center gap-0.5 mt-0.5">
                    ↑ 0.2 <span class="text-gray-400 font-normal">so với tháng trước</span>
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Main Dashboard: 3-column Layout (Left ~30%, Center ~40%, Right ~30%) -->
    <div class="grid grid-cols-1 xl:grid-cols-10 gap-5 items-start">
        
        <!-- ================= LEFT COLUMN: Thông tin cửa hàng (~30%) ================= -->
        <div class="xl:col-span-3 bg-white rounded-[20px] p-5 border border-[#E9ECF1] shadow-2xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <h3 class="text-sm font-bold text-gray-900">Thông tin cửa hàng</h3>
                </div>
                <button 
                    type="button" 
                    onclick="document.getElementById('edit-profile-modal').classList.remove('hidden')"
                    class="text-xs font-semibold text-blue-500 hover:text-blue-600 flex items-center gap-1 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Chỉnh sửa</span>
                </button>
            </div>

            <!-- Key / Value list display -->
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Tên gian hàng</span>
                    <span class="font-medium text-gray-800 text-right">{{ $store->name ?? 'Gian hàng' }}</span>
                </div>
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Loại hình kinh doanh</span>
                    <span class="font-medium text-gray-800 text-right">{{ $store->products->first()?->category?->name ?? 'Gian hàng chính hãng Mall' }}</span>
                </div>
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Mã gian hàng</span>
                    <span class="font-mono font-medium text-blue-600 flex items-center gap-1">
                        SHM{{ str_pad($store->id ?? 1, 5, '0', STR_PAD_LEFT) }}
                        <svg class="w-3.5 h-3.5 text-gray-400 hover:text-gray-700 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Ngày tham gia</span>
                    <span class="font-medium text-gray-800 text-right">{{ $store->created_at ? $store->created_at->format('d/m/Y') : '12/05/2024' }}</span>
                </div>
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Địa chỉ</span>
                    <span class="font-medium text-gray-800 text-right max-w-[190px]">{{ $store->address ?? 'TP. Hồ Chí Minh' }}</span>
                </div>
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Email liên hệ</span>
                    <span class="font-medium text-gray-800 text-right">{{ $user->email ?? 'seller@shopmart.vn' }}</span>
                </div>
                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 font-medium shrink-0">Số điện thoại</span>
                    <span class="font-medium text-gray-800 text-right">{{ $store->phone ?? $user->phone ?? '+84 912 345 678' }}</span>
                </div>
            </div>

            <!-- Bottom Verification Banner matching Reference -->
            <div class="p-3 rounded-xl bg-[#F0F7FF] border border-[#DBEAFE] flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-blue-500 text-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-blue-900 text-xs">Gian hàng đã được xác minh</p>
                        <p class="text-[10px] text-blue-600">Đã xác thực thông tin doanh nghiệp và giấy tờ pháp lý</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        <!-- ================= CENTER COLUMN: Hiệu suất hoạt động (~40%) ================= -->
        <div class="xl:col-span-4 bg-white rounded-[20px] p-5 border border-[#E9ECF1] shadow-2xs flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <h3 class="text-sm font-bold text-gray-900">Hiệu suất hoạt động</h3>
                    </div>

                    <select class="text-xs bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1 text-gray-600 font-medium focus:outline-hidden">
                        <option>30 ngày gần nhất</option>
                        <option>7 ngày gần nhất</option>
                        <option>90 ngày gần nhất</option>
                    </select>
                </div>

                <!-- Legend matching reference bullets -->
                <div class="flex items-center gap-4 text-xs font-semibold mb-3 text-gray-600">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#3B82F6]"></span>
                        Đơn hàng
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#06B6D4]"></span>
                        Lượt truy cập
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span>
                        Doanh thu
                    </span>
                </div>

                <!-- Multi-line Chart matching reference curves -->
                <div class="h-48 w-full relative">
                    <canvas id="storePerformanceChart"></canvas>
                </div>
            </div>

            <!-- 3 Mini Summary Boxes at the bottom matching Reference -->
            <div class="grid grid-cols-3 gap-2.5 pt-3 mt-3 border-t border-gray-100">
                <!-- Box 1: Lượt truy cập -->
                <div class="p-2.5 bg-[#F0F7FF] rounded-xl border border-[#DBEAFE]/70">
                    <div class="flex items-center gap-1 text-blue-600 mb-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span class="text-[10px] font-medium">Lượt truy cập</span>
                    </div>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">12.430</p>
                    <span class="text-[10px] font-semibold text-[#10B981]">↑ 15%</span>
                </div>

                <!-- Box 2: Đơn hàng -->
                <div class="p-2.5 bg-[#F5F3FF] rounded-xl border border-[#EDE9FE]/70">
                    <div class="flex items-center gap-1 text-purple-600 mb-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-[10px] font-medium">Đơn hàng</span>
                    </div>
                    <p class="text-sm font-bold text-gray-900 mt-0.5">1.248</p>
                    <span class="text-[10px] font-semibold text-[#10B981]">↑ 18%</span>
                </div>

                <!-- Box 3: Doanh thu -->
                <div class="p-2.5 bg-[#FFFBEB] rounded-xl border border-[#FEF3C7]/70">
                    <div class="flex items-center gap-1 text-amber-600 mb-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-medium">Doanh thu</span>
                    </div>
                    <p class="text-xs font-bold text-gray-900 mt-0.5 truncate">125.430.000đ</p>
                    <span class="text-[10px] font-semibold text-[#10B981]">↑ 22%</span>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT COLUMN: Đánh giá & Hoạt động (~30%) ================= -->
        <div class="xl:col-span-3 bg-white rounded-[20px] p-5 border border-[#E9ECF1] shadow-2xs space-y-5">
            
            <!-- 1. Đánh giá gần đây matching Reference -->
            <div>
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <h3 class="text-xs font-bold text-gray-900">Đánh giá gần đây</h3>
                    </div>
                    <a href="{{ route('seller.orders.index') }}" class="text-[11px] font-medium text-blue-500 hover:underline">Xem tất cả</a>
                </div>

                <div class="space-y-2.5 text-xs">
                    @forelse($recentReviews as $rev)
                        <div class="space-y-1 {{ !$loop->first ? 'pt-2 border-t border-gray-50' : '' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <img src="{{ $rev->user?->avatar_url ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=60&q=80' }}" class="w-5 h-5 rounded-full object-cover shrink-0">
                                    <span class="font-semibold text-gray-900 text-[11px] truncate">{{ $rev->user?->name ?? 'Khách hàng' }}</span>
                                    <div class="flex items-center text-amber-400 gap-0.5 ml-1">
                                        @for($i=0; $i < ($rev->rating ?? 5); $i++)
                                            <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-[10px] text-gray-400 shrink-0">{{ $rev->created_at ? $rev->created_at->format('d/m/Y') : 'Vừa xong' }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 line-clamp-1">{{ $rev->comment ?? 'Sản phẩm chính hãng, rất hài lòng!' }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-2">Chưa có đánh giá nào gần đây.</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. Hoạt động gần đây matching Reference -->
            <div class="pt-2">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100">
                    <h3 class="text-xs font-bold text-gray-900">Hoạt động gần đây</h3>
                    <a href="{{ route('seller.orders.index') }}" class="text-[11px] font-medium text-blue-500 hover:underline">Xem tất cả</a>
                </div>

                <div class="space-y-3 text-xs">
                    @forelse($recentOrders as $ro)
                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#EFF6FF] text-[#3B82F6] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <p class="font-semibold text-gray-900 text-[11px] truncate">Đơn hàng #{{ $ro->order_code ?? 'SM' . $ro->id }}</p>
                                    <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md {{ $ro->status === 'completed' ? 'bg-[#E8F8F0] text-[#10B981]' : 'bg-amber-50 text-amber-700' }} shrink-0">
                                        {{ $ro->status === 'completed' ? 'Hoàn thành' : ($ro->status === 'shipping' ? 'Đang giao' : 'Chờ xử lý') }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-gray-500 truncate">{{ $ro->items->first()?->product_name ?? 'Sản phẩm gian hàng' }}</p>
                                <span class="text-[10px] text-gray-400 block mt-0.5">{{ $ro->created_at ? $ro->created_at->diffForHumans() : 'Gần đây' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 py-2">Chưa có hoạt động đơn hàng nào gần đây.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<!-- 4. Edit Store Profile Modal -->
<div id="edit-profile-modal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-[24px] max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-[#E9ECF1] p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Thiết lập Gian hàng & Thông tin người bán</h2>
                <p class="text-xs text-gray-400">Cập nhật thông tin thương hiệu, liên hệ và địa chỉ kho</p>
            </div>
            <button 
                type="button" 
                onclick="document.getElementById('edit-profile-modal').classList.add('hidden')"
                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('seller.profile.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Tên gian hàng <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="store_name" 
                        value="{{ old('store_name', $store->name) }}" 
                        required
                        class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                    >
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Trạng thái mở bán</label>
                    <select 
                        name="status" 
                        class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                    >
                        <option value="active" {{ old('status', $store->status) === 'active' ? 'selected' : '' }}>Đang hoạt động (Mở bán)</option>
                        <option value="inactive" {{ old('status', $store->status) === 'inactive' ? 'selected' : '' }}>Tạm dừng kinh doanh</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Mô tả / Giới thiệu gian hàng</label>
                <textarea 
                    name="store_description" 
                    rows="3" 
                    class="w-full p-3.5 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                >{{ old('store_description', $store->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Họ & Tên người đại diện</label>
                    <input 
                        type="text" 
                        name="user_name" 
                        value="{{ old('user_name', $user->name) }}" 
                        required
                        class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                    >
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Số điện thoại liên hệ</label>
                    <input 
                        type="text" 
                        name="phone" 
                        value="{{ old('phone', $store->phone ?? $user->phone) }}" 
                        class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                    >
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1.5">Địa chỉ kho hàng / Xuất xứ</label>
                <input 
                    type="text" 
                    name="address" 
                    value="{{ old('address', $store->address) }}" 
                    placeholder="123 Nguyễn Văn Cừ, Quận 1, TP. Hồ Chí Minh"
                    class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">URL Logo thương hiệu</label>
                    <input 
                        type="url" 
                        name="store_logo_url" 
                        value="{{ old('store_logo_url', $store->logo_url) }}" 
                        placeholder="https://..."
                        class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                    >
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">URL Ảnh bìa (Banner)</label>
                    <input 
                        type="url" 
                        name="store_banner_url" 
                        value="{{ old('store_banner_url', $store->banner_url) }}" 
                        placeholder="https://..."
                        class="w-full h-11 px-4 bg-[#F7F8FA] border border-[#E9ECF1] rounded-xl text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden transition-colors"
                    >
                </div>
            </div>

            <!-- Bank Payout Settings -->
            <div class="pt-3 border-t border-gray-100">
                <h4 class="font-bold text-gray-800 text-xs mb-3">Tài khoản ngân hàng nhận doanh thu</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-gray-500 font-semibold mb-1">Ngân hàng</label>
                        <input 
                            type="text" 
                            name="bank_name" 
                            value="{{ old('bank_name', $store->bank_name) }}" 
                            placeholder="Vietcombank"
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-[#E9ECF1] rounded-lg text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-500 font-semibold mb-1">Số tài khoản</label>
                        <input 
                            type="text" 
                            name="bank_account_number" 
                            value="{{ old('bank_account_number', $store->bank_account_number) }}" 
                            placeholder="0123456789"
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-[#E9ECF1] rounded-lg text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-500 font-semibold mb-1">Tên chủ thẻ</label>
                        <input 
                            type="text" 
                            name="bank_account_name" 
                            value="{{ old('bank_account_name', $store->bank_account_name) }}" 
                            placeholder="NGUYEN VAN A"
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-[#E9ECF1] rounded-lg text-gray-900 focus:bg-white focus:border-[#F52245] focus:outline-hidden"
                        >
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button 
                    type="button" 
                    onclick="document.getElementById('edit-profile-modal').classList.add('hidden')"
                    class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors cursor-pointer"
                >
                    Hủy
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-[#F52245] hover:bg-[#d8193a] text-white font-bold shadow-xs transition-all cursor-pointer"
                >
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const perfCtx = document.getElementById('storePerformanceChart')?.getContext('2d');
        if (perfCtx) {
            new Chart(perfCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels ?? ['10/05', '14/05', '18/05', '22/05', '26/05', '30/05', '02/06']) !!},
                    datasets: [
                        {
                            label: 'Lượt truy cập',
                            data: {!! json_encode($chartVisits ?? [450, 920, 1100, 1250, 1420, 1600, 1850]) !!},
                            borderColor: '#06b6d4', // Cyan curve matching reference
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            pointBackgroundColor: '#06b6d4',
                        },
                        {
                            label: 'Doanh thu',
                            data: {!! json_encode($chartRevenue ?? [320, 680, 850, 1020, 1190, 1340, 1550]) !!},
                            borderColor: '#f59e0b', // Amber curve matching reference
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            pointBackgroundColor: '#f59e0b',
                        },
                        {
                            label: 'Đơn hàng',
                            data: {!! json_encode($chartOrders ?? [280, 520, 640, 760, 890, 1020, 1180]) !!},
                            borderColor: '#3b82f6', // Blue curve matching reference
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            pointBackgroundColor: '#3b82f6',
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
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 8,
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 10 }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#94a3b8' }
                        },
                        y: {
                            min: 0,
                            max: 2000,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                stepSize: 500,
                                font: { size: 10 },
                                color: '#94a3b8',
                                callback: function(val) {
                                    if (val === 0) return '0';
                                    return val >= 1000 ? (val / 1000).toFixed(1) + 'K' : val;
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
