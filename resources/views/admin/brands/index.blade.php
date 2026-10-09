@extends('layouts.admin')

@section('title', 'Quản lý thương hiệu & từ khóa - ShopMart Admin')
@section('page_title', 'Quản lý thương hiệu & từ khóa')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-100 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="admin-brand-layout">
        <section class="admin-brand-management order-2 rounded-2xl border border-gray-100 bg-white p-6 shadow-xs">
            <h3 class="text-sm font-black text-gray-900">Thêm brand chuẩn</h3>
            <p class="mt-1 mb-6 text-xs leading-5 text-gray-500">Brand seller nhập mới sẽ ở trạng thái chờ duyệt. Admin có thể thêm trực tiếp brand đã xác minh.</p>

            <form method="POST" action="{{ route('admin.brands.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Tên thương hiệu <span class="text-rose-500">*</span></label>
                    <input name="name" required class="form-input w-full" placeholder="Ví dụ: Apple, Samsung, Anker...">
                </div>
                <div>
                    <label class="form-label">Logo URL <span class="font-normal text-gray-400">(không bắt buộc)</span></label>
                    <input name="logo_url" type="url" class="form-input w-full" placeholder="https://...">
                </div>
                <div>
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select w-full">
                        <option value="approved">Đã duyệt</option>
                        <option value="pending">Chờ duyệt</option>
                        <option value="hidden">Đang ẩn</option>
                    </select>
                </div>
                <button class="btn btn-primary w-full py-2.5">Thêm thương hiệu</button>
            </form>

            <div class="mt-6 rounded-xl bg-slate-50 p-4 text-xs leading-5 text-slate-500">
                <strong class="text-slate-700">Cách hoạt động:</strong> seller nhập brand mới thì hệ thống tự tạo một brand chờ duyệt. Khi duyệt alias như “Táo”, “Apple VN” có thể trỏ về cùng một brand chuẩn.
            </div>

            <div class="mt-6 border-t border-gray-100 pt-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Brand chờ duyệt</h3>
                        <p class="mt-1 text-xs text-gray-500">Kiểm tra và xác nhận brand seller gửi lên.</p>
                    </div>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-700">
                        {{ $brands->where('status', 'pending')->count() }} chờ duyệt
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($brands->where('status', 'pending') as $pendingBrand)
                        <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-gray-900">{{ $pendingBrand->name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $pendingBrand->products_count }} sản phẩm · slug: {{ $pendingBrand->slug }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.brands.approve', $pendingBrand->id) }}">
                                    @csrf
                                    <button class="shrink-0 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">Duyệt brand</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-xs text-gray-400">
                            Không có brand nào đang chờ duyệt.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="admin-brand-list order-1 rounded-2xl border border-gray-100 bg-white p-6 shadow-xs">
            <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Danh sách brand</h3>
                    <p class="mt-1 text-xs text-gray-500">Duyệt, ẩn/hiện và quản lý từ khóa đồng nghĩa mà seller sử dụng.</p>
                </div>
                <span class="rounded-full bg-rose-50 px-3 py-1 text-[11px] font-bold text-rose-600">{{ $brands->count() }} brand</span>
            </div>

            <div class="space-y-3">
                @forelse($brands as $brand)
                    <details class="group rounded-2xl border border-gray-100 bg-gray-50/50" @if($brand->status === 'pending') open @endif>
                        <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white text-sm font-black text-rose-500 shadow-xs">
                                @if($brand->logo_url)
                                    <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="h-full w-full object-contain">
                                @else
                                    {{ mb_strtoupper(mb_substr($brand->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-gray-900">{{ $brand->name }}</span>
                                    @if($brand->status === 'pending')
                                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">Chờ duyệt</span>
                                    @elseif($brand->status === 'approved')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Đã duyệt</span>
                                    @else
                                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-bold text-gray-600">Đang ẩn</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-[11px] text-gray-400">{{ $brand->products_count }} sản phẩm · {{ $brand->aliases->count() }} từ khóa thay thế · slug: {{ $brand->slug }}</p>
                            </div>
                            <span class="text-gray-400 transition-transform group-open:rotate-180">⌄</span>
                        </summary>

                        <div class="admin-brand-detail-grid border-t border-gray-100 bg-white p-4">
                            <form method="POST" action="{{ route('admin.brands.update', $brand->id) }}" class="space-y-3">
                                @csrf @method('PUT')
                                <p class="text-xs font-black text-gray-900">Thông tin chuẩn</p>
                                <input name="name" value="{{ $brand->name }}" required class="form-input w-full text-xs">
                                <input name="logo_url" value="{{ $brand->logo_url }}" type="url" class="form-input w-full text-xs" placeholder="Logo URL">
                                <div class="flex gap-2">
                                    <select name="status" class="form-select min-w-0 flex-1 text-xs">
                                        <option value="approved" @selected($brand->status === 'approved')>Đã duyệt</option>
                                        <option value="pending" @selected($brand->status === 'pending')>Chờ duyệt</option>
                                        <option value="hidden" @selected($brand->status === 'hidden')>Đang ẩn</option>
                                    </select>
                                    <button class="btn btn-primary px-4 py-2 text-xs">Lưu</button>
                                </div>
                            </form>

                            <div>
                                <p class="mb-3 text-xs font-black text-gray-900">Từ khóa đồng nghĩa / alias</p>
                                <form method="POST" action="{{ route('admin.brands.aliases.store', $brand->id) }}" class="mb-3 flex gap-2">
                                    @csrf
                                    <input name="alias" required class="form-input min-w-0 flex-1 text-xs" placeholder="Ví dụ: Táo, Apple VN">
                                    <button class="btn btn-primary px-4 py-2 text-xs">Thêm</button>
                                </form>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($brand->aliases as $alias)
                                        <details class="group/alias inline-block">
                                            <summary class="inline-flex cursor-pointer list-none items-center gap-1 rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-semibold text-gray-600">
                                                {{ $alias->alias }} <span class="text-gray-400">⌄</span>
                                            </summary>
                                            <div class="absolute z-20 mt-1 w-56 rounded-xl border border-gray-100 bg-white p-3 shadow-lg">
                                                <form method="POST" action="{{ route('admin.brands.aliases.update', $alias->id) }}" class="space-y-2">
                                                    @csrf @method('PUT')
                                                    <input name="alias" value="{{ $alias->alias }}" required class="form-input w-full text-xs">
                                                    <button class="btn btn-primary w-full py-1.5 text-xs">Lưu từ khóa</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.brands.aliases.destroy', $alias->id) }}" class="mt-2" onsubmit="return confirm('Xóa từ khóa này?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="w-full rounded-lg bg-rose-50 px-2 py-1.5 text-xs font-bold text-rose-600">Xóa từ khóa</button>
                                                </form>
                                            </div>
                                        </details>
                                    @empty
                                        <span class="text-xs text-gray-400">Chưa có alias.</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 lg:col-span-2">
                                @if($brand->status === 'pending')
                                    <form method="POST" action="{{ route('admin.brands.approve', $brand->id) }}">@csrf<button class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">Duyệt brand</button></form>
                                @endif
                                <form method="POST" action="{{ route('admin.brands.toggle', $brand->id) }}">@csrf<button class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">{{ $brand->status === 'hidden' ? 'Hiển thị lại' : 'Ẩn brand' }}</button></form>
                                <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" onsubmit="return confirm('Brand sẽ được ẩn, sản phẩm cũ không bị mất liên kết. Tiếp tục?')">@csrf @method('DELETE')<button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600">Ẩn khỏi hệ thống</button></form>
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 py-12 text-center text-sm text-gray-400">Chưa có brand nào.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
