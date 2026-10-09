@extends('layouts.app')

@section('title', 'Thông tin cá nhân - ' . ($user->username ?? $user->name) . ' | ShopMart')
@section('meta_description', 'Quản lý và cập nhật thông tin cá nhân của bạn tại ShopMart.')

@section('content')
<div class="page-container py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: SIDEBAR MENU -->
            <div class="lg:col-span-3">
                <x-user-sidebar active="info" />
            </div>

            <!-- RIGHT COLUMN: MAIN FORM -->
            <div class="lg:col-span-9 space-y-6">
                <x-third-party-password-alert />
                
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                    <!-- Section Title -->
                    <div class="pb-6 border-b border-gray-100">
                        <h1 class="text-xl font-black text-gray-900">Hồ Sơ Của Tôi</h1>
                        <p class="text-xs text-gray-500 mt-1">Quản lý và cập nhật thông tin tài khoản cá nhân để bảo mật trải nghiệm mua sắm.</p>
                    </div>

                    <!-- Main Form -->
                    <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="pt-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                            
                            <!-- Left: Form Inputs -->
                            <div class="md:col-span-8 space-y-5">
                                <!-- Tên đăng nhập -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">Tên đăng nhập</label>
                                    <div class="sm:col-span-8">
                                        <input 
                                            type="text" 
                                            name="username" 
                                            value="{{ old('username', $user->username) }}" 
                                            class="form-input"
                                            placeholder="Nhập tên đăng nhập"
                                        >
                                    </div>
                                </div>

                                <!-- Họ và tên -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">
                                        Họ và tên <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="sm:col-span-8">
                                        <input 
                                            type="text" 
                                            name="name" 
                                            value="{{ old('name', $user->name) }}" 
                                            required 
                                            class="form-input"
                                            placeholder="Nhập họ và tên đầy đủ"
                                        >
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">Email</label>
                                    <div class="sm:col-span-8 flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-800">{{ $user->masked_email ?? preg_replace('/(?<=.{2}).(?=.*@)/u', '*', $user->email) }}</span>
                                        <span class="text-[11px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md font-semibold">Đã xác minh</span>
                                    </div>
                                </div>

                                <!-- Số điện thoại -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">Số điện thoại</label>
                                    <div class="sm:col-span-8">
                                        <input 
                                            type="text" 
                                            name="phone" 
                                            value="{{ old('phone', $user->phone) }}" 
                                            class="form-input"
                                            placeholder="Ví dụ: 0912345678"
                                        >
                                    </div>
                                </div>

                                <!-- Giới tính -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">Giới tính</label>
                                    <div class="sm:col-span-8 flex items-center gap-6">
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="gender" value="Nam" {{ old('gender', $user->gender) === 'Nam' ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-rose-500 border-gray-300">
                                            <span>Nam</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="gender" value="Nữ" {{ old('gender', $user->gender) === 'Nữ' ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-rose-500 border-gray-300">
                                            <span>Nữ</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="gender" value="Khác" {{ old('gender', $user->gender) === 'Khác' || !in_array($user->gender, ['Nam', 'Nữ']) ? 'checked' : '' }} class="w-4 h-4 text-primary focus:ring-rose-500 border-gray-300">
                                            <span>Khác</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Ngày sinh -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">
                                        Ngày sinh <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="sm:col-span-8">
                                        <div class="profile-birthday-wrapper">
                                            <!-- Unified Segmented Birthday Card (matches concept) -->
                                            <div class="profile-birthday-card">
                                                <!-- Segment: Ngày -->
                                                <div class="profile-birthday-segment" data-dropdown="day">
                                                    <span class="profile-birthday-segment-label">Ngày</span>
                                                    <button type="button" class="profile-birthday-trigger" id="birthday-day-btn" aria-haspopup="listbox" aria-expanded="false">
                                                        <span class="profile-birthday-value" id="birthday-day-display">DD</span>
                                                        <svg class="profile-birthday-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    <input type="hidden" id="birthday-day" value="">
                                                    <div class="profile-birthday-menu custom-scrollbar" id="birthday-day-menu" role="listbox">
                                                        <div class="profile-birthday-option profile-birthday-option-empty" data-value="">DD</div>
                                                        @for($d = 1; $d <= 31; $d++)
                                                            @php $dVal = sprintf('%02d', $d); @endphp
                                                            <div class="profile-birthday-option" data-value="{{ $dVal }}" role="option">{{ $dVal }}</div>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div class="profile-birthday-divider"></div>

                                                <!-- Segment: Tháng -->
                                                <div class="profile-birthday-segment" data-dropdown="month">
                                                    <span class="profile-birthday-segment-label">Tháng</span>
                                                    <button type="button" class="profile-birthday-trigger" id="birthday-month-btn" aria-haspopup="listbox" aria-expanded="false">
                                                        <span class="profile-birthday-value" id="birthday-month-display">MM</span>
                                                        <svg class="profile-birthday-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    <input type="hidden" id="birthday-month" value="">
                                                    <div class="profile-birthday-menu custom-scrollbar" id="birthday-month-menu" role="listbox">
                                                        <div class="profile-birthday-option profile-birthday-option-empty" data-value="">MM</div>
                                                        @for($m = 1; $m <= 12; $m++)
                                                            @php $mVal = sprintf('%02d', $m); @endphp
                                                            <div class="profile-birthday-option" data-value="{{ $mVal }}" role="option">{{ $mVal }}</div>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div class="profile-birthday-divider"></div>

                                                <!-- Segment: Năm -->
                                                <div class="profile-birthday-segment" data-dropdown="year">
                                                    <span class="profile-birthday-segment-label">Năm</span>
                                                    <button type="button" class="profile-birthday-trigger" id="birthday-year-btn" aria-haspopup="listbox" aria-expanded="false">
                                                        <span class="profile-birthday-value" id="birthday-year-display">YYYY</span>
                                                        <svg class="profile-birthday-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    <input type="hidden" id="birthday-year" value="">
                                                    <div class="profile-birthday-menu custom-scrollbar" id="birthday-year-menu" role="listbox">
                                                        <div class="profile-birthday-option profile-birthday-option-empty" data-value="">YYYY</div>
                                                        @php $currentYear = (int) date('Y'); @endphp
                                                        @for($y = $currentYear; $y >= 1930; $y--)
                                                            <div class="profile-birthday-option" data-value="{{ $y }}" role="option">{{ $y }}</div>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <div class="profile-birthday-divider"></div>

                                                <!-- Calendar Picker Icon Button -->
                                                <label class="profile-birthday-calendar-box" title="Chọn từ lịch" aria-label="Chọn từ lịch">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <input type="date" id="birthday-native-picker" class="profile-birthday-native-input" max="{{ date('Y-m-d') }}">
                                                </label>
                                            </div>

                                            <!-- Hidden Bound Input for standard form submission & programmatic sync -->
                                            <input 
                                                type="hidden" 
                                                name="birthday" 
                                                id="birthday-hidden"
                                                value="{{ old('birthday', $user->birthday) }}" 
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons (Save & Cancel) -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 pt-4">
                                    <div class="sm:col-span-4"></div>
                                    <div class="sm:col-span-8 flex items-center gap-3">
                                        <!-- Save Button (Green, disabled until changes detected) -->
                                        <button 
                                            type="submit" 
                                            id="btn-profile-submit"
                                            disabled
                                            class="btn profile-btn-save px-7 py-2.5 text-sm font-bold"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Lưu thay đổi</span>
                                        </button>

                                        <!-- Cancel Button (Gray, resets form & avatar preview) -->
                                        <button 
                                            type="button" 
                                            id="btn-profile-cancel"
                                            disabled
                                            class="btn profile-btn-cancel px-6 py-2.5 text-sm font-semibold"
                                        >
                                            Hủy
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- Right: Avatar Preview & Upload -->
                            <div class="md:col-span-4 flex flex-col items-center justify-center p-6 border-t md:border-t-0 md:border-l border-gray-100 space-y-4">
                                <div class="relative group">
                                    <img 
                                        id="user-avatar-preview"
                                        src="{{ $user->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=300&q=80' }}" 
                                        alt="{{ $user->username ?? $user->name }}" 
                                        class="w-28 h-28 rounded-full object-cover border-4 border-gray-100 shadow-md ring-2 ring-rose-500/20"
                                    >
                                </div>
                                <label for="avatar" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer shadow-2xs inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Chọn ảnh mới</span>
                                </label>
                                <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" class="hidden">
                                <button type="button" id="btn-cancel-avatar" class="text-xs text-rose-500 hover:underline hidden cursor-pointer">Hủy thay đổi</button>
                                
                                <div class="text-center text-[11px] text-gray-400 space-y-1">
                                    <p>Dung lượng file tối đa 3 MB</p>
                                    <p>Định dạng: .JPEG, .PNG, .WEBP, .GIF</p>
                                </div>
                                @error('avatar')
                                    <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </form>
                </div>

                <!-- ==================== XÓA TÀI KHOẢN CARD (DANGER ZONE) ==================== -->
                <div class="profile-danger-card">
                    <!-- Left Red Accent Bar -->
                    <div class="profile-danger-stripe"></div>

                    <!-- Left Content: Icon + Text -->
                    <div class="flex items-center gap-4 pl-2">
                        <!-- Trash Icon Badge -->
                        <div class="profile-danger-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 leading-snug">Xóa tài khoản vĩnh viễn</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tài khoản của bạn sẽ bị xóa hoàn toàn và không thể khôi phục.</p>
                        </div>
                    </div>

                    <!-- Right: Delete Button -->
                    <button 
                        type="button" 
                        onclick="openDeleteAccountModal()" 
                        class="profile-danger-btn"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Xóa tài khoản</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>

                @if($errors->has('delete_account') || $errors->has('confirm_password') || $errors->has('confirm_text'))
                    <div class="mt-3 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-primary font-semibold space-y-1">
                        @foreach($errors->get('delete_account') as $err)
                            <p>&bull; {{ $err }}</p>
                        @endforeach
                        @foreach($errors->get('confirm_password') as $err)
                            <p>&bull; {{ $err }}</p>
                        @endforeach
                        @foreach($errors->get('confirm_text') as $err)
                            <p>&bull; {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>

<!-- ==================== DELETE ACCOUNT CONFIRMATION MODAL ==================== -->
<div id="delete-account-modal" class="modal-backdrop {{ ($errors->has('delete_account') || $errors->has('confirm_password') || $errors->has('confirm_text')) ? '' : 'hidden' }}">
    <div class="modal-dialog max-w-md">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4 border border-rose-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>

        <h3 class="text-lg font-black text-gray-900 text-center">Xác nhận xóa tài khoản?</h3>
        <p class="text-xs text-gray-500 text-center mt-2 leading-relaxed">
            Hành động này là <strong>vĩnh viễn và không thể đảo ngược</strong>. Toàn bộ hồ sơ thành viên, địa chỉ giao hàng và giỏ hàng của bạn sẽ bị xóa hoàn toàn khỏi ShopMart.
        </p>

        <form action="{{ route('profile.destroy') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('DELETE')

            @if(! $user->provider)
                <!-- Traditional password account confirmation -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        Nhập mật khẩu hiện tại để xác nhận:
                    </label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        required 
                        placeholder="Nhập mật khẩu tài khoản" 
                        class="form-input text-xs"
                    >
                </div>
            @else
                <!-- Social OAuth account confirmation -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        Gõ chữ <span class="text-rose-600 font-extrabold">XÓA</span> để xác nhận:
                    </label>
                    <input 
                        type="text" 
                        name="confirm_text" 
                        required 
                        placeholder="Gõ XÓA" 
                        class="form-input text-xs"
                    >
                </div>
            @endif

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button 
                    type="button" 
                    onclick="closeDeleteAccountModal()" 
                    class="btn btn-outline btn-sm"
                >
                    Hủy bỏ
                </button>
                <button 
                    type="submit" 
                    class="btn btn-primary btn-sm"
                >
                    Xác nhận xóa vĩnh viễn
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@vite(['resources/js/pages/profile-info.js'])
@endpush
@endsection
