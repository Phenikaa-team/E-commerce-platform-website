@extends('layouts.admin')

@section('title', 'Quản Lý Mã Giảm Giá - ShopMart Admin')
@section('page_title', 'Quản Lý Mã Giảm Giá & Voucher Toàn Sàn')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left: Create Voucher Form (4 cols) -->
    <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <h3 class="text-sm font-black text-gray-900 mb-1">Tạo mã giảm giá mới</h3>
        <p class="text-xs text-gray-500 mb-6">Thiết lập coupon khuyến mãi theo % hoặc số tiền cố định</p>

        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="code" class="form-label">Mã Voucher (Code) <span class="text-rose-500">*</span></label>
                <input type="text" name="code" id="code" required placeholder="Ví dụ: SALE50K, FREESHIP..." class="form-input font-mono font-bold uppercase text-primary">
                @error('code')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="form-label">Tên chương trình khuyến mãi <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required placeholder="Ví dụ: Giảm 50k cho đơn từ 300k" class="form-input">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="discount_type" class="form-label">Loại giảm giá <span class="text-rose-500">*</span></label>
                    <select name="discount_type" id="discount_type" class="form-select">
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định (₫)</option>
                    </select>
                </div>

                <div>
                    <label for="discount_value" class="form-label">Mức giảm <span class="text-rose-500">*</span></label>
                    <input type="number" name="discount_value" id="discount_value" required min="1" placeholder="10 (%) hoặc 50000 (₫)" class="form-input font-bold text-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="min_order_value" class="form-label">Đơn tối thiểu (₫)</label>
                    <input type="number" name="min_order_value" id="min_order_value" min="0" placeholder="0" class="form-input">
                </div>

                <div>
                    <label for="max_discount_amount" class="form-label">Giảm tối đa (₫)</label>
                    <input type="number" name="max_discount_amount" id="max_discount_amount" min="0" placeholder="Không giới hạn" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="usage_limit" class="form-label">Giới hạn lượt dùng</label>
                    <input type="number" name="usage_limit" id="usage_limit" min="1" placeholder="100" class="form-input">
                </div>

                <div>
                    <label for="expires_at" class="form-label">Ngày hết hạn</label>
                    <input type="date" name="expires_at" id="expires_at" class="form-input">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full py-2.5">
                Tạo Mã Giảm Giá
            </button>
        </form>
    </div>

    <!-- Right: Coupons List Table (8 cols) -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-black text-gray-900">Danh sách Voucher toàn sàn</h3>
                <p class="text-xs text-gray-500">Các mã đang áp dụng tại trang thanh toán của người mua</p>
            </div>
        </div>

        @if($coupons->isEmpty())
            <div class="py-12 text-center text-gray-400 text-xs">
                Chưa có mã giảm giá nào. Hãy tạo mã đầu tiên!
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100">
                            <th class="pb-3 px-3">Mã Code</th>
                            <th class="pb-3 px-3">Mức giảm</th>
                            <th class="pb-3 px-3">Điều kiện</th>
                            <th class="pb-3 px-3">Đã dùng</th>
                            <th class="pb-3 px-3">Hạn dùng</th>
                            <th class="pb-3 px-3">Trạng thái</th>
                            <th class="pb-3 px-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($coupons as $cp)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-3">
                                    <span class="font-mono font-black text-primary text-sm block">{{ $cp->code }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $cp->name }}</span>
                                </td>

                                <td class="py-3.5 px-3 font-bold text-gray-900">
                                    {{ $cp->discount_type === 'percent' ? $cp->discount_value.'%' : number_format($cp->discount_value, 0, ',', '.').'₫' }}
                                    @if($cp->max_discount_amount)
                                        <span class="text-[10px] text-gray-400 block">Tối đa: {{ number_format($cp->max_discount_amount, 0, ',', '.') }}₫</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3 text-gray-600">
                                    Đơn từ: {{ number_format($cp->min_order_value, 0, ',', '.') }}₫
                                </td>

                                <td class="py-3.5 px-3 text-gray-800">
                                    <span class="font-bold">{{ $cp->used_count }}</span>
                                    @if($cp->usage_limit)
                                        <span class="text-gray-400">/ {{ $cp->usage_limit }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3 text-gray-500">
                                    {{ $cp->expires_at ? $cp->expires_at->format('d/m/Y') : 'Vô thời hạn' }}
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cp->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                                        {{ $cp->is_active ? 'Đang bật' : 'Đã tắt' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <form action="{{ route('admin.coupons.toggle', $cp->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-colors cursor-pointer {{ $cp->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                {{ $cp->is_active ? 'Tắt' : 'Bật' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.coupons.destroy', $cp->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg text-gray-400 hover:text-primary hover:bg-rose-50 transition-colors cursor-pointer" title="Xóa">
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
