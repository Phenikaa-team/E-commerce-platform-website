@extends('layouts.seller')

@section('title', 'Tài Khoản & Bảo Mật Gian Hàng - ShopMart Seller')
@section('page_title', 'Tài Khoản & Thiết Lập An Toàn Gian Hàng')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- Top Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-gray-200/80">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Tài Khoản &amp; An Toàn Gian Hàng</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Quản lý quyền sở hữu, thiết lập vận hành shop, an ninh dữ liệu và quy chế đóng/tạm dừng gian hàng.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $store->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                <span class="w-2 h-2 rounded-full {{ $store->status === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                {{ $store->status === 'active' ? 'Gian hàng đang hoạt động' : 'Tạm dừng kinh doanh' }}
            </span>
        </div>
    </div>

    @if($errors->has('close_store_error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="space-y-1">
                <p class="font-bold text-rose-900">Không thể thực hiện yêu cầu đóng gian hàng:</p>
                <p>{{ $errors->first('close_store_error') }}</p>
            </div>
        </div>
    @endif

    <!-- SECTION 1: TÁCH BIỆT TÀI KHOẢN CÁ NHÂN & GIAN HÀNG -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Account & Ownership Relation Card (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-2xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Liên Kết Tài Khoản Cá Nhân</h2>
                    <p class="text-[11px] text-gray-500">Chủ sở hữu &amp; quyền pháp lý của gian hàng</p>
                </div>
            </div>

            <!-- Notice explaining separation -->
            <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl text-xs text-blue-900 space-y-1.5">
                <p class="font-bold flex items-center gap-1.5 text-blue-950">
                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Cơ chế bảo mật hai tầng</span>
                </p>
                <p class="text-[11px] text-blue-800 leading-relaxed">
                    Kênh Người Bán chỉ quản lý vận hành của <strong>Gian hàng</strong> (tên shop, kho bãi, doanh thu, thanh toán). Mật khẩu đăng nhập, bảo mật 2 lớp, email và số điện thoại định danh thuộc về <strong>Tài khoản cá nhân</strong> của bạn.
                </p>
            </div>

            <!-- Owner Profile Info Preview -->
            <div class="space-y-3 text-xs bg-gray-50 p-4 rounded-xl border border-gray-200/70">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 font-medium">Chủ sở hữu gian hàng:</span>
                    <span class="font-bold text-gray-900">{{ $user->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 font-medium">Email định danh:</span>
                    <span class="font-mono text-gray-800">{{ $user->masked_email ?? $user->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 font-medium">Số điện thoại tài khoản:</span>
                    <span class="font-mono text-gray-800">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 font-medium">Vai trò hệ thống:</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">Người Bán (Seller)</span>
                </div>
            </div>

            <!-- Link to Personal Profile & Security -->
            <div class="pt-2">
                <a href="{{ route('profile.info') }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-800 font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-2xs group">
                    <svg class="w-4 h-4 text-gray-500 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Quản lý Đổi Mật Khẩu &amp; Hồ Sơ Cá Nhân</span>
                </a>
                <p class="text-[10px] text-gray-400 text-center mt-1.5">
                    Mở tab Thông tin cá nhân để đổi mật khẩu, OTP hoặc chỉnh sửa tài khoản chính.
                </p>
            </div>
        </div>

        <!-- Right: Store Operational & Business Status Settings (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-2xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Thiết Lập Vận Hành Gian Hàng</h2>
                        <p class="text-[11px] text-gray-500">Cập nhật thông tin đại diện cửa hàng &amp; chế độ tạm nghỉ</p>
                    </div>
                </div>

                <span class="text-[11px] text-gray-400 font-mono">Mã shop: #{{ str_pad($store->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>

            <form action="{{ route('seller.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Tên Gian Hàng <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="store_name" 
                            value="{{ old('store_name', $store->name) }}" 
                            required
                            class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:border-primary focus:outline-hidden transition-colors"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Trạng Thái Kinh Doanh (Chế độ bán)</label>
                        <select 
                            name="status" 
                            class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:border-primary focus:outline-hidden transition-colors font-medium"
                        >
                            <option value="active" {{ old('status', $store->status) === 'active' ? 'selected' : '' }}>🟢 Mở bán bình thường</option>
                            <option value="inactive" {{ old('status', $store->status) === 'inactive' ? 'selected' : '' }}>⏸️ Tạm nghỉ / Tạm dừng nhận đơn</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1.5">Mô Tả / Khẩu Hiệu Gian Hàng</label>
                    <textarea 
                        name="store_description" 
                        rows="2" 
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:border-primary focus:outline-hidden transition-colors"
                    >{{ old('store_description', $store->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Hotline CSKH Shop</label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $store->phone) }}" 
                            placeholder="0912 345 678"
                            class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:border-primary focus:outline-hidden transition-colors"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1.5">Địa Chỉ Kho Lấy Hàng</label>
                        <input 
                            type="text" 
                            name="address" 
                            value="{{ old('address', $store->address) }}" 
                            placeholder="Quận/Huyện, Tỉnh/TP"
                            class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:border-primary focus:outline-hidden transition-colors"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <x-image-picker 
                            name="store_logo" 
                            label="Logo đại diện shop" 
                            :value="$store->logo_url" 
                            preview-shape="rounded" 
                            :max-size-mb="3" 
                            help-text="Logo vuông hiển thị trên sàn. Tối đa 3MB."
                        />
                    </div>
                    <div>
                        <x-image-picker 
                            name="store_banner" 
                            label="Ảnh bìa gian hàng" 
                            :value="$store->banner_url" 
                            preview-shape="banner" 
                            :max-size-mb="5" 
                            help-text="Banner đầu trang gian hàng. Tối đa 5MB."
                        />
                    </div>
                </div>

                <!-- Bank payout quick check -->
                <div class="pt-3 border-t border-gray-100">
                    <label class="block font-bold text-gray-800 mb-2">Tài khoản ngân hàng thụ hưởng dòng tiền</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <input 
                            type="text" 
                            name="bank_name" 
                            value="{{ old('bank_name', $store->bank_name) }}" 
                            placeholder="Ngân hàng (Vietcombank...)"
                            class="w-full h-9 px-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-xs focus:bg-white focus:border-primary focus:outline-hidden"
                        >
                        <input 
                            type="text" 
                            name="bank_account_number" 
                            value="{{ old('bank_account_number', $store->bank_account_number) }}" 
                            placeholder="Số tài khoản"
                            class="w-full h-9 px-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-xs focus:bg-white focus:border-primary focus:outline-hidden"
                        >
                        <input 
                            type="text" 
                            name="bank_account_name" 
                            value="{{ old('bank_account_name', $store->bank_account_name) }}" 
                            placeholder="Tên chủ thẻ (In hoa)"
                            class="w-full h-9 px-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-xs focus:bg-white focus:border-primary focus:outline-hidden"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                        Lưu Cài Đặt Gian Hàng
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- SECTION 2: QUY CHẾ VÀ ĐÓNG GIAN HÀNG (CHUYỂN VỀ TÀI KHOẢN THƯỜNG) -->
    <div class="bg-white rounded-2xl border border-rose-200 shadow-xs overflow-hidden">
        
        <!-- Header banner: Danger zone -->
        <div class="p-5 sm:p-6 bg-rose-50/50 border-b border-rose-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-rose-950">Đóng Cửa Hàng &amp; Chuyển Về Tài Khoản Thường</h3>
                    <p class="text-xs text-rose-800 mt-0.5">
                        Dừng vĩnh viễn hoặc tạm thời vai trò Người Bán, ẩn toàn bộ sản phẩm và chuyển tài khoản về người mua sắm (Buyer).
                    </p>
                </div>
            </div>

            <button 
                type="button"
                onclick="openCloseStoreModal()"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition-colors shadow-xs shrink-0 cursor-pointer flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                <span>Quy trình đóng gian hàng</span>
            </button>
        </div>

        <div class="p-5 sm:p-6 space-y-6">
            
            <!-- Check conditions checklist -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Điều Kiện Tiên Quyết Trước Khi Đóng Gian Hàng</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- Criterion 1: Đơn hàng tồn -->
                    <div class="p-4 rounded-xl border {{ $activeOrdersCount === 0 ? 'bg-emerald-50/60 border-emerald-200' : 'bg-rose-50/60 border-rose-200' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold {{ $activeOrdersCount === 0 ? 'text-emerald-900' : 'text-rose-900' }}">1. Không còn đơn hàng dở dang</span>
                            @if($activeOrdersCount === 0)
                                <span class="w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center">✓</span>
                            @else
                                <span class="w-6 h-6 rounded-full bg-rose-500 text-white text-xs font-bold flex items-center justify-center">✕</span>
                            @endif
                        </div>
                        <p class="text-[11px] mt-1.5 {{ $activeOrdersCount === 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            @if($activeOrdersCount === 0)
                                Toàn bộ đơn hàng đã hoàn tất hoặc đã hủy (0 đơn đang xử lý/giao hàng).
                            @else
                                Còn <strong>{{ $activeOrdersCount }} đơn hàng</strong> đang chờ xử lý hoặc vận chuyển. Bạn phải hoàn thành trước.
                            @endif
                        </p>
                    </div>

                    <!-- Criterion 2: Số dư ký quỹ Escrow -->
                    <div class="p-4 rounded-xl border {{ $pendingEscrow <= 0 ? 'bg-emerald-50/60 border-emerald-200' : 'bg-rose-50/60 border-rose-200' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold {{ $pendingEscrow <= 0 ? 'text-emerald-900' : 'text-rose-900' }}">2. Đối soát ví &amp; Escrow</span>
                            @if($pendingEscrow <= 0)
                                <span class="w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center">✓</span>
                            @else
                                <span class="w-6 h-6 rounded-full bg-rose-500 text-white text-xs font-bold flex items-center justify-center">✕</span>
                            @endif
                        </div>
                        <p class="text-[11px] mt-1.5 {{ $pendingEscrow <= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            @if($pendingEscrow <= 0)
                                Không còn khoản tiền nào bị tạm giữ trong Escrow.
                            @else
                                Còn <strong>{{ number_format($pendingEscrow, 0, ',', '.') }}₫</strong> tiền tạm giữ chưa quyết toán.
                            @endif
                        </p>
                    </div>

                    <!-- Criterion 3: Số dư khả dụng trong ví -->
                    <div class="p-4 rounded-xl border bg-gray-50 border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">3. Số dư ví khả dụng</span>
                            <span class="font-bold text-xs text-primary">{{ number_format($availableBalance, 0, ',', '.') }}₫</span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1.5">
                            Khuyến nghị rút toàn bộ về ngân hàng tại <a href="{{ route('seller.finances.index') }}" class="text-blue-600 underline font-semibold">Ví Shop &amp; Đối soát</a> trước khi thực hiện đóng shop.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Điều lệ đóng cửa hàng -->
            <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200 text-xs text-amber-950 space-y-2">
                <h4 class="font-bold text-amber-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Điều lệ cam kết khi đóng gian hàng trên ShopMart:</span>
                </h4>
                <ul class="list-disc list-inside space-y-1 text-amber-900 pl-1 text-[11px] leading-relaxed">
                    <li>Toàn bộ sản phẩm của gian hàng sẽ lập tức chuyển sang trạng thái ẩn (ngừng kinh doanh) và không hiển thị trên sàn.</li>
                    <li>Tài khoản của bạn sẽ lập tức chuyển từ <strong>Người bán (Seller)</strong> về <strong>Người mua (Buyer)</strong>. Bạn vẫn có thể mua hàng, xem lịch sử đơn hàng cá nhân như bình thường.</li>
                    <li>Người bán vẫn có trách nhiệm pháp lý giải quyết các khiếu nại, bảo hành phát sinh đối với các đơn hàng đã bán trong thời hạn quy định.</li>
                    <li>Nếu trong tương lai bạn muốn kinh doanh lại, bạn có thể gửi yêu cầu <strong>Kích hoạt lại gian hàng</strong> bất cứ lúc nào mà không cần tạo lại thông tin từ đầu.</li>
                </ul>
            </div>

        </div>

    </div>

</div>

<!-- ==================== MODAL XÁC NHẬN ĐÓNG GIAN HÀNG ==================== -->
<div id="close-store-modal" class="modal-backdrop hidden animate-fade-in" onclick="if(event.target === this) closeCloseStoreModal()">
    <div class="modal-dialog max-w-lg p-6 sm:p-7 space-y-5">
        
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">Xác Nhận Đóng Cửa Hàng</h3>
                    <p class="text-[11px] text-gray-500">Chuyển tài khoản về người mua thông thường</p>
                </div>
            </div>
            <button type="button" onclick="closeCloseStoreModal()" class="modal-close-btn" aria-label="Đóng">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if(! $canCloseStore)
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1">
                <p class="font-bold">Chưa đủ điều kiện đóng cửa hàng:</p>
                @if($activeOrdersCount > 0)
                    <p>• Bạn còn {{ $activeOrdersCount }} đơn hàng đang trong tiến trình giao nhận.</p>
                @endif
                @if($pendingEscrow > 0)
                    <p>• Dòng tiền tạm giữ Escrow còn {{ number_format($pendingEscrow, 0, ',', '.') }}₫ chưa quyết toán.</p>
                @endif
                <p class="text-[11px] text-rose-700 pt-1">Vui lòng hoàn tất toàn bộ các giao dịch trước khi đóng gian hàng.</p>
            </div>
            <div class="pt-2 flex justify-end">
                <button type="button" onclick="closeCloseStoreModal()" class="btn btn-outline btn-sm">Đóng</button>
            </div>
        @else
            <form action="{{ route('seller.settings.close-store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-gray-700 leading-relaxed text-[11px]">
                    Gian hàng <strong>{{ $store->name }}</strong> sẽ dừng hoạt động ngay lập tức. Bạn sẽ được điều hướng về tài khoản mua sắm cá nhân.
                </div>

                <div>
                    <label class="block font-bold text-gray-800 mb-1">
                        Gõ cụm từ xác nhận: <span class="text-rose-600 font-mono select-all">ĐÓNG CỬA HÀNG</span> <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="confirm_text" 
                        required 
                        placeholder="Nhập chính xác: ĐÓNG CỬA HÀNG" 
                        class="form-input text-xs font-semibold"
                    >
                </div>

                <div>
                    <label class="block font-bold text-gray-800 mb-1">
                        Mật khẩu tài khoản cá nhân <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Nhập mật khẩu của bạn để xác thực" 
                        class="form-input text-xs"
                    >
                </div>

                <div>
                    <label class="block font-bold text-gray-800 mb-1">
                        Lý do đóng cửa hàng (Tùy chọn)
                    </label>
                    <textarea 
                        name="reason" 
                        rows="2" 
                        placeholder="Ví dụ: Tạm nghỉ kinh doanh, chuyển đổi ngành nghề..." 
                        class="form-input text-xs"
                    ></textarea>
                </div>

                <div class="pt-1">
                    <label class="flex items-start gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="agree_terms" required class="mt-0.5 rounded text-rose-600 focus:ring-rose-500">
                        <span class="text-[11px] text-gray-600">
                            Tôi cam kết đã giải quyết xong mọi trách nhiệm hậu mãi, giao dịch và đồng ý chuyển gian hàng về trạng thái đóng.
                        </span>
                    </label>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeCloseStoreModal()" class="btn btn-outline btn-sm">Hủy bỏ</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer">
                        Xác Nhận Đóng Gian Hàng
                    </button>
                </div>
            </form>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
function openCloseStoreModal() {
    const modal = document.getElementById('close-store-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeCloseStoreModal() {
    const modal = document.getElementById('close-store-modal');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCloseStoreModal();
    }
});
</script>
@endpush
