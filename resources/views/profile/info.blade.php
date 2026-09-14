@extends('layouts.app')

@section('title', 'Thông tin cá nhân - ' . ($user->username ?? $user->name) . ' | ShopMart')
@section('meta_description', 'Quản lý và cập nhật thông tin cá nhân của bạn tại ShopMart.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: SIDEBAR MENU -->
            <div class="lg:col-span-3">
                <x-user-sidebar active="info" />
            </div>

            <!-- RIGHT COLUMN: MAIN FORM -->
            <div class="lg:col-span-9 space-y-6">
                
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                    <!-- Section Title -->
                    <div class="pb-6 border-b border-gray-100">
                        <h1 class="text-xl font-black text-gray-900">Hồ Sơ Của Tôi</h1>
                        <p class="text-xs text-gray-500 mt-1">Quản lý và cập nhật thông tin tài khoản cá nhân để bảo mật trải nghiệm mua sắm.</p>
                    </div>

                    <!-- Main Form -->
                    <form action="{{ route('profile.update') }}" method="POST" class="pt-6">
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
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:border-[#ea384c] focus:outline-none transition-colors"
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
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:border-[#ea384c] focus:outline-none transition-colors"
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
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:border-[#ea384c] focus:outline-none transition-colors"
                                            placeholder="Ví dụ: 0912345678"
                                        >
                                    </div>
                                </div>

                                <!-- Giới tính -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-2 sm:gap-4">
                                    <label class="sm:col-span-4 text-xs sm:text-right font-semibold text-gray-500">Giới tính</label>
                                    <div class="sm:col-span-8 flex items-center gap-6">
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="gender" value="Nam" {{ old('gender', $user->gender) === 'Nam' ? 'checked' : '' }} class="w-4 h-4 text-[#ea384c] focus:ring-rose-500 border-gray-300">
                                            <span>Nam</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="gender" value="Nữ" {{ old('gender', $user->gender) === 'Nữ' ? 'checked' : '' }} class="w-4 h-4 text-[#ea384c] focus:ring-rose-500 border-gray-300">
                                            <span>Nữ</span>
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="radio" name="gender" value="Khác" {{ old('gender', $user->gender) === 'Khác' || !in_array($user->gender, ['Nam', 'Nữ']) ? 'checked' : '' }} class="w-4 h-4 text-[#ea384c] focus:ring-rose-500 border-gray-300">
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
                                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:bg-white focus:border-[#ea384c] focus:outline-none transition-colors"
                                        >
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-4 pt-4">
                                    <div class="sm:col-span-4"></div>
                                    <div class="sm:col-span-8">
                                        <button 
                                            type="submit" 
                                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-sm font-bold shadow-md shadow-rose-500/20 active:scale-95 transition-all cursor-pointer"
                                        >
                                            Lưu thay đổi
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- Right: Avatar Preview -->
                            <div class="md:col-span-4 flex flex-col items-center justify-center p-6 border-t md:border-t-0 md:border-l border-gray-100 space-y-4">
                                <div class="relative group">
                                    <img 
                                        src="{{ $user->avatar_url ?? 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=300&q=80' }}" 
                                        alt="{{ $user->username ?? $user->name }}" 
                                        class="w-28 h-28 rounded-full object-cover border-4 border-gray-100 shadow-md ring-2 ring-rose-500/20"
                                    >
                                </div>
                                <button type="button" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors cursor-pointer shadow-2xs">
                                    Chọn ảnh mới
                                </button>
                                <div class="text-center text-[11px] text-gray-400 space-y-1">
                                    <p>Dung lượng file tối đa 1 MB</p>
                                    <p>Định dạng: .JPEG, .PNG, .WEBP</p>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- Đổi mật khẩu Card -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-xs">
                    <div class="pb-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-gray-900">Đổi Mật Khẩu</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST" class="pt-6 max-w-xl space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" required placeholder="Nhập mật khẩu hiện tại" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mật khẩu mới</label>
                            <input type="password" name="password" required placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                            <input type="password" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-[#ea384c] focus:outline-none">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                                Xác nhận đổi mật khẩu
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
                            class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-[#ea384c] text-[#ea384c] hover:text-white border border-rose-200 text-xs font-bold transition-all shadow-xs shrink-0 cursor-pointer flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Xóa tài khoản vĩnh viễn</span>
                        </button>
                    </div>

                    @if($errors->has('delete_account') || $errors->has('confirm_password') || $errors->has('confirm_text'))
                        <div class="mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-[#ea384c] font-semibold space-y-1">
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
<div id="delete-account-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 {{ ($errors->has('delete_account') || $errors->has('confirm_password') || $errors->has('confirm_text')) ? '' : 'hidden' }} animate-fade-in">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative border border-gray-100">
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
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
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
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-900 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                    >
                </div>
            @endif

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button 
                    type="button" 
                    onclick="closeDeleteAccountModal()" 
                    class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer"
                >
                    Hủy bỏ
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-[#ea384c] hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-500/20 active:scale-95 transition-all cursor-pointer"
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
</script>
@endsection
