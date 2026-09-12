@extends('layouts.admin')

@section('title', 'Quản Lý Người Dùng & Gian Hàng - ShopMart Admin')
@section('page_title', 'Quản Lý Người Dùng & Gian Hàng Đối Tác')

@section('content')
<div class="space-y-6">

    <!-- Tabs Bar: Users vs Stores (Clean Light Theme) -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-xs flex gap-1">
            <a href="{{ route('admin.users.index', ['tab' => 'users']) }}" class="px-5 py-2 rounded-xl text-xs font-black transition-all {{ $tab === 'users' ? 'bg-[#ea384c] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Người Dùng Toàn Sàn ({{ $users->total() }})
            </a>
            <a href="{{ route('admin.users.index', ['tab' => 'stores']) }}" class="px-5 py-2 rounded-xl text-xs font-black transition-all {{ $tab === 'stores' ? 'bg-[#ea384c] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                Gian Hàng Đối Tác ({{ $stores->total() }})
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                placeholder="Tìm kiếm tên, email, SĐT..." 
                class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs text-gray-800 focus:border-[#ea384c] focus:outline-hidden shadow-xs w-64"
            >
            <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-[#ea384c] text-white text-xs font-bold rounded-xl transition-colors cursor-pointer shadow-xs">
                Tìm kiếm
            </button>
        </form>
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
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-colors cursor-pointer {{ $u->status === 'banned' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-[#ea384c] hover:bg-rose-100' }}">
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
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="text-gray-800 font-semibold block">{{ $st->user?->name ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $st->user?->email }}</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <form action="{{ route('admin.stores.toggle-status', $st->id) }}" method="POST">
                                         @csrf
                                         <input type="hidden" name="toggle_mall" value="1">
                                         <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-black transition-colors cursor-pointer {{ $st->is_mall ? 'bg-rose-50 text-[#ea384c] border border-rose-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                             {{ $st->is_mall ? 'ShopMall' : 'Shop Thường' }}
                                         </button>
                                    </form>
                                </td>

                                <td class="py-3.5 px-3 font-semibold text-gray-700">
                                    {{ $st->products_count }} sản phẩm
                                </td>

                                <td class="py-3.5 px-3 text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-amber-400 fill-current shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>{{ $st->rating }} • {{ $st->followers ?? '0' }} theo dõi</span>
                                </td>

                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $st->status === 'banned' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $st->status === 'banned' ? 'Đã khóa shop' : 'Hoạt động' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <form action="{{ route('admin.stores.toggle-status', $st->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái gian hàng này?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-colors cursor-pointer {{ $st->status === 'banned' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-[#ea384c] hover:bg-rose-100' }}">
                                            {{ $st->status === 'banned' ? 'Mở khóa Shop' : 'Khóa Shop' }}
                                        </button>
                                    </form>
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
            <svg class="animate-spin h-7 w-7 mx-auto text-[#ea384c] mb-3" fill="none" viewBox="0 0 24 24">
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
                <button type="button" onclick="switchModalTab('profile', this)" class="modal-tab-btn active pb-3 pt-3 border-b-2 border-[#ea384c] text-[#ea384c] font-black cursor-pointer flex items-center gap-1.5">
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
                                <span class="text-gray-500 font-medium">Điểm tích lũy</span>
                                <span id="modal-finance-coins" class="font-black text-amber-600">1.248 điểm</span>
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
                                class="px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs flex-1 focus:border-[#ea384c] focus:outline-hidden"
                            >
                            <button type="submit" class="px-4 py-2 bg-[#ea384c] hover:bg-[#d3273b] text-white text-xs font-bold rounded-xl cursor-pointer transition-all shadow-xs">
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
                        <textarea rows="3" placeholder="Nhập ghi chú đặc biệt cho tài khoản này (chỉ Admin nhìn thấy)..." class="w-full p-3 bg-white border border-gray-200 rounded-xl text-xs focus:border-[#ea384c] focus:outline-hidden"></textarea>
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
@endsection

@push('scripts')
<script>
    async function openUserDetailModal(userId) {
        const modal = document.getElementById('admin-user-detail-modal');
        const loading = document.getElementById('modal-loading');
        const content = document.getElementById('modal-content');
        
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');

        // Reset to first tab
        switchModalTab('profile', document.querySelector('.modal-tab-btn'));

        try {
            const res = await fetch(`/admin/users/${userId}`);
            const data = await res.json();

            if (data.success) {
                const u = data.user;

                // ID & Avatar
                document.getElementById('modal-user-id').textContent = u.id;
                document.getElementById('modal-user-avatar').src = u.avatar_url;
                document.getElementById('modal-user-name').textContent = u.name;
                document.getElementById('modal-user-username').textContent = u.username;
                document.getElementById('modal-user-role-label').textContent = u.role_label;
                
                // Contacts
                document.getElementById('modal-user-email').querySelector('span').textContent = u.email;
                document.getElementById('modal-user-phone').querySelector('span').textContent = u.phone;
                
                // Metadata
                document.getElementById('modal-user-joined').textContent = u.created_at;
                document.getElementById('modal-user-last-login').textContent = u.last_login_at;

                // Status Badge & Controls
                const statusEl = document.getElementById('modal-user-status');
                const onlineDot = document.getElementById('modal-online-dot');
                const statusCard = document.getElementById('modal-status-card');
                const statusTitle = document.getElementById('modal-status-title');
                const statusDesc = document.getElementById('modal-status-desc');
                const statusBtn = document.getElementById('modal-status-btn');
                const statusIcon = document.getElementById('modal-status-icon');
                const toggleForm = document.getElementById('modal-toggle-status-form');

                toggleForm.action = `/admin/users/${u.id}/toggle-status`;

                if (u.status === 'banned') {
                    statusEl.textContent = 'Đã khóa';
                    statusEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-200/60';
                    onlineDot.className = 'w-3.5 h-3.5 rounded-full bg-rose-500 border-2 border-white absolute bottom-0 right-0';
                    
                    statusCard.className = 'p-4 rounded-2xl border border-rose-100 bg-rose-50/40 flex items-center justify-between gap-4 transition-colors';
                    statusIcon.className = 'w-9 h-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0';
                    statusIcon.innerHTML = '<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                    statusTitle.textContent = 'Tài khoản đang bị tạm khóa';
                    statusDesc.textContent = 'Người dùng bị chặn đăng nhập và thực hiện giao dịch trên hệ thống.';
                    statusBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold border border-emerald-200 bg-white hover:bg-emerald-50 text-emerald-600 transition-colors shadow-2xs cursor-pointer flex items-center gap-1.5';
                    statusBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg><span>Mở khóa tài khoản</span>';
                } else {
                    statusEl.textContent = 'Hoạt động';
                    statusEl.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60';
                    onlineDot.className = 'w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0';
                    
                    statusCard.className = 'p-4 rounded-2xl border border-emerald-100 bg-emerald-50/40 flex items-center justify-between gap-4 transition-colors';
                    statusIcon.className = 'w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0';
                    statusIcon.innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
                    statusTitle.textContent = 'Tài khoản đang hoạt động';
                    statusDesc.textContent = 'Người dùng có thể đăng nhập và sử dụng đầy đủ chức năng.';
                    statusBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 transition-colors shadow-2xs cursor-pointer flex items-center gap-1.5';
                    statusBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg><span>Khóa tài khoản</span>';
                }

                // Tab 1 Info Cards
                document.getElementById('modal-card-name').textContent = u.name;
                document.getElementById('modal-card-joined').textContent = u.created_at;
                document.getElementById('modal-card-phone').textContent = u.phone;
                document.getElementById('modal-card-email').textContent = u.email;
                document.getElementById('modal-card-gender').textContent = u.gender;
                document.getElementById('modal-card-birthday').textContent = u.birthday;
                document.getElementById('modal-card-address').textContent = u.default_address;

                // Tab 2: Orders
                document.getElementById('modal-orders-count').textContent = u.total_orders_count;
                document.getElementById('modal-total-spent').textContent = u.total_spent;
                document.getElementById('modal-total-orders').textContent = u.total_orders_count + ' đơn hàng';
                
                const ordersList = document.getElementById('modal-orders-list');
                ordersList.innerHTML = '';
                if (u.recent_orders && u.recent_orders.length > 0) {
                    u.recent_orders.forEach(o => {
                        ordersList.innerHTML += `
                            <div class="p-3 bg-gray-50/80 rounded-xl border border-gray-100 flex items-center justify-between">
                                <div>
                                    <span class="font-mono font-bold text-gray-900">${o.order_number}</span>
                                    <span class="text-gray-400 text-[10px] block">${o.created_at} • ${o.items_count} sản phẩm</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-black text-gray-900 block">${o.total_amount}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${o.status === 'completed' || o.status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200'}">${o.status === 'completed' ? 'Đã giao' : (o.status === 'shipping' ? 'Đang giao' : o.status)}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    ordersList.innerHTML = '<p class="text-gray-400 italic text-center py-4">Chưa có đơn hàng phát sinh.</p>';
                }

                // Tab 3: Finance / Wallet
                document.getElementById('modal-finance-role').textContent = u.role_label;
                document.getElementById('modal-finance-coins').textContent = u.coins;
                document.getElementById('modal-finance-wallet').textContent = u.wallet_balance;
                document.getElementById('modal-reset-password-form').action = `/admin/users/${u.id}/reset-password`;

                // Tab 4: Activities (Matching Screenshot 2)
                const activitiesList = document.getElementById('modal-activities-list');
                activitiesList.innerHTML = '';
                if (u.activities && u.activities.length > 0) {
                    u.activities.forEach(act => {
                        let iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>';
                        let bgClass = 'bg-teal-50 text-teal-600';
                        if (act.icon === 'cart') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'; 
                            bgClass = 'bg-blue-50 text-blue-600'; 
                        }
                        if (act.icon === 'lock') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>'; 
                            bgClass = 'bg-indigo-50 text-indigo-600'; 
                        }
                        if (act.icon === 'user') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'; 
                            bgClass = 'bg-purple-50 text-purple-600'; 
                        }
                        if (act.icon === 'chat') { 
                            iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>'; 
                            bgClass = 'bg-pink-50 text-pink-600'; 
                        }

                        activitiesList.innerHTML += `
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full ${bgClass} flex items-center justify-center shrink-0 font-bold">
                                    ${iconSvg}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold text-gray-900 text-xs">${act.title}</p>
                                        <span class="text-[10px] text-gray-400 font-medium">${act.time}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">${act.subtext}</p>
                                </div>
                            </div>
                        `;
                    });
                }

                loading.classList.add('hidden');
                content.classList.remove('hidden');
            }
        } catch (e) {
            console.error(e);
            loading.innerHTML = '<p class="text-rose-500 py-6">Lỗi khi tải dữ liệu người dùng.</p>';
        }
    }

    function closeUserDetailModal() {
        const modal = document.getElementById('admin-user-detail-modal');
        if (modal) modal.classList.add('hidden');
    }

    function switchModalTab(tabId, btn) {
        document.querySelectorAll('.modal-tab-btn').forEach(b => {
            b.className = 'modal-tab-btn pb-3 pt-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 cursor-pointer font-bold flex items-center gap-1.5';
        });
        if (btn) {
            btn.className = 'modal-tab-btn active pb-3 pt-3 border-b-2 border-[#ea384c] text-[#ea384c] font-black cursor-pointer flex items-center gap-1.5';
        }

        document.querySelectorAll('.modal-section').forEach(sec => {
            sec.classList.add('hidden');
        });

        const targetSec = document.getElementById(`section-${tabId}`);
        if (targetSec) targetSec.classList.remove('hidden');
    }
</script>
@endpush
