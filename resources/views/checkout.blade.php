@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng - ShopMart')

@section('content')
<div class="checkout-container py-4 sm:py-6">
    
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-primary">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('cart') }}" class="hover:text-primary">Giỏ hàng</a>
        <span>/</span>
        <span class="text-gray-900 font-bold">Thanh toán</span>
    </nav>

    <!-- Main Checkout Grid -->
    <form action="{{ route('checkout.order') }}" method="POST" id="checkout-form">
        @csrf
        @if(!empty($isBuyNow))
            <input type="hidden" name="buy_now" value="1">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Unified Single Continuous Block (8 cols) -->
            <div class="lg:col-span-8 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs divide-y divide-gray-200">

                <!-- 1. Top Section: Shipping Address with Airmail Envelope Stripe -->
                <div class="relative bg-white p-5">
                    <!-- Airmail letter border line (Screenshot 3) -->
                    <div class="airmail-stripe absolute top-0 left-0 right-0"></div>

                    <div class="flex items-center justify-between mb-3 pt-1">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-rose-50 text-primary flex items-center justify-center">
                                <x-icon name="map-pin" class="w-3.5 h-3.5 text-primary" />
                            </div>
                            <h2 class="text-sm font-bold text-gray-900">Địa chỉ nhận hàng</h2>
                        </div>

                        @auth
                            <button type="button" onclick="window.AddressModalManager?.openSelectorModal()" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline cursor-pointer">
                                Thay đổi
                            </button>
                        @endauth
                    </div>

                    <!-- Address Display or Empty State -->
                    <div id="checkout-address-display" class="{{ $defaultAddress ? '' : 'hidden' }}">
                        <div class="bg-gray-50/70 rounded-xl p-3.5 border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs sm:text-sm font-bold text-gray-900" id="display-recipient-name">
                                        {{ $defaultAddress->recipient_name ?? '' }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium font-mono" id="display-recipient-phone">
                                        {{ $defaultAddress ? '(' . $defaultAddress->phone . ')' : '' }}
                                    </span>
                                    <span id="display-badge-default" class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200 {{ ($defaultAddress && $defaultAddress->is_default) ? '' : 'hidden' }}">Mặc định</span>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed" id="display-recipient-address">
                                    {{ $defaultAddress->address_line ?? '' }}
                                </p>
                            </div>
                            <button type="button" onclick="window.AddressModalManager?.openSelectorModal()" class="text-xs font-semibold text-primary hover:underline cursor-pointer shrink-0 self-start sm:self-center">
                                Thay đổi
                            </button>
                        </div>
                    </div>

                    <!-- Empty State when no address exists -->
                    <div id="checkout-address-empty" class="{{ $defaultAddress ? 'hidden' : '' }}">
                        <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <x-icon name="map-pin" class="w-4 h-4 text-amber-600" />
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Bạn chưa có địa chỉ nhận hàng</h4>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Vui lòng thêm địa chỉ nhận hàng để tiếp tục đặt hàng.</p>
                                </div>
                            </div>
                            <button type="button" onclick="window.AddressModalManager?.openCreateModal()" class="px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg transition-colors cursor-pointer shrink-0 shadow-xs">
                                + Thêm địa chỉ nhận hàng
                            </button>
                        </div>
                    </div>

                    <!-- Hidden inputs sent to form -->
                    <input type="hidden" name="recipient_name" id="input-recipient-name" value="{{ $defaultAddress->recipient_name ?? '' }}">
                    <input type="hidden" name="phone" id="input-recipient-phone" value="{{ $defaultAddress->phone ?? '' }}">
                    <input type="hidden" name="address_line" id="input-recipient-address" value="{{ $defaultAddress->address_line ?? '' }}">
                    <input type="hidden" name="address_id" id="input-recipient-id" value="{{ $defaultAddress->id ?? '' }}">
                </div>

                <!-- 2. Ordered Products Section (Shopee Table Layout matching Screenshot 1) -->
                <div class="bg-white">
                    <!-- Products Table Header (Shopee Style) -->
                    <div class="px-5 py-3 bg-gray-50/80 border-b border-gray-100 hidden sm:flex items-center text-xs text-gray-500 font-medium">
                        <div class="flex-1">Sản phẩm</div>
                        <div class="w-28 text-center">Đơn giá</div>
                        <div class="w-24 text-center">Số lượng</div>
                        <div class="w-32 text-right">Thành tiền</div>
                    </div>

                    @php
                        // Group items by store for authentic marketplace layout
                        $itemsByStore = $selectedItems->groupBy(function($item) {
                            return $item->product->store_id ?? 0;
                        });
                    @endphp

                    @foreach($itemsByStore as $storeId => $storeItems)
                        @php
                            $store = $storeItems->first()?->product?->store;
                            $storeName = $store->name ?? 'ShopMart Mall';
                        @endphp
                        <div class="border-b border-gray-100 last:border-b-0">
                            <input type="hidden" name="shop_voucher_codes[{{ $storeId }}]" id="shop-voucher-input-{{ $storeId }}" value="">
                            <!-- Shop Header (Screenshot 1: [Yêu thích+] Store Name | Chat ngay) -->
                            <div class="px-5 py-3 flex items-center gap-2 border-b border-gray-50 bg-white">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-500 text-white shadow-2xs">Yêu thích+</span>
                                <span class="text-xs font-bold text-gray-900">{{ $storeName }}</span>
                                <span class="text-gray-300">|</span>
                                <a href="javascript:void(0)" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span>Chat ngay</span>
                                </a>
                            </div>

                            <!-- Products in this store -->
                            <div class="divide-y divide-gray-50 px-5">
                                @foreach($storeItems as $item)
                                    <div class="py-3 flex flex-col sm:flex-row sm:items-center gap-3">
                                        <!-- Product image & info -->
                                        <div class="flex-1 flex items-center gap-3 min-w-0">
                                            <img src="{{ $item->product->main_image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $item->product->name }}" class="w-14 h-14 object-cover rounded-lg border border-gray-100 shrink-0">
                                            <div class="min-w-0">
                                                <h3 class="text-xs font-bold text-gray-900 truncate" title="{{ $item->product->name }}">{{ $item->product->name }}</h3>
                                                @if($item->selected_variant)
                                                    <p class="text-[11px] text-gray-400 mt-0.5">Loại: <span class="text-gray-600 font-medium">{{ $item->selected_variant }}</span></p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Price columns on desktop -->
                                        <div class="flex items-center justify-between sm:justify-end gap-4 shrink-0 text-xs">
                                            <div class="sm:w-28 sm:text-center text-gray-600 font-medium">
                                                {{ number_format((float) $item->unit_price, 0, ',', '.') }}₫
                                            </div>
                                            <div class="sm:w-24 sm:text-center text-gray-500">
                                                {{ $item->quantity }}
                                            </div>
                                            <div class="sm:w-32 sm:text-right font-bold text-gray-900">
                                                {{ number_format((float) ($item->subtotal ?? $item->unit_price * $item->quantity), 0, ',', '.') }}₫
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Voucher của Shop row (RIÊNG BIỆT với Voucher Sàn) -->
                            <div class="px-5 py-3 border-t border-gray-50 bg-rose-50/20 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-primary font-bold text-xs flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        <span>Voucher của Shop</span>
                                    </span>
                                    <span id="shop-voucher-badge" class="hidden text-[10px] font-bold text-orange-700 bg-orange-100 px-2 py-0.5 rounded border border-orange-200"></span>
                                </div>
                                <button 
                                    type="button" 
                                    onclick="openShopVoucherModal('{{ $storeName }}')" 
                                    class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline cursor-pointer"
                                >
                                    Chọn Voucher
                                </button>
                            </div>

                            <!-- Lời nhắn & Phương thức vận chuyển (Screenshot 1) -->
                            <div class="grid grid-cols-1 md:grid-cols-12 border-t border-gray-100 bg-gray-50/40 text-xs divide-y md:divide-y-0 md:divide-x divide-gray-100">
                                <!-- Left: Lời nhắn cho người bán -->
                                <div class="md:col-span-5 p-4 flex items-center gap-3">
                                    <span class="text-gray-600 shrink-0 font-medium">Lời nhắn:</span>
                                    <input 
                                        type="text" 
                                        name="notes" 
                                        placeholder="Lưu ý cho Người bán..." 
                                        class="flex-1 px-3 py-1.5 bg-white border border-gray-200 rounded-md text-xs text-gray-800 focus:border-primary focus:outline-hidden"
                                    >
                                </div>

                                <!-- Right: Phương thức vận chuyển -->
                                <div class="md:col-span-7 p-4 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 font-medium text-gray-800">
                                            <span class="text-gray-500">Phương thức vận chuyển:</span>
                                            <span class="font-bold text-gray-900">Nhanh</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-semibold text-blue-600 hover:underline cursor-pointer">Thay Đổi</span>
                                            <span class="font-bold text-gray-900" id="store-shipping-fee-display">
                                                @if($shippingFee == 0)
                                                    <span class="text-emerald-600 font-extrabold">0₫</span>
                                                @else
                                                    {{ number_format($shippingFee, 0, ',', '.') }}₫
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-500">Nhận hàng dự kiến: {{ now()->addDays(2)->format('d \T\h\g m') }} - {{ now()->addDays(3)->format('d \T\h\g m') }}</p>
                                    <p class="text-[11px] text-gray-400">Nhận tối đa 15.000₫ nếu đơn hàng giao trễ</p>
                                    <div class="pt-1.5 border-t border-gray-100 flex items-center gap-1 text-[11px] text-emerald-700 font-medium">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Được đồng kiểm.</span>
                                        <svg class="w-3 h-3 text-gray-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Subtotal Row (Screenshot 1: Tổng số tiền (X sản phẩm): XXX.XXX₫) -->
                            <div class="px-5 py-3.5 bg-gray-50/80 border-t border-gray-100 flex items-center justify-end gap-2 text-xs">
                                <span class="text-gray-500">Tổng số tiền ({{ $storeItems->count() }} sản phẩm):</span>
                                <span class="text-base font-black text-primary">
                                    {{ number_format((float) $storeItems->sum(fn($i) => $i->subtotal ?? ($i->unit_price * $i->quantity)), 0, ',', '.') }}₫
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. Shopee Voucher & Xu Rows (RIÊNG BIỆT VỚI VOUCHER SHOP) -->
                <div class="bg-white divide-y divide-gray-100">
                    <!-- Row 1: Shopee Voucher / ShopMart Voucher (Voucher Toàn Sàn) -->
                    <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50/60 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-gray-800 block">ShopMart Voucher (Toàn Sàn)</span>
                                <span class="text-[11px] text-gray-400" id="platform-voucher-status">Mã miễn phí vận chuyển & mã giảm giá toàn sàn</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span id="freeship-voucher-badge" class="hidden text-[10px] font-bold text-teal-700 bg-teal-100 px-2 py-0.5 rounded border border-teal-200"></span>
                            <span id="platform-voucher-badge" class="hidden text-[10px] font-bold text-orange-700 bg-orange-100 px-2 py-0.5 rounded border border-orange-200"></span>
                            <button 
                                type="button" 
                                onclick="openShopeeModal()" 
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline cursor-pointer"
                            >
                                Chọn Voucher
                            </button>
                        </div>
                    </div>

                    <!-- Row 2: Shopee Xu / ShopMart Xu -->
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-gray-800 block">ShopMart Xu</span>
                                <span class="text-[11px] text-gray-400">Dùng 50.000 ShopMart Xu (giảm 50.000₫)</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <span class="text-xs font-bold text-gray-400" id="xu-discount-text">[-0₫]</span>
                            <input 
                                type="checkbox" 
                                id="checkout-points-toggle" 
                                name="use_points" 
                                value="1" 
                                class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary cursor-pointer"
                            >
                        </div>
                    </div>
                </div>

                <!-- 4. Bottom Section: Payment Methods (Compact List) -->
                <div class="bg-white p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h2 class="text-sm font-bold text-gray-900">Phương thức thanh toán</h2>
                    </div>

                    <div id="payment-methods-wrapper" class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                        <!-- COD -->
                        <label class="payment-list-option is-selected flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="cod" checked class="sr-only">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-gray-900 block">Thanh toán khi nhận hàng (COD)</span>
                                <span class="text-[10px] text-gray-400">Kiểm tra hàng trước khi trả tiền mặt</span>
                            </div>
                            <span class="payment-list-dot w-4 h-4 rounded-full border-2 border-primary bg-primary flex items-center justify-center shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            </span>
                        </label>

                        <!-- VNPay -->
                        <label class="payment-list-option flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="vnpay" class="sr-only">
                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-blue-700">VNPay</span>
                                    <span class="px-1 py-0.2 rounded text-[9px] font-extrabold bg-blue-100 text-blue-800">QR / Thẻ</span>
                                </div>
                                <span class="text-[10px] text-gray-400">ATM, Visa/Master, VNPAY-QR</span>
                            </div>
                            <span class="payment-list-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                            </span>
                        </label>

                        <!-- MoMo -->
                        <label class="payment-list-option flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="momo" class="sr-only">
                            <span class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-gray-900 block">Ví MoMo</span>
                                <span class="text-[10px] text-gray-400">Thanh toán qua ứng dụng MoMo</span>
                            </div>
                            <span class="payment-list-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                            </span>
                        </label>

                        <!-- ZaloPay -->
                        <label class="payment-list-option flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="zalopay" class="sr-only">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-gray-900 block">ZaloPay</span>
                                <span class="text-[10px] text-gray-400">Thanh toán qua ví ZaloPay</span>
                            </div>
                            <span class="payment-list-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                            </span>
                        </label>

                        <!-- Bank Transfer -->
                        <label class="payment-list-option flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only">
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-gray-900 block">Chuyển khoản ngân hàng</span>
                                <span class="text-[10px] text-gray-400">Vietcombank, BIDV, Techcombank...</span>
                            </div>
                            <span class="payment-list-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                            </span>
                        </label>

                        <!-- ShopMart Wallet -->
                        <label class="payment-list-option flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" name="payment_method" value="wallet" class="sr-only">
                            <span class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-gray-900 block">Ví ShopMart</span>
                                <span class="text-[10px] text-gray-400">Số dư: 2.000.000₫ • Thanh toán 1 chạm</span>
                            </div>
                            <span class="payment-list-dot w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-white hidden"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Right Column: Sticky Payment Breakdown (4 cols) -->
            <div class="lg:col-span-4 space-y-4">

                <!-- Hidden inputs to submit all voucher data -->
                <input type="hidden" name="coupon_code" id="hidden-coupon-code" value="">
                <input type="hidden" name="freeship_code" id="hidden-freeship-code" value="">
                <input type="hidden" name="shop_voucher_code" id="hidden-shop-code" value="">
                <input type="hidden" name="platform_voucher_code" id="hidden-platform-code" value="">

                <!-- Cost Breakdown & Grand Total Sticky Box -->
                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-xs sticky top-20">
                    <h3 class="text-sm font-bold text-gray-900 pb-3 border-b border-gray-100 mb-3">Chi tiết thanh toán</h3>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between text-gray-600">
                            <span>Tạm tính tiền hàng:</span>
                            <span class="font-bold text-gray-900" id="summary-subtotal">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600">
                            <span>Phí vận chuyển:</span>
                            <span class="font-bold text-gray-900" id="summary-shipping">
                                @if($shippingFee == 0)
                                    <span class="text-emerald-600 font-extrabold">MIỄN PHÍ</span>
                                @else
                                    {{ number_format($shippingFee, 0, ',', '.') }}₫
                                @endif
                            </span>
                        </div>

                        <!-- Freeship discount row -->
                        <div class="flex items-center justify-between text-teal-600 font-medium hidden" id="freeship-discount-row">
                            <span>Giảm phí vận chuyển:</span>
                            <span class="font-bold" id="summary-freeship-discount">-0₫</span>
                        </div>

                        <!-- Shop discount row -->
                        <div class="flex items-center justify-between text-rose-600 font-medium hidden" id="shop-discount-row">
                            <span>Voucher của Shop:</span>
                            <span class="font-bold" id="summary-shop-discount">-0₫</span>
                        </div>

                        <!-- Platform discount row -->
                        <div class="flex items-center justify-between text-orange-600 font-medium hidden" id="platform-discount-row">
                            <span>ShopMart Voucher:</span>
                            <span class="font-bold" id="summary-platform-discount">-0₫</span>
                        </div>

                        <!-- Xu discount row -->
                        <div class="flex items-center justify-between text-amber-600 font-medium hidden" id="xu-discount-row">
                            <span>ShopMart Xu:</span>
                            <span class="font-bold" id="summary-xu-discount">-0₫</span>
                        </div>

                        <!-- Grand Total -->
                        <div class="pt-3 border-t border-gray-100 flex items-baseline justify-between">
                            <div>
                                <span class="text-sm font-black text-gray-900 block">Tổng thanh toán:</span>
                                <span class="text-[10px] text-gray-400">(Đã bao gồm VAT & phí)</span>
                            </div>
                            <span class="text-xl font-black text-primary" id="summary-grand-total">
                                {{ number_format($total, 0, ',', '.') }}₫
                            </span>
                        </div>
                    </div>

                    <!-- Place Order CTA Button -->
                    <button 
                        type="submit" 
                        id="btn-submit-order" 
                        class="w-full mt-5 py-3 bg-gradient-to-r from-primary to-rose-500 hover:from-primary-hover hover:to-primary text-white text-sm font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98"
                    >
                        <span>Đặt hàng</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>

                    <div class="mt-3 flex items-center justify-center gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Bảo mật SSL</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Đổi trả 15 ngày</span>
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<!-- ==================== ADDRESS SELECTION MODAL (SHOPEE-LIKE UX) ==================== -->
<div id="address-selector-modal" class="modal-backdrop hidden" style="z-index: 200;">
    <div class="modal-dialog max-w-xl w-full max-h-[90vh] flex flex-col bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        
        <!-- Header -->
        <div class="px-5 py-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-rose-50 text-primary flex items-center justify-center shrink-0">
                    <x-icon name="map-pin" class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Địa chỉ nhận hàng</h3>
                    <p class="text-[11px] text-gray-400">Chọn địa chỉ giao hàng có sẵn hoặc thêm mới</p>
                </div>
            </div>
            <button type="button" onclick="window.AddressModalManager?.closeSelectorModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors cursor-pointer" aria-label="Đóng">
                <x-icon name="close" class="w-4 h-4" />
            </button>
        </div>

        <!-- Address List Body -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 bg-gray-50/50" id="address-selector-list">
            @forelse($addresses ?? collect() as $addr)
                @php
                    $isSelected = ($defaultAddress && $defaultAddress->id === $addr->id);
                @endphp
                <div class="address-select-card p-3.5 bg-white rounded-xl border-2 transition-all cursor-pointer group {{ $isSelected ? 'is-selected' : '' }}"
                     data-id="{{ $addr->id }}"
                     data-name="{{ $addr->recipient_name }}"
                     data-phone="{{ $addr->phone }}"
                     data-address="{{ $addr->address_line }}"
                     data-default="{{ $addr->is_default ? '1' : '0' }}"
                     onclick="window.AddressModalManager?.selectItem(this)">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0 mt-0.5 address-radio-circle">
                                <span class="w-2 h-2 rounded-full bg-primary {{ $isSelected ? '' : 'hidden' }} address-radio-dot"></span>
                            </div>
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs sm:text-sm font-bold text-gray-900 addr-item-name">{{ $addr->recipient_name }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-xs text-gray-600 font-medium font-mono addr-item-phone">{{ $addr->phone }}</span>
                                    @if($addr->is_default)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Mặc định</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed addr-item-address">
                                    {{ $addr->address_line }}
                                </p>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            onclick="event.stopPropagation(); window.AddressModalManager?.openEditModal(@json($addr))" 
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline shrink-0 pt-0.5 cursor-pointer"
                        >
                            Sửa
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-white rounded-xl border border-dashed border-gray-200 p-6">
                    <p class="text-xs text-gray-500">Chưa có địa chỉ nào được lưu.</p>
                </div>
            @endforelse
        </div>

        <!-- Add Address Button in selector modal -->
        <div class="px-4 sm:px-5 py-2.5 bg-white border-t border-gray-100 shrink-0">
            <button 
                type="button" 
                onclick="window.AddressModalManager?.openCreateModal()" 
                class="w-full py-2.5 px-3 border border-dashed border-gray-300 hover:border-primary text-gray-700 hover:text-primary rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 bg-gray-50/50 hover:bg-rose-50/20 cursor-pointer"
            >
                <x-icon name="plus" class="w-4 h-4 text-primary" />
                <span>Thêm địa chỉ mới</span>
            </button>
        </div>

        <!-- Modal Footer: Hủy & Xác nhận -->
        <div class="px-5 py-3 bg-white border-t border-gray-100 flex items-center justify-end gap-3 shrink-0">
            <button 
                type="button" 
                onclick="window.AddressModalManager?.closeSelectorModal()" 
                class="px-5 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-100 text-xs font-semibold transition-colors cursor-pointer"
            >
                Hủy
            </button>
            <button 
                type="button" 
                onclick="window.AddressModalManager?.confirmSelection()" 
                class="px-6 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
            >
                Xác nhận
            </button>
        </div>

    </div>
</div>

<!-- Reusable Address Form Modal (Shared with Profile) -->
<x-address-form-modal id="address-form-modal" />

<!-- ==================== 1. MODAL RIÊNG BIỆT: VOUCHER CỦA SHOP ==================== -->
<div id="shop-voucher-modal" class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-xl border border-gray-100 overflow-hidden">
        
        <!-- Modal Top Header -->
        <div class="h-12 px-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeShopVoucherModal()" class="text-gray-500 hover:text-gray-800 p-1 rounded-md hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Voucher của Shop</h3>
                    <span class="text-[10px] text-gray-400 block -mt-0.5" id="shop-modal-store-name">Ưu đãi độc quyền từ Shop</span>
                </div>
            </div>
            <span class="text-[10px] font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded border border-orange-200">Shop Voucher</span>
        </div>

        <!-- Voucher Input Form -->
        <div class="p-3 bg-gray-50 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    id="shop-modal-input" 
                    placeholder="Nhập mã voucher của shop..." 
                    class="flex-1 px-3 py-2 bg-white border border-orange-400 focus:border-primary rounded-lg text-xs font-bold uppercase tracking-wider text-gray-900 focus:outline-hidden"
                >
                <button 
                    type="button" 
                    onclick="applyManualShopCoupon()"
                    class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg shadow-xs transition-all cursor-pointer shrink-0"
                >
                    Áp dụng
                </button>
            </div>
            <p id="shop-modal-message" class="text-xs mt-1.5 px-1 hidden font-semibold"></p>
        </div>

        <!-- Shop Vouchers List (Only Shop Vouchers) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
            @forelse($shopCoupons ?? collect() as $cp)
                @include('checkout._voucher_card', ['cp' => $cp, 'isFreeship' => false, 'voucherType' => 'shop'])
            @empty
                <div class="p-8 bg-white rounded-lg border border-dashed border-gray-200 text-center text-xs text-gray-400">
                    Hiện chưa có mã giảm giá riêng của Shop này
                </div>
            @endforelse
        </div>

        <!-- Sticky Footer -->
        <div class="p-3 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <span class="text-[11px] text-gray-400 block">Voucher Shop đã chọn:</span>
                <div class="text-xs font-bold text-gray-900" id="shop-modal-selected">
                    <span class="text-gray-400 font-normal">Chưa chọn mã</span>
                </div>
            </div>
            <button 
                type="button" 
                onclick="closeShopVoucherModal()"
                class="px-5 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer"
            >
                Đồng ý
            </button>
        </div>

    </div>
</div>

<!-- ==================== 2. MODAL RIÊNG BIỆT: SHOPMART VOUCHER (TOÀN SÀN & FREESHIP) ==================== -->
<div id="shopee-voucher-modal" class="fixed inset-0 z-[200] bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-xl border border-gray-100 overflow-hidden">
        
        <!-- Modal Top Header -->
        <div class="h-12 px-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button type="button" id="btn-close-voucher-modal" onclick="closeShopeeModal()" class="text-gray-500 hover:text-gray-800 p-1 rounded-md hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">ShopMart Voucher (Toàn Sàn)</h3>
                    <span class="text-[10px] text-gray-400 block -mt-0.5">Mã Freeship & mã giảm toàn sàn</span>
                </div>
            </div>
            <span class="text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">Sàn ShopMart</span>
        </div>

        <!-- Voucher Input Form -->
        <div class="p-3 bg-gray-50 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    id="modal-voucher-input" 
                    placeholder="Nhập mã voucher toàn sàn..." 
                    class="flex-1 px-3 py-2 bg-white border border-orange-400 focus:border-primary rounded-lg text-xs font-bold uppercase tracking-wider text-gray-900 focus:outline-hidden"
                >
                <button 
                    type="button" 
                    id="btn-modal-apply" 
                    onclick="applyManualCoupon()"
                    class="px-4 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg shadow-xs transition-all cursor-pointer shrink-0"
                >
                    Áp dụng
                </button>
            </div>
            <p id="modal-coupon-message" class="text-xs mt-1.5 px-1 hidden font-semibold"></p>
        </div>

        <!-- Scrollable Ticket Vouchers List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50" id="modal-vouchers-list">
            
            <!-- SECTION 1: MÃ MIỄN PHÍ VẬN CHUYỂN (Freeship) -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span>Mã Miễn Phí Vận Chuyển</span>
                    </h4>
                    <span class="text-[10px] text-gray-400">Áp dụng tối đa 1 mã</span>
                </div>

                @php
                    $fsList = $freeshipCoupons ?? collect();
                    $initialFs = $fsList->take(2);
                    $moreFs = $fsList->slice(2);
                @endphp

                <!-- Initial 2 Freeship Vouchers -->
                <div class="space-y-2.5">
                    @forelse($initialFs as $cp)
                        @include('checkout._voucher_card', ['cp' => $cp, 'isFreeship' => true, 'voucherType' => 'freeship'])
                    @empty
                        <div class="p-3 bg-white rounded-lg border border-dashed border-gray-200 text-center text-xs text-gray-400">
                            Không có mã Freeship khả dụng
                        </div>
                    @endforelse
                </div>

                <!-- Expandable Freeship Vouchers List (Shown via "Xem thêm") -->
                @if($moreFs->isNotEmpty())
                    <div id="extra-freeship-list" class="space-y-2.5 hidden">
                        @foreach($moreFs as $cp)
                            @include('checkout._voucher_card', ['cp' => $cp, 'isFreeship' => true, 'voucherType' => 'freeship'])
                        @endforeach
                    </div>

                    <button 
                        type="button" 
                        id="btn-toggle-more-freeship" 
                        onclick="toggleMoreFreeship()" 
                        class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 py-1 cursor-pointer"
                    >
                        <span id="freeship-toggle-text">Xem thêm mã Miễn phí vận chuyển ({{ $moreFs->count() }})</span>
                        <svg id="freeship-toggle-icon" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                @endif
            </div>

            <!-- SECTION 2: MÃ GIẢM GIÁ TOÀN SÀN (CHỈ VOUCHER CỦA SÀN, KHÔNG CÓ SHOP) -->
            <div class="space-y-2.5 pt-2 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        <span>Mã Giảm Giá Toàn Sàn</span>
                    </h4>
                    <span class="text-[10px] text-gray-400">Áp dụng tối đa 1 mã toàn sàn</span>
                </div>

                @php
                    $platList = $platformCoupons ?? collect();
                @endphp

                <div class="space-y-2.5">
                    @forelse($platList as $cp)
                        @include('checkout._voucher_card', ['cp' => $cp, 'isFreeship' => false, 'voucherType' => 'platform'])
                    @empty
                        <div class="p-3 bg-white rounded-lg border border-dashed border-gray-200 text-center text-xs text-gray-400">
                            Không có mã giảm giá toàn sàn khả dụng
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Sticky Modal Footer matching Shopee -->
        <div class="p-3 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <span class="text-[11px] text-gray-400 block">Voucher Sàn đã chọn:</span>
                <div class="text-xs font-bold text-gray-900 flex flex-wrap items-center gap-1.5" id="modal-selected-summary">
                    <span class="text-gray-400 font-normal">Chưa chọn voucher</span>
                </div>
            </div>
            <button 
                type="button" 
                id="btn-agree-voucher" 
                onclick="closeShopeeModal()"
                class="px-5 py-2 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer"
            >
                Đồng ý
            </button>
        </div>

    </div>
</div>

{{-- ==================== ADDRESS SELECTOR MODAL (CHECKOUT) ==================== --}}
<div id="address-selector-modal" class="fixed inset-0 z-[220] bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[88vh] flex flex-col shadow-2xl border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="px-5 py-4 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-base font-bold text-gray-900">Địa chỉ nhận hàng</h3>
                <p class="text-xs text-gray-400 mt-0.5">Chọn địa chỉ giao hàng cho đơn này</p>
            </div>
            <button type="button" onclick="window.AddressModalManager?.closeSelectorModal()"
                class="text-gray-400 hover:text-gray-700 p-1.5 rounded-full hover:bg-gray-100 transition-colors cursor-pointer"
                aria-label="Đóng">
                <x-icon name="close" class="w-4 h-4" />
            </button>
        </div>

        {{-- Address List --}}
        <div id="address-selector-list" class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse(auth()->user()?->addresses ?? [] as $addr)
                <div class="address-select-card p-3.5 bg-white rounded-xl border-2 transition-all cursor-pointer group {{ $addr->is_default ? 'is-selected' : '' }}"
                     data-id="{{ $addr->id }}"
                     data-name="{{ $addr->recipient_name }}"
                     data-phone="{{ $addr->phone }}"
                     data-address="{{ $addr->address_line }}"
                     data-default="{{ $addr->is_default ? '1' : '0' }}"
                     onclick="window.AddressModalManager?.selectItem(this)">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 address-radio-circle border-gray-300">
                                <span class="w-2 h-2 rounded-full bg-primary address-radio-dot {{ $addr->is_default ? '' : 'hidden' }}"></span>
                            </div>
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs sm:text-sm font-bold text-gray-900">{{ $addr->recipient_name }}</span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-xs text-gray-600 font-medium font-mono">{{ $addr->phone }}</span>
                                    @if($addr->is_default)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Mặc định</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $addr->address_line }}</p>
                            </div>
                        </div>
                        <button type="button"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline shrink-0 pt-0.5 cursor-pointer"
                            onclick="event.stopPropagation(); window.AddressModalManager?.openEditModal({{ json_encode(['id'=>$addr->id,'recipient_name'=>$addr->recipient_name,'phone'=>$addr->phone,'address_line'=>$addr->address_line,'is_default'=>$addr->is_default]) }})">
                            Sửa
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-xs text-gray-400">
                    <x-icon name="map-pin" class="w-8 h-8 mx-auto text-gray-300 mb-2" />
                    Chưa có địa chỉ nào được lưu
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-5 py-4 bg-white border-t border-gray-100 flex items-center justify-between shrink-0">
            <button type="button" onclick="window.AddressModalManager?.openCreateModal()"
                class="text-xs font-semibold text-primary hover:text-rose-700 flex items-center gap-1.5 cursor-pointer">
                <x-icon name="plus" class="w-3.5 h-3.5" />
                Thêm địa chỉ mới
            </button>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.AddressModalManager?.closeSelectorModal()"
                    class="px-4 py-2 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-100 text-xs font-semibold transition-colors cursor-pointer">
                    Hủy
                </button>
                <button type="button" onclick="window.AddressModalManager?.confirmSelection()"
                    class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                    Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Shared Address Form Modal (Add / Edit) --}}
