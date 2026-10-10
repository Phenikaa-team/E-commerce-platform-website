@extends('layouts.app')

@section('title', 'Đăng Ký Gian Hàng - ShopMart Seller')

@section('content')
<div class="seller-reg-page-container">
    <div class="seller-reg-card">
        <!-- Left Column: Branding, Illustration & Benefits -->
        <div class="seller-reg-left">
            <a href="{{ route('home') }}" class="seller-back-link">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Quay lại</span>
            </a>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 w-fit mb-4">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-xs font-black tracking-tight text-rose-700">ShopMart</span>
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
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="seller-reg-form-title" id="seller-wizard-main-title">Đăng Ký Gian Hàng</h2>
                        <p class="seller-reg-form-subtitle" id="seller-wizard-main-subtitle">Vui lòng điền đầy đủ thông tin bên dưới để tạo gian hàng trên ShopMart.</p>
                    </div>
                    <div class="seller-support-badge">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11a9 9 0 0118 0v3a3 3 0 01-3 3h-1m-14-3a3 3 0 013-3h1"/>
                        </svg>
                        <div class="text-left leading-tight">
                            <div class="text-[11px] font-bold text-rose-600">Hỗ trợ</div>
                            <div class="text-[10px] font-bold text-rose-500">24/7</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="seller-pending-alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(($applicationStatus ?? null) === 'pending')
                <div class="seller-pending-wrapper">
                    <div class="seller-pending-icon-wrap">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>

                    <h3 class="seller-pending-title">Hồ Sơ Đang Chờ Phê Duyệt (Bước 5/8)</h3>
                    <p class="seller-pending-desc">
                        Đội ngũ kiểm duyệt ShopMart đang xác minh thông tin & giấy tờ gian hàng của bạn (thời gian xử lý 1–2 ngày làm việc). Sau khi được duyệt, bạn sẽ nhận được thông báo để bắt đầu đăng tải sản phẩm và đón tiếp khách hàng!
                    </p>

                    <div class="seller-pending-meta-list">
                        <div class="seller-pending-meta-row">
                            <span class="seller-pending-meta-label">Tên gian hàng:</span>
                            <span class="seller-pending-meta-val font-bold text-gray-900">{{ $store->name }}</span>
                        </div>
                        <div class="seller-pending-meta-row">
                            <span class="seller-pending-meta-label">Gói đăng ký:</span>
                            <span class="seller-pending-meta-val font-bold text-rose-600 uppercase">{{ $store->package_plan ?? 'free' }}</span>
                        </div>
                        <div class="seller-pending-meta-row">
                            <span class="seller-pending-meta-label">Đường dây hỗ trợ người bán:</span>
                            <span class="seller-pending-meta-val font-bold text-blue-600">1900 6868 (Phím 2)</span>
                        </div>
                    </div>
                </div>
            @else
                @if(($applicationStatus ?? null) === 'rejected')
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-800">
                        Hồ sơ trước đó chưa được chấp thuận. Bạn có thể cập nhật lại thông tin, tài liệu và gửi lại để quản trị viên xem xét.
                    </div>
                @endif

                <!-- Visual Step Indicator (Steps 1 to 4 with connecting line matching concept) -->
                <div class="seller-reg-stepper" id="seller-wizard-stepper">
                    <div class="seller-reg-step-item is-active" data-step="1" onclick="goToStep(1)">
                        <div class="seller-reg-step-circle">1</div>
                        <span class="seller-reg-step-title">Thông tin shop</span>
                    </div>
                    <div class="seller-reg-step-item" data-step="2" onclick="goToStep(2)">
                        <div class="seller-reg-step-circle">2</div>
                        <span class="seller-reg-step-title">Xác thực OTP</span>
                    </div>
                    <div class="seller-reg-step-item" data-step="3" onclick="goToStep(3)">
                        <div class="seller-reg-step-circle">3</div>
                        <span class="seller-reg-step-title">Chọn gói shop</span>
                    </div>
                    <div class="seller-reg-step-item" data-step="4" onclick="goToStep(4)">
                        <div class="seller-reg-step-circle">4</div>
                        <span class="seller-reg-step-title">Thanh toán & Vận chuyển</span>
                    </div>
                </div>

                <form action="{{ route('seller.register.post') }}" method="POST" enctype="multipart/form-data" id="seller-register-form">
                    @csrf

                    <!-- ==================== STEP 1: THÔNG TIN CÁ NHÂN / DOANH NGHIỆP ==================== -->
                    <div class="seller-step-panel is-active" id="step-panel-1">
                        <div class="space-y-4">
                            <!-- Loại hình kinh doanh -->
                            <div>
                                <label class="seller-reg-label">Hình thức kinh doanh <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3 mt-1.5">
                                    <label class="seller-option-card cursor-pointer" id="card-type-individual">
                                        <input type="radio" name="business_type" value="individual" checked class="text-rose-600 focus:ring-rose-500" onchange="toggleBusinessType(this.value)">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900">Cá nhân / Hộ kinh doanh</div>
                                            <div class="text-[11px] text-gray-400">Thủ tục đơn giản, duyệt siêu nhanh</div>
                                        </div>
                                    </label>
                                    <label class="seller-option-card cursor-pointer" id="card-type-business">
                                        <input type="radio" name="business_type" value="business" class="text-rose-600 focus:ring-rose-500" onchange="toggleBusinessType(this.value)">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900">Công ty / Doanh nghiệp</div>
                                            <div class="text-[11px] text-gray-400">Có mã số thuế & hóa đơn VAT</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Tên gian hàng & Đại diện -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="seller-reg-label">Tên gian hàng / Shop <span class="text-rose-500">*</span></label>
                                    <div class="seller-reg-input-wrap">
                                        <span class="seller-reg-input-icon">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </span>
                                        <input type="text" name="name" id="name" required value="{{ old('name', $store->name ?? '') }}" placeholder="Ví dụ: Anker Official, Miniso..." class="seller-reg-input">
                                    </div>
                                    @error('name')<p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="representative_name" class="seller-reg-label">Họ tên người đại diện / Chủ shop <span class="text-rose-500">*</span></label>
                                    <div class="seller-reg-input-wrap">
                                        <span class="seller-reg-input-icon">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </span>
                                        <input type="text" name="representative_name" id="representative_name" required value="{{ old('representative_name', $store->representative_name ?? auth()->user()->name) }}" placeholder="Nguyễn Văn A" class="seller-reg-input">
                                    </div>
                                </div>
                            </div>

                            <!-- Phone & Address -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="seller-reg-label">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
                                    <div class="seller-reg-input-wrap">
                                        <span class="seller-reg-input-icon">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </span>
                                        <input type="tel" name="phone" id="phone" required value="{{ old('phone', $store->phone ?? auth()->user()->phone ?? '') }}" placeholder="0912 345 678" class="seller-reg-input">
                                    </div>
                                    @error('phone')<p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="address" class="seller-reg-label">Địa chỉ kho lấy hàng / Cửa hàng <span class="text-rose-500">*</span></label>
                                    <div class="seller-reg-input-wrap">
                                        <span class="seller-reg-input-icon">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </span>
                                        <input type="text" name="address" id="address" required value="{{ old('address', $store->address ?? '') }}" placeholder="Số nhà, đường, phường, quận, tỉnh thành phố" class="seller-reg-input">
                                    </div>
                                    @error('address')<p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <!-- Doanh nghiệp: Mã số thuế & Giấy phép KD -->
                            <div id="business-fields" class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3 hidden">
                                <h4 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Hồ Sơ Pháp Lý Doanh Nghiệp
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label for="tax_code" class="seller-reg-label">Mã số thuế doanh nghiệp (MST)</label>
                                        <input type="text" name="tax_code" id="tax_code" value="{{ old('tax_code', $store->tax_code ?? '') }}" placeholder="Ví dụ: 0101234567" class="seller-reg-input">
                                    </div>
                                    <div>
                                        <label for="id_card_number" class="seller-reg-label">Số CMND / CCCD người đại diện</label>
                                        <input type="text" name="id_card_number" id="id_card_number" value="{{ old('id_card_number', $store->id_card_number ?? '') }}" placeholder="12 chữ số CCCD" class="seller-reg-input">
                                    </div>
                                </div>
                            </div>

                            <!-- Mô tả gian hàng -->
                            <div>
                                <label for="description" class="seller-reg-label">Mô tả gian hàng <span class="text-rose-500">*</span></label>
                                <div class="seller-reg-textarea-wrap">
                                    <span class="seller-reg-textarea-icon">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </span>
                                    <textarea name="description" id="description" rows="3" maxlength="500" required placeholder="Giới thiệu về các mặt hàng kinh doanh chính, cam kết chất lượng của Shop..." class="seller-reg-textarea" oninput="document.getElementById('desc-char-count').textContent = this.value.length">{{ old('description', $store->description ?? '') }}</textarea>
                                    <div class="seller-reg-counter">
                                        <span id="desc-char-count">{{ strlen(old('description', $store->description ?? '')) }}</span>/500
                                    </div>
                                </div>
                            </div>

                            <!-- Logo gian hàng -->
                            <div>
                                <x-image-picker name="logo" label="Logo gian hàng (Tùy chọn)" :value="$store->logo_url ?? null" preview-shape="rounded" :max-size-mb="3" help-text="Tải ảnh logo vuông, định dạng JPG, PNG, WEBP. Tối đa 3MB." />
                            </div>
                        </div>

                        <div class="seller-wizard-nav-btns justify-end">
                            <button type="button" class="seller-btn-primary" onclick="goToStep(2)">
                                <span>Tiếp tục: Xác thực tài khoản</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== STEP 2: XÁC THỰC OTP (EXACT CONCEPT DESIGN) ==================== -->
                    <div class="seller-step-panel" id="step-panel-2">
                        <div class="space-y-5">
                            <!-- Email Notification Banner matching mockup -->
                            <div class="seller-otp-email-banner">
                                <div class="flex items-center gap-3">
                                    <div class="seller-otp-email-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">Mã OTP đã được gửi đến email của bạn</div>
                                        <div class="text-xs font-bold text-gray-700 mt-0.5" id="otp-target-display">{{ auth()->user()->masked_email ?? preg_replace('/(?<=.{2}).(?=.*@)/u', '*', auth()->user()->email) }}</div>
                                    </div>
                                </div>
                                <button type="button" onclick="goToStep(1)" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">
                                    Thay đổi email
                                </button>
                            </div>

                            <!-- Nhập mã OTP Section -->
                            <div>
                                <label class="block text-xs font-extrabold text-gray-900 mb-2">Nhập mã OTP</label>
                                
                                <!-- 6 Separate Digit Boxes matching concept -->
                                <div class="seller-otp-boxes-grid" id="otp-boxes-container">
                                    <input type="text" maxlength="1" inputmode="numeric" class="seller-otp-box-digit" data-index="0" autofocus>
                                    <input type="text" maxlength="1" inputmode="numeric" class="seller-otp-box-digit" data-index="1">
                                    <input type="text" maxlength="1" inputmode="numeric" class="seller-otp-box-digit" data-index="2">
                                    <input type="text" maxlength="1" inputmode="numeric" class="seller-otp-box-digit" data-index="3">
                                    <input type="text" maxlength="1" inputmode="numeric" class="seller-otp-box-digit" data-index="4">
                                    <input type="text" maxlength="1" inputmode="numeric" class="seller-otp-box-digit" data-index="5">
                                </div>

                                <!-- Timer & Validity row -->
                                <div class="flex items-center justify-between text-xs text-gray-500 mt-3 font-medium">
                                    <span>Mã OTP có hiệu lực trong 5 phút.</span>
                                    <div class="flex items-center gap-1.5 text-rose-600 font-bold" id="otp-timer-box">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span id="otp-countdown-text">05:00</span>
                                    </div>
                                </div>

                                <!-- Resend and Info Card matching mockup -->
                                <div class="seller-otp-actions-bar">
                                    <button type="button" id="btn-resend-otp" onclick="requestOtpCode()" class="seller-otp-resend-btn">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span id="resend-btn-label">Gửi lại mã</span>
                                    </button>

                                    <div class="seller-otp-hint-card">
                                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        </div>
                                        <div class="text-[11px] leading-tight">
                                            <div class="font-bold text-gray-800">Không nhận được mã?</div>
                                            <div class="text-gray-400 mt-0.5">Vui lòng kiểm tra hòm thư rác hoặc thử gửi lại mã.</div>
                                        </div>
                                    </div>
                                </div>

                                <div id="otp-feedback-msg" class="text-xs font-semibold mt-2 hidden"></div>

                                <!-- Big Full Width Red Submit Button matching concept -->
                                <div class="pt-5">
                                    <button type="button" id="btn-submit-otp" onclick="submitVerifyOtp()" class="seller-reg-submit-btn">
                                        <span>Xác nhận mã</span>
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== STEP 3: CHỌN GÓI GIAN HÀNG ==================== -->
                    <div class="seller-step-panel" id="step-panel-3">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xs font-bold text-gray-800">Chọn gói gian hàng kinh doanh phù hợp</h3>
                                <p class="text-[11px] text-gray-400 mt-0.5">Bạn có thể nâng cấp hoặc thay đổi gói bất cứ lúc nào trong Kênh Quản Trị Người Bán.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                                <!-- Gói Miễn Phí (Cơ Bản) -->
                                <label class="seller-plan-card is-selected" id="plan-free" onclick="selectPlan('free')">
                                    <input type="radio" name="package_plan" value="free" checked class="sr-only">
                                    <div class="flex items-start justify-between">
                                        <div class="text-xs font-black text-gray-900 uppercase tracking-tight">Gói Cơ Bản<br>(Miễn Phí)</div>
                                        <div class="seller-plan-radio-circle"></div>
                                    </div>
                                    <div class="text-lg font-black text-gray-900 mt-2">0₫ <span class="text-xs font-normal text-gray-400">/ tháng</span></div>
                                    <ul class="mt-3.5 space-y-2 text-[11px] text-gray-600">
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Đăng tối đa 50 sản phẩm</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Phí giao dịch 2.5%</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Quản lý kho cơ bản</li>
                                        <li class="flex items-center gap-1.5 text-gray-400"><span class="text-gray-300">✕</span> Không hỗ trợ huy hiệu Mall</li>
                                    </ul>
                                </label>

                                <!-- Gói Pro (Tăng Tốc) -->
                                <label class="seller-plan-card is-featured" id="plan-pro" onclick="selectPlan('pro')">
                                    <span class="seller-plan-badge">Phổ biến</span>
                                    <input type="radio" name="package_plan" value="pro" class="sr-only">
                                    <div class="flex items-start justify-between">
                                        <div class="text-xs font-black text-gray-900 uppercase tracking-tight">Gói Tăng Tốc<br>(Pro)</div>
                                        <div class="seller-plan-radio-circle"></div>
                                    </div>
                                    <div class="text-lg font-black text-gray-900 mt-2">199.000₫ <span class="text-xs font-normal text-gray-400">/ tháng</span></div>
                                    <ul class="mt-3.5 space-y-2 text-[11px] text-gray-600">
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Không giới hạn sản phẩm</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Ưu đãi phí sàn còn 1.5%</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Hỗ trợ mã giảm giá riêng</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Ưu tiên hiển thị tìm kiếm</li>
                                    </ul>
                                </label>

                                <!-- Gói Doanh Nghiệp (Enterprise) -->
                                <label class="seller-plan-card" id="plan-enterprise" onclick="selectPlan('enterprise')">
                                    <input type="radio" name="package_plan" value="enterprise" class="sr-only">
                                    <div class="flex items-start justify-between">
                                        <div class="text-xs font-black text-gray-900 uppercase tracking-tight">ShopMall<br>Enterprise</div>
                                        <div class="seller-plan-radio-circle"></div>
                                    </div>
                                    <div class="text-lg font-black text-gray-900 mt-2">499.000₫ <span class="text-xs font-normal text-gray-400">/ tháng</span></div>
                                    <ul class="mt-3.5 space-y-2 text-[11px] text-gray-600">
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Huy hiệu ShopMall Đỏ</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Trợ giá vận chuyển toàn sàn</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Hỗ trợ tài khoản Key Account 1:1</li>
                                        <li class="flex items-center gap-1.5"><span class="text-emerald-500 font-bold">✓</span> Tham gia Banner Flash Sale</li>
                                    </ul>
                                </label>
                            </div>
                        </div>

                        <div class="seller-wizard-nav-btns">
                            <button type="button" class="seller-btn-secondary" onclick="goToStep(2)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                <span>Quay lại</span>
                            </button>
                            <button type="button" class="seller-btn-primary" onclick="goToStep(4)">
                                <span>Tiếp tục: Phương thức thanh toán & Vận chuyển</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- ==================== STEP 4: THANH TOÁN & VẬN CHUYỂN (EXACT CONCEPT DESIGN) ==================== -->
                    <div class="seller-step-panel" id="step-panel-4">
                        <div class="space-y-4">
                            <!-- Thông tin tài khoản nhận tiền (Soft blue panel with bank card icon) -->
                            <div class="seller-bank-info-panel">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <span class="text-xs font-bold text-gray-800">Thông tin tài khoản nhận tiền</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="seller-reg-label">Tên Ngân hàng</label>
                                        <input type="text" name="bank_name" value="{{ old('bank_name', $store->bank_name ?? '') }}" placeholder="Vietcombank, MB, Techcombank..." class="seller-reg-input text-xs">
                                    </div>
                                    <div>
                                        <label class="seller-reg-label">Số tài khoản</label>
                                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $store->bank_account_number ?? '') }}" placeholder="0123456789" class="seller-reg-input text-xs">
                                    </div>
                                    <div>
                                        <label class="seller-reg-label">Tên chủ tài khoản</label>
                                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $store->bank_account_name ?? '') }}" placeholder="NGUYEN VAN A" class="seller-reg-input text-xs">
                                    </div>
                                </div>
                            </div>

                            <!-- Đối tác vận chuyển liên kết -->
                            <div>
                                <label class="block text-xs font-bold text-gray-900 mb-2">Đối tác vận chuyển liên kết</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <label class="seller-shipping-card is-checked">
                                        <input type="checkbox" name="shipping_partners[]" value="shopmart_logistics" checked class="seller-shipping-checkbox" onchange="this.closest('.seller-shipping-card').classList.toggle('is-checked', this.checked)">
                                        <span class="text-xs font-semibold text-gray-800">ShopMart Express</span>
                                    </label>
                                    <label class="seller-shipping-card is-checked">
                                        <input type="checkbox" name="shipping_partners[]" value="ghn" checked class="seller-shipping-checkbox" onchange="this.closest('.seller-shipping-card').classList.toggle('is-checked', this.checked)">
                                        <span class="text-xs font-semibold text-gray-800">Giao Hàng Nhanh (GHN)</span>
                                    </label>
                                    <label class="seller-shipping-card is-checked">
                                        <input type="checkbox" name="shipping_partners[]" value="viettel" checked class="seller-shipping-checkbox" onchange="this.closest('.seller-shipping-card').classList.toggle('is-checked', this.checked)">
                                        <span class="text-xs font-semibold text-gray-800">Viettel Post</span>
                                    </label>
                                    <label class="seller-shipping-card">
                                        <input type="checkbox" name="shipping_partners[]" value="ghtk" class="seller-shipping-checkbox" onchange="this.closest('.seller-shipping-card').classList.toggle('is-checked', this.checked)">
                                        <span class="text-xs font-semibold text-gray-800">GHTK</span>
                                    </label>
                                    <label class="seller-shipping-card">
                                        <input type="checkbox" name="shipping_partners[]" value="jnt" class="seller-shipping-checkbox" onchange="this.closest('.seller-shipping-card').classList.toggle('is-checked', this.checked)">
                                        <span class="text-xs font-semibold text-gray-800">J&T Express</span>
                                    </label>
                                    <label class="seller-shipping-card">
                                        <input type="checkbox" name="shipping_partners[]" value="express_2h" class="seller-shipping-checkbox" onchange="this.closest('.seller-shipping-card').classList.toggle('is-checked', this.checked)">
                                        <span class="text-xs font-semibold text-gray-800">Hỏa tốc nội thành 2H</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Cam kết chính sách (Chữ không có card, checkbox đỏ bo góc, highlight link mở popup) -->
                            <div class="pt-2">
                                <label class="seller-terms-check-row">
                                    <input type="checkbox" name="terms_agreed" id="terms_agreed" value="1" required checked class="seller-terms-checkbox">
                                    <span class="text-xs font-semibold text-gray-800">
                                        Tôi đã đọc, hiểu và đồng ý với mọi 
                                        <button type="button" onclick="openPolicyModal()" class="seller-terms-link">điều khoản hoạt động</button> 
                                        của ShopMart Seller.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Bottom Navigation Buttons matching mockup -->
                        <div class="flex items-center gap-3 pt-6 mt-4">
                            <button type="button" class="seller-step4-back-btn" onclick="goToStep(3)">
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Quay lại</span>
                            </button>
                            <button type="submit" class="seller-step4-submit-btn">
                                <span>Xác nhận & Gửi Hồ Sơ Mở Gian Hàng</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Policy Terms Modal Popup -->
