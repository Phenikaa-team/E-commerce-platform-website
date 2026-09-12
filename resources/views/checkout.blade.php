@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng - ShopMart')

@section('content')
<div class="checkout-container">
    
    <!-- Breadcrumb & Steps -->
    <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-[#ea384c]">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('cart') }}" class="hover:text-[#ea384c]">Giỏ hàng</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">Thanh toán & Đặt hàng</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Thanh Toán An Toàn</h1>
        <p class="text-sm text-gray-500 mt-1">Vui lòng kiểm tra kỹ địa chỉ nhận hàng và phương thức thanh toán trước khi hoàn tất</p>
    </div>

    <!-- Main Checkout Grid -->
    <form action="{{ route('checkout.order') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Address, Products, Payment Methods (7 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Shipping Address Section -->
                <div class="checkout-card">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 via-amber-500 to-rose-500"></div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-[#ea384c] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Địa chỉ nhận hàng</h2>
                        </div>

                        @auth
                            <button type="button" id="btn-open-address-modal" class="text-xs font-semibold text-[#ea384c] hover:underline cursor-pointer">
                                Thay đổi / Thêm mới
                            </button>
                        @endauth
                    </div>

                    <!-- Address Display / Inputs -->
                    <div id="selected-address-card" class="bg-gray-50/80 rounded-xl p-4 border border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-sm font-bold text-gray-900" id="display-recipient-name">
                                    {{ $defaultAddress->recipient_name ?? (auth()->user()->name ?? 'Khách Mua Hàng') }}
                                </span>
                                <span class="text-xs text-gray-500 ml-2" id="display-recipient-phone">
                                    {{ $defaultAddress->phone ?? (auth()->user()->phone ?? 'Chưa cập nhật SĐT') }}
                                </span>
                                @if(isset($defaultAddress) && $defaultAddress->is_default)
                                    <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Mặc định</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-2 leading-relaxed" id="display-recipient-address">
                            {{ $defaultAddress->address_line ?? 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh' }}
                        </p>
                    </div>

                    <!-- Hidden inputs sent to form -->
                    <input type="hidden" name="recipient_name" id="input-recipient-name" value="{{ $defaultAddress->recipient_name ?? (auth()->user()->name ?? 'Khách Mua Hàng') }}">
                    <input type="hidden" name="phone" id="input-recipient-phone" value="{{ $defaultAddress->phone ?? (auth()->user()->phone ?? '0912345678') }}">
                    <input type="hidden" name="address_line" id="input-recipient-address" value="{{ $defaultAddress->address_line ?? 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh' }}">
                </div>

                <!-- 2. Ordered Products List -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <h2 class="text-base font-bold text-gray-900">Danh sách sản phẩm mua ({{ $selectedItems->count() }})</h2>
                        </div>
                        <a href="{{ route('cart') }}" class="text-xs font-semibold text-gray-500 hover:text-[#ea384c]">Chỉnh sửa giỏ hàng</a>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($selectedItems as $item)
                            <div class="py-4 flex items-center gap-4 first:pt-0 last:pb-0">
                                <img src="{{ $item->product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 shrink-0">
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 truncate">{{ $item->product->name }}</h3>
                                    @if($item->selected_variant)
                                        <p class="text-xs text-gray-400 mt-0.5">Phân loại: <span class="text-gray-600 font-medium">{{ $item->selected_variant }}</span></p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">Cung cấp bởi: <span class="text-amber-700 font-medium">{{ $item->product->store->name ?? 'ShopMart Mall' }}</span></p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-sm font-black text-gray-900">{{ number_format((float) $item->unit_price, 0, ',', '.') }}₫</p>
                                    <p class="text-xs text-gray-400">Số lượng: x{{ $item->quantity }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Notes -->
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <label for="order-notes" class="block text-xs font-semibold text-gray-700 mb-1.5">Ghi chú cho đơn hàng (tùy chọn)</label>
                        <input type="text" name="notes" id="order-notes" placeholder="Ví dụ: Giao hàng vào giờ hành chính, gọi trước khi giao..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 focus:bg-white focus:border-[#ea384c] focus:outline-hidden transition-all">
                    </div>
                </div>

                <!-- 3. Payment Method Selection -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <h2 class="text-base font-bold text-gray-900">Phương thức thanh toán</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" id="payment-methods-wrapper">
                        <!-- COD -->
                        <label class="payment-option relative flex flex-col p-4 rounded-xl border-2 border-[#ea384c] bg-rose-50/40 cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="cod" checked class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <span class="payment-check-dot w-4 h-4 rounded-full border-2 border-[#ea384c] bg-[#ea384c] flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </span>
                            </div>
                            <span class="text-xs font-bold text-gray-900">Thanh toán khi nhận hàng (COD)</span>
                            <span class="text-[11px] text-gray-500 mt-1">Kiểm tra hàng trước khi thanh toán tiền mặt</span>
                        </label>

                        <!-- VNPay Sandbox -->
                        <label class="payment-option relative flex flex-col p-4 rounded-xl border-2 border-gray-200 bg-white hover:border-gray-300 cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="vnpay" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <span class="payment-check-dot w-4 h-4 rounded-full border-2 border-gray-300 bg-transparent flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-blue-700">VNPay Sandbox</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-blue-100 text-blue-800">QR / Thẻ</span>
                            </div>
                            <span class="text-[11px] text-gray-500 mt-1">Quét mã VNPAY-QR, ATM nội địa, Visa/Master</span>
                        </label>

                        <!-- MoMo -->
                        <label class="payment-option relative flex flex-col p-4 rounded-xl border-2 border-gray-200 bg-white hover:border-gray-300 cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="momo" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                                </div>
                                <span class="payment-check-dot w-4 h-4 rounded-full border-2 border-gray-300 bg-transparent flex items-center justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                                </span>
                            </div>
                            <span class="text-xs font-bold text-gray-900">Ví MoMo</span>
                            <span class="text-[11px] text-gray-500 mt-1">Thanh toán tiện lợi qua ứng dụng MoMo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column: Coupon & Order Summary Sticky Card (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- 1. Shopee-Style Voucher Bar & Quick Entry -->
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
                    <!-- Shopee Voucher Selector Trigger Row -->
                    <div 
                        id="btn-open-voucher-modal" 
                        onclick="openShopeeModal()"
                        class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-orange-50/60 to-rose-50/40 border border-orange-200/70 hover:border-[#ea384c] cursor-pointer transition-all group"
                    >
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#ea384c] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-black text-gray-900 block">ShopMart Voucher</span>
                                <span class="text-[11px] text-gray-500 group-hover:text-[#ea384c] transition-colors" id="voucher-status-text">
                                    Chọn hoặc nhập mã khuyến mãi >
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 text-xs font-bold text-[#ea384c]">
                            <span id="voucher-applied-pill" class="hidden px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700"></span>
                            <span class="text-gray-400 group-hover:text-[#ea384c] text-sm font-bold">›</span>
                        </div>
                    </div>

                    <!-- Direct Quick Input (Shopee Style: Input + Orange/Red Áp dụng) -->
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input 
                                    type="text" 
                                    id="coupon-input" 
                                    placeholder="Nhập mã voucher (vd: FREESHIP25K, ABC...)" 
                                    class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider text-gray-800 focus:bg-white focus:border-[#ee4d2d] focus:outline-hidden transition-all"
                                >
                            </div>
                            <button 
                                type="button" 
                                id="btn-apply-coupon" 
                                onclick="submitCoupon(document.getElementById('coupon-input').value.trim(), false)"
                                class="px-4 py-2 bg-[#ee4d2d] hover:bg-[#d03b1d] text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs active:scale-95 shrink-0"
                            >
                                Áp dụng
                            </button>
                        </div>
                        <p id="coupon-message" class="text-xs mt-2 hidden"></p>
                    </div>

                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="coupon_code" id="hidden-coupon-code" value="">
                </div>

                <!-- 2. Cost Breakdown & Grand Total Sticky Card -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xl sticky top-24">
                    <h3 class="text-sm font-bold text-gray-900 pb-4 border-b border-gray-100 mb-4">Chi tiết thanh toán</h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Tạm tính tiền hàng:</span>
                            <span class="font-bold text-gray-900" id="summary-subtotal">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600">
                            <span>Phí vận chuyển toàn quốc:</span>
                            <span class="font-bold text-gray-900" id="summary-shipping">
                                @if($shippingFee == 0)
                                    <span class="text-emerald-600 font-extrabold">MIỄN PHÍ</span>
                                @else
                                    {{ number_format($shippingFee, 0, ',', '.') }}₫
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-emerald-600 font-medium hidden" id="discount-row">
                            <span id="discount-label">Giảm giá voucher:</span>
                            <span class="font-bold" id="summary-discount">-0₫</span>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex items-baseline justify-between">
                            <div>
                                <span class="text-sm font-black text-gray-900 block">Tổng thanh toán:</span>
                                <span class="text-[10px] text-gray-400">(Đã bao gồm VAT & phí)</span>
                            </div>
                            <span class="text-2xl font-black text-[#ea384c]" id="summary-grand-total">
                                {{ number_format($total, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>

                    <!-- Place Order CTA Button -->
                    <button 
                        type="submit" 
                        id="btn-submit-order" 
                        class="w-full mt-6 py-3.5 bg-gradient-to-r from-[#ea384c] to-[#ff5c6c] hover:from-[#d3273b] hover:to-[#ea384c] text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98"
                    >
                        <span>Đặt hàng ngay</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <div class="mt-4 flex items-center justify-center gap-4 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Bảo mật SSL 256-bit</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Đổi trả miễn phí 15 ngày</span>
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<!-- ==================== ADDRESS CHANGE / MODAL ==================== -->
<div id="address-modal" class="fixed inset-0 z-[200] bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-base font-bold text-gray-900">Địa chỉ nhận hàng</h3>
            <button type="button" id="btn-close-address-modal" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <!-- Existing Addresses List -->
        @if(isset($addresses) && $addresses->isNotEmpty())
            <div class="space-y-3 mb-6 max-h-56 overflow-y-auto pr-1">
                @foreach($addresses as $addr)
                    <div class="p-3 rounded-xl border border-gray-200 hover:border-[#ea384c] cursor-pointer transition-colors address-item-option"
                         data-name="{{ $addr->recipient_name }}"
                         data-phone="{{ $addr->phone }}"
                         data-address="{{ $addr->address_line }}"
                         data-default="{{ $addr->is_default ? '1' : '0' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-900">{{ $addr->recipient_name }} - {{ $addr->phone }}</span>
                            @if($addr->is_default)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Mặc định</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 mt-1">{{ $addr->address_line }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Quick Add New Address Form -->
        <div class="pt-4 border-t border-gray-100">
            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3">+ Thêm địa chỉ mới</h4>
            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" id="modal-name-input" placeholder="Họ và tên người nhận" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                    <input type="text" id="modal-phone-input" placeholder="Số điện thoại" class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                </div>
                <input type="text" id="modal-address-input" placeholder="Địa chỉ chi tiết (Số nhà, đường, phường, quận, TP)" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                <button type="button" id="btn-save-new-address" class="w-full py-2.5 bg-gray-900 hover:bg-[#ea384c] text-white font-bold rounded-xl transition-colors cursor-pointer text-xs">
                    Lưu & Sử dụng địa chỉ này
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== SHOPEE-STYLE VOUCHER MODAL ==================== -->
<div id="shopee-voucher-modal" class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Top Header matching Shopee Screenshot 1 & 2 -->
        <div class="h-14 px-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button type="button" id="btn-close-voucher-modal" onclick="closeShopeeModal()" class="text-gray-500 hover:text-gray-800 p-1.5 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h3 class="text-base font-bold text-gray-900">Mã Giảm Giá</h3>
            </div>
            <a href="javascript:void(0)" class="text-xs font-semibold text-gray-500 hover:text-[#ee4d2d]">Lịch sử</a>
        </div>

        <!-- Voucher Input Form matching Shopee Screenshot 2 -->
        <div class="p-3.5 bg-gray-50/80 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        id="modal-voucher-input" 
                        placeholder="Nhập mã voucher" 
                        class="w-full px-3.5 py-2.5 bg-white border-2 border-orange-500 focus:border-[#ee4d2d] rounded-xl text-xs font-bold uppercase tracking-wider text-gray-900 focus:outline-hidden transition-all placeholder:normal-case placeholder:font-normal"
                    >
                </div>
                <button 
                    type="button" 
                    id="btn-modal-apply" 
                    onclick="submitCoupon(document.getElementById('modal-voucher-input').value.trim(), true)"
                    class="px-5 py-2.5 bg-[#ee4d2d] hover:bg-[#d03b1d] text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer shrink-0 active:scale-95"
                >
                    Áp dụng
                </button>
            </div>
            <p id="modal-coupon-message" class="text-xs mt-2 px-1 hidden font-semibold"></p>
        </div>

        <!-- Category Tabs matching Shopee Screenshot 1 -->
        <div class="flex items-center border-b border-gray-100 px-2 bg-white text-xs shrink-0 overflow-x-auto scrollbar-none">
            <button type="button" class="voucher-tab-btn py-3 px-3.5 font-bold text-[#ee4d2d] border-b-2 border-[#ee4d2d] shrink-0" data-cat="all">
                Tất cả
            </button>
            <button type="button" class="voucher-tab-btn py-3 px-3.5 font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent shrink-0" data-cat="freeship">
                Mã Vận Chuyển
            </button>
            <button type="button" class="voucher-tab-btn py-3 px-3.5 font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent shrink-0" data-cat="shopmart">
                ShopMart Mall
            </button>
            <button type="button" class="voucher-tab-btn py-3 px-3.5 font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent shrink-0" data-cat="category">
                Danh mục & Shop
            </button>
        </div>

        <!-- Sub-filter Pills matching Shopee Screenshot 1 -->
        <div class="px-4 py-2 bg-gray-50/50 border-b border-gray-100 flex items-center gap-2 shrink-0">
            <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-white text-[#ee4d2d] border border-orange-200 shadow-2xs flex items-center gap-1">
                <svg class="w-3 h-3 text-[#ee4d2d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Mới nhất
            </span>
            <span class="px-3 py-1 rounded-md text-[11px] font-medium bg-white text-gray-600 border border-gray-200 cursor-pointer hover:border-gray-300">
                Phổ biến
            </span>
            <span class="px-3 py-1 rounded-md text-[11px] font-medium bg-white text-gray-600 border border-gray-200 cursor-pointer hover:border-gray-300">
                Sắp hết hạn
            </span>
        </div>

        <!-- Scrollable Ticket Vouchers List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#f8fafc]" id="modal-vouchers-list">
            @if(isset($availableCoupons) && $availableCoupons->isNotEmpty())
                @foreach($availableCoupons as $cp)
                    @php
                        $isFreeship = str_contains(strtoupper($cp->code), 'FREESHIP') || str_contains(strtolower($cp->name), 'vận chuyển');
                        $isMall = str_contains(strtoupper($cp->code), 'MALL');
                        $isTech = str_contains(strtoupper($cp->code), 'TECH') || str_contains(strtoupper($cp->code), 'DIENTU');
                        $isFashion = str_contains(strtoupper($cp->code), 'FASHION');

                        $catType = 'shopmart';
                        if ($isFreeship) $catType = 'freeship';
                        elseif ($isTech || $isFashion) $catType = 'category';

                        $badgeColor = 'bg-rose-500';
                        $leftTitle = 'ShopMart';
                        $iconType = 'tag';
                        if ($isFreeship) {
                            $badgeColor = 'bg-[#00bfa5]';
                            $leftTitle = 'Mã vận chuyển';
                            $iconType = 'freeship';
                        } elseif ($isTech) {
                            $badgeColor = 'bg-[#e03a3c]';
                            $leftTitle = 'Điện Tử';
                            $iconType = 'tech';
                        } elseif ($isFashion) {
                            $badgeColor = 'bg-[#ea580c]';
                            $leftTitle = 'Thời Trang';
                            $iconType = 'fashion';
                        } elseif ($isMall) {
                            $badgeColor = 'bg-[#c51624]';
                            $leftTitle = 'Mall';
                            $iconType = 'mall';
                        }
                    @endphp

                    <!-- Authentic Shopee Ticket Stub Card -->
                    <div 
                        class="voucher-card relative flex bg-white rounded-xl border border-gray-200 hover:border-orange-400 shadow-xs overflow-hidden transition-all duration-200"
                        data-category="{{ $catType }}"
                        data-code="{{ $cp->code }}"
                    >
                        <!-- Left Coupon Stub (Colored with Icon) -->
                        <div class="w-28 sm:w-32 {{ $badgeColor }} text-white p-3 flex flex-col justify-between items-center text-center shrink-0 relative">
                            <!-- Limited quantity ribbon -->
                            <span class="text-[8px] font-extrabold uppercase tracking-tight bg-amber-400 text-amber-950 px-1.5 py-0.5 rounded-full absolute top-1.5 left-1.5 shadow-2xs">
                                Có hạn
                            </span>

                            <div class="my-auto pt-3">
                                @if($iconType === 'freeship')
                                    <span class="text-xs font-black tracking-tighter block leading-tight uppercase">FREE SHIP</span>
                                @elseif($iconType === 'mall')
                                    <span class="text-xs font-black tracking-tighter block leading-tight uppercase">MALL</span>
                                @elseif($iconType === 'tech')
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @elseif($iconType === 'fashion')
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                @else
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                @endif
                                <span class="text-[9px] font-bold uppercase tracking-wider block opacity-90 mt-1">{{ $leftTitle }}</span>
                            </div>

                            <span class="text-[8px] opacity-75">Tất cả hình thức</span>
                        </div>

                        <!-- Ticket Perforated Cutout Separator (Circles top & bottom) -->
                        <div class="relative w-0 flex flex-col justify-between items-center z-10">
                            <div class="w-3 h-3 bg-[#f8fafc] rounded-full -mt-1.5 -ml-1.5 border-b border-gray-200"></div>
                            <div class="h-full border-r border-dashed border-gray-300 my-1"></div>
                            <div class="w-3 h-3 bg-[#f8fafc] rounded-full -mb-1.5 -ml-1.5 border-t border-gray-200"></div>
                        </div>

                        <!-- Right Coupon Details -->
                        <div class="flex-1 p-3 pl-4 flex flex-col justify-between min-w-0">
                            <div>
                                <div class="flex items-start justify-between gap-1">
                                    <h4 class="text-xs font-black text-gray-900 leading-snug">
                                        @if($cp->discount_type === 'percent')
                                            Giảm {{ (int)$cp->discount_value }}% (Tối đa ₫{{ number_format($cp->max_discount_amount ?? 50000, 0, ',', '.') }})
                                        @else
                                            Giảm ₫{{ number_format($cp->discount_value, 0, ',', '.') }}
                                        @endif
                                    </h4>
                                    <span class="text-[10px] font-extrabold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded shrink-0">
                                        {{ $cp->code }}
                                    </span>
                                </div>

                                <p class="text-[10px] text-gray-500 mt-1">
                                    Đơn tối thiểu ₫{{ number_format($cp->min_order_value, 0, ',', '.') }}
                                </p>

                                <!-- Tag & Expiry -->
                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    <span class="text-[9px] font-bold text-red-700 bg-red-50 border border-red-200 px-1.5 py-0.2 rounded">
                                        Chỉ có trên ShopMart
                                    </span>
                                    <span class="text-[9px] text-gray-400">
                                        HSD: {{ $cp->expires_at ? $cp->expires_at->format('d.m.Y') : 'Vô thời hạn' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Bottom Action Row: Progress Bar & Button -->
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                                <div class="text-[9px] text-gray-400">
                                    <span>Đã dùng {{ $cp->used_count ?? 12 }}%</span>
                                    <div class="w-16 h-1 bg-gray-100 rounded-full mt-0.5 overflow-hidden">
                                        <div class="h-full bg-orange-500 rounded-full" style="width: {{ min(100, max(15, (int)($cp->used_count ?? 25))) }}%"></div>
                                    </div>
                                </div>

                                <button 
                                    type="button" 
                                    class="btn-modal-select-coupon px-3.5 py-1.5 border border-[#ee4d2d] hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                    data-code="{{ $cp->code }}"
                                    onclick="submitCoupon('{{ $cp->code }}', true)"
                                >
                                    Dùng ngay
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center text-gray-400 text-xs">
                    Hiện chưa có mã giảm giá nào đang khả dụng.
                </div>
            @endif
        </div>

        <!-- Sticky Modal Footer matching Shopee -->
        <div class="p-3.5 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <span class="text-xs text-gray-500 block">Voucher đã chọn:</span>
                <span class="text-xs font-black text-gray-900" id="modal-selected-code-display">Chưa chọn voucher</span>
            </div>
            <button 
                type="button" 
                id="btn-agree-voucher" 
                onclick="closeShopeeModal()"
                class="px-6 py-2.5 bg-[#ee4d2d] hover:bg-[#d03b1d] text-white text-xs font-bold rounded-xl shadow-md shadow-orange-500/20 transition-all cursor-pointer"
            >
                Đồng ý
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
window.openShopeeModal = function() {
    const modal = document.getElementById('shopee-voucher-modal');
    if (modal) modal.classList.remove('hidden');
};

window.closeShopeeModal = function() {
    const modal = document.getElementById('shopee-voucher-modal');
    if (modal) modal.classList.add('hidden');
};

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const currentSubtotal = {{ (float) $subtotal }};

    // 1. Payment Method Switcher
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            paymentOptions.forEach(o => {
                o.classList.remove('border-[#ea384c]', 'bg-rose-50/40');
                o.classList.add('border-gray-200', 'bg-white');
                const dot = o.querySelector('.payment-check-dot');
                if (dot) {
                    dot.classList.remove('border-[#ea384c]', 'bg-[#ea384c]');
                    dot.classList.add('border-gray-300', 'bg-transparent');
                    const inner = dot.querySelector('span');
                    if (inner) inner.classList.add('hidden');
                }
            });

            opt.classList.remove('border-gray-200', 'bg-white');
            opt.classList.add('border-[#ea384c]', 'bg-rose-50/40');
            const dot = opt.querySelector('.payment-check-dot');
            if (dot) {
                dot.classList.remove('border-gray-300', 'bg-transparent');
                dot.classList.add('border-[#ea384c]', 'bg-[#ea384c]');
                const inner = dot.querySelector('span');
                if (inner) inner.classList.remove('hidden');
            }

            const radio = opt.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // 2. Shopee Voucher System & AJAX Validation
    const couponInput = document.getElementById('coupon-input');
    const applyBtn = document.getElementById('btn-apply-coupon');
    const couponMsg = document.getElementById('coupon-message');
    const discountRow = document.getElementById('discount-row');
    const summaryDiscount = document.getElementById('summary-discount');
    const summaryGrandTotal = document.getElementById('summary-grand-total');
    const hiddenCouponCode = document.getElementById('hidden-coupon-code');

    const voucherModal = document.getElementById('shopee-voucher-modal');
    const openVoucherModalBtn = document.getElementById('btn-open-voucher-modal');
    const closeVoucherModalBtn = document.getElementById('btn-close-voucher-modal');
    const agreeVoucherBtn = document.getElementById('btn-agree-voucher');
    const modalVoucherInput = document.getElementById('modal-voucher-input');
    const modalApplyBtn = document.getElementById('btn-modal-apply');
    const modalCouponMsg = document.getElementById('modal-coupon-message');
    const voucherStatusText = document.getElementById('voucher-status-text');
    const voucherAppliedPill = document.getElementById('voucher-applied-pill');
    const modalSelectedDisplay = document.getElementById('modal-selected-code-display');

    async function submitCoupon(code, isFromModal = false) {
        if (!code) {
            const msgTarget = isFromModal ? modalCouponMsg : couponMsg;
            if (msgTarget) {
                msgTarget.classList.remove('hidden');
                msgTarget.className = 'text-xs mt-2 text-rose-600 font-bold';
                msgTarget.textContent = 'Vui lòng nhập mã giảm giá.';
            }
            return;
        }

        const activeBtn = isFromModal ? modalApplyBtn : applyBtn;
        if (activeBtn) {
            activeBtn.disabled = true;
            activeBtn.textContent = '...';
        }

        try {
            const res = await fetch('{{ route("checkout.apply-coupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    code: code,
                    subtotal: currentSubtotal
                })
            });

            const data = await res.json();
            const msgEl = isFromModal ? modalCouponMsg : couponMsg;

            if (msgEl) {
                msgEl.classList.remove('hidden');
            }

            if (data.success) {
                // Success message
                if (msgEl) {
                    msgEl.className = 'text-xs mt-2 text-emerald-600 font-bold';
                    msgEl.textContent = `${data.message} (${data.coupon_name})`;
                }

                // Update summary calculations
                discountRow.classList.remove('hidden');
                summaryDiscount.textContent = data.formatted_discount;
                summaryGrandTotal.textContent = data.formatted_new_total;
                hiddenCouponCode.value = data.coupon_code;

                // Sync inputs
                if (couponInput) couponInput.value = data.coupon_code;
                if (modalVoucherInput) modalVoucherInput.value = data.coupon_code;

                // Update Shopee voucher bar text
                if (voucherStatusText) {
                    voucherStatusText.textContent = `Đã áp dụng mã: ${data.coupon_code} (${data.formatted_discount})`;
                    voucherStatusText.classList.add('text-emerald-700', 'font-bold');
                }
                if (voucherAppliedPill) {
                    voucherAppliedPill.textContent = data.coupon_code;
                    voucherAppliedPill.classList.remove('hidden');
                }
                if (modalSelectedDisplay) {
                    modalSelectedDisplay.textContent = `${data.coupon_code} (${data.formatted_discount})`;
                }

                // Highlight selected card in modal
                document.querySelectorAll('.voucher-card').forEach(card => {
                    const cardCode = card.getAttribute('data-code');
                    const btn = card.querySelector('.btn-modal-select-coupon');
                    if (cardCode === data.coupon_code) {
                        card.classList.add('border-orange-500', 'ring-2', 'ring-orange-400/40');
                        if (btn) {
                            btn.textContent = 'Đã chọn';
                            btn.className = 'btn-modal-select-coupon px-3.5 py-1.5 bg-[#ee4d2d] text-white rounded-lg text-xs font-bold transition-colors cursor-pointer';
                        }
                    } else {
                        card.classList.remove('border-orange-500', 'ring-2', 'ring-orange-400/40');
                        if (btn) {
                            btn.textContent = 'Dùng ngay';
                            btn.className = 'btn-modal-select-coupon px-3.5 py-1.5 border border-[#ee4d2d] hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white rounded-lg text-xs font-bold transition-colors cursor-pointer';
                        }
                    }
                });

                if (isFromModal) {
                    // Close modal smoothly after feedback
                    setTimeout(() => {
                        if (voucherModal) voucherModal.classList.add('hidden');
                    }, 400);
                }
            } else {
                if (msgEl) {
                    msgEl.className = 'text-xs mt-2 text-rose-600 font-bold';
                    msgEl.textContent = data.message || 'Mã không hợp lệ';
                }
            }
        } catch (e) {
            console.error(e);
        } finally {
            if (activeBtn) {
                activeBtn.disabled = false;
                activeBtn.textContent = 'Áp dụng';
            }
        }
    }

    // Prevent Enter key from submitting checkout form
    if (couponInput) {
        couponInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitCoupon(couponInput.value.trim(), false);
            }
        });
    }

    if (modalVoucherInput) {
        modalVoucherInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitCoupon(modalVoucherInput.value.trim(), true);
            }
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', (e) => {
            e.preventDefault();
            submitCoupon(couponInput.value.trim(), false);
        });
    }

    if (modalApplyBtn) {
        modalApplyBtn.addEventListener('click', (e) => {
            e.preventDefault();
            submitCoupon(modalVoucherInput.value.trim(), true);
        });
    }

    // Open & Close Shopee Voucher Modal
    if (openVoucherModalBtn && voucherModal) {
        openVoucherModalBtn.addEventListener('click', () => {
            voucherModal.classList.remove('hidden');
        });
    }

    if (closeVoucherModalBtn && voucherModal) {
        closeVoucherModalBtn.addEventListener('click', () => {
            voucherModal.classList.add('hidden');
        });
    }

    if (agreeVoucherBtn && voucherModal) {
        agreeVoucherBtn.addEventListener('click', () => {
            voucherModal.classList.add('hidden');
        });
    }

    // Modal select button click
    document.querySelectorAll('.btn-modal-select-coupon').forEach(b => {
        b.addEventListener('click', (e) => {
            e.preventDefault();
            const code = b.getAttribute('data-code');
            submitCoupon(code, true);
        });
    });

    // Modal Category Tabs Filter
    const tabButtons = document.querySelectorAll('.voucher-tab-btn');
    const voucherCards = document.querySelectorAll('.voucher-card');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(t => {
                t.classList.remove('font-bold', 'text-[#ee4d2d]', 'border-[#ee4d2d]');
                t.classList.add('font-semibold', 'text-gray-500', 'border-transparent');
            });

            btn.classList.remove('font-semibold', 'text-gray-500', 'border-transparent');
            btn.classList.add('font-bold', 'text-[#ee4d2d]', 'border-[#ee4d2d]');

            const category = btn.getAttribute('data-cat');
            voucherCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });

    // 3. Address Modal Interactions
    const addressModal = document.getElementById('address-modal');
    const openAddrBtn = document.getElementById('btn-open-address-modal');
    const closeAddrBtn = document.getElementById('btn-close-address-modal');

    if (openAddrBtn && addressModal) {
        openAddrBtn.addEventListener('click', () => addressModal.classList.remove('hidden'));
        closeAddrBtn.addEventListener('click', () => addressModal.classList.add('hidden'));
    }

    // Select existing address from list
    document.querySelectorAll('.address-item-option').forEach(item => {
        item.addEventListener('click', () => {
            const name = item.getAttribute('data-name');
            const phone = item.getAttribute('data-phone');
            const addr = item.getAttribute('data-address');

            document.getElementById('display-recipient-name').textContent = name;
            document.getElementById('display-recipient-phone').textContent = phone;
            document.getElementById('display-recipient-address').textContent = addr;

            document.getElementById('input-recipient-name').value = name;
            document.getElementById('input-recipient-phone').value = phone;
            document.getElementById('input-recipient-address').value = addr;

            addressModal.classList.add('hidden');
        });
    });

    // Quick Add New Address via AJAX
    const saveNewAddrBtn = document.getElementById('btn-save-new-address');
    if (saveNewAddrBtn) {
        saveNewAddrBtn.addEventListener('click', async () => {
            const name = document.getElementById('modal-name-input').value.trim();
            const phone = document.getElementById('modal-phone-input').value.trim();
            const address = document.getElementById('modal-address-input').value.trim();

            if (!name || !phone || !address) {
                alert('Vui lòng điền đầy đủ tên, số điện thoại và địa chỉ nhận hàng.');
                return;
            }

            try {
                const res = await fetch('{{ route("checkout.quick-address") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient_name: name,
                        phone: phone,
                        address_line: address,
                        is_default: true
                    })
                });

                const data = await res.json();
                if (data.success) {
                    document.getElementById('display-recipient-name').textContent = name;
                    document.getElementById('display-recipient-phone').textContent = phone;
                    document.getElementById('display-recipient-address').textContent = address;

                    document.getElementById('input-recipient-name').value = name;
                    document.getElementById('input-recipient-phone').value = phone;
                    document.getElementById('input-recipient-address').value = address;

                    addressModal.classList.add('hidden');
                } else {
                    alert(data.message || 'Lỗi khi thêm địa chỉ');
                }
            } catch (err) {
                console.error(err);
            }
        });
    }

    // Expose submitCoupon to window
    window.submitCoupon = submitCoupon;

    // Auto-apply coupon from URL query param (e.g. from Voucher Portal)
    const urlParams = new URLSearchParams(window.location.search);
    const couponFromUrl = urlParams.get('coupon');
    if (couponFromUrl) {
        if (couponInput) couponInput.value = couponFromUrl;
        if (modalVoucherInput) modalVoucherInput.value = couponFromUrl;
        submitCoupon(couponFromUrl, false);
    }
});
</script>
@endpush
