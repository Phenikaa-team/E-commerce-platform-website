@extends('layouts.seller')

@section('title', 'Chỉnh Sửa Sản Phẩm - ' . $product->name)
@section('page_title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xs">
        
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Cập nhật thông tin: {{ $product->name }}</h2>
                <p class="text-xs text-gray-400">Mã sản phẩm: #{{ $product->id }} | Lượt bán: {{ $product->sold_count }}</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-[#ea384c] font-semibold transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Quay lại danh sách</span>
            </a>
        </div>

        <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Product Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tên sản phẩm <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name', $product->name) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Brand -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Danh mục ngành hàng <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Thương hiệu</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Giá bán ưu đãi (VNĐ) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="price" required min="0" value="{{ old('price', (int) $product->price) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-[#ea384c] focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>

                <div>
                    <label for="original_price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Giá niêm yết gốc (VNĐ)</label>
                    <input type="number" name="original_price" id="original_price" min="0" value="{{ old('original_price', (int) $product->original_price) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-500 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>

                <div>
                    <label for="stock" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tồn kho <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" required min="0" value="{{ old('stock', $product->stock) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>
            </div>

            <!-- Variants Configuration -->
            @php
                $colorList = '';
                if (!empty($product->variants['colors'])) {
                    $colorList = implode(', ', array_map(fn($c) => is_array($c) ? ($c['label'] ?? '') : $c, $product->variants['colors']));
                }
                $optionList = '';
                if (!empty($product->variants['options'])) {
                    $optionList = implode(', ', array_map(fn($o) => is_array($o) ? ($o['name'] ?? '') : $o, $product->variants['options']));
                }
            @endphp
            <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-100 space-y-4">
                <span class="text-xs font-bold text-amber-900 block">Thiết lập biến thể phân loại</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="color_variants" class="block text-xs text-gray-600 mb-1">Màu sắc (ngăn cách bằng dấu phẩy):</label>
                        <input type="text" name="color_variants" id="color_variants" value="{{ old('color_variants', $colorList) }}" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs">
                    </div>

                    <div>
                        <label for="size_variants" class="block text-xs text-gray-600 mb-1">Kích thước / Dung lượng (ngăn cách bằng dấu phẩy):</label>
                        <input type="text" name="size_variants" id="size_variants" value="{{ old('size_variants', $optionList) }}" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs">
                    </div>
                </div>
            </div>

            <!-- Current & New Images -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Ảnh sản phẩm hiện tại</label>
                <div class="flex items-center gap-3">
                    <img src="{{ $product->main_image_url }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                    @foreach($product->images as $img)
                        <img src="{{ $img->url }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="main_image" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Thay ảnh đại diện mới</label>
                    <input type="file" name="main_image" id="main_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                </div>

                <div>
                    <label for="images" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tải thêm ảnh vào bộ sưu tập</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mô tả sản phẩm chi tiết <span class="text-rose-500">*</span></label>
                <textarea name="description" id="description" rows="6" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Flash Sale Checkbox -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_flash_sale" id="is_flash_sale" value="1" {{ $product->is_flash_sale ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400">
                <label for="is_flash_sale" class="text-xs font-bold text-gray-700 cursor-pointer">Đăng ký tham gia Flash Sale giờ vàng</label>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Hủy</a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                    Lưu Thay Đổi
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
