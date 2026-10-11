@extends('layouts.admin')

@section('title', 'Chỉnh sửa voucher sàn - ShopMart Admin')
@section('page_title', 'Chỉnh sửa voucher giảm giá toàn sàn')

@section('content')
<div class="space-y-6">

    <!-- Top Header: Back + Title & Subtitle -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.coupons.index') }}" class="seller-back-btn" title="Quay lại danh sách">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Chỉnh sửa voucher: {{ $coupon->code }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">Cập nhật thông tin mã giảm giá toàn sàn trên hệ thống ShopMart.</p>
        </div>
    </div>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" id="seller-coupon-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN (Span 8) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. THÔNG TIN CƠ BẢN -->
                <div class="seller-section-card">
                    <div class="seller-section-title justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v18M3 9h18"/>
                            </svg>
                            <span>Thông tin cơ bản</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <div class="space-y-4">
                        <!-- Row 1: Tên voucher & Mã voucher -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Tên voucher (Span 6) -->
                            <div class="md:col-span-6 seller-input-group">
                                <label for="coupon_name" class="seller-field-label">Tên chương trình voucher <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="coupon_name" 
                                    required 
                                    value="{{ old('name', $coupon->name) }}" 
                                    placeholder="Nhập tên voucher" 
                                    class="seller-field-input"
                                >
                                @error('name') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Mã voucher (Span 6) with Random Generator -->
                            <div class="md:col-span-6 seller-input-group">
                                <label for="coupon_code" class="seller-field-label">Mã voucher <span class="required">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input 
                                        type="text" 
                                        name="code" 
                                        id="coupon_code" 
                                        required 
                                        value="{{ old('code', $coupon->code) }}" 
                                        placeholder="Nhập mã voucher" 
                                        class="seller-field-input uppercase font-mono font-bold text-rose-600"
                                    >
                                    <button type="button" id="btn-generate-code" class="seller-btn-ghost-action" title="Tạo mã ngẫu nhiên">
                                        <span>✨ Tạo mã ngẫu nhiên</span>
                                    </button>
                                </div>
                                @error('code') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Row 2: Loại giảm giá, Giá trị giảm, Đơn tối thiểu -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Loại giảm giá -->
                            <div class="seller-input-group">
                                <label class="seller-field-label">Loại giảm giá <span class="required">*</span></label>
                                <div class="custom-select-wrapper" data-select-for="coupon_type">
                                    <select name="discount_type" id="coupon_type" class="hidden">
                                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>Số tiền cố định (₫)</option>
                                        <option value="percent" {{ old('discount_type', $coupon->discount_type) === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    </select>
                                    <div class="custom-select-trigger">
                                        <span class="custom-select-label">{{ old('discount_type', $coupon->discount_type) === 'percent' ? 'Phần trăm (%)' : 'Số tiền cố định (₫)' }}</span>
                                        <svg class="custom-select-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                    <div class="custom-select-options">
                                        <div class="custom-select-option" data-value="fixed">
                                            <span>Số tiền cố định (₫)</span>
                                        </div>
                                        <div class="custom-select-option" data-value="percent">
                                            <span>Phần trăm (%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Giá trị giảm -->
                            <div class="seller-input-group">
                                <label for="coupon_value" class="seller-field-label">Giá trị giảm <span class="required">*</span></label>
                                <div class="seller-input-with-suffix">
                                    <input 
                                        type="number" 
                                        name="discount_value" 
                                        id="coupon_value" 
                                        required 
                                        min="1" 
                                        step="any"
                                        value="{{ old('discount_value', $coupon->discount_value) }}" 
                                        class="seller-field-input font-bold text-gray-900"
                                    >
                                    <span class="seller-input-suffix" id="coupon_value_suffix">{{ $coupon->discount_type === 'percent' ? '%' : '₫' }}</span>
                                </div>
                                @error('discount_value') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Đơn tối thiểu -->
                            <div class="seller-input-group">
                                <label for="min_order_value" class="seller-field-label">Đơn tối thiểu <span class="required">*</span></label>
                                <div class="seller-input-with-suffix">
                                    <input 
                                        type="number" 
                                        name="min_order_value" 
                                        id="min_order_value" 
                                        min="0" 
                                        step="1000"
                                        value="{{ old('min_order_value', $coupon->min_order_value) }}" 
                                        class="seller-field-input"
                                    >
                                    <span class="seller-input-suffix">₫</span>
                                </div>
                                @error('min_order_value') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Optional: Giảm tối đa (khi chọn phần trăm) -->
                        <div id="max_discount_wrapper" class="seller-input-group {{ old('discount_type', $coupon->discount_type) === 'percent' ? '' : 'hidden' }}">
                            <label for="max_discount_amount" class="seller-field-label">Giảm tối đa (₫) <span class="optional">(tùy chọn)</span></label>
                            <div class="seller-input-with-suffix">
                                <input 
                                    type="number" 
                                    name="max_discount_amount" 
                                    id="max_discount_amount" 
                                    min="0" 
                                    step="5000"
                                    placeholder="Không giới hạn" 
                                    value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" 
                                    class="seller-field-input"
                                >
                                <span class="seller-input-suffix">₫</span>
                            </div>
                        </div>

                        <!-- Row 3: Số lượng phát hành, Số lượt dùng tối đa, Thời gian áp dụng -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Số lượng phát hành -->
                            <div class="seller-input-group">
                                <label for="usage_limit" class="seller-field-label">Số lượng phát hành <span class="required">*</span></label>
                                <input 
                                    type="number" 
                                    name="usage_limit" 
                                    id="usage_limit" 
                                    min="1" 
                                    required 
                                    value="{{ old('usage_limit', $coupon->usage_limit ?? 500) }}" 
                                    class="seller-field-input"
                                >
                                @error('usage_limit') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Số lượt dùng tối đa (mỗi khách) -->
                            <div class="seller-input-group">
                                <label for="per_customer_limit" class="seller-field-label">Số lượt dùng tối đa (mỗi khách) <span class="required">*</span></label>
                                <input 
                                    type="number" 
                                    id="per_customer_limit" 
                                    min="1" 
                                    value="1" 
                                    class="seller-field-input bg-gray-50/60"
                                >
                            </div>

                            <!-- Thời gian áp dụng -->
                            <div class="seller-input-group">
                                <label for="expires_at" class="seller-field-label">Thời gian áp dụng <span class="required">*</span></label>
                                <div class="relative">
                                    <input 
                                        type="datetime-local" 
                                        name="expires_at" 
                                        id="expires_at" 
                                        required
                                        value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : now()->addDays(30)->format('Y-m-d\TH:i')) }}" 
                                        class="seller-field-input text-xs"
                                    >
                                </div>
                                @error('expires_at') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. ĐIỀU KIỆN ÁP DỤNG -->
                <div class="seller-section-card">
                    <div class="seller-section-title justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>
                            </svg>
                            <div>
                                <span class="block">Điều kiện áp dụng</span>
                                <span class="block text-[11px] font-normal text-gray-400 mt-0.5">Chọn phạm vi và nhóm khách hàng được hưởng quyền lợi từ voucher này.</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
                        <!-- Áp dụng cho sản phẩm -->
                        <div>
                            <span class="block text-xs font-bold text-gray-700 mb-3">Áp dụng cho sản phẩm</span>
                            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                <label class="voucher-radio-label">
                                    <input type="radio" name="apply_scope" value="all" checked>
                                    <span>Toàn bộ sản phẩm sàn</span>
                                </label>
                                <label class="voucher-radio-label">
                                    <input type="radio" name="apply_scope" value="specific">
                                    <span>Gian hàng chỉ định</span>
                                </label>
                                <label class="voucher-radio-label">
                                    <input type="radio" name="apply_scope" value="categories">
                                    <span>Ngành hàng chỉ định</span>
                                </label>
                            </div>
                        </div>

                        <!-- Đối tượng khách hàng -->
                        <div>
                            <span class="block text-xs font-bold text-gray-700 mb-3">Đối tượng khách hàng</span>
                            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                <label class="voucher-radio-label">
                                    <input type="radio" name="target_audience" value="all" checked>
                                    <span>Tất cả người mua</span>
                                </label>
                                <label class="voucher-radio-label">
                                    <input type="radio" name="target_audience" value="new">
                                    <span>Người dùng mới</span>
                                </label>
                                <label class="voucher-radio-label">
                                    <input type="radio" name="target_audience" value="group">
                                    <span>Khách hàng thân thiết</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. MÔ TẢ & GHI CHÚ -->
                <div class="seller-section-card">
                    <div class="seller-section-title justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <div>
                                <span class="block">Mô tả &amp; ghi chú</span>
                                <span class="block text-[11px] font-normal text-gray-400 mt-0.5">Thông tin chi tiết về thể lệ chương trình hiển thị trong ví voucher của khách.</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <div class="seller-editor-container">
                        <!-- Functional toolbar matching product editor -->
                        <div class="seller-editor-toolbar">
                            <button type="button" class="seller-toolbar-btn font-bold" data-editor-action="bold" title="In đậm">B</button>
                            <button type="button" class="seller-toolbar-btn italic" data-editor-action="italic" title="In nghiêng">I</button>
                            <button type="button" class="seller-toolbar-btn underline" data-editor-action="underline" title="Gạch chân">U</button>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="link" title="Chèn liên kết">🔗</button>
                            <span class="seller-toolbar-divider"></span>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="bullet" title="Danh sách dấu tròn">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <button type="button" class="seller-toolbar-btn" data-editor-action="number" title="Danh sách số">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6h1v4M4 14h2v4H4"/></svg>
                            </button>
                        </div>

                        <textarea 
                            name="description" 
                            id="description" 
                            rows="4" 
                            maxlength="1000"
                            placeholder="Nhập mô tả chi tiết, điều kiện sử dụng, lưu ý..." 
                            class="seller-editor-textarea"
                        >{{ old('description', $coupon->description) }}</textarea>

                        <div class="seller-editor-footer">
                            <span>Mô tả khuyến mãi toàn sàn</span>
                            <span id="description-char-count">0/1000</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Span 4) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- 1. XEM TRƯỚC VOUCHER (REALTIME PREVIEW) -->
                <div class="seller-section-card">
                    <div class="seller-section-title">
                        <svg class="seller-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                        </svg>
                        <span>Xem trước voucher sàn</span>
                    </div>

                    <!-- The Ticket Card Graphic Matching Mockup -->
                    <div class="voucher-ticket-card">
                        <div class="voucher-ticket-left">
                            <div class="voucher-ticket-icon-box">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                    <path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12z"/>
                                </svg>
                            </div>
                            <span class="voucher-ticket-brand">SHOPMART</span>
                        </div>

                        <div class="voucher-ticket-main">
                            <span class="voucher-ticket-type-label">Voucher sàn</span>
                            <span class="voucher-ticket-amount" id="preview-discount-amount">{{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : number_format($coupon->discount_value, 0, ',', '.').'đ' }}</span>
                            <span class="voucher-ticket-condition" id="preview-condition">Cho đơn từ {{ number_format($coupon->min_order_value, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="voucher-ticket-code-tag" id="preview-code-tag">
                            Mã: {{ $coupon->code }}
                        </div>
                    </div>

                    <!-- Meta details below card -->
                    <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
                        <div class="flex items-center gap-1.5" id="preview-scope-text">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span>Toàn bộ sản phẩm sàn</span>
                        </div>
                        <div class="flex items-center gap-1.5" id="preview-date-text">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span id="preview-date-range">{{ $coupon->expires_at ? now()->format('d/m/Y') . ' - ' . $coupon->expires_at->format('d/m/Y') : 'Vô thời hạn' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. LƯU Ý QUAN TRỌNG -->
                <div class="voucher-notice-card">
                    <div class="voucher-notice-header">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span>Lưu ý quản trị toàn sàn</span>
                    </div>
                    <ul class="voucher-notice-list space-y-1.5">
                        <li>Voucher sàn sẽ áp dụng cho tất cả đơn hàng thỏa điều kiện bất kể người bán.</li>
                        <li>Ngân sách giảm giá sẽ được hạch toán vào chi phí khuyến mãi của nền tảng.</li>
                        <li>Có thể tạm dừng hoặc điều chỉnh số lượng phát hành bất cứ lúc nào.</li>
                    </ul>
                </div>

                <!-- 3. ACTIONS (HỦY / CẬP NHẬT VOUCHER) -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.coupons.index') }}" class="seller-btn-cancel text-center">
                        Hủy
                    </a>
                    <button type="submit" class="seller-btn-save">
                        Cập nhật voucher sàn
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
@vite(['resources/js/pages/seller-coupons.js'])
@endpush
