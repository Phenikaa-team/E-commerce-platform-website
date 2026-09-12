@extends('layouts.seller')

@section('title', 'Mã Giảm Giá Của Shop - ShopMart Seller')
@section('page_title', 'Mã Giảm Giá Của Shop (Kênh Khuyến Mãi)')

@section('content')
<div class="space-y-6">

    <!-- Top Banner Explainer -->
    <div class="bg-gradient-to-r from-rose-500 via-[#ea384c] to-[#ff5c6c] rounded-2xl p-6 text-white shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold mb-2">
                <span>KÊNH MARKETING TĂNG DOANH SỐ</span>
            </div>
            <h2 class="text-xl font-black">Khuyến Mãi Gian Hàng {{ $store->name }}</h2>
            <p class="text-xs text-white/90 mt-1 max-w-xl">
                Tạo mã giảm giá riêng của shop để thu hút khách hàng quay lại, gia tăng tỷ lệ chốt đơn và kích cầu đơn hàng giá trị cao.
            </p>
        </div>
        <div class="bg-white/15 backdrop-blur-md px-4 py-3 rounded-xl border border-white/20 text-xs">
            <span class="block text-[11px] text-white/80">Số voucher đang chạy:</span>
            <span class="text-xl font-black">{{ $coupons->where('is_active', true)->count() }} / {{ $coupons->total() }}</span>
        </div>
    </div>

    <!-- 2 Columns: Create Voucher & List -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Create Form (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <h3 class="text-sm font-black text-gray-900 mb-1">Tạo Voucher Gian Hàng Mới</h3>
            <p class="text-xs text-gray-500 mb-4">Chi phí giảm giá sẽ trừ trực tiếp vào doanh thu từng đơn của shop.</p>

            <form action="{{ route('seller.coupons.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Mã Voucher (Code) <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="code" 
                        required 
                        value="{{ old('code') }}" 
                        placeholder="VD: SHOPGIA20K, MUAHE50" 
                        class="w-full h-10 px-3 uppercase font-mono font-bold bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                    >
                    @error('code') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tên chương trình / Mô tả <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        value="{{ old('name') }}" 
                        placeholder="VD: Giảm 20k cho đơn từ 200k" 
                        class="w-full h-10 px-3 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                    >
                    @error('name') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Loại giảm giá</label>
                        <select 
                            name="type" 
                            id="coupon_type"
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                            <option value="fixed">Số tiền cố định (₫)</option>
                            <option value="percent">Phần trăm (%)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                        <input 
                            type="number" 
                            name="value" 
                            id="coupon_value"
                            required 
                            min="1000" 
                            step="1000"
                            value="{{ old('value', 20000) }}" 
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden font-bold text-gray-900"
                        >
                        @error('value') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Đơn tối thiểu (₫)</label>
                        <input 
                            type="number" 
                            name="min_spend" 
                            min="0" 
                            step="10000"
                            value="{{ old('min_spend', 0) }}" 
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Số lượt dùng tối đa</label>
                        <input 
                            type="number" 
                            name="usage_limit" 
                            min="1" 
                            placeholder="Không giới hạn"
                            value="{{ old('usage_limit', 100) }}" 
                            class="w-full h-10 px-3 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Bắt đầu từ</label>
                        <input 
                            type="datetime-local" 
                            name="starts_at" 
                            value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}"
                            class="w-full h-10 px-2 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden text-[11px]"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Hết hạn vào</label>
                        <input 
                            type="datetime-local" 
                            name="expires_at" 
                            value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d\TH:i')) }}"
                            class="w-full h-10 px-2 bg-[#F7F8FA] border border-gray-200 rounded-xl focus:bg-white focus:border-[#ea384c] focus:outline-hidden text-[11px]"
                        >
                    </div>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full h-11 rounded-xl bg-[#ea384c] hover:bg-rose-600 text-white font-bold transition-all shadow-xs hover:shadow-md cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span>Phát Hành Voucher Ngay</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Coupons Table (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Danh sách Voucher của shop</h3>
                    <p class="text-xs text-gray-500">Khách hàng sẽ thấy mã này trên trang cửa hàng và trang chi tiết sản phẩm của bạn</p>
                </div>
            </div>

            @if($coupons->isEmpty())
                <div class="py-14 text-center text-gray-400 text-xs">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <p class="font-bold text-gray-700">Gian hàng chưa tạo mã giảm giá nào</p>
                    <p class="text-gray-400 mt-1">Hãy tạo mã giảm giá đầu tiên để khuyến khích khách mua nhiều hơn!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 font-bold border-b border-gray-100">
                                <th class="pb-3 px-3">Mã Voucher</th>
                                <th class="pb-3 px-3">Mức giảm</th>
                                <th class="pb-3 px-3">Đơn tối thiểu</th>
                                <th class="pb-3 px-3">Lượt dùng</th>
                                <th class="pb-3 px-3">Hạn sử dụng</th>
                                <th class="pb-3 px-3">Trạng thái</th>
                                <th class="pb-3 px-3 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($coupons as $c)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="py-3.5 px-3">
                                        <span class="font-mono font-black text-amber-700 text-sm block">{{ $c->code }}</span>
                                        <span class="text-[10px] text-gray-500">{{ $c->name }}</span>
                                    </td>

                                    <td class="py-3.5 px-3 font-bold text-gray-900">
                                        {{ $c->discount_type === 'percent' ? $c->discount_value.'%' : number_format($c->discount_value, 0, ',', '.').'₫' }}
                                    </td>

                                    <td class="py-3.5 px-3 text-gray-600">
                                        {{ number_format($c->min_order_value, 0, ',', '.') }}₫
                                    </td>

                                    <td class="py-3.5 px-3 text-gray-800">
                                        <span class="font-bold">{{ $c->used_count }}</span>
                                        @if($c->usage_limit)
                                            <span class="text-gray-400">/ {{ $c->usage_limit }}</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-3 text-gray-500">
                                        {{ $c->expires_at ? $c->expires_at->format('d/m/Y') : 'Vô thời hạn' }}
                                    </td>

                                    <td class="py-3.5 px-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                            {{ $c->is_active ? 'Đang bật' : 'Đã tắt' }}
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-3 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <form action="{{ route('seller.coupons.toggle', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-colors cursor-pointer {{ $c->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ $c->is_active ? 'Tắt' : 'Bật' }}
                                                </button>
                                            </form>

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

                <div class="mt-4 pt-3 border-t border-gray-100">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
