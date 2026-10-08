@extends('layouts.admin')

@section('title', ($product->exists ? 'Sửa Sản Phẩm' : 'Thêm Sản Phẩm Mới') . ' - ShopMart Admin')
@section('page_title', $product->exists ? 'Cập Nhật Thông Tin Sản Phẩm' : 'Thêm Sản Phẩm Mới Vào Hệ Thống')

@section('content')
<div class="max-w-4xl bg-white rounded-xl border border-gray-100 shadow-xs p-6">
    <div class="pb-4 mb-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">{{ $product->exists ? 'Chỉnh sửa sản phẩm #' . $product->id : 'Tạo mới sản phẩm' }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Điền thông tin chi tiết về sản phẩm, giá bán, tồn kho và danh mục</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-md rounded-xl text-xs font-bold">
            Quay lại danh sách
        </a>
    </div>

    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product->id) : route('admin.products.store') }}" class="space-y-6">
        @csrf
        @if($product->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="name" class="form-label">Tên sản phẩm <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name', $product->name) }}" placeholder="Nhập tên sản phẩm..." class="form-input w-full">
                @error('name')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="store_id" class="form-label">Gian hàng sở hữu <span class="text-rose-500">*</span></label>
                <select name="store_id" id="store_id" required class="form-select w-full">
                    <option value="">-- Chọn gian hàng --</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" @selected(old('store_id', $product->store_id) == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('store_id')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="form-label">Danh mục ngành hàng</label>
                <select name="category_id" id="category_id" class="form-select w-full">
                    <option value="">-- Chưa phân loại --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->seller_menu_title ?? $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="form-label">Giá bán hiện tại (₫) <span class="text-rose-500">*</span></label>
                <input type="number" name="price" id="price" required min="0" value="{{ old('price', $product->price) }}" placeholder="Ví dụ: 150000" class="form-input w-full font-bold text-primary">
                @error('price')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="original_price" class="form-label">Giá gốc chưa giảm (₫)</label>
                <input type="number" name="original_price" id="original_price" min="0" value="{{ old('original_price', $product->original_price) }}" placeholder="Ví dụ: 200000" class="form-input w-full text-gray-500">
                @error('original_price')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="stock" class="form-label">Số lượng tồn kho <span class="text-rose-500">*</span></label>
                <input type="number" name="stock" id="stock" required min="0" value="{{ old('stock', $product->stock ?? 0) }}" placeholder="Ví dụ: 100" class="form-input w-full font-semibold">
                @error('stock')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="form-label">Trạng thái bán</label>
                <select name="status" id="status" class="form-select w-full">
                    <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Đang mở bán</option>
                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Tạm ẩn</option>
                    <option value="draft" @selected(old('status', $product->status) === 'draft')>Lưu bản nháp</option>
                </select>
                @error('status')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="form-label">Mô tả sản phẩm</label>
                <textarea name="description" id="description" rows="5" placeholder="Nhập mô tả chi tiết sản phẩm..." class="form-textarea w-full">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="btn btn-primary btn-md rounded-xl px-6 py-2.5 text-xs font-bold shadow-xs">
                {{ $product->exists ? 'Lưu thay đổi' : 'Thêm sản phẩm' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-md rounded-xl px-5 py-2.5 text-xs font-bold">
                Hủy bỏ
            </a>
        </div>
    </form>
</div>
@endsection
