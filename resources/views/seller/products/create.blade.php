@extends('layouts.seller')

@section('title', 'Thêm sản phẩm mới - ShopMart Seller')
@section('page_title', 'Thêm sản phẩm mới')

@section('content')
<div class="seller-product-container">
    
    <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" id="seller-product-form">
        @csrf
        <input type="hidden" name="submission_token" value="{{ $submissionToken }}">

        <!-- Top Header: Back + Title/Subtitle + Actions (Only at top) -->
        <div class="seller-page-header">
            <div class="seller-page-header-left">
                <a href="{{ route('seller.products.index') }}" class="seller-back-btn" title="Quay lại danh sách">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="seller-page-title">Thêm sản phẩm mới</h1>
                    <p class="seller-page-subtitle">Điền đầy đủ thông tin để thêm sản phẩm vào cửa hàng của bạn</p>
                </div>
            </div>

            <div class="seller-header-actions">
                <a href="{{ route('seller.products.index') }}" class="seller-btn-cancel">Hủy</a>
                <button type="submit" class="seller-btn-save">
                    Lưu sản phẩm
                </button>
            </div>
        </div>

        <!-- 2-Column Responsive Layout matching Mockup -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN (Span 8) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- 1. THÔNG TIN CƠ BẢN -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v18M3 9h18"/>
                        </svg>
                        <span>Thông tin cơ bản</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Row 1: Tên sản phẩm, Danh mục, Thương hiệu -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
                            <!-- Tên sản phẩm (Span 5) -->
                            <div class="md:col-span-5 seller-input-group">
                                <label for="name" class="seller-field-label">Tên sản phẩm <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    required 
                                    value="{{ old('name') }}" 
                                    placeholder="Nhập tên sản phẩm..." 
                                    class="seller-field-input"
                                >
                            </div>

                            <!-- Danh mục (Span 4) with Modern Custom Dropdown -->
                            <div class="md:col-span-4 seller-input-group">
                                <label class="seller-field-label">Danh mục <span class="required">*</span></label>
                                <div class="flex items-center gap-1.5">
                                    <div class="custom-select-wrapper flex-1" data-select-for="category_id">
                                        <select name="category_id" id="category_id" required class="hidden">
                                            <option value="">Chọn danh mục...</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->seller_menu_title ?? $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="custom-select-trigger">
                                            @php
                                                $selectedCat = $categories->firstWhere('id', old('category_id'));
                                            @endphp
                                            <span class="custom-select-text truncate">
                                                {{ $selectedCat ? ($selectedCat->seller_menu_title ?? $selectedCat->name) : 'Chọn danh mục...' }}
                                            </span>
                                            <svg class="custom-select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                        <div class="custom-select-options custom-scrollbar">
                                            <div class="custom-select-option text-gray-400" data-value="" data-label="Chọn danh mục...">Chọn danh mục...</div>
                                            @foreach($categories as $cat)
                                                <div 
                                                    class="custom-select-option {{ old('category_id') == $cat->id ? 'is-selected' : '' }}" 
                                                    data-value="{{ $cat->id }}" 
                                                    data-label="{{ $cat->seller_menu_title ?? $cat->name }}"
                                                >
                                                    <span>{{ $cat->seller_menu_title ?? $cat->name }}</span>
                                                    @if(old('category_id') == $cat->id)
                                                        <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button type="button" class="seller-btn-add-action" data-action-add-category title="Thêm nhanh danh mục">+</button>
                                </div>
                            </div>

                            <!-- Thương hiệu (Span 3) -->
                            <div class="md:col-span-3 seller-input-group">
                                <label for="brand" class="seller-field-label">Thương hiệu</label>
                                <div class="flex items-center gap-1.5">
                                    <input 
                                        type="text" 
                                        name="brand" 
                                        id="brand" 
                                        list="registered_brands_list"
                                        value="{{ old('brand') }}" 
                                        placeholder="Chọn thương hiệu..." 
                                        class="seller-field-input flex-1"
                                    >
                                    <datalist id="registered_brands_list">
                                        @foreach($registeredBrands ?? [] as $rb)
                                            <option value="{{ $rb }}"></option>
                                        @endforeach
                                    </datalist>
                                    <button type="button" class="seller-btn-add-action" data-action-add-brand title="Thêm nhanh thương hiệu">+</button>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Mã sản phẩm (SKU), Barcode (mã vạch), Trạng thái toggle -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end pt-1">
                            <!-- Mã sản phẩm (SKU) (Span 5) -->
                            <div class="md:col-span-5 seller-input-group">
                                <label for="sku" class="seller-field-label">Mã sản phẩm (SKU) <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    name="sku" 
                                    id="sku" 
                                    value="{{ old('sku', 'SP' . rand(1000, 9999)) }}" 
                                    placeholder="VD: SP001" 
                                    class="seller-field-input"
                                >
                            </div>

                            <!-- Barcode (mã vạch) (Span 4) -->
                            <div class="md:col-span-4 seller-input-group">
                                <label for="barcode" class="seller-field-label">Barcode (mã vạch)</label>
                                <input 
                                    type="text" 
                                    name="barcode" 
                                    id="barcode" 
                                    value="{{ old('barcode') }}" 
                                    placeholder="Nhập mã vạch (nếu có)" 
                                    class="seller-field-input"
                                >
                            </div>

                            <!-- Trạng thái (Span 3) -->
                            <div class="md:col-span-3 seller-input-group">
                                <label class="seller-field-label">Trạng thái</label>
                                <div class="seller-toggle-wrapper h-10 flex items-center">
                                    <label class="seller-switch">
                                        <input type="checkbox" name="status" value="active" checked>
                                        <span class="seller-switch-slider"></span>
                                    </label>
                                    <span class="seller-switch-label">Đang hoạt động</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. GIÁ & KHO HÀNG -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                        </svg>
                        <span>Giá &amp; Kho hàng</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Row 1: Giá bán, Giá khuyến mãi, Giá nhập, Số lượng tồn kho -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <!-- Giá bán * -->
                            <div class="seller-input-group">
                                <label for="original_price" class="seller-field-label">Giá niêm yết <span class="optional">(tùy chọn)</span></label>
                                <div class="seller-input-with-suffix">
                                    <input 
                                        type="number" 
                                        name="original_price" 
                                        id="original_price" 
                                        min="0" 
                                        value="{{ old('original_price', 0) }}" 
                                        placeholder="0" 
                                        class="seller-field-input"
                                    >
                                    <span class="seller-input-suffix">₫</span>
                                </div>
                            </div>

                            <!-- Giá khuyến mãi -->
                            <div class="seller-input-group">
                                <div class="flex items-center justify-between">
                                    <label for="price" class="seller-field-label">Giá bán thường <span class="optional">(nếu có khuyến mãi)</span></label>
                                    <button type="button" data-action-promo-schedule class="text-[10px] text-gray-400 hover:text-gray-700 inline-flex items-center gap-1 cursor-pointer bg-transparent border-none p-0" title="Cài đặt lịch áp dụng">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Thời gian áp dụng
                                    </button>
                                </div>
                                <div class="seller-input-with-suffix">
                                    <input 
                                        type="number" 
                                        name="price" 
                                        id="price" 
                                        min="0" 
                                        value="{{ old('price', 0) }}" 
                                        placeholder="0" 
                                        class="seller-field-input"
                                    >
                                    <span class="seller-input-suffix">₫</span>
                                </div>
                            </div>

                            <!-- Giá nhập (tùy chọn) -->
                            <div class="seller-input-group">
                                <label for="import_price" class="seller-field-label">Giá nhập <span class="optional">(tùy chọn)</span></label>
                                <div class="seller-input-with-suffix">
                                    <input 
                                        type="number" 
                                        name="import_price" 
                                        id="import_price" 
                                        min="0" 
                                        value="{{ old('import_price', 0) }}" 
                                        placeholder="0" 
                                        class="seller-field-input"
                                    >
                                    <span class="seller-input-suffix">₫</span>
                                </div>
                            </div>

                            <!-- Số lượng tồn kho * -->
                            <div class="seller-input-group">
                                <label for="stock" class="seller-field-label">Số lượng tồn kho <span class="required">*</span></label>
                                <input 
                                    type="number" 
                                    name="stock" 
                                    id="stock" 
                                    min="0" 
                                    required 
                                    value="{{ old('stock', 0) }}" 
                                    placeholder="0" 
                                    class="seller-field-input"
                                >
                            </div>
                        </div>

                        <!-- Row 2: Đơn vị tính, Kích thước (D x R x C), Trọng lượng -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end pt-1">
                            <!-- Đơn vị tính (Span 3) with Modern Custom Dropdown -->
                            <div class="md:col-span-3 seller-input-group">
                                <label class="seller-field-label">Đơn vị tính</label>
                                <div class="custom-select-wrapper" data-select-for="unit">
                                    <select name="unit" id="unit" class="hidden">
                                        <option value="">Chọn đơn vị...</option>
                                        <option value="cai" selected>Cái</option>
                                        <option value="chiec">Chiếc</option>
                                        <option value="hop">Hộp</option>
                                        <option value="bo">Bộ</option>
                                    </select>
                                    <div class="custom-select-trigger">
                                        <span class="custom-select-text">Cái</span>
                                        <svg class="custom-select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                    <div class="custom-select-options">
                                        <div class="custom-select-option is-selected" data-value="cai" data-label="Cái">Cái</div>
                                        <div class="custom-select-option" data-value="chiec" data-label="Chiếc">Chiếc</div>
                                        <div class="custom-select-option" data-value="hop" data-label="Hộp">Hộp</div>
                                        <div class="custom-select-option" data-value="bo" data-label="Bộ">Bộ</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kích thước (D x R x C) (Span 5) -->
                            <div class="md:col-span-5 seller-input-group">
                                <label class="seller-field-label">Kích thước (D x R x C)</label>
                                <div class="seller-dimension-grid">
                                    <input type="number" name="dimension_l" placeholder="Dài" min="0" value="{{ old('dimension_l') }}">
                                    <input type="number" name="dimension_w" placeholder="Rộng" min="0" value="{{ old('dimension_w') }}">
                                    <input type="number" name="dimension_h" placeholder="Cao" min="0" value="{{ old('dimension_h') }}">
                                    <span class="seller-dimension-unit">cm</span>
                                </div>
                            </div>

                            <!-- Trọng lượng (Span 4) -->
                            <div class="md:col-span-4 seller-input-group">
                                <label for="weight" class="seller-field-label">Trọng lượng</label>
                                <div class="seller-input-with-suffix">
                                    <input 
                                        type="number" 
                                        name="weight" 
                                        id="weight" 
                                        step="0.01" 
                                        min="0" 
                                        value="{{ old('weight', 0) }}" 
                                        placeholder="0" 
                                        class="seller-field-input"
                                    >
                                    <span class="seller-input-suffix">kg</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden variant configuration bridge -->
                        <div class="hidden">
                            <input type="text" name="color_variants" id="color_variants" value="{{ old('color_variants') }}">
                            <input type="text" name="size_variants" id="size_variants" value="{{ old('size_variants') }}">
                            <input type="hidden" name="variants_data" id="variants_data" value="{{ old('variants_data') }}">
                        </div>
                    </div>
                </div>

                <!-- 3. HÌNH ẢNH & GALLERY -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <span>Hình ảnh &amp; Gallery</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <!-- Cột tải ảnh chính (Span 5) -->
                        <div class="md:col-span-5">
                            <div class="seller-main-image-zone" id="main-image-dropzone">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100" id="main-image-placeholder-icon">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <img id="main-image-preview-el" src="" alt="Preview" class="hidden w-24 h-24 object-cover rounded-lg border border-gray-200">
                                
                                <div>
                                    <label for="main_image" class="seller-btn-upload-red">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>Tải ảnh chính</span>
                                    </label>
                                    <input type="file" name="main_image" id="main_image" accept="image/*" class="hidden">
                                </div>
                                <p class="text-[11px] text-gray-400 leading-tight">
                                    Kéo thả ảnh hoặc chọn từ máy tính<br>
                                    JPG, PNG, WEBP (tối đa 10MB)
                                </p>
                            </div>
                        </div>

                        <!-- Cột Gallery sản phẩm (Span 7) -->
                        <div class="md:col-span-7 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-700">Gallery sản phẩm <span class="text-gray-400 font-normal">(tối đa 12 ảnh, 10MB/ảnh)</span></span>
                            </div>
                            
                            <div class="seller-gallery-wrapper">
                                <!-- Nút Thêm ảnh -->
                                <label for="images" class="seller-gallery-add-box" title="Thêm ảnh gallery">
                                    <span class="text-lg leading-none">+</span>
                                    <span>Thêm ảnh</span>
                                </label>
                                <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">

                                <!-- Preview container for loaded images -->
                                <div id="gallery-preview-grid" class="flex items-center gap-3"></div>

                                <!-- Empty placeholder slots matching mockup -->
                                <div class="seller-gallery-slot gallery-mock-slot text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <div class="seller-gallery-slot gallery-mock-slot text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <div class="seller-gallery-slot gallery-mock-slot text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <div class="seller-gallery-slot gallery-mock-slot text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                            </div>
                            <p id="gallery-error-msg" class="text-[11px] font-semibold text-rose-500 hidden"></p>
                        </div>
                    </div>
                </div>

                <!-- 4. MÔ TẢ CHI TIẾT -->
                <div class="seller-section-card">
                    <div class="flex items-center justify-between mb-3">
                        <div class="seller-section-title mb-0">
                            <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Mô tả chi tiết</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <span>Chế độ xem trước</span>
                            <label class="seller-switch scale-75 origin-right">
                                <input type="checkbox" id="toggle-preview">
                                <span class="seller-switch-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="seller-editor-container">
                        <!-- Functional toolbar -->
                        <div class="seller-editor-toolbar">
                            <button type="button" class="seller-toolbar-btn font-normal" data-editor-action="bold">Normal ▾</button>
                            <span class="seller-toolbar-divider"></span>
                            <button type="button" class="seller-toolbar-btn font-bold" data-editor-action="bold" title="In đậm">B</button>
                            <button type="button" class="seller-toolbar-btn italic" data-editor-action="italic" title="In nghiêng">I</button>
                            <button type="button" class="seller-toolbar-btn underline" data-editor-action="underline" title="Gạch chân">U</button>
                            <button type="button" class="seller-toolbar-btn line-through" data-editor-action="strike" title="Gạch ngang">S</button>
                            <span class="seller-toolbar-divider"></span>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="bullet" title="Danh sách dấu tròn">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="number" title="Danh sách số">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6h1v4M4 14h2v4H4"/></svg>
                            </button>
                            <span class="seller-toolbar-divider"></span>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="link" title="Chèn liên kết">🔗</button>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="image" title="Chèn ảnh">🖼️</button>
                        </div>

                        <textarea 
                            name="description" 
                            id="description" 
                            rows="5" 
                            placeholder="Nhập mô tả chi tiết sản phẩm tại đây..." 
                            class="seller-editor-textarea"
                        >{{ old('description') }}</textarea>

                        <div id="description-preview-area" class="hidden p-3 min-h-[8rem] text-xs text-gray-800 bg-gray-50/50"></div>

                        <div class="seller-editor-footer">
                            <span>Mô tả hỗ trợ Markdown &amp; HTML cơ bản</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Span 4) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- 1. THÔNG TIN HIỂN THỊ -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="20" height="14" x="2" y="3" rx="2" ry="2"/>
                            <line x1="8" x2="16" y1="21" y2="21"/>
                            <line x1="12" x2="12" y1="17" y2="21"/>
                        </svg>
                        <span>Thông tin hiển thị</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Slug (tùy chọn) -->
                        <div class="seller-input-group">
                            <label for="slug" class="seller-field-label">Slug <span class="optional">(tùy chọn)</span></label>
                            <div class="relative flex items-center">
                                <input 
                                    type="text" 
                                    name="slug" 
                                    id="slug" 
                                    value="{{ old('slug') }}" 
                                    placeholder="ten-san-pham" 
                                    class="seller-field-input pr-8"
                                >
                                <button type="button" id="btn-copy-slug" class="absolute right-2.5 text-gray-400 hover:text-gray-700 cursor-pointer" title="Sao chép slug">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Tags (Youtube style round pill tags) -->
                        <div class="seller-input-group">
                            <label class="seller-field-label">Tags <span class="optional">(tùy chọn)</span></label>
                            <input type="hidden" name="tags" id="tags" value="{{ old('tags') }}">
                            <div class="youtube-tag-container" id="youtube-tag-container">
                                <input 
                                    type="text" 
                                    id="youtube-tag-input" 
                                    placeholder="Nhập tag và nhấn Enter..." 
                                    class="youtube-tag-input"
                                >
                            </div>
                            <p class="text-[10px] text-gray-400">Gõ từ khóa rồi nhấn <kbd class="px-1 py-0.5 bg-gray-100 rounded text-gray-600 font-mono">Enter</kbd> hoặc dấu phẩy để tạo tag</p>
                        </div>

                        <!-- Trạng thái hiển thị with Modern Custom Dropdown -->
                        <div class="seller-input-group">
                            <label class="seller-field-label">Trạng thái hiển thị</label>
                            <div class="custom-select-wrapper" data-select-for="visibility">
                                <select name="visibility" id="visibility" class="hidden">
                                    <option value="visible" selected>Hiển thị</option>
                                    <option value="hidden">Ẩn</option>
                                </select>
                                <div class="custom-select-trigger">
                                    <span class="custom-select-text">Hiển thị</span>
                                    <svg class="custom-select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                                <div class="custom-select-options">
                                    <div class="custom-select-option is-selected" data-value="visible" data-label="Hiển thị">Hiển thị</div>
                                    <div class="custom-select-option" data-value="hidden" data-label="Ẩn">Ẩn</div>
                                </div>
                            </div>
                        </div>

                        <!-- Nổi bật switch toggle -->
                        <div class="seller-input-group pt-1">
                            <label class="seller-field-label">Nổi bật</label>
                            <div class="seller-toggle-wrapper">
                                <label class="seller-switch">
                                    <input type="checkbox" name="is_featured" value="1">
                                    <span class="seller-switch-slider"></span>
                                </label>
                                <span class="seller-switch-label text-xs text-gray-500">Đưa vào danh sách nổi bật</span>
                            </div>
                        </div>

                        <!-- Sắp xếp -->
                        <div class="seller-input-group">
                            <label for="sort_order" class="seller-field-label">Sắp xếp</label>
                            <input 
                                type="number" 
                                name="sort_order" 
                                id="sort_order" 
                                value="{{ old('sort_order', 0) }}" 
                                placeholder="Nhập số thứ tự..." 
                                class="seller-field-input"
                            >
                            <p class="text-[10px] text-gray-400 mt-0.5">Số nhỏ hơn sẽ hiển thị trước</p>
                        </div>
                    </div>
                </div>

                <!-- 2. VẬN CHUYỂN & THUẾ -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="16" height="13" x="1" y="3" rx="2" ry="2"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        <span>Vận chuyển &amp; Thuế</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Trọng lượng vận chuyển (tùy chọn) -->
                        <div class="seller-input-group">
                            <label for="shipping_weight" class="seller-field-label">Trọng lượng vận chuyển <span class="optional">(tùy chọn)</span></label>
                            <div class="seller-input-with-suffix">
                                <input 
                                    type="number" 
                                    name="shipping_weight" 
                                    id="shipping_weight" 
                                    step="0.01" 
                                    min="0" 
                                    value="{{ old('shipping_weight', 0) }}" 
                                    placeholder="0" 
                                    class="seller-field-input"
                                >
                                <span class="seller-input-suffix">kg</span>
                            </div>
                        </div>

                        <!-- Kích thước đóng gói (D x R x C) -->
                        <div class="seller-input-group">
                            <label class="seller-field-label">Kích thước đóng gói (D x R x C)</label>
                            <div class="seller-dimension-grid">
                                <input type="number" name="package_l" placeholder="Dài" min="0" value="{{ old('package_l') }}">
                                <input type="number" name="package_w" placeholder="Rộng" min="0" value="{{ old('package_w') }}">
                                <input type="number" name="package_h" placeholder="Cao" min="0" value="{{ old('package_h') }}">
                                <span class="seller-dimension-unit">cm</span>
                            </div>
                        </div>

                        <!-- Thuế suất with Modern Custom Dropdown -->
                        <div class="seller-input-group">
                            <label class="seller-field-label">Thuế suất</label>
                            <div class="custom-select-wrapper" data-select-for="tax_rate">
                                <select name="tax_rate" id="tax_rate" class="hidden">
                                    <option value="">Chọn mức thuế...</option>
                                    <option value="0">0% (Không chịu thuế)</option>
                                    <option value="8">8% (Thuế VAT ưu đãi)</option>
                                    <option value="10" selected>10% (Thuế VAT chuẩn)</option>
                                </select>
                                <div class="custom-select-trigger">
                                    <span class="custom-select-text">10% (Thuế VAT chuẩn)</span>
                                    <svg class="custom-select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                                <div class="custom-select-options">
                                    <div class="custom-select-option" data-value="0" data-label="0% (Không chịu thuế)">0% (Không chịu thuế)</div>
                                    <div class="custom-select-option" data-value="8" data-label="8% (Thuế VAT ưu đãi)">8% (Thuế VAT ưu đãi)</div>
                                    <div class="custom-select-option is-selected" data-value="10" data-label="10% (Thuế VAT chuẩn)">10% (Thuế VAT chuẩn)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. GHI CHÚ -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/>
                        </svg>
                        <span>Ghi chú</span>
                    </div>

                    <div class="seller-input-group">
                        <textarea 
                            name="internal_notes" 
                            id="internal_notes" 
                            rows="3" 
                            placeholder="Ghi chú nội bộ (không hiển thị cho khách hàng)..." 
                            class="seller-field-input h-auto resize-vertical py-2"
                        >{{ old('internal_notes') }}</textarea>
                    </div>
                </div>

            </div>

        </div>

    </form>