<div class="seller-policy-modal-backdrop" id="seller-policy-modal" onclick="if(event.target === this) closePolicyModal()">
    <div class="seller-policy-modal-card">
        <div class="seller-policy-modal-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Điều Khoản & Chính Sách ShopMart Seller</h3>
                    <p class="text-[11px] text-gray-500">Quy chuẩn hoạt động và quyền lợi của người bán</p>
                </div>
            </div>
            <button type="button" onclick="closePolicyModal()" class="w-7 h-7 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="seller-policy-modal-body space-y-3.5 text-xs text-gray-600 leading-relaxed">
            <div class="p-3 rounded-lg bg-rose-50/60 border border-rose-100 text-rose-800 font-medium">
                Chào mừng bạn gia nhập cộng đồng nhà bán hàng ShopMart. Bằng việc nhấn đăng ký, bạn đồng ý thực hiện đúng các cam kết dưới đây:
            </div>

            <div>
                <h4 class="font-bold text-gray-900 mb-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    1. Tính xác thực & Chất lượng hàng hóa
                </h4>
                <p class="text-gray-600 pl-3">Cam kết 100% hàng hóa được bày bán là hàng chính hãng, có nguồn gốc xuất xứ rõ ràng. Tuyệt đối không bán hàng giả, hàng nhái hoặc các danh mục hàng cấm theo quy định của pháp luật Việt Nam.</p>
            </div>

            <div>
                <h4 class="font-bold text-gray-900 mb-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    2. Thời gian chuẩn bị & Vận hành đơn hàng
                </h4>
                <p class="text-gray-600 pl-3">Đóng gói đúng tiêu chuẩn bảo vệ hàng hoá và bàn giao cho bưu tá các đối tác vận chuyển trong vòng tối đa 24 giờ kể từ khi hệ thống ghi nhận đơn hàng mới.</p>
            </div>

            <div>
                <h4 class="font-bold text-gray-900 mb-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    3. Chính sách đổi trả & Chăm sóc khách hàng
                </h4>
                <p class="text-gray-600 pl-3">Tuân thủ chính sách bảo vệ quyền lợi người tiêu dùng của ShopMart: hỗ trợ trả hàng hoàn tiền 7 ngày nếu sản phẩm lỗi, sai mẫu mã hoặc bể vỡ trong quá trình vận chuyển.</p>
            </div>

            <div>
                <h4 class="font-bold text-gray-900 mb-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    4. Bảo mật thông tin & Đối soát thanh toán
                </h4>
                <p class="text-gray-600 pl-3">Bảo mật thông tin cá nhân khách hàng. Doanh thu bán hàng được quyết toán và chuyển về tài khoản ngân hàng liên kết chính chủ theo kỳ hạn thanh toán minh bạch.</p>
            </div>
        </div>

        <div class="seller-policy-modal-footer">
            <button type="button" onclick="closePolicyModal()" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors">
                Tôi Đã Hiểu & Đồng Ý
            </button>
        </div>
    </div>
