@extends('layouts.seller')

@section('title', 'Mã Giảm Giá Của Shop - ShopMart Seller')
@section('page_title', 'Mã Giảm Giá Của Shop (Kênh Khuyến Mãi)')

@section('content')
<div class="space-y-6">

    <!-- Top Header: Title + Action Button to Create Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Mã Giảm Giá Của Shop</h1>
            <p class="text-xs text-gray-500 mt-0.5">Quản lý các chương trình ưu đãi, voucher giảm giá kích cầu mua sắm tại cửa hàng của bạn.</p>
        </div>

        <a href="{{ route('seller.coupons.create') }}" class="seller-btn-save inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Tạo Voucher Mới</span>
        </a>
    </div>

    <!-- Top Banner Explainer -->
    <div class="seller-banner-card">
        <div>
            <div class="seller-banner-badge">
                <span>KÊNH MARKETING TĂNG DOANH SỐ</span>
            </div>
            <h2 class="text-xl font-black">Khuyến Mãi Gian Hàng {{ $store->name }}</h2>
            <p class="text-xs text-white/90 mt-1 max-w-xl">
                Tạo mã giảm giá riêng của shop để thu hút khách hàng quay lại, gia tăng tỷ lệ chốt đơn và kích cầu đơn hàng giá trị cao.
            </p>
        </div>
        <div class="seller-banner-stats">
            <span class="block text-[11px] text-white/80">Số voucher đang chạy:</span>
            <span class="text-xl font-black">{{ $coupons->where('is_active', true)->count() }} / {{ $coupons->total() }}</span>
        </div>
    </div>

    <!-- Coupons Table Card -->
    <div class="seller-card overflow-hidden">
        <div class="seller-card-header flex items-center justify-between">
            <div>
                <h3 class="seller-card-title">Danh sách Voucher của shop</h3>
                <p class="seller-card-subtitle">Khách hàng sẽ thấy mã này trên trang cửa hàng và trang chi tiết sản phẩm của bạn</p>
            </div>
            <a href="{{ route('seller.coupons.create') }}" class="seller-btn-ghost-action text-xs">
                <span>+ Thêm voucher</span>
            </a>
        </div>

        @if($coupons->isEmpty())
            <div class="py-14 text-center text-gray-400 text-xs">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <p class="font-bold text-gray-700">Gian hàng chưa tạo mã giảm giá nào</p>
                <p class="text-gray-400 mt-1 mb-4">Hãy tạo mã giảm giá đầu tiên để khuyến khích khách mua nhiều hơn!</p>
                <a href="{{ route('seller.coupons.create') }}" class="seller-btn-save inline-flex items-center gap-1.5 mx-auto">
                    <span>Tạo voucher ngay</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100">
                            <th class="pb-3 px-4">Mã Voucher</th>
                            <th class="pb-3 px-4">Mức giảm</th>
                            <th class="pb-3 px-4">Đơn tối thiểu</th>
                            <th class="pb-3 px-4">Lượt dùng</th>
                            <th class="pb-3 px-4">Hạn sử dụng</th>
                            <th class="pb-3 px-4">Trạng thái</th>
                            <th class="pb-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($coupons as $c)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center font-black text-xs shrink-0">
                                            🏷️
                                        </div>
                                        <div>
                                            <span class="font-mono font-black text-rose-600 text-sm block">{{ $c->code }}</span>
                                            <span class="text-[10px] text-gray-500">{{ $c->name }}</span>
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
                                    {{ $c->expires_at ? $c->expires_at->format('d/m/Y') : 'Vô thời hạn' }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                        {{ $c->is_active ? 'Đang bật' : 'Đã tắt' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit button -->
                                        <a href="{{ route('seller.coupons.edit', $c->id) }}" class="p-1 rounded-lg text-gray-400 hover:text-sky-600 hover:bg-sky-50 transition-colors" title="Chỉnh sửa voucher">
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
                                            <button type="submit" class="p-1 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Xóa">
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

            <div class="mt-4 pt-3 border-t border-gray-100 px-4 pb-2">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