</div>

@push('scripts')
@vite(['resources/js/pages/seller-product-form.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('seller-product-form');
    if (!form) return;

    // Auto-generate slug from name if empty
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.dataset.manualEdit) {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[đĐ]/g, 'd')
                    .replace(/([^0-9a-z-\s])/g, '')
                    .replace(/(\s+)/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
        slugInput.addEventListener('input', function() {
            slugInput.dataset.manualEdit = 'true';
        });
    }

    // Main image preview
    const mainImgInput = document.getElementById('main_image');
    const mainImgPreview = document.getElementById('main-image-preview-el');
    const mainImgPlaceholder = document.getElementById('main-image-placeholder-icon');
    if (mainImgInput && mainImgPreview) {
        mainImgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                mainImgPreview.src = URL.createObjectURL(file);
                mainImgPreview.classList.remove('hidden');
                if (mainImgPlaceholder) mainImgPlaceholder.classList.add('hidden');
            }
        });
    }

    // Copy slug button
    const copySlugBtn = document.getElementById('btn-copy-slug');
    if (copySlugBtn && slugInput) {
        copySlugBtn.addEventListener('click', function() {
            if (slugInput.value) {
                navigator.clipboard.writeText(slugInput.value);
                alert('Đã sao chép slug!');
            }
        });
    }

    // Submit lock
    form.addEventListener('submit', function () {
        const buttons = form.querySelectorAll('.seller-btn-save');
        buttons.forEach(button => {
            button.disabled = true;
            button.textContent = 'Đang lưu sản phẩm...';
            button.classList.add('opacity-70', 'cursor-not-allowed');
        });
    });
});
</script>
@endpush
@endsection
