@extends('layouts.app')

@section('title', 'Đăng Ký Mở Gian Hàng - ShopMart Seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="seller-register-card">
        <div class="seller-register-stripe"></div>

        <div class="text-center max-w-xl mx-auto mb-10">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-primary flex items-center justify-center mx-auto mb-4 shadow-xs border border-rose-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Mở Gian Hàng Kinh Doanh Cùng ShopMart</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-2">
                Tiếp cận hơn 10.000+ khách hàng mỗi ngày, miễn phí khởi tạo gian hàng và công cụ quản lý bán hàng chuyên nghiệp.
            </p>
        </div>

        @if(($applicationStatus ?? null) === 'pending')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-lg font-black text-amber-900">Hồ sơ đang chờ phê duyệt</h2>
                <p class="mt-2 text-sm text-amber-800">Quản trị viên sẽ kiểm tra thông tin gian hàng. Bạn chỉ có thể truy cập Kênh Người Bán sau khi hồ sơ được chấp thuận.</p>
            </div>
        @else
            @if(($applicationStatus ?? null) === 'rejected')
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    Hồ sơ trước đó chưa được chấp thuận. Bạn có thể cập nhật thông tin và gửi lại để quản trị viên xem xét.
                </div>
            @endif

        <form action="{{ route('seller.register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Shop Name -->
                <div>
                    <label for="name" class="form-label">Tên gian hàng / Shop <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Ví dụ: TechZone Official, Miniso Fashion..." class="form-input">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="form-label">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" required value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="0912 345 678" class="form-input">
                    @error('phone')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="form-label">Địa chỉ kho lấy hàng / Cửa hàng <span class="text-rose-500">*</span></label>
                <input type="text" name="address" id="address" required value="{{ old('address') }}" placeholder="Số nhà, đường, phường, quận, tỉnh thành phố lấy hàng" class="form-input">
                @error('address')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="form-label">Mô tả gian hàng <span class="text-rose-500">*</span></label>
                <textarea name="description" id="description" rows="3" required placeholder="Giới thiệu về các mặt hàng kinh doanh chính, cam kết chất lượng của Shop..." class="form-textarea">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo Upload -->
            <div>
                <x-image-picker 
                    name="logo" 
                    label="Logo gian hàng (tùy chọn)" 
                    preview-shape="rounded" 
                    :max-size-mb="3" 
                    help-text="Tải ảnh logo vuông. Định dạng JPG, PNG, WEBP. Tối đa 3MB."
                />
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="seller-register-submit">
                    Hoàn Tất Đăng Ký & Vào Kênh Người Bán
                </button>
            </div>
        </form>
        @endif

    </div>
</div>
@endsection
