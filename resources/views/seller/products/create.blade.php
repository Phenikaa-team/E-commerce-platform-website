@extends('layouts.seller')

@section('title', 'Thêm Sản Phẩm Mới - ShopMart Seller')
@section('page_title', 'Tạo & Đăng Bán Sản Phẩm Mới')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="seller-form-panel">
        
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
                <label for="name" class="seller-form-label">Tên sản phẩm <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Ví dụ: Tai nghe chống ồn Sony WH-1000XM5 Chính Hãng" class="seller-form-input">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Brand -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="category_id" class="seller-form-label mb-0">Danh mục ngành hàng <span class="text-rose-500">*</span></label>
                        @if(!empty($store->registered_categories))
                            <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                Đã đăng ký cho shop
                            </span>
                        @endif
                    </div>
                    <select name="category_id" id="category_id" required class="seller-form-input">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @if(!empty($store->registered_categories))
                        <p class="text-[11px] text-gray-400 mt-1">Chỉ được chọn các danh mục mà gian hàng đã đăng ký với ShopMart.</p>
                    @endif
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="brand" class="seller-form-label mb-0">Thương hiệu / Brand</label>
                        @if(!empty($registeredBrands))
                            <span class="text-[10px] text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                                Thương hiệu ủy quyền
                            </span>
                        @endif
                    </div>
                    @if(!empty($registeredBrands) && count($registeredBrands) > 0)
                        <div class="space-y-2">
                            <input 
                                type="text" 
                                name="brand" 
                                id="brand" 
                                list="registered_brands_list"
                                value="{{ old('brand', $registeredBrands[0] ?? '') }}" 
                                placeholder="Chọn hoặc nhập thương hiệu..." 
                                class="seller-form-input"
                            >
                            <datalist id="registered_brands_list">
                                @foreach($registeredBrands as $rb)
                                    <option value="{{ $rb }}"></option>
                                @endforeach
                            </datalist>
                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                <span class="text-[10px] text-gray-400">Gợi ý nhanh:</span>
                                @foreach($registeredBrands as $rb)
                                    <button 
                                        type="button" 
                                        onclick="document.getElementById('brand').value = '{{ $rb }}'" 
                                        class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 cursor-pointer transition-colors"
                                    >
                                        {{ $rb }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Ví dụ: Sony, Apple, Samsung..." class="seller-form-input">
                    @endif
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="price" class="seller-form-label">Giá bán ưu đãi (VNĐ) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="price" required min="0" value="{{ old('price') }}" placeholder="2990000" class="seller-form-input font-bold text-primary">
                </div>

                <div>
                    <label for="original_price" class="seller-form-label">Giá niêm yết gốc (VNĐ)</label>
                    <input type="number" name="original_price" id="original_price" min="0" value="{{ old('original_price') }}" placeholder="3990000" class="seller-form-input text-gray-500">
                </div>

                <div>
                    <label for="stock" class="seller-form-label">Số lượng tồn kho <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" required min="0" value="{{ old('stock', 10) }}" placeholder="10" class="seller-form-input">
                </div>
            </div>

            <!-- Variants Configuration -->
            <div class="seller-variants-panel">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-amber-900 block">Thiết lập biến thể & Bảng giá phân loại</span>
                        <p class="text-[11px] text-gray-500 mt-0.5">Cho phép cài đặt giá và kho riêng cho từng dung lượng, màu sắc hoặc phiên bản giới hạn.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="color_variants" class="block text-xs font-semibold text-gray-700 mb-1">Màu sắc (ngăn cách bằng dấu phẩy):</label>
                        <input type="text" name="color_variants" id="color_variants" value="{{ old('color_variants') }}" placeholder="Titan Tự Nhiên, Titan Sa Mạc (Bản giới hạn), Titan Đen" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 focus:outline-hidden">
                    </div>

                    <div>
                        <label for="size_variants" class="block text-xs font-semibold text-gray-700 mb-1">Dung lượng / Kích thước (ngăn cách bằng dấu phẩy):</label>
                        <input type="text" name="size_variants" id="size_variants" value="{{ old('size_variants') }}" placeholder="256GB, 512GB, 1TB hoặc S, M, L" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 focus:outline-hidden">
                    </div>
                </div>

                <!-- Live Variant Price Matrix Table -->
                <div id="variant-matrix-container" class="hidden pt-3 border-t border-amber-200/60 space-y-2.5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>Danh sách phân loại & giá cụ thể cho từng phiên bản:</span>
                        </span>
                        <button type="button" id="btn-sync-base-prices" class="px-3 py-1 bg-white hover:bg-amber-100 border border-amber-300 text-amber-900 rounded-lg text-[11px] font-bold shadow-2xs transition-colors cursor-pointer">
                            ⚡ Áp dụng giá cơ bản cho tất cả
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-2xs">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-bold">
                                    <th class="py-2.5 px-3">Tên biến thể</th>
                                    <th class="py-2.5 px-3">Giá bán ưu đãi (VNĐ) <span class="text-rose-500">*</span></th>
                                    <th class="py-2.5 px-3">Giá niêm yết (VNĐ)</th>
                                    <th class="py-2.5 px-3 w-28">Tồn kho <span class="text-rose-500">*</span></th>
                                    <th class="py-2.5 px-3 w-32">Mã SKU</th>
                                </tr>
                            </thead>
                            <tbody id="variant-matrix-tbody" class="divide-y divide-gray-100">
                                <!-- Generated dynamically via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-gray-400 italic">Mẹo: Bạn có thể đặt giá cao hơn cho các bản dung lượng lớn hơn (ví dụ 1TB đắt hơn 512GB) hoặc các bản màu giới hạn đặc biệt.</p>
                </div>

                <input type="hidden" name="variants_data" id="variants_data" value="{{ old('variants_data') }}">
            </div>

            <!-- Main Image & Gallery Upload -->
            <div class="seller-upload-panel">
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
                    <label class="seller-form-label">Thêm bộ ảnh mô tả (Gallery)</label>
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
                <label for="description" class="seller-form-label">Mô tả sản phẩm chi tiết <span class="text-rose-500">*</span></label>
                <textarea name="description" id="description" rows="6" required placeholder="Nhập các thông số kỹ thuật, tính năng nổi bật, cam kết chất lượng..." class="seller-form-textarea">{{ old('description') }}</textarea>
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
@vite(['resources/js/pages/seller-product-form.js'])
@endpush
@endsection
