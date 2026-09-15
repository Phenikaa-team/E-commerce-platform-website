@extends('layouts.app')

@section('title', 'Đăng Ký Mở Gian Hàng - ShopMart Seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-gray-100 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-amber-500 via-rose-500 to-amber-500"></div>

        <div class="text-center max-w-xl mx-auto mb-10">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-primary flex items-center justify-center mx-auto mb-4 shadow-xs border border-rose-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Mở Gian Hàng Kinh Doanh Cùng ShopMart</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-2">
                Tiếp cận hơn 10.000+ khách hàng mỗi ngày, miễn phí khởi tạo gian hàng và công cụ quản lý bán hàng chuyên nghiệp.
            </p>
        </div>

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
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-600 hover:to-rose-600 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-500/20 transition-all cursor-pointer">
                    Hoàn Tất Đăng Ký & Vào Kênh Người Bán
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
