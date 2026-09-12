@extends('layouts.admin')

@section('title', 'Hồ Sơ Quản Trị Viên - ShopMart Admin')
@section('page_title', 'Hồ sơ cá nhân & Bảo mật Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Profile Header Card (Pure Modern Light Theme) -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 sm:p-8 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 relative z-10">
            <div class="relative group shrink-0">
                <img 
                    src="{{ $admin->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' }}" 
                    alt="{{ $admin->name }}" 
                    class="w-24 h-24 rounded-2xl object-cover border-2 border-rose-100 shadow-md"
                >
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white" title="Trực tuyến">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $admin->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-[#ea384c] text-white shadow-xs">
                        SUPER ADMIN
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ ucfirst($admin->status ?? 'active') }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 flex items-center gap-2">
                    <span>Email: <strong class="text-gray-800">{{ $admin->email }}</strong></span>
                    <span>•</span>
                    <span>SĐT: <strong class="text-gray-800">{{ $admin->phone ?? 'Chưa cập nhật' }}</strong></span>
                </p>

                <!-- Stats summary strip -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-gray-100 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Ngày tham gia</span>
                        <span class="font-bold text-gray-800">{{ $stats['joined_date'] }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Người dùng hệ thống</span>
                        <span class="font-black text-[#ea384c]">{{ number_format($stats['total_users']) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Tổng gian hàng</span>
                        <span class="font-black text-amber-600">{{ number_format($stats['total_stores']) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Đơn hàng toàn sàn</span>
                        <span class="font-black text-emerald-600">{{ number_format($stats['total_orders']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Column Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Edit Profile Form (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 shadow-xs p-6 space-y-6">
            <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-[#ea384c] flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">Thông Tin Tài Khoản Quản Trị</h3>
                    <p class="text-[11px] text-gray-500">Cập nhật họ tên hiển thị, email liên hệ và ảnh đại diện</p>
                </div>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Họ và tên Quản trị viên <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $admin->name) }}" 
                        required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                    >
                    @error('name')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email Quản trị <span class="text-rose-500">*</span></label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $admin->email) }}" 
                            required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                        >
                        @error('email')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Số điện thoại</label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $admin->phone) }}" 
                            placeholder="0912345678"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                        >
                        @error('phone')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Đường dẫn ảnh đại diện (Avatar URL)</label>
                    <input 
                        type="url" 
                        name="avatar_url" 
                        value="{{ old('avatar_url', $admin->avatar_url) }}" 
                        placeholder="https://images.unsplash.com/..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-colors"
                    >
                    @error('avatar_url')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer"
                    >
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>

        <!-- Column 2: Password & Privileges (5 cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Change Password Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-[#ea384c] flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Đổi Mật Khẩu Admin</h3>
                        <p class="text-[11px] text-gray-500">Tăng cường an toàn cho hệ thống</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Mật khẩu hiện tại</label>
                        <input 
                            type="password" 
                            name="current_password" 
                            required
                            placeholder="••••••••"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                        @error('current_password')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Mật khẩu mới</label>
                        <input 
                            type="password" 
                            name="password" 
                            required
                            placeholder="Tối thiểu 6 ký tự"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                        @error('password')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Xác nhận mật khẩu mới</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required
                            placeholder="••••••••"
                            class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-2.5 bg-gray-900 hover:bg-[#ea384c] text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs"
                    >
                        Cập Nhật Mật Khẩu
                    </button>
                </form>
            </div>

            <!-- Privileges Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-3">
                <h4 class="text-xs font-black uppercase tracking-wider text-gray-400">Quyền hạn hệ thống cấp phát</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-gray-700 font-medium">Quản lý người dùng & Khóa tài khoản</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Toàn quyền</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-gray-700 font-medium">Duyệt & Quản trị Gian hàng (Stores)</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Toàn quyền</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-gray-700 font-medium">Phát hành Voucher toàn sàn</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Toàn quyền</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="text-gray-700 font-medium">Xem Báo cáo tài chính & Doanh thu</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Toàn quyền</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