<x-address-form-modal id="address-form-modal" />

@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="{{ asset('js/address-manager.js') }}"></script>
<script>
// State holding active voucher selections
window.checkoutState = {
    freeshipCode: '',
    shopCode: '',
    platformCode: '',
    usePoints: false,
    currentSubtotal: {{ (float) $subtotal }},
    baseShippingFee: {{ (float) $shippingFee }}
};

// 1. Shop Voucher Modal Controls (RIÊNG BIỆT)
window.openShopVoucherModal = function(storeName) {
    const modal = document.getElementById('shop-voucher-modal');
    if (!modal) return;
    if (storeName) {
        const nameEl = document.getElementById('shop-modal-store-name');
        if (nameEl) nameEl.textContent = `Ưu đãi độc quyền từ ${storeName}`;
    }
    modal.classList.remove('hidden');
};

window.closeShopVoucherModal = function() {
    const modal = document.getElementById('shop-voucher-modal');
    if (modal) modal.classList.add('hidden');
};

// 2. Platform Voucher Modal Controls (RIÊNG BIỆT)
window.openShopeeModal = function() {
    const modal = document.getElementById('shopee-voucher-modal');
    if (modal) modal.classList.remove('hidden');
};

window.closeShopeeModal = function() {
    const modal = document.getElementById('shopee-voucher-modal');
    if (modal) modal.classList.add('hidden');
};

