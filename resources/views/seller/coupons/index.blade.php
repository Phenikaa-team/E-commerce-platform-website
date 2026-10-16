@extends('layouts.seller')

@section('title', 'Voucher của shop - ShopMart Seller')
@section('page_title', 'Voucher của shop')

@section('content')
<div class="space-y-5 max-w-[1600px] w-full mx-auto">


    <!-- 2. Header: Title + Subtitle + "+ Tạo voucher" Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Voucher của shop</h1>
            <p class="text-xs text-gray-500 mt-1">
                Tạo và quản lý voucher giảm giá để thu hút khách hàng và tăng doanh số bán hàng cho gian hàng của bạn.
            </p>
        </div>

        <a href="{{ route('seller.coupons.create') }}" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold shadow-xs hover:shadow-md transition-all shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Tạo voucher</span>
        </a>
    </div>

    <!-- 3. Highlight Pink Banner: Khuyến mãi gian hàng [Store Name] -->
    <div class="seller-voucher-hero-card">
        <div class="flex items-center gap-3.5">
            <div class="seller-voucher-hero-icon-box">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12.75 3.03a1.5 1.5 0 0 0-1.06.44l-8.25 8.25a1.5 1.5 0 0 0 0 2.12l5.72 5.72a1.5 1.5 0 0 0 2.12 0l8.25-8.25a1.5 1.5 0 0 0 .44-1.06V4.5a1.5 1.5 0 0 0-1.5-1.5h-5.72ZM17.25 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"/>
                    <path d="m9.5 8.5 4 4m-4 1.5a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm4-4a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Khuyến mãi gian hàng {{ $store->name ?? 'Shop' }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tạo voucher để thu hút khách hàng, tăng tỷ lệ chuyển đổi và phát triển gian hàng của bạn.</p>
            </div>
        </div>

        <div class="seller-voucher-hero-status">
            <span class="seller-voucher-hero-dot"></span>
            <span><strong class="text-gray-900 font-bold">{{ $activeCount ?? 0 }}</strong> đang hoạt động</span>
        </div>
    </div>

    <!-- 4. Main White Card: Header, Filter Tabs, Search Bar, Table / Empty State -->
    <div class="seller-voucher-main-card">
        <div class="mb-4">
            <h2 class="text-base font-bold text-gray-900">Voucher của shop</h2>
            <p class="text-xs text-gray-400 mt-0.5">Danh sách các voucher mà bạn đã tạo. Bạn có thể tìm kiếm, lọc theo trạng thái để dễ dàng quản lý.</p>
        </div>

        <!-- Filter Tabs & Search Bar Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-0.5">
            <!-- Navigation Tabs matching Mockup -->
            <div class="seller-voucher-tabs-bar border-b-0 pb-0">
                <a href="{{ route('seller.coupons.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}" class="seller-voucher-tab-btn {{ ($status ?? 'all') === 'all' ? 'is-active' : '' }}">
                    Tất cả ({{ $totalCount ?? 0 }})
                </a>
                <a href="{{ route('seller.coupons.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}" class="seller-voucher-tab-btn {{ ($status ?? 'all') === 'active' ? 'is-active' : '' }}">
                    Đang hoạt động ({{ $activeCount ?? 0 }})
                </a>
                <a href="{{ route('seller.coupons.index', array_merge(request()->except(['status', 'page']), ['status' => 'expired'])) }}" class="seller-voucher-tab-btn {{ ($status ?? 'all') === 'expired' ? 'is-active' : '' }}">
                    Đã kết thúc ({{ $expiredCount ?? 0 }})
                </a>
            </div>

            <!-- Search Input with Search Icon matching Mockup -->
            <form action="{{ route('seller.coupons.index') }}" method="GET" class="seller-voucher-search-wrap mb-2 sm:mb-0">
                @if(!empty($status) && $status !== 'all')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <svg class="seller-voucher-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Tìm mã voucher..." 
                    class="seller-voucher-search-input"
                >
            </form>
        </div>

        <!-- 5. Content Area: Empty State OR Data Table -->
        @if($coupons->isEmpty())
            <!-- Minimalist Empty State matching mockup perfectly -->
            <div class="seller-voucher-empty-box">
                <div class="seller-voucher-empty-icon-wrapper">
                    <svg class="w-7 h-7 fill-none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03a1.5 1.5 0 0 0-1.06.44l-8.25 8.25a1.5 1.5 0 0 0 0 2.12l5.72 5.72a1.5 1.5 0 0 0 2.12 0l8.25-8.25a1.5 1.5 0 0 0 .44-1.06V4.5a1.5 1.5 0 0 0-1.5-1.5h-5.72ZM17.25 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Chưa có voucher nào</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm">
                    Voucher bạn tạo sẽ hiển thị tại đây để bạn theo dõi và quản lý.
                </p>
                <a href="{{ route('seller.coupons.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold border border-gray-200 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Tạo voucher ngay</span>
                </a>
            </div>
        @else
            <!-- Data Table with Sleek Formatting -->
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100">
                            <th class="py-3 px-4">Mã Voucher</th>
                            <th class="py-3 px-4">Mức giảm</th>
                            <th class="py-3 px-4">Đơn tối thiểu</th>
                            <th class="py-3 px-4">Lượt dùng</th>
                            <th class="py-3 px-4">Hạn sử dụng</th>
                            <th class="py-3 px-4">Trạng thái</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($coupons as $c)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 fill-none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03a1.5 1.5 0 0 0-1.06.44l-8.25 8.25a1.5 1.5 0 0 0 0 2.12l5.72 5.72a1.5 1.5 0 0 0 2.12 0l8.25-8.25a1.5 1.5 0 0 0 .44-1.06V4.5a1.5 1.5 0 0 0-1.5-1.5h-5.72ZM17.25 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-mono font-bold text-rose-600 text-sm block">{{ $c->code }}</span>
                                            <span class="text-[11px] text-gray-500">{{ $c->name }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $c->discount_type === 'percent' ? $c->discount_value.'%' : number_format($c->discount_value, 0, ',', '.').'₫' }}
                                </td>

                                <td class="py-3.5 px-4 text-gray-600">
                                    {{ number_format($c->min_order_value, 0, ',', '.') }}₫
                                </td>

                                <td class="py-3.5 px-4 text-gray-800">
                                    <span class="font-bold">{{ $c->used_count }}</span>
                                    @if($c->usage_limit)
                                        <span class="text-gray-400">/ {{ $c->usage_limit }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-gray-500">
                                    {{ $c->expires_at ? $c->expires_at->format('d/m/Y H:i') : 'Vô thời hạn' }}
                                </td>

                                <td class="py-3.5 px-4">
                                    @php
                                        $isExpired = $c->expires_at && $c->expires_at->isPast();
                                    @endphp
                                    @if($isExpired)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            Đã kết thúc
                                        </span>
                                    @elseif($c->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Đang hoạt động
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Tạm tắt
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit button -->
                                        <a href="{{ route('seller.coupons.edit', $c->id) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-sky-600 hover:bg-sky-50 transition-colors" title="Chỉnh sửa voucher">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <!-- Toggle button -->
                                        <form action="{{ route('seller.coupons.toggle', $c->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-colors cursor-pointer {{ $c->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                {{ $c->is_active ? 'Tắt' : 'Bật' }}
                                            </button>
                                        </form>

                                        <!-- Delete button -->
                                        <form action="{{ route('seller.coupons.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Xóa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
