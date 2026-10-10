@extends('layouts.admin')

@section('title', 'Quản Lý Người Dùng & Gian Hàng - ShopMart Admin')
@section('page_title', 'Quản Lý Người Dùng & Gian Hàng Đối Tác')

@section('content')
<div class="space-y-6">

    <!-- Top KPI Stats Unified Card -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-4 sm:p-5">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-y-4 gap-x-2 divide-y md:divide-y-0 lg:divide-x divide-gray-100">
            <!-- 1. Total Users -->
            <div class="flex items-center gap-3 px-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 block">Tổng User</span>
                    <span class="text-base font-black text-gray-900">{{ number_format($stats['total_users'] ?? 0) }}</span>
                </div>
            </div>

            <!-- 2. Sellers -->
            <div class="flex items-center gap-3 px-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 block">Người Bán</span>
                    <span class="text-base font-black text-gray-900">{{ number_format($stats['total_sellers'] ?? 0) }}</span>
                </div>
            </div>

            <!-- 3. Total Stores -->
            <div class="flex items-center gap-3 px-3 pt-3 md:pt-0">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 block">Gian Hàng</span>
                    <span class="text-base font-black text-gray-900">{{ number_format($stats['total_stores'] ?? 0) }}</span>
                </div>
            </div>

            <!-- 4. Pending Stores -->
            <div class="flex items-center gap-3 px-3 pt-3 md:pt-0">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 block">Chờ Xét Duyệt</span>
                    <span class="text-base font-black text-amber-600">{{ number_format($stats['pending_stores'] ?? 0) }}</span>
                </div>
            </div>

            <!-- 5. Active Stores -->
            <div class="flex items-center gap-3 px-3 pt-3 lg:pt-0">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 block">Đang Hoạt Động</span>
                    <span class="text-base font-black text-emerald-600">{{ number_format($stats['active_stores'] ?? 0) }}</span>
                </div>
            </div>

            <!-- 6. ShopMall -->
            <div class="flex items-center gap-3 px-3 pt-3 lg:pt-0">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-gray-400 block">ShopMall</span>
                    <span class="text-base font-black text-primary">{{ number_format($stats['mall_stores'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Bar & Rich Filters Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <!-- Switch Tabs -->
        <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-xs flex gap-1 shrink-0">
            <a href="{{ route('admin.users.index', ['tab' => 'users']) }}" class="px-5 py-2 rounded-xl text-xs font-black transition-all {{ $tab === 'users' ? 'bg-primary text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Người Dùng Toàn Sàn ({{ $users->total() }})
            </a>
            <a href="{{ route('admin.users.index', ['tab' => 'stores']) }}" class="px-5 py-2 rounded-xl text-xs font-black transition-all {{ $tab === 'stores' ? 'bg-primary text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Gian Hàng Đối Tác ({{ $stores->total() }})
                @if(($stats['pending_stores'] ?? 0) > 0)
                    <span class="ml-1.5 px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'stores' ? 'bg-white text-primary' : 'bg-amber-100 text-amber-800' }}">{{ $stats['pending_stores'] }}</span>
                @endif
            </a>
        </div>

        <!-- Search & Filter Controls -->
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                @if($tab === 'users')
                    <!-- Filter Role -->
                    <select name="role" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 font-semibold focus:border-primary focus:outline-hidden shadow-xs cursor-pointer">
                        <option value="">Tất cả vai trò</option>
                        <option value="buyer" {{ request('role') === 'buyer' ? 'selected' : '' }}>Buyer (Người mua)</option>
                        <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Seller (Người bán)</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Quản trị)</option>
                    </select>

                    <!-- Filter Status -->
                    <select name="status" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 font-semibold focus:border-primary focus:outline-hidden shadow-xs cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Đã bị khóa</option>
                    </select>
                @else
                    <!-- Filter Store Status -->
                    <select name="status" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 font-semibold focus:border-primary focus:outline-hidden shadow-xs cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt hồ sơ</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                        <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Bị khóa</option>
                    </select>

                    <!-- Filter Plan -->
                    <select name="plan" class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 font-semibold focus:border-primary focus:outline-hidden shadow-xs cursor-pointer">
                        <option value="">Tất cả gói shop</option>
                        <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Gói Miễn phí (Free)</option>
                        <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Gói Chuyên nghiệp (Pro)</option>
                        <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Gói Doanh nghiệp (Enterprise)</option>
                    </select>
                @endif

                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="{{ $tab === 'users' ? 'Tìm tên, email, SĐT...' : 'Tìm tên shop, MST, chủ sở hữu...' }}" 
                        class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 focus:border-primary focus:outline-hidden shadow-xs w-56 sm:w-64"
                    >
                </div>

                <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-primary text-white text-xs font-bold rounded-xl transition-colors cursor-pointer shadow-xs">
                    Lọc
                </button>

                @if(request('search') || request('role') || request('status') || request('plan'))
                    <a href="{{ route('admin.users.index', ['tab' => $tab]) }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold rounded-xl transition-colors">
                        Xóa lọc
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.users.export', request()->query()) }}" 
               class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/70 text-xs font-bold rounded-xl transition-all shadow-xs flex items-center gap-2 shrink-0"
               title="Xuất danh sách ra file Excel">
                <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="8" y1="13" x2="16" y2="13"></line>
                    <line x1="8" y1="17" x2="16" y2="17"></line>
                </svg>
                <span>Xuất Excel</span>
            </a>
        </div>
    </div>

    @if($tab === 'users')
        <!-- Users Table Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Danh sách tài khoản hệ thống</h3>
                    <p class="text-xs text-gray-500">Xem chi tiết hồ sơ, quản lý phân quyền và kiểm soát khóa tài khoản</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100">
                            <th class="pb-3 px-3">Người dùng</th>
                            <th class="pb-3 px-3">Email & SĐT</th>
                            <th class="pb-3 px-3">Vai trò (Role)</th>
                            <th class="pb-3 px-3">Gian hàng</th>
                            <th class="pb-3 px-3">Trạng thái</th>
                            <th class="pb-3 px-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-3 flex items-center gap-3">
                                    <img src="{{ $u->avatar_url ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80' }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <span class="font-bold text-gray-900 block">{{ $u->name }}</span>
                                        <span class="text-[10px] text-gray-400">Tham gia: {{ $u->created_at ? $u->created_at->format('d/m/Y') : 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="text-gray-900 block font-mono text-xs">{{ $u->email }}</span>
                                    <span class="text-[11px] text-gray-500">{{ $u->phone ?? 'Chưa cập nhật' }}</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <!-- Role changer -->
                                    <form action="{{ route('admin.users.role', $u->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <select name="role" onchange="this.form.submit()" class="px-2.5 py-1 bg-gray-50 border border-gray-200 rounded-lg text-[11px] font-bold text-gray-800 focus:outline-hidden cursor-pointer hover:border-gray-300">
                                            <option value="buyer" {{ $u->role === 'buyer' ? 'selected' : '' }}>Buyer (Người mua)</option>
                                            <option value="seller" {{ $u->role === 'seller' ? 'selected' : '' }}>Seller (Người bán)</option>
                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin (Quản trị)</option>
                                        </select>
                                    </form>
                                </td>

                                <td class="py-3.5 px-3">
                                    @if($u->store)
                                        <span class="text-amber-700 font-bold block">{{ $u->store->name }}</span>
                                        <span class="text-[10px] text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-amber-400 fill-current shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            {{ $u->store->rating }} • {{ $u->store->is_mall ? 'ShopMall' : 'Chuẩn' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Chưa mở Shop</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $u->status === 'banned' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $u->status === 'banned' ? 'Đã khóa' : 'Hoạt động' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- View Details Button -->
                                        <button 
                                            type="button" 
                                            onclick="openUserDetailModal({{ $u->id }})"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors cursor-pointer flex items-center gap-1"
                                            title="Xem toàn bộ thông tin chi tiết"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Chi tiết</span>
                                        </button>

                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái tài khoản này?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-colors cursor-pointer {{ $u->status === 'banned' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-primary hover:bg-rose-100' }}">
                                                    {{ $u->status === 'banned' ? 'Mở khóa' : 'Khóa' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-[10px] italic">Tài khoản này</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>
    @else
        <!-- Stores Table Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Danh sách Gian Hàng Bán Hàng</h3>
                    <p class="text-xs text-gray-500">Quản lý đối tác bán hàng, cấp chứng nhận ShopMall và trạng thái hoạt động</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 font-bold border-b border-gray-100">
                            <th class="pb-3 px-3">Tên Gian hàng</th>
                            <th class="pb-3 px-3">Chủ sở hữu</th>
                            <th class="pb-3 px-3">Chứng nhận Mall</th>
                            <th class="pb-3 px-3">Sản phẩm</th>
                            <th class="pb-3 px-3">Đánh giá & Follow</th>
                            <th class="pb-3 px-3">Trạng thái</th>
                            <th class="pb-3 px-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($stores as $st)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-3">
                                    <span class="font-bold text-gray-900 block text-sm">{{ $st->name }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $st->slug }}</span>
                                    @if($st->business_type === 'business')
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Doanh nghiệp (MST: {{ $st->tax_code ?? 'Chưa có' }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded text-[9px] font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                            Cá nhân {{ $st->id_card_number ? '(CCCD: '.$st->id_card_number.')' : '' }}
                                        </span>
                                    @endif
                                    @if($st->business_license_image || $st->id_card_image)
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($st->business_license_image)
                                                <a href="{{ $st->business_license_image }}" target="_blank" class="text-[10px] text-primary hover:underline font-bold">Xem GPKD</a>
                                            @endif
                                            @if($st->id_card_image)
                                                <a href="{{ $st->id_card_image }}" target="_blank" class="text-[10px] text-primary hover:underline font-bold">Xem CCCD</a>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="text-gray-800 font-semibold block">{{ $st->user?->name ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $st->user?->email }}</span>
                                    <span class="text-[10px] text-gray-500 block">{{ $st->phone }}</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <div class="space-y-1">
                                        <form action="{{ route('admin.stores.toggle-status', $st->id) }}" method="POST">
                                             @csrf
                                             <input type="hidden" name="toggle_mall" value="1">
                                             <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-black transition-colors cursor-pointer {{ $st->is_mall ? 'bg-rose-50 text-primary border border-rose-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                                 {{ $st->is_mall ? 'ShopMall' : 'Shop Thường' }}
                                             </button>
                                        </form>
                                        <div>
                                            <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ ($st->package_plan ?? 'free') === 'enterprise' ? 'bg-amber-100 text-amber-800' : (($st->package_plan ?? 'free') === 'pro' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700') }}">
                                                Gói: {{ strtoupper($st->package_plan ?? 'free') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 font-semibold text-gray-700">
                                    {{ $st->products_count }} sản phẩm
                                </td>

                                <td class="py-3.5 px-3 text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-amber-400 fill-current shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>{{ $st->rating }} • {{ $st->followers ?? '0' }} theo dõi</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $st->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($st->status === 'rejected' || $st->status === 'banned' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                        {{ $st->status === 'pending' ? 'Chờ duyệt' : ($st->status === 'rejected' ? 'Từ chối' : ($st->status === 'banned' ? 'Đã khóa shop' : 'Hoạt động')) }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <!-- View Details Button -->
                                        <button type="button" 
                                                data-view-store-id="{{ $st->id }}"
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors cursor-pointer flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Chi tiết</span>
                                        </button>

                                        @if($st->status === 'pending')
                                            <form action="{{ route('admin.stores.approve', $st->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors cursor-pointer">Phê duyệt</button>
                                            </form>
                                            <form action="{{ route('admin.stores.reject', $st->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn từ chối hồ sơ này?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors cursor-pointer">Từ chối</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.stores.toggle-status', $st->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái gian hàng này?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-colors cursor-pointer {{ $st->status === 'banned' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-primary hover:bg-rose-100' }}">
                                                    {{ $st->status === 'banned' ? 'Mở khóa' : 'Khóa' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100">
                {{ $stores->links() }}
            </div>
        </div>
    @endif

</div>

<!-- ==================== USER DETAIL MODAL (Redesigned Modern Inspector matching Mockup) ==================== -->
<div id="admin-user-detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-[24px] max-w-3xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in duration-200">
        
        <!-- Loading Indicator -->
        <div id="modal-loading" class="text-center py-16 text-gray-400 text-xs">
            <svg class="animate-spin h-7 w-7 mx-auto text-primary mb-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Đang tải dữ liệu hồ sơ người dùng...
        </div>

        <div id="modal-content" class="hidden">
            <!-- Modal Header matching Screenshots 1 & 4 -->
            <div class="p-6 sm:p-7 border-b border-gray-100 bg-white">
                <div class="flex items-start justify-between gap-4">
                    <!-- Left: Avatar + Details -->
                    <div class="flex items-center gap-4">
                        <div class="relative shrink-0">
                            <img id="modal-user-avatar" src="" alt="Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md bg-gray-100">
                            <!-- Online Status Dot -->
                            <span id="modal-online-dot" class="w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0"></span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 id="modal-user-name" class="text-base sm:text-lg font-black text-gray-900 tracking-tight">---</h3>
                                <span id="modal-user-status" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                    Hoạt động
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 font-medium">
                                <span id="modal-user-username" class="text-gray-400">@username</span>
                                <span>•</span>
                                <span id="modal-user-role-label" class="font-semibold text-gray-700">Người mua</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                                <span id="modal-user-phone" class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>---</span>
                                </span>
                                <span class="text-gray-300">•</span>
                                <span id="modal-user-email" class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>---</span>
                                </span>
                            </div>

                            <div class="text-[11px] text-gray-400 pt-0.5">
                                <span>Tham gia: <b class="text-gray-600 font-normal" id="modal-user-joined">---</b></span>
                                <span class="mx-1.5 text-gray-300">|</span>
                                <span>Đăng nhập gần nhất: <b class="text-gray-600 font-normal" id="modal-user-last-login">---</b></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button 
                            type="button" 
                            onclick="alert('Chức năng gửi tin nhắn trực tiếp tới người dùng đang được kết nối!')"
                            class="px-3.5 py-1.5 bg-white hover:bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 rounded-xl transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Nhắn tin</span>
                        </button>

                        <button type="button" onclick="closeUserDetailModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors cursor-pointer ml-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Underline Tabs Navigation matching Mockup -->
            <div class="flex items-center border-b border-gray-100 bg-white px-6 sm:px-7 gap-5 text-xs font-bold text-gray-500 overflow-x-auto" id="modal-tabs">
                <button type="button" onclick="switchModalTab('profile', this)" class="modal-tab-btn active pb-3 pt-3 border-b-2 border-primary text-primary font-black cursor-pointer flex items-center gap-1.5">
                    <span>Thông tin cơ bản</span>
                </button>
                <button type="button" onclick="switchModalTab('orders', this)" class="modal-tab-btn pb-3 pt-3 border-b-2 border-transparent hover:text-gray-800 cursor-pointer flex items-center gap-1.5">
                    <span>Đơn hàng (<span id="modal-orders-count">0</span>)</span>
                </button>
                <button type="button" onclick="switchModalTab('wallet', this)" class="modal-tab-btn pb-3 pt-3 border-b-2 border-transparent hover:text-gray-800 cursor-pointer flex items-center gap-1.5">
                    <span>Giao dịch</span>
                </button>
                <button type="button" onclick="switchModalTab('activities', this)" class="modal-tab-btn pb-3 pt-3 border-b-2 border-transparent hover:text-gray-800 cursor-pointer flex items-center gap-1.5">
                    <span>Hoạt động</span>
                </button>
                <button type="button" onclick="switchModalTab('notes', this)" class="modal-tab-btn pb-3 pt-3 border-b-2 border-transparent hover:text-gray-800 cursor-pointer flex items-center gap-1.5">
                    <span>Ghi chú</span>
                </button>
            </div>

            <!-- Modal Body Content -->
            <div class="p-6 sm:p-7 max-h-[60vh] overflow-y-auto space-y-4">
                
                <!-- Tab 1: Profile (Thông tin cơ bản matching Screenshot 1) -->
                <div id="section-profile" class="modal-section space-y-4">
                    <!-- 6 Grid Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Card 1: Họ và tên -->
                        <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-gray-400 block">Họ và tên</span>
                                <span id="modal-card-name" class="text-xs font-bold text-gray-900 block truncate">---</span>
                            </div>
                        </div>

                        <!-- Card 2: Ngày tham gia -->
                        <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-gray-400 block">Ngày tham gia</span>
                                <span id="modal-card-joined" class="text-xs font-bold text-gray-900 block truncate">---</span>
                            </div>
                        </div>

                        <!-- Card 3: Số điện thoại -->
                        <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-gray-400 block">Số điện thoại</span>
                                <span id="modal-card-phone" class="text-xs font-bold text-gray-900 block truncate">---</span>
                            </div>
                        </div>

                        <!-- Card 4: Email -->
                        <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-gray-400 block">Email</span>
                                <span id="modal-card-email" class="text-xs font-bold text-gray-900 block truncate">---</span>
                            </div>
                        </div>

                        <!-- Card 5: Giới tính -->
                        <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-gray-400 block">Giới tính</span>
                                <span id="modal-card-gender" class="text-xs font-bold text-gray-900 block truncate">---</span>
                            </div>
                        </div>

                        <!-- Card 6: Ngày sinh -->
                        <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h10z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-semibold text-gray-400 block">Ngày sinh</span>
                                <span id="modal-card-birthday" class="text-xs font-bold text-gray-900 block truncate">---</span>
                            </div>
                        </div>
                    </div>

                    <!-- Full Width: Địa chỉ giao hàng -->
                    <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100/80 flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-white text-gray-600 flex items-center justify-center shadow-2xs border border-gray-100 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[11px] font-semibold text-gray-400 block">Địa chỉ giao hàng</span>
                            <span id="modal-card-address" class="text-xs font-bold text-gray-900 block">---</span>
                        </div>
                    </div>

                    <!-- Account Status & Lock Action Card matching Mockup -->
                    <div id="modal-status-card" class="p-4 rounded-2xl border flex items-center justify-between gap-4 transition-colors">
                        <div class="flex items-center gap-3">
                            <div id="modal-status-icon" class="w-9 h-9 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 id="modal-status-title" class="text-xs font-bold text-gray-900">Tài khoản đang hoạt động</h4>
                                <p id="modal-status-desc" class="text-[11px] text-gray-500">Người dùng có thể đăng nhập và sử dụng đầy đủ chức năng.</p>
                            </div>
                        </div>

                        <form id="modal-toggle-status-form" method="POST">
                            @csrf
                            <button 
                                type="submit" 
                                id="modal-status-btn"
                                class="px-4 py-2 rounded-xl text-xs font-bold border transition-colors shadow-2xs cursor-pointer flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Khóa tài khoản</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab 2: Orders (Đơn hàng) -->
                <div id="section-orders" class="modal-section hidden space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                        <div class="p-3.5 bg-emerald-50/60 rounded-xl border border-emerald-100">
                            <span class="text-emerald-700 block text-[10px] uppercase font-bold">Tổng chi tiêu mua sắm</span>
                            <span id="modal-total-spent" class="font-black text-emerald-800 text-base">---</span>
                        </div>
                        <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-100">
                            <span class="text-blue-700 block text-[10px] uppercase font-bold">Tổng số đơn hàng</span>
                            <span id="modal-total-orders" class="font-black text-blue-800 text-base">---</span>
                        </div>
                    </div>

                    <h4 class="text-xs font-black text-gray-900">Danh sách đơn hàng phát sinh</h4>
                    <div id="modal-orders-list" class="space-y-2.5 text-xs"></div>
                </div>

                <!-- Tab 3: Transactions & Wallet (Giao dịch & Trạng thái tài khoản matching Screenshot 3) -->
                <div id="section-wallet" class="modal-section hidden space-y-4">
                    <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-xs space-y-3">
                        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50/80 border border-emerald-100">
                            <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-emerald-800">Đang hoạt động</p>
                                <p class="text-[10px] text-emerald-600 font-medium">Tài khoản bình thường, đầy đủ tính năng</p>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100 text-xs pt-1">
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-gray-500 font-medium">Nhóm người dùng</span>
                                <span id="modal-finance-role" class="font-bold text-gray-900">Khách hàng</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-gray-500 font-medium">Số dư ví</span>
                                <span id="modal-finance-wallet" class="font-black text-gray-900">320.000₫</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-gray-500 font-medium">Đã xác thực KYC</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">Chưa</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Password Card -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70">
                        <h4 class="text-xs font-bold text-gray-900 mb-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            <span>Đặt lại mật khẩu cho tài khoản này</span>
                        </h4>
                        <form id="modal-reset-password-form" method="POST" class="flex items-center gap-2 mt-2">
                            @csrf
                            <input 
                                type="password" 
                                name="new_password" 
                                required 
                                placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" 
                                class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs flex-1 focus:border-primary focus:outline-hidden"
                            >
                            <button type="submit" class="btn btn-primary px-4 py-2 text-xs">
                                Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab 4: Activities (Lịch sử hoạt động matching Screenshot 2) -->
                <div id="section-activities" class="modal-section hidden space-y-4">
                    <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h4 class="text-xs font-black text-gray-900">Lịch sử hoạt động gần đây</h4>
                            <span class="text-xs text-blue-600 font-bold">Thời gian thực</span>
                        </div>

                        <div id="modal-activities-list" class="space-y-3.5 text-xs">
                            <!-- Injected dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Notes (Ghi chú nội bộ Admin) -->
                <div id="section-notes" class="modal-section hidden space-y-3">
                    <div class="p-4 bg-gray-50/70 rounded-2xl border border-gray-100 space-y-3">
                        <h4 class="text-xs font-bold text-gray-900">Ghi chú nội bộ quản trị viên</h4>
                        <textarea rows="3" placeholder="Nhập ghi chú đặc biệt cho tài khoản này (chỉ Admin nhìn thấy)..." class="w-full p-3 bg-white border border-gray-200 rounded-xl text-xs focus:border-primary focus:outline-hidden"></textarea>
                        <button type="button" onclick="alert('Đã lưu ghi chú quản trị viên!')" class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                            Lưu ghi chú
                        </button>
                    </div>
                </div>

            </div>

            <!-- Modal Footer matching Screenshot 1 -->
            <div class="px-6 sm:px-7 py-4 bg-white border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-400 font-mono">ID người dùng: #<span id="modal-user-id">---</span></span>
                <button type="button" onclick="closeUserDetailModal()" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors cursor-pointer">
                    Đóng
                </button>
            </div>
        </div>

    </div>
</div>

<!-- ==================== STORE DETAIL MODAL (Admin Store Inspector) ==================== -->
<div id="admin-store-detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-[24px] max-w-4xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in duration-200">
        
        <!-- Loading Indicator -->
        <div id="store-modal-loading" class="text-center py-16 text-gray-400 text-xs">
            <svg class="animate-spin h-7 w-7 mx-auto text-primary mb-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Đang tải dữ liệu hồ sơ gian hàng...
        </div>

        <div id="store-modal-content" class="hidden">
            <!-- Modal Header -->
            <div class="p-6 sm:p-7 border-b border-gray-100 bg-white">
                <div class="flex items-start justify-between gap-4">
                    <!-- Left: Logo + Store Name + Badges -->
                    <div class="flex items-center gap-4">
                        <div class="relative shrink-0">
                            <img id="modal-store-logo" src="" alt="Logo" class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-md bg-gray-100">
                            <span id="modal-store-status-dot" class="w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0"></span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 id="modal-store-name" class="text-base sm:text-lg font-black text-gray-900 tracking-tight">---</h3>
                                <span id="modal-store-status-badge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                    Hoạt động
                                </span>
                                <span id="modal-store-mall-badge" class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-50 text-primary border border-rose-200 hidden">
                                    ShopMall
                                </span>
                                <span id="modal-store-plan-badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Gói: FREE
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 font-medium">
                                <span id="modal-store-slug" class="text-gray-400 font-mono">shop-slug</span>
                                <span>•</span>
                                <span id="modal-store-business-type" class="font-semibold text-gray-700">Cá nhân</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                                <span id="modal-store-phone" class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>---</span>
                                </span>
                                <span class="text-gray-300">•</span>
                                <span id="modal-store-address" class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate max-w-xs">---</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Close Button -->
                    <button type="button" onclick="closeStoreDetailModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Tabs navigation inside Store Modal -->
                <div class="flex items-center gap-6 mt-6 border-b border-gray-100 text-xs">
                    <button type="button" class="store-modal-tab-btn active pb-3 pt-3 border-b-2 border-primary text-primary font-black cursor-pointer flex items-center gap-1.5" data-store-tab-id="overview">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Tổng quan & Chủ sở hữu
                    </button>
                    <button type="button" class="store-modal-tab-btn pb-3 pt-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 cursor-pointer font-bold flex items-center gap-1.5" data-store-tab-id="legal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Hồ sơ pháp lý & Giấy tờ
                    </button>
                    <button type="button" class="store-modal-tab-btn pb-3 pt-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 cursor-pointer font-bold flex items-center gap-1.5" data-store-tab-id="banking">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Ngân hàng & Vận chuyển
                    </button>
                    <button type="button" class="store-modal-tab-btn pb-3 pt-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 cursor-pointer font-bold flex items-center gap-1.5" data-store-tab-id="products">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Sản phẩm & Hiệu suất kinh doanh
                    </button>
                </div>
            </div>

            <!-- Modal Body Content -->
            <div class="p-6 sm:p-7 max-h-[62vh] overflow-y-auto space-y-6">

                <!-- Tab 1: Overview & Owner -->
                <div id="store-section-overview" class="store-modal-section space-y-5">
                    <!-- Key Metric Banner: Single Unified Panel with Dividers -->
                    <div class="bg-gray-50/80 rounded-2xl border border-gray-100 p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-200/60">
                            <div class="flex items-center gap-3 px-2">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 block">Sản phẩm</span>
                                    <span id="modal-store-products-count" class="text-base font-black text-gray-900 block">0</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 px-2 pt-3 sm:pt-0">
                                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-400 block">Đơn hàng</span>
                                    <span id="modal-store-orders-count" class="text-base font-black text-gray-900 block">0</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 px-2 pt-3 sm:pt-0">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-600 block">Doanh thu</span>
                                    <span id="modal-store-revenue" class="text-base font-black text-emerald-700 block">0₫</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 px-2 pt-3 sm:pt-0">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-amber-700 block">Đánh giá & Follow</span>
                                    <span id="modal-store-rating-follow" class="text-xs font-black text-amber-900 block truncate">5.0 • 0 theo dõi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Owner & Store Info: Unified Single Card with Divider -->
                    <div class="p-5 rounded-2xl border border-gray-100 bg-white shadow-xs">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:divide-x divide-gray-100">
                            <!-- Left: Owner Profile -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <h4 class="text-xs font-black text-gray-900">Chủ sở hữu gian hàng</h4>
                                    </div>
                                    <span id="modal-store-owner-role" class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700">SELLER</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <img id="modal-store-owner-avatar" src="" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                                    <div class="space-y-0.5 min-w-0">
                                        <p id="modal-store-owner-name" class="font-bold text-gray-900 text-sm truncate">---</p>
                                        <p id="modal-store-owner-email" class="text-xs text-gray-500 font-mono truncate">---</p>
                                        <p id="modal-store-owner-phone" class="text-xs text-gray-500">---</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Store Metadata -->
                            <div class="space-y-3 md:pl-6">
                                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <h4 class="text-xs font-black text-gray-900">Thông tin vận hành</h4>
                                    </div>
                                    <span id="modal-store-created-at" class="text-xs text-gray-400 font-medium">Tham gia: ---</span>
                                </div>
                                <div class="text-xs space-y-2">
                                    <div>
                                        <span class="text-gray-400 block font-medium">Mô tả giới thiệu:</span>
                                        <p id="modal-store-description" class="text-gray-700 mt-0.5 italic line-clamp-2">Chưa có mô tả.</p>
                                    </div>
                                    <div class="pt-1 flex items-center justify-between">
                                        <span class="text-gray-400 font-medium">Gói đăng ký:</span>
                                        <span id="modal-store-plan-name" class="font-bold text-gray-800">FREE</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Row for Store -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/70 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Thao tác phê duyệt & cấp quyền</span>
                                <span class="text-[11px] text-gray-500">Duyệt mở shop, đóng/mở tài khoản hoặc gắn chứng nhận ShopMall</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Toggle Mall Form -->
                            <form id="modal-store-toggle-mall-form" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="toggle_mall" value="1">
                                <button type="submit" id="modal-store-toggle-mall-btn" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-primary border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    <span>Bật/Tắt ShopMall</span>
                                </button>
                            </form>

                            <!-- Toggle Ban/Active Form -->
                            <form id="modal-store-toggle-status-form" method="POST" class="inline">
                                @csrf
                                <button type="submit" id="modal-store-toggle-status-btn" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-white hover:bg-black transition-colors cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Thay đổi trạng thái</span>
                                </button>
                            </form>

                            <!-- Direct Approve/Reject for Pending -->
                            <div id="modal-store-pending-actions" class="hidden items-center gap-2">
                                <form id="modal-store-approve-form" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors cursor-pointer flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Phê duyệt</span>
                                    </button>
                                </form>
                                <form id="modal-store-reject-form" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối hồ sơ này?')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 transition-colors cursor-pointer flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Từ chối</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Legal Documents & Business Profile: Single Unified Panel -->
                <div id="store-section-legal" class="store-modal-section hidden space-y-5">
                    <div class="p-5 rounded-2xl border border-gray-100 bg-white shadow-xs space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <h4 class="text-xs font-black text-gray-900">Hồ sơ định danh & Pháp nhân kinh doanh (KYC)</h4>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700">Đã nộp đối soát</span>
                        </div>

                        <!-- 4 Field items divided cleanly -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 text-xs">
                            <div class="px-2">
                                <span class="text-gray-400 block text-[11px] font-medium">Hình thức</span>
                                <span id="modal-store-legal-type" class="font-bold text-gray-900 text-sm mt-0.5 block">Cá nhân</span>
                            </div>
                            <div class="px-2 pt-3 sm:pt-0">
                                <span class="text-gray-400 block text-[11px] font-medium">Người đại diện</span>
                                <span id="modal-store-legal-rep" class="font-bold text-gray-900 text-sm mt-0.5 block truncate">---</span>
                            </div>
                            <div class="px-2 pt-3 sm:pt-0">
                                <span class="text-gray-400 block text-[11px] font-medium">Mã số thuế (MST)</span>
                                <span id="modal-store-legal-tax" class="font-bold text-gray-900 text-sm mt-0.5 block truncate">Chưa có</span>
                            </div>
                            <div class="px-2 pt-3 sm:pt-0">
                                <span class="text-gray-400 block text-[11px] font-medium">Số CMND / CCCD</span>
                                <span id="modal-store-legal-idcard" class="font-bold text-gray-900 text-sm mt-0.5 block truncate">Chưa có</span>
                            </div>
                        </div>

                        <!-- Short Separator Line -->
                        <div class="relative py-1">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                            <div class="relative flex justify-center text-[10px] text-gray-400 uppercase tracking-widest"><span class="bg-white px-3 font-semibold">Tài liệu hình ảnh đính kèm</span></div>
                        </div>

                        <!-- Uploaded Documents Preview -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- GPKD -->
                            <div class="border border-gray-200/80 rounded-xl p-3 flex flex-col items-center text-center bg-gray-50/40">
                                <span class="text-xs font-bold text-gray-800 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Giấy phép ĐKKD (Doanh nghiệp)
                                </span>
                                <div id="modal-store-license-preview" class="w-full h-44 bg-white rounded-lg flex items-center justify-center overflow-hidden border border-gray-100 shadow-2xs">
                                    <span class="text-xs text-gray-400 italic">Chưa tải lên</span>
                                </div>
                                <a id="modal-store-license-link" href="#" target="_blank" class="mt-2 text-xs text-primary font-bold hover:underline hidden flex items-center gap-1">
                                    <span>Mở ảnh gốc</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <!-- CCCD -->
                            <div class="border border-gray-200/80 rounded-xl p-3 flex flex-col items-center text-center bg-gray-50/40">
                                <span class="text-xs font-bold text-gray-800 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    CCCD / Hộ chiếu đại diện
                                </span>
                                <div id="modal-store-idcard-preview" class="w-full h-44 bg-white rounded-lg flex items-center justify-center overflow-hidden border border-gray-100 shadow-2xs">
                                    <span class="text-xs text-gray-400 italic">Chưa tải lên</span>
                                </div>
                                <a id="modal-store-idcard-link" href="#" target="_blank" class="mt-2 text-xs text-primary font-bold hover:underline hidden flex items-center gap-1">
                                    <span>Mở ảnh gốc</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Banking & Shipping: Unified Single Panel -->
                <div id="store-section-banking" class="store-modal-section hidden space-y-5">
                    <div class="p-5 rounded-2xl border border-gray-100 bg-white shadow-xs space-y-5">
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <h4 class="text-xs font-black text-gray-900">Tài chính, Ngân hàng & Vận chuyển</h4>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700">Tự động quyết toán</span>
                        </div>

                        <!-- Top row: Bank card + Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                            <!-- Bank Payout Card Mockup -->
                            <div class="p-4 rounded-xl bg-slate-900 text-white shadow-md space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ngân Hàng Liên Kết</span>
                                    </div>
                                    <span id="modal-store-bank-name" class="font-black text-amber-400 text-xs">---</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block font-mono">Số tài khoản nhận tiền</span>
                                    <span id="modal-store-bank-acc" class="text-base font-black font-mono tracking-widest text-white">---- ---- ---- ----</span>
                                </div>
                                <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Chủ tài khoản:</span>
                                    <span id="modal-store-bank-holder" class="font-bold text-white uppercase">---</span>
                                </div>
                            </div>

                            <!-- Payout Note / Help text -->
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 text-xs space-y-2">
                                <span class="font-bold text-gray-900 block flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Cơ chế quyết toán ví người bán
                                </span>
                                <p class="text-gray-500 text-[11px] leading-relaxed">
                                    Doanh thu các đơn hàng hoàn tất sẽ tự động cộng vào ví Shop và được chuyển vào tài khoản ngân hàng định danh này theo kỳ đối soát định kỳ.
                                </p>
                            </div>
                        </div>

                        <!-- Short Separator Line -->
                        <div class="relative py-1">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                            <div class="relative flex justify-center text-[10px] text-gray-400 uppercase tracking-widest"><span class="bg-white px-3 font-semibold">Cấu hình kết nối hệ thống</span></div>
                        </div>

                        <!-- Bottom row: Shipping & Payments separated by divider -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:divide-x divide-gray-100">
                            <!-- Shipping -->
                            <div class="space-y-2">
                                <h5 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    Đơn vị vận chuyển đã kết nối
                                </h5>
                                <div id="modal-store-shipping-list" class="flex flex-wrap gap-1.5 pt-1">
                                    <!-- Injected dynamically -->
                                </div>
                            </div>

                            <!-- Payment -->
                            <div class="space-y-2 md:pl-4">
                                <h5 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Phương thức thanh toán chấp nhận
                                </h5>
                                <div id="modal-store-payment-list" class="flex flex-wrap gap-1.5 pt-1">
                                    <!-- Injected dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Products & Catalog preview -->
                <div id="store-section-products" class="store-modal-section hidden space-y-4">
                    <div class="p-5 rounded-2xl border border-gray-100 bg-white shadow-xs space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div>
                                <h4 class="text-xs font-black text-gray-900">Sản phẩm tiêu biểu của gian hàng</h4>
                                <span class="text-[11px] text-gray-400">Xem trước danh mục sản phẩm gần đây</span>
                            </div>
                            <a id="modal-store-view-shop-link" href="#" target="_blank" class="text-xs text-primary font-bold hover:underline">
                                Xem trang gian hàng ngoài sàn &rarr;
                            </a>
                        </div>

                        <div id="modal-store-products-grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 sm:px-7 py-4 bg-white border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-400 font-mono">Mã gian hàng: #<span id="modal-store-id">---</span></span>
                <button type="button" onclick="closeStoreDetailModal()" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors cursor-pointer">
                    Đóng
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/pages/admin-users.js'])
@endpush