</div>

<script>
    function openPolicyModal() {
        const modal = document.getElementById('seller-policy-modal');
        if (modal) modal.classList.add('is-open');
    }

    function closePolicyModal() {
        const modal = document.getElementById('seller-policy-modal');
        if (modal) modal.classList.remove('is-open');
    }

    let otpCountdownInterval = null;
    let otpSecondsRemaining = 300; // 5 minutes
    let isOtpVerified = {{ session('seller_reg_otp_verified') || ($store ?? false) ? 'true' : 'false' }};

    const stepTitles = {
        1: { title: 'Đăng Ký Gian Hàng', subtitle: 'Vui lòng điền đầy đủ thông tin bên dưới để tạo gian hàng trên ShopMart.' },
        2: { title: 'Xác Thực OTP', subtitle: 'Vui lòng nhập mã OTP đã được gửi đến email của bạn để tiếp tục đăng ký gian hàng.' },
        3: { title: 'Chọn Gói Gian Hàng', subtitle: 'Chọn gói gian hàng kinh doanh phù hợp với quy mô của bạn.' },
        4: { title: 'Thanh Toán & Vận Chuyển', subtitle: 'Thiết lập tài khoản nhận tiền và đối tác vận chuyển liên kết.' }
    };

    function startOtpCountdown() {
        if (otpCountdownInterval) clearInterval(otpCountdownInterval);
        otpSecondsRemaining = 300;
        updateOtpCountdownDisplay();

        otpCountdownInterval = setInterval(() => {
            otpSecondsRemaining--;
            if (otpSecondsRemaining <= 0) {
                clearInterval(otpCountdownInterval);
                otpSecondsRemaining = 0;
                document.getElementById('otp-countdown-text').textContent = 'Hết hạn';
                document.getElementById('btn-resend-otp').disabled = false;
            } else {
                updateOtpCountdownDisplay();
            }
        }, 1000);
    }

    function updateOtpCountdownDisplay() {
        const m = Math.floor(otpSecondsRemaining / 60);
        const s = otpSecondsRemaining % 60;
        const text = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        const el = document.getElementById('otp-countdown-text');
        if (el) el.textContent = text;
    }

    function goToStep(step) {
        // Validation check for Step 1 fields before moving beyond Step 1
        if (step > 1) {
            const name = document.getElementById('name');
            const phone = document.getElementById('phone');
            const address = document.getElementById('address');
            const desc = document.getElementById('description');

            if (name && !name.value.trim()) {
                alert('Vui lòng nhập Tên gian hàng trước khi tiếp tục.');
                name.focus();
                return;
            }
            if (phone && !phone.value.trim()) {
                alert('Vui lòng nhập Số điện thoại liên hệ.');
                phone.focus();
                return;
            }
            if (address && !address.value.trim()) {
                alert('Vui lòng nhập Địa chỉ kho/cửa hàng.');
                address.focus();
                return;
            }
            if (desc && !desc.value.trim()) {
                alert('Vui lòng nhập Mô tả gian hàng.');
                desc.focus();
                return;
            }
        }

        // Security check: cannot jump to step 3 or 4 if OTP is not verified yet
        if (step >= 3 && !isOtpVerified) {
            alert('Vui lòng hoàn tất xác thực mã OTP tại Bước 2 trước khi tiếp tục.');
            goToStep(2);
            return;
        }

        // Switch panels
        document.querySelectorAll('.seller-step-panel').forEach(p => p.classList.remove('is-active'));
        const targetPanel = document.getElementById('step-panel-' + step);
        if (targetPanel) targetPanel.classList.add('is-active');

        // Update titles
        if (stepTitles[step]) {
            const titleEl = document.getElementById('seller-wizard-main-title');
            const subtitleEl = document.getElementById('seller-wizard-main-subtitle');
            if (titleEl) titleEl.textContent = stepTitles[step].title;
            if (subtitleEl) subtitleEl.textContent = stepTitles[step].subtitle;
        }

        // Update stepper indicators
        document.querySelectorAll('.seller-reg-step-item').forEach(item => {
            const itemStep = parseInt(item.getAttribute('data-step'), 10);
            item.classList.remove('is-active', 'is-completed');
            const circle = item.querySelector('.seller-reg-step-circle');
            if (itemStep === step) {
                item.classList.add('is-active');
                if (circle) circle.innerHTML = itemStep;
            } else if (itemStep < step) {
                item.classList.add('is-completed');
                if (circle) circle.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
            } else {
                if (circle) circle.innerHTML = itemStep;
            }
        });

        if (step === 2) {
            startOtpCountdown();
            if (!isOtpVerified) {
                // Auto trigger send OTP when user proceeds to Step 2
                requestOtpCode();
            }
            setTimeout(() => {
                const firstBox = document.querySelector('.seller-otp-box-digit[data-index="0"]');
                if (firstBox) firstBox.focus();
            }, 100);
        }

        window.scrollTo({ top: 120, behavior: 'smooth' });
    }

    function toggleBusinessType(type) {
        const businessFields = document.getElementById('business-fields');
        const cardInd = document.getElementById('card-type-individual');
        const cardBus = document.getElementById('card-type-business');

        if (type === 'business') {
            if (businessFields) businessFields.classList.remove('hidden');
            if (cardBus) cardBus.classList.add('is-checked');
            if (cardInd) cardInd.classList.remove('is-checked');
        } else {
            if (businessFields) businessFields.classList.add('hidden');
            if (cardInd) cardInd.classList.add('is-checked');
            if (cardBus) cardBus.classList.remove('is-checked');
        }
    }

    function selectPlan(plan) {
        ['free', 'pro', 'enterprise'].forEach(p => {
            const el = document.getElementById('plan-' + p);
            if (el) el.classList.remove('is-selected');
        });
        const selectedEl = document.getElementById('plan-' + plan);
        if (selectedEl) selectedEl.classList.add('is-selected');
    }

    // Setup 6-digit OTP input boxes (auto focus next, backspace, paste)
    document.addEventListener('DOMContentLoaded', () => {
        const boxes = document.querySelectorAll('.seller-otp-box-digit');
        boxes.forEach((box, idx) => {
            box.addEventListener('input', (e) => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? val[val.length - 1] : '';
                if (e.target.value) {
                    box.classList.add('is-filled');
                    if (idx < boxes.length - 1) {
                        boxes[idx + 1].focus();
                    }
                } else {
                    box.classList.remove('is-filled');
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    boxes[idx - 1].focus();
                    boxes[idx - 1].value = '';
                    boxes[idx - 1].classList.remove('is-filled');
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                const digits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
                if (digits) {
                    digits.split('').forEach((char, i) => {
                        if (boxes[i]) {
                            boxes[i].value = char;
                            boxes[i].classList.add('is-filled');
                        }
                    });
                    const nextIdx = Math.min(digits.length, boxes.length - 1);
                    boxes[nextIdx].focus();
                }
            });
        });

        // Initialize countdown on step 2 load
        startOtpCountdown();
    });

    function getEnteredOtp() {
        let code = '';
        document.querySelectorAll('.seller-otp-box-digit').forEach(b => {
            code += (b.value || '').trim();
        });
        return code;
    }

    async function requestOtpCode() {
        const btn = document.getElementById('btn-resend-otp');
        const label = document.getElementById('resend-btn-label');
        const feedback = document.getElementById('otp-feedback-msg');
        const phone = document.getElementById('phone')?.value || '';
        const channel = 'email';

        btn.disabled = true;
        label.textContent = 'Đang gửi lại...';

        try {
            const res = await fetch('{{ route("seller.register.otp.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ channel, phone })
            });
            const data = await res.json();

            feedback.classList.remove('hidden', 'text-rose-600', 'text-emerald-600');
            if (data.success) {
                feedback.classList.add('text-emerald-600');
                feedback.textContent = data.message + (data.otp ? ' [Mã test: ' + data.otp + ']' : '');
                startOtpCountdown();
                label.textContent = 'Đã gửi lại';
                setTimeout(() => {
                    btn.disabled = false;
                    label.textContent = 'Gửi lại mã';
                }, 15000);
            } else {
                feedback.classList.add('text-rose-600');
                feedback.textContent = data.message || 'Không thể gửi mã OTP.';
                btn.disabled = false;
                label.textContent = 'Gửi lại mã';
            }
        } catch (e) {
            feedback.classList.remove('hidden');
            feedback.classList.add('text-rose-600');
            feedback.textContent = 'Lỗi kết nối máy chủ.';
            btn.disabled = false;
            label.textContent = 'Gửi lại mã';
        }
    }

    async function submitVerifyOtp() {
        const feedback = document.getElementById('otp-feedback-msg');
        const phone = document.getElementById('phone')?.value || '';
        const channel = 'email';
        const otpCode = getEnteredOtp();

        if (!otpCode || otpCode.length !== 6) {
            alert('Vui lòng nhập đủ 6 chữ số mã OTP vào các ô.');
            return;
        }

        const submitBtn = document.getElementById('btn-submit-otp');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>Đang xác minh...</span>';

        try {
            const res = await fetch('{{ route("seller.register.otp.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ channel, phone, otp_code: otpCode })
            });
            const data = await res.json();

            feedback.classList.remove('hidden', 'text-rose-600', 'text-emerald-600');
            if (data.success) {
                isOtpVerified = true;
                feedback.classList.add('text-emerald-600');
                feedback.textContent = '✓ ' + data.message;
                submitBtn.innerHTML = '<span>Xác thực thành công ✓</span>';
                setTimeout(() => goToStep(3), 600);
            } else {
                feedback.classList.add('text-rose-600');
                feedback.textContent = data.message || 'Mã OTP không chính xác.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Xác nhận mã</span><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
            }
        } catch (e) {
            feedback.classList.remove('hidden');
            feedback.classList.add('text-rose-600');
            feedback.textContent = 'Lỗi kết nối khi xác thực OTP.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Xác nhận mã</span><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
        }
    }
</script>
@endsection
