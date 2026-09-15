@extends('layouts.seller')

@section('title', 'Thêm Sản Phẩm Mới - ShopMart Seller')
@section('page_title', 'Tạo & Đăng Bán Sản Phẩm Mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xs">
        
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Thông tin sản phẩm</h2>
                <p class="text-xs text-gray-400">Điền thông tin chi tiết để sản phẩm hiển thị bắt mắt trên sàn</p>
            </div>
            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-primary font-semibold transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Quay lại danh sách</span>
            </a>
        </div>

        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Product Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tên sản phẩm <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Ví dụ: Tai nghe chống ồn Sony WH-1000XM5 Chính Hãng" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Brand -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Danh mục ngành hàng <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Thương hiệu / Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Ví dụ: Sony, Apple, Samsung..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Giá bán ưu đãi (VNĐ) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="price" required min="0" value="{{ old('price') }}" placeholder="2990000" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold text-primary focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>

                <div>
                    <label for="original_price" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Giá niêm yết gốc (VNĐ)</label>
                    <input type="number" name="original_price" id="original_price" min="0" value="{{ old('original_price') }}" placeholder="3990000" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-500 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>

                <div>
                    <label for="stock" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Số lượng tồn kho <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" required min="0" value="{{ old('stock', 10) }}" placeholder="10" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">
                </div>
            </div>

            <!-- Variants Configuration -->
            <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-100 space-y-4">
                <span class="text-xs font-bold text-amber-900 block">Thiết lập biến thể phân loại (Tùy chọn)</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="color_variants" class="block text-xs text-gray-600 mb-1">Màu sắc (ngăn cách bằng dấu phẩy):</label>
                        <input type="text" name="color_variants" id="color_variants" value="{{ old('color_variants') }}" placeholder="Đen Nhám, Trắng Bạc, Xanh Titan" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs">
                    </div>

                    <div>
                        <label for="size_variants" class="block text-xs text-gray-600 mb-1">Kích thước / Dung lượng (ngăn cách bằng dấu phẩy):</label>
                        <input type="text" name="size_variants" id="size_variants" value="{{ old('size_variants') }}" placeholder="128GB, 256GB, 512GB hoặc S, M, L" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs">
                    </div>
                </div>
            </div>

            <!-- Main Image & Gallery Upload -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50/70 border border-gray-100 rounded-2xl">
                <div>
                    <x-image-picker 
                        name="main_image" 
                        label="Ảnh đại diện sản phẩm" 
                        preview-shape="rounded" 
                        :max-size-mb="3" 
                        help-text="Ảnh đại diện chính. Định dạng JPG, PNG, WEBP. Tối đa 3MB."
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Thêm bộ ảnh mô tả (Gallery)</label>
                    <div class="space-y-3">
                        <label for="images" class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-2xs transition-all cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Chọn nhiều ảnh tải lên</span>
                        </label>
                        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/avif" class="hidden">
                        <p class="text-[11px] text-gray-400">Có thể chọn nhiều ảnh cùng lúc (JPG, PNG, WEBP). Tối đa 3MB/ảnh.</p>
                        
                        <!-- Multi-image Preview Grid -->
                        <div id="gallery-preview-grid" class="flex flex-wrap gap-2 pt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Mô tả sản phẩm chi tiết <span class="text-rose-500">*</span></label>
                <textarea name="description" id="description" rows="6" required placeholder="Nhập các thông số kỹ thuật, tính năng nổi bật, cam kết chất lượng..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-900 focus:bg-white focus:border-amber-500 focus:outline-hidden">{{ old('description') }}</textarea>
            </div>

            <!-- Flash Sale Checkbox -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_flash_sale" id="is_flash_sale" value="1" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400">
                <label for="is_flash_sale" class="text-xs font-bold text-gray-700 cursor-pointer">Đăng ký tham gia chương trình Flash Sale giờ vàng</label>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('seller.products.index') }}" class="px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Hủy bỏ</a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                    Đăng Bán Sản Phẩm
                </button>
            </div>

        </form>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const imagesInput = document.getElementById('images');
    const previewGrid = document.getElementById('gallery-preview-grid');

    if (imagesInput && previewGrid) {
        imagesInput.addEventListener('change', (e) => {
            previewGrid.innerHTML = '';
            const files = Array.from(e.target.files || []);
            
            files.forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;
                
                const card = document.createElement('div');
                card.className = 'image-preview-card group';
                
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'image-preview-card__img';
                
                const badge = document.createElement('span');
                badge.className = 'image-preview-card__badge';
                badge.textContent = '#' + (index + 1);

                card.appendChild(img);
                card.appendChild(badge);
                previewGrid.appendChild(card);
            });
        });
    }
});
</script>
@endpush
@endsection
