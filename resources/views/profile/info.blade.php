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
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="pt-6">
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
                                        <span class="text-sm font-medium text-gray-800">{{ $user->email }}</span>
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
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">Ngày sinh</label>
                                    <div class="sm:col-span-8">
                                        <input 
                                            type="text" 
                                            name="birthday" 
                                            value="{{ old('birthday', $user->birthday) }}" 
                                            placeholder="DD/MM/YYYY" 
                                            class="form-input"
                                        >
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 pt-4">
                                    <div class="sm:col-span-4"></div>
                                    <div class="sm:col-span-8">
                                        <button 
                                            type="submit" 
                                            class="btn btn-primary px-8 py-3 text-sm"
                                        >
                                            Lưu thay đổi
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

                <!-- Đổi mật khẩu Card -->
                @php
                    $hasPassword = $user->hasCustomPassword();
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                    <div class="pb-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-gray-900">{{ $hasPassword ? 'Đổi Mật Khẩu' : 'Thiết Lập Mật Khẩu' }}</h2>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $hasPassword ? 'Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác.' : 'Tài khoản đăng nhập qua ' . ucfirst($user->provider ?? 'Google') . ' chưa có mật khẩu riêng. Bạn có thể tạo mật khẩu mới để đăng nhập bằng email.' }}
                            </p>
                        </div>
                    </div>

                    @if(!$hasPassword)
                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-bold">Đăng nhập trực tiếp bằng {{ ucfirst($user->provider ?? 'Google') }}</p>
                            <p class="mt-0.5 text-amber-700 leading-relaxed">Bạn không cần nhập mật khẩu hiện tại. Hãy tạo mật khẩu mới để có thể đăng nhập bằng email hoặc số điện thoại bất cứ lúc nào.</p>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('profile.password') }}" method="POST" class="pt-6 max-w-xl space-y-4">
                        @csrf
                        @if($hasPassword)
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required placeholder="Nhập mật khẩu hiện tại" class="form-input">
                        </div>
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">{{ $hasPassword ? 'Mật khẩu mới' : 'Mật khẩu tạo mới' }}</label>
                            <input type="password" name="password" required minlength="6" placeholder="Tối thiểu 6 ký tự" class="form-input">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                            <input type="password" name="password_confirmation" required minlength="6" placeholder="Nhập lại mật khẩu mới" class="form-input">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-secondary btn-sm px-6 py-2.5">
                                {{ $hasPassword ? 'Xác nhận đổi mật khẩu' : 'Thiết lập mật khẩu ngay' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ==================== XÓA TÀI KHOẢN CARD (DANGER ZONE) ==================== -->
                <div class="bg-white rounded-2xl border border-rose-100 p-6 sm:p-8 shadow-xs">
                    <div class="pb-4 border-b border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                                <h2 class="text-base font-black text-gray-900">Khu Vực Nguy Hiểm &bull; Xóa Tài Khoản</h2>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 max-w-xl leading-relaxed">
                                Khi xác nhận xóa tài khoản, toàn bộ dữ liệu hồ sơ cá nhân, sổ địa chỉ, lịch sử đơn hàng và các ưu đãi thành viên sẽ bị xóa vĩnh viễn và không thể khôi phục.
                            </p>
                        </div>
                        <button 
                            type="button" 
                            onclick="openDeleteAccountModal()" 
                            class="btn btn-outline-primary btn-sm px-5 py-2.5 shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Xóa tài khoản vĩnh viễn</span>
                        </button>
                    </div>

                    @if($errors->has('delete_account') || $errors->has('confirm_password') || $errors->has('confirm_text'))
                        <div class="mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-primary font-semibold space-y-1">
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

<script>
function openDeleteAccountModal() {
    document.getElementById('delete-account-modal').classList.remove('hidden');
}

function closeDeleteAccountModal() {
    document.getElementById('delete-account-modal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('user-avatar-preview');
    const btnCancelAvatar = document.getElementById('btn-cancel-avatar');
    const originalAvatarSrc = avatarPreview ? avatarPreview.src : '';

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (file) {
                if (file.size > 3 * 1024 * 1024) {
                    alert('Dung lượng ảnh vượt quá 3MB. Vui lòng chọn ảnh nhỏ hơn.');
                    avatarInput.value = '';
                    return;
                }
                avatarPreview.src = URL.createObjectURL(file);
                if (btnCancelAvatar) btnCancelAvatar.classList.remove('hidden');
            }
        });

        if (btnCancelAvatar) {
            btnCancelAvatar.addEventListener('click', () => {
                avatarInput.value = '';
                avatarPreview.src = originalAvatarSrc;
                btnCancelAvatar.classList.add('hidden');
            });
        }
    }
});
</script>
@endsection