window.toggleMoreFreeship = function() {
    const extraList = document.getElementById('extra-freeship-list');
    const toggleText = document.getElementById('freeship-toggle-text');
    const toggleIcon = document.getElementById('freeship-toggle-icon');
    if (!extraList) return;

    if (extraList.classList.contains('hidden')) {
        extraList.classList.remove('hidden');
        if (toggleText) toggleText.textContent = 'Thu gọn';
        if (toggleIcon) toggleIcon.classList.add('rotate-180');
    } else {
        extraList.classList.add('hidden');
        if (toggleText) toggleText.textContent = `Xem thêm mã Miễn phí vận chuyển`;
        if (toggleIcon) toggleIcon.classList.remove('rotate-180');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Payment Method Switcher
    const paymentOptions = document.querySelectorAll('.payment-list-option');
    paymentOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            paymentOptions.forEach(o => {
                o.classList.remove('is-selected');
                const dot = o.querySelector('.payment-list-dot');
                if (dot) {
                    dot.classList.remove('border-primary', 'bg-primary');
                    dot.classList.add('border-gray-300');
                    const inner = dot.querySelector('span');
                    if (inner) inner.classList.add('hidden');
                }
            });
            opt.classList.add('is-selected');
            const radio = opt.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            const dot = opt.querySelector('.payment-list-dot');
            if (dot) {
                dot.classList.add('border-primary', 'bg-primary');
                dot.classList.remove('border-gray-300');
                const inner = dot.querySelector('span');
                if (inner) inner.classList.remove('hidden');
            }
        });
    });

    // Shopee Xu Toggle Handler
    const pointsToggle = document.getElementById('checkout-points-toggle');
    const xuDiscountText = document.getElementById('xu-discount-text');
    if (pointsToggle) {
        pointsToggle.addEventListener('change', () => {
            window.checkoutState.usePoints = pointsToggle.checked;
            if (xuDiscountText) {
                xuDiscountText.textContent = pointsToggle.checked ? '[-50.000₫]' : '[-0₫]';
                xuDiscountText.className = pointsToggle.checked ? 'text-xs font-bold text-orange-600' : 'text-xs font-bold text-gray-400';
            }
            syncVouchersWithBackend();
        });
    }

    // Select / Deselect Voucher in Modals
    window.selectVoucherCard = function(code, type) {
        if (type === 'freeship') {
            window.checkoutState.freeshipCode = (window.checkoutState.freeshipCode === code) ? '' : code;
        } else if (type === 'shop') {
            window.checkoutState.shopCode = (window.checkoutState.shopCode === code) ? '' : code;
        } else {
            window.checkoutState.platformCode = (window.checkoutState.platformCode === code) ? '' : code;
        }
        syncVouchersWithBackend();
    };

    // Apply manual shop coupon
    window.applyManualShopCoupon = async function() {
        const input = document.getElementById('shop-modal-input');
        const msgEl = document.getElementById('shop-modal-message');
        const code = input ? input.value.trim() : '';

        if (!code) {
            if (msgEl) {
                msgEl.textContent = 'Vui lòng nhập mã của shop';
                msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
            }
            return;
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
                    shop_code: code,
                    subtotal: window.checkoutState.currentSubtotal
                })
            });

            const data = await res.json();
            if (data.success) {
                window.checkoutState.shopCode = code;
                if (msgEl) {
                    msgEl.textContent = `Áp dụng thành công mã: ${code}`;
                    msgEl.className = 'text-xs mt-1.5 px-1 text-emerald-600 font-bold block';
                }
                syncVouchersWithBackend();
            } else {
                if (msgEl) {
                    msgEl.textContent = data.message || 'Mã shop không hợp lệ';
                    msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
                }
            }
        } catch (e) {
            console.error(e);
        }
    };

    // Apply manual platform coupon
    window.applyManualCoupon = async function() {
        const input = document.getElementById('modal-voucher-input');
        const msgEl = document.getElementById('modal-coupon-message');
        const code = input ? input.value.trim() : '';

        if (!code) {
            if (msgEl) {
                msgEl.textContent = 'Vui lòng nhập mã voucher';
                msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
            }
            return;
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
                    subtotal: window.checkoutState.currentSubtotal
                })
            });

            const data = await res.json();
            if (data.success) {
                if (data.freeship_code) window.checkoutState.freeshipCode = data.freeship_code;
                if (data.platform_code) window.checkoutState.platformCode = data.platform_code;

                if (msgEl) {
                    msgEl.textContent = `${data.message} (${data.applied_codes_string})`;
                    msgEl.className = 'text-xs mt-1.5 px-1 text-emerald-600 font-bold block';
                }
                syncVouchersWithBackend();
            } else {
                if (msgEl) {
                    msgEl.textContent = data.message || 'Mã không hợp lệ hoặc không đủ điều kiện';
                    msgEl.className = 'text-xs mt-1.5 px-1 text-rose-600 font-bold block';
                }
            }
        } catch (e) {
            console.error(e);
        }
    };

    // Sync Vouchers with Backend & Update DOM
    async function syncVouchersWithBackend() {
        try {
            const res = await fetch('{{ route("checkout.apply-coupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    freeship_code: window.checkoutState.freeshipCode,
                    shop_code: window.checkoutState.shopCode,
                    platform_code: window.checkoutState.platformCode,
                    use_points: window.checkoutState.usePoints,
                    subtotal: window.checkoutState.currentSubtotal
                })
            });

            const data = await res.json();

            // Update hidden inputs for checkout submission
            document.getElementById('hidden-freeship-code').value = window.checkoutState.freeshipCode;
            document.getElementById('hidden-shop-code').value = window.checkoutState.shopCode;
            document.getElementById('hidden-platform-code').value = window.checkoutState.platformCode;
            document.getElementById('hidden-coupon-code').value = data.applied_codes_string || '';

            // Update UI tags on checkout cards
            const shopBadge = document.getElementById('shop-voucher-badge');
            if (shopBadge) {
                if (window.checkoutState.shopCode) {
                    shopBadge.textContent = window.checkoutState.shopCode;
                    shopBadge.classList.remove('hidden');
                } else {
                    shopBadge.classList.add('hidden');
                }
            }

            const platformBadge = document.getElementById('platform-voucher-badge');
            if (platformBadge) {
                if (window.checkoutState.platformCode) {
                    platformBadge.textContent = window.checkoutState.platformCode;
                    platformBadge.classList.remove('hidden');
                } else {
                    platformBadge.classList.add('hidden');
                }
            }

            const freeshipBadge = document.getElementById('freeship-voucher-badge');
            if (freeshipBadge) {
                if (window.checkoutState.freeshipCode) {
                    freeshipBadge.textContent = window.checkoutState.freeshipCode;
                    freeshipBadge.classList.remove('hidden');
                } else {
                    freeshipBadge.classList.add('hidden');
                }
            }

            // Update breakdown rows in right sticky summary
            const freeshipRow = document.getElementById('freeship-discount-row');
            const summaryFreeship = document.getElementById('summary-freeship-discount');
            if (data.freeship_discount > 0) {
                freeshipRow.classList.remove('hidden');
                summaryFreeship.textContent = data.formatted_freeship_discount;
            } else {
                freeshipRow.classList.add('hidden');
            }

            const shopRow = document.getElementById('shop-discount-row');
            const summaryShop = document.getElementById('summary-shop-discount');
            if (data.product_discount > 0 && window.checkoutState.shopCode) {
                shopRow.classList.remove('hidden');
                summaryShop.textContent = '-' + Number(data.product_discount).toLocaleString('vi-VN') + '₫';
            } else {
                shopRow.classList.add('hidden');
            }

            const platformRow = document.getElementById('platform-discount-row');
            const summaryPlatform = document.getElementById('summary-platform-discount');
            if (data.product_discount > 0 && window.checkoutState.platformCode) {
                platformRow.classList.remove('hidden');
                summaryPlatform.textContent = '-' + Number(data.product_discount).toLocaleString('vi-VN') + '₫';
            } else {
                platformRow.classList.add('hidden');
            }

            const xuRow = document.getElementById('xu-discount-row');
            const summaryXu = document.getElementById('summary-xu-discount');
            if (data.points_discount > 0) {
                xuRow.classList.remove('hidden');
                summaryXu.textContent = data.formatted_points_discount;
            } else {
                xuRow.classList.add('hidden');
            }

            // Shipping fee & grand total
            const summaryShipping = document.getElementById('summary-shipping');
            if (summaryShipping && data.formatted_shipping) {
                summaryShipping.textContent = data.formatted_shipping;
            }

            const summaryGrandTotal = document.getElementById('summary-grand-total');
            if (summaryGrandTotal && data.formatted_new_total) {
                summaryGrandTotal.textContent = data.formatted_new_total;
            }

            // Update shop modal selected display
            const shopModalSelected = document.getElementById('shop-modal-selected');
            if (shopModalSelected) {
                shopModalSelected.innerHTML = window.checkoutState.shopCode 
                    ? `<span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 text-[11px] font-extrabold border border-rose-200">${window.checkoutState.shopCode}</span>`
                    : '<span class="text-gray-400 font-normal">Chưa chọn mã</span>';
            }

            // Update platform modal selected status bar
            const modalSelectedSummary = document.getElementById('modal-selected-summary');
            if (modalSelectedSummary) {
                const platCodes = [window.checkoutState.freeshipCode, window.checkoutState.platformCode].filter(Boolean);
                if (platCodes.length > 0) {
                    modalSelectedSummary.innerHTML = platCodes.map(c => 
                        `<span class="px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-[11px] font-extrabold border border-orange-200">${c}</span>`
                    ).join('');
                } else {
                    modalSelectedSummary.innerHTML = '<span class="text-gray-400 font-normal">Chưa chọn voucher</span>';
                }
            }

            // Highlight selected cards in both modals
            document.querySelectorAll('.voucher-card').forEach(card => {
                const cCode = card.getAttribute('data-code');
                const isSelected = [window.checkoutState.freeshipCode, window.checkoutState.shopCode, window.checkoutState.platformCode].includes(cCode);
                const btn = card.querySelector('.btn-modal-select-coupon');
                if (isSelected) {
                    card.classList.add('border-orange-500', 'ring-2', 'ring-orange-400/40');
                    if (btn) {
                        btn.textContent = 'Bỏ chọn';
                        btn.className = 'btn-modal-select-coupon px-3 py-1 bg-primary text-white rounded-md text-xs font-bold transition-colors cursor-pointer';
                    }
                } else {
                    card.classList.remove('border-orange-500', 'ring-2', 'ring-orange-400/40');
                    if (btn) {
                        btn.textContent = 'Dùng ngay';
                        btn.className = 'btn-modal-select-coupon px-3 py-1 border border-primary text-primary hover:bg-primary hover:text-white rounded-md text-xs font-bold transition-colors cursor-pointer';
                    }
                }
            });

        } catch (e) {
            console.error('Failed to sync vouchers:', e);
        }
    }

    // Address Modal Manager
    if (window.AddressModalManager) {
        window.AddressModalManager.init('checkout');
    }

    // Mandatory address validation before placing order
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const name = document.getElementById('input-recipient-name')?.value.trim();
            const phone = document.getElementById('input-recipient-phone')?.value.trim();
            const addr = document.getElementById('input-recipient-address')?.value.trim();

            if (!name || !phone || !addr) {
                e.preventDefault();
                alert('Bắt buộc phải cài đặt địa chỉ nhận hàng trước khi tiến hành thanh toán!');
                window.AddressModalManager?.openSelectorModal();
                return false;
            }
        });
    }

    // Auto-apply coupon from URL param if present
    const urlParams = new URLSearchParams(window.location.search);
    const couponFromUrl = urlParams.get('coupon');
    if (couponFromUrl) {
        const input = document.getElementById('modal-voucher-input');
        if (input) input.value = couponFromUrl;
        applyManualCoupon();
    }
});
</script>
@endpush
