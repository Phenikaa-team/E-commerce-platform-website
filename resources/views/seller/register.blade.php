@extends('layouts.app')

@section('title', 'Đăng Ký Gian Hàng - ShopMart Seller')

@section('content')
<div class="seller-reg-page-container">
    <div class="seller-reg-card">
        <!-- Left Column: Branding, Illustration & Benefits -->
        <div class="seller-reg-left">
            <div class="seller-reg-brand-badge">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>

            <h1 class="seller-reg-hero-title">
                Mở Gian Hàng Kinh Doanh Cùng ShopMart
            </h1>
            <p class="seller-reg-hero-desc">
                Tiếp cận hơn 10.000+ khách hàng mỗi ngày, miễn phí khởi tạo gian hàng và công cụ quản lý bán hàng chuyên nghiệp.
            </p>

            <!-- Storefront & Parcel Concept Illustration -->
            <div class="seller-reg-illustration-box">
                <img 
                    src="{{ project_asset('images/seller-registration-hero.png') }}" 
                    alt="Mở gian hàng ShopMart" 
                    class="seller-reg-illustration-img"
                    loading="lazy"
                >
            </div>

            <!-- 4 Features / Value propositions -->
            <div class="seller-reg-benefits-list">
                <!-- Benefit 1 -->
                <div class="seller-reg-benefit-item">
                    <div class="seller-reg-benefit-icon">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="seller-reg-benefit-title">Tiếp cận khách hàng lớn</h2>
                        <p class="seller-reg-benefit-desc">Hàng triệu người dùng tin tưởng mỗi ngày</p>
                    </div>
                </div>

                <!-- Benefit 2 -->
                <div class="seller-reg-benefit-item">
                    <div class="seller-reg-benefit-icon">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="seller-reg-benefit-title">Miễn phí khởi tạo</h2>
                        <p class="seller-reg-benefit-desc">Bắt đầu kinh doanh ngay, không tốn chi phí</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div class="seller-reg-benefit-item">
                    <div class="seller-reg-benefit-icon">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="seller-reg-benefit-title">Công cụ quản lý chuyên nghiệp</h2>
                        <p class="seller-reg-benefit-desc">Dễ dàng theo dõi đơn hàng, doanh thu, tồn kho</p>
                    </div>
                </div>

                <!-- Benefit 4 -->
                <div class="seller-reg-benefit-item">
                    <div class="seller-reg-benefit-icon">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="seller-reg-benefit-title">Bảo mật thông tin tuyệt đối</h2>
                        <p class="seller-reg-benefit-desc">Thông tin của bạn được bảo vệ an toàn</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Registration Form -->
        <div class="seller-reg-right">
            <div class="seller-reg-form-header">
                <h2 class="seller-reg-form-title">Đăng Ký Gian Hàng</h2>
                <p class="seller-reg-form-subtitle">Vui lòng điền đầy đủ thông tin bên dưới để tạo gian hàng trên ShopMart.</p>
            </div>

            @if(($applicationStatus ?? null) === 'pending')
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center my-6">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-amber-900">Hồ sơ đang chờ phê duyệt</h3>
                    <p class="mt-2 text-xs text-amber-800 leading-relaxed">Quản trị viên sẽ kiểm tra thông tin gian hàng. Bạn chỉ có thể truy cập Kênh Người Bán sau khi hồ sơ được chấp thuận.</p>
                </div>
            @else
                @if(($applicationStatus ?? null) === 'rejected')
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-800">
                        Hồ sơ trước đó chưa được chấp thuận. Bạn có thể cập nhật thông tin và gửi lại để quản trị viên xem xét.
                    </div>
                @endif

                <form action="{{ route('seller.register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- 2 Columns: Shop Name & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tên gian hàng / Shop -->
                        <div>
                            <label for="name" class="seller-reg-label">
                                Tên gian hàng / Shop <span class="text-rose-500">*</span>
                            </label>
                            <div class="seller-reg-input-wrap">
                                <span class="seller-reg-input-icon">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </span>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    required 
                                    value="{{ old('name') }}" 
                                    placeholder="Ví dụ: TechZone Official, Miniso Fashion..." 
                                    class="seller-reg-input"
                                >
                            </div>
                            @error('name')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Số điện thoại liên hệ -->
                        <div>
                            <label for="phone" class="seller-reg-label">
                                Số điện thoại liên hệ <span class="text-rose-500">*</span>
                            </label>
                            <div class="seller-reg-input-wrap">
                                <span class="seller-reg-input-icon">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </span>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    id="phone" 
                                    required 
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" 
                                    placeholder="0912 345 678" 
                                    class="seller-reg-input"
                                >
                            </div>
                            @error('phone')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Địa chỉ kho lấy hàng / Cửa hàng -->
                    <div>
                        <label for="address" class="seller-reg-label">
                            Địa chỉ kho lấy hàng / Cửa hàng <span class="text-rose-500">*</span>
                        </label>
                        <div class="seller-reg-input-wrap">
                            <span class="seller-reg-input-icon">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                name="address" 
                                id="address" 
                                required 
                                value="{{ old('address') }}" 
                                placeholder="Số nhà, đường, phường, quận, tỉnh thành phố lấy hàng" 
                                class="seller-reg-input"
                            >
                        </div>
                        @error('address')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mô tả gian hàng -->
                    <div>
                        <label for="description" class="seller-reg-label">
                            Mô tả gian hàng <span class="text-rose-500">*</span>
                        </label>
                        <div class="seller-reg-textarea-wrap">
                            <span class="seller-reg-textarea-icon">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="3" 
                                maxlength="500"
                                required 
                                placeholder="Giới thiệu về các mặt hàng kinh doanh chính, cam kết chất lượng của Shop..." 
                                class="seller-reg-textarea"
                                oninput="document.getElementById('desc-char-count').textContent = this.value.length"
                            >{{ old('description') }}</textarea>
                            <div class="seller-reg-counter">
                                <span id="desc-char-count">{{ strlen(old('description', '')) }}</span>/500
                            </div>
                        </div>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo gian hàng (Tùy chọn) -->
                    <div>
                        <x-image-picker 
                            name="logo" 
                            label="Logo gian hàng (Tùy chọn)" 
                            preview-shape="rounded" 
                            :max-size-mb="3" 
                            help-text="Tải ảnh logo vuông, định dạng JPG, PNG, WEBP. Tối đa 3MB."
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="seller-reg-submit-btn">
                            <span>Hoàn Tất Đăng Ký & Vào Kênh Người Bán</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
