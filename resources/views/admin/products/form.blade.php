@extends('layouts.admin')

@section('title', $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm')

@section('content')
    <div class="max-w-3xl bg-white rounded-2xl border border-gray-100 p-6">
        <h1 class="text-lg font-black mb-5">{{ $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h1>

        <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product->id) : route('admin.products.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div class="md:col-span-2">
                <label class="form-label">Tên sản phẩm *</label>
                <input name="name" required value="{{ old('name', $product->name) }}" class="form-input w-full">
            </div>

            <div>
                <label class="form-label">Gian hàng *</label>
                <select name="store_id" required class="form-select w-full">
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}" @selected(old('store_id', $product->store_id) == $store->id)>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Danh mục</label>
                <select name="category_id" class="form-select w-full">
                    <option value="">Chưa phân loại</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Giá *</label>
                <input name="price" type="number" required value="{{ old('price', $product->price) }}" class="form-input w-full">
            </div>

            <div>
                <label class="form-label">Giá gốc</label>
                <input name="original_price" type="number" value="{{ old('original_price', $product->original_price) }}" class="form-input w-full">
            </div>

            <div>
                <label class="form-label">Tồn kho *</label>
                <input name="stock" type="number" required value="{{ old('stock', $product->stock ?? 0) }}" class="form-input w-full">
            </div>

            <div>
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select w-full">
                    <option value="active" @selected(old('status', $product->status) === 'active')>Đang bán</option>
                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Ẩn</option>
                    <option value="draft" @selected(old('status', $product->status) === 'draft')>Nháp</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="5" class="form-textarea w-full">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <button class="btn btn-primary">Lưu sản phẩm</button>
            </div>
        </form>
    </div>
@endsection