@extends('layouts.admin')

@section('title', 'Báo Cáo Doanh Thu Toàn Sàn - ShopMart Admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-6 bg-primary rounded-full inline-block"></span>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Báo Cáo Doanh Thu & Tài Chính Sàn</h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">Phân tích dòng tiền toàn diện, tổng giá trị giao dịch (GMV) và hoa hồng dịch vụ nền tảng</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Period Filter Pills -->
            <div class="inline-flex p-1 bg-white border border-gray-200 rounded-xl shadow-2xs text-xs font-semibold self-start sm:self-auto">
                <a href="{{ route('admin.revenue', ['period' => '7days', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === '7days' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    7 ngày qua
                </a>
                <a href="{{ route('admin.revenue', ['period' => '30days', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === '30days' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    30 ngày qua
                </a>
                <a href="{{ route('admin.revenue', ['period' => 'this_month', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === 'this_month' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Tháng này
                </a>
                <a href="{{ route('admin.revenue', ['period' => 'all', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === 'all' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Tất cả
                </a>
            </div>

            <!-- Export Excel Button -->
            <a href="{{ route('admin.revenue.export', request()->query()) }}" 
               class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/70 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 shrink-0"
               title="Xuất báo cáo tài chính ra file Excel">
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

    <!-- 4 Key Financial Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Tổng GMV Sàn -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tổng giá trị GMV</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                ₫ {{ number_format($totalGmv, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Tổng đơn không bị hủy trên toàn sàn
            </p>
        </div>

        <!-- Metric 2: Phí hoa hồng sàn thu (5%) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hoa hồng sàn (5%)</span>
                <div class="w-9 h-9 rounded-xl bg-primary-light text-primary flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-primary tracking-tight mt-2 leading-none">
                ₫ {{ number_format($platformEarnings, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                Thu nhập dịch vụ nền tảng thực tế
            </p>
        </div>

        <!-- Metric 3: Giá trị trung bình đơn (AOV) -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Giá trị đơn TB (AOV)</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                ₫ {{ number_format($averageOrderValue, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                Mức chi tiêu trung bình mỗi đơn hoàn tất
            </p>
        </div>

        <!-- Metric 4: Trợ giá khuyến mãi -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Trợ giá Voucher</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight mt-2 leading-none">
                ₫ {{ number_format($totalDiscountSponsored, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Tổng ngân sách mã giảm giá đã sử dụng
            </p>
        </div>
    </div>

    <!-- Escrow & Financial Settlement Breakdown for Platform -->
    <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-3">
            <div>
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Hạch Toán Dòng Tiền &amp; Escrow Nền Tảng (Mô Hình Ăn Chia 3 Bên)</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Dòng tiền tạm giữ, các loại phí dịch vụ sàn thu từ người bán và chi phí tài trợ kích cầu</p>
            </div>
            <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 shrink-0 self-start sm:self-auto">
                Tỷ lệ: Phí thanh toán 2.5% + Hoa hồng sàn 3.0%
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 pt-5">
            <!-- 1. Tiền trong Escrow -->
            <div class="p-3 text-left">
                <span class="text-[11px] font-semibold text-gray-500 block">Dòng tiền Escrow</span>
                <span class="text-base sm:text-lg font-black text-gray-900 mt-1 block">
                    {{ number_format($platformFinancials['escrow_holding'] ?? 0, 0, ',', '.') }}₫
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Tạm giữ chờ giao hàng</span>
            </div>

            <!-- 2. Phí thanh toán 2.5% -->
            <div class="p-3 text-left">
                <span class="text-[11px] font-semibold text-gray-500 block">Phí thanh toán (2.5%)</span>
                <span class="text-base sm:text-lg font-black text-gray-900 mt-1 block">
                    {{ number_format($platformFinancials['payment_fee'] ?? 0, 0, ',', '.') }}₫
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Bù chi phí cổng TT</span>
            </div>

            <!-- 3. Phí hoa hồng 3.0% -->
            <div class="p-3 text-left">
                <span class="text-[11px] font-semibold text-gray-500 block">Phí hoa hồng (3.0%)</span>
                <span class="text-base sm:text-lg font-black text-emerald-600 mt-1 block">
                    {{ number_format($platformFinancials['commission_fee'] ?? 0, 0, ',', '.') }}₫
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Duy trì &amp; vận hành sàn</span>
            </div>

            <!-- 4. Chi phí Voucher sàn tài trợ -->
            <div class="p-3 text-left">
                <span class="text-[11px] font-semibold text-gray-500 block">Chi phí Voucher sàn</span>
                <span class="text-base sm:text-lg font-black text-rose-600 mt-1 block">
                    -{{ number_format($platformFinancials['voucher_cost'] ?? 0, 0, ',', '.') }}₫
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Sàn tài trợ kích cầu</span>
            </div>

            <!-- 5. Lợi nhuận ròng của sàn -->
            <div class="p-3 text-left">
                <span class="text-[11px] font-bold text-gray-700 block">Lợi nhuận ròng sàn</span>
                <span class="text-base sm:text-lg font-black text-emerald-600 mt-1 block">
                    {{ ($platformFinancials['net_profit'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($platformFinancials['net_profit'] ?? 0, 0, ',', '.') }}₫
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Phí thu - Voucher tài trợ</span>
            </div>

            <!-- 6. Hoàn xu thưởng người dùng -->
            <div class="p-3 text-left">
                <span class="text-[11px] font-semibold text-gray-500 block">Xu thưởng hoàn khách</span>
                <span class="text-base sm:text-lg font-black text-amber-600 mt-1 block">
                    {{ number_format($platformFinancials['cashback_points'] ?? 0, 0, ',', '.') }} xu
                </span>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Trích 10% hoa hồng</span>
            </div>
        </div>
    </div>

    <!-- Charts Section: Doanh thu chi tiết & Phân bổ phương thức thanh toán -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Biểu đồ chi tiết theo ngày (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Biến Động Doanh Thu & Lượng Giao Dịch</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tiến trình doanh thu gộp theo từng ngày (Kỳ lọc: {{ $period === '7days' ? '7 ngày' : ($period === 'this_month' ? 'Tháng này' : ($period === 'all' ? 'Toàn bộ' : '30 ngày')) }})</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-primary"></span>
                        GMV Doanh thu
                    </span>
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        Số lượng đơn
                    </span>
                </div>
            </div>
            <div class="h-72 w-full">
                <canvas id="detailedRevenueChart"></canvas>
            </div>
        </div>

        <!-- Phân bổ phương thức thanh toán & Phí phụ trợ (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Cơ Cấu Thanh Toán</h3>
                <p class="text-xs text-gray-400 mt-0.5">Tỉ lệ dòng tiền theo các cổng giao dịch</p>
                
                <div class="relative flex items-center justify-center my-4 h-44">
                    <canvas id="paymentMethodChart"></canvas>
                </div>

                <!-- Legend details -->
                <div class="space-y-1.5 text-xs pt-2 divide-y divide-gray-50">
                    <div class="flex items-center justify-between py-1.5">
                        <span class="flex items-center gap-2 font-medium text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            COD (Tiền mặt)
                        </span>
                        <span class="font-bold text-gray-900">₫ {{ number_format($paymentMethodsDistribution['cod'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="flex items-center gap-2 font-medium text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            Cổng VNPay
                        </span>
                        <span class="font-bold text-gray-900">₫ {{ number_format($paymentMethodsDistribution['vnpay'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="flex items-center gap-2 font-medium text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                            Ví MoMo
                        </span>
                        <span class="font-bold text-gray-900">₫ {{ number_format($paymentMethodsDistribution['momo'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping Fees Note -->
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Tổng phí ship toàn sàn:</span>
                <span class="font-bold text-gray-900">₫ {{ number_format($totalShippingFees, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Top Revenue Stores Table -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">Top Gian Hàng Đóng Góp Doanh Thu Cao Nhất</h3>
                <p class="text-xs text-gray-400 mt-0.5">Xếp hạng đối tác gian hàng mang lại doanh số lớn nhất cho sàn</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-primary hover:underline">Xem tất cả gian hàng</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase text-[10px] font-bold">
                        <th class="pb-3">Hạng</th>
                        <th class="pb-3">Gian hàng</th>
                        <th class="pb-3">Sản phẩm</th>
                        <th class="pb-3">Đơn hoàn tất</th>
                        <th class="pb-3">Tổng GMV</th>
                        <th class="pb-3">Hoa hồng sàn (5%)</th>
                        <th class="pb-3 text-right">Doanh thu Shop</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topStores as $idx => $st)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3.5">
                                <span class="w-6 h-6 rounded-full {{ $idx === 0 ? 'bg-primary text-white' : ($idx === 1 ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700') }} font-bold text-xs flex items-center justify-center">
                                    {{ $idx + 1 }}
                                </span>
                            </td>
                            <td class="py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $st->logo_url ?? asset('images/placeholders/store-logo-placeholder.svg') }}" class="w-8 h-8 rounded-lg object-cover border border-gray-200">
                                    <div>
                                        <a href="{{ route('store.show', $st->slug ?? $st->id) }}" target="_blank" class="font-bold text-gray-900 hover:text-primary transition-colors">
                                            {{ $st->name }}
                                        </a>
                                        <p class="text-[10px] text-gray-400">Store ID: #{{ $st->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 font-semibold text-gray-600">{{ $st->products_count }} SP</td>
                            <td class="py-3.5 font-bold text-gray-900">{{ number_format($st->orders_count) }} đơn</td>
                            <td class="py-3.5 font-black text-gray-900">₫ {{ number_format($st->gmv, 0, ',', '.') }}</td>
                            <td class="py-3.5 font-bold text-primary">₫ {{ number_format($st->platform_commission, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-black text-emerald-600">₫ {{ number_format($st->net_payout, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400">Chưa có dữ liệu giao dịch gian hàng</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Transactions & Revenue Ledger Table with Pagination -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Sổ Nhật Ký Dòng Tiền Đơn Hàng Chi Tiết</h3>
                <p class="text-xs text-gray-400 mt-0.5">Bóc tách từng khoản thanh toán, phí nền tảng và doanh thu đối tác</p>
            </div>

            <!-- Payment Method Filter Form -->
            <form method="GET" action="{{ route('admin.revenue') }}" class="flex items-center gap-2">
                <input type="hidden" name="period" value="{{ $period }}">
                <select name="payment_method" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-hidden focus:border-primary">
                    <option value="">Tất cả phương thức</option>
                    <option value="cod" {{ $paymentMethod === 'cod' ? 'selected' : '' }}>COD (Tiền mặt)</option>
                    <option value="vnpay" {{ $paymentMethod === 'vnpay' ? 'selected' : '' }}>VNPay</option>
                    <option value="momo" {{ $paymentMethod === 'momo' ? 'selected' : '' }}>MoMo</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase text-[10px] font-bold">
                        <th class="pb-3">Mã đơn</th>
                        <th class="pb-3">Thời gian</th>
                        <th class="pb-3">Khách hàng</th>
                        <th class="pb-3">Thanh toán</th>
                        <th class="pb-3">Trạng thái</th>
                        <th class="pb-3">Tổng tiền (GMV)</th>
                        <th class="pb-3">Phí sàn (5%)</th>
                        <th class="pb-3 text-right">Chi trả Shop (95%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $t)
                        @php
                            $fee = $t->total * $platformFeeRate;
                            $payout = $t->total - $fee;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 font-bold text-gray-900">#{{ $t->order_code ?? ('SHM'.str_pad($t->id, 5, '0', STR_PAD_LEFT)) }}</td>
                            <td class="py-3 text-gray-500">{{ $t->created_at ? $t->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="py-3 font-semibold text-gray-800">{{ $t->user->name ?? 'Khách Mua' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 uppercase">
                                    {{ strtoupper($t->payment_method ?? 'COD') }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $t->status_badge }}">
                                    {{ $t->status_label }}
                                </span>
                            </td>
                            <td class="py-3 font-bold text-gray-900">₫ {{ number_format($t->total, 0, ',', '.') }}</td>
                            <td class="py-3 font-bold text-primary">₫ {{ number_format($fee, 0, ',', '.') }}</td>
                            <td class="py-3 text-right font-black text-emerald-600">₫ {{ number_format($payout, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">Không tìm thấy giao dịch nào phù hợp</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination with our white styling -->
        <div class="pt-4 border-t border-gray-100 flex justify-center">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script type="application/json" id="admin-revenue-data">
{
    "chartLabels": @json($chartLabels),
    "chartRevenue": @json($chartRevenue),
    "chartOrders": @json($chartOrders),
    "paymentMethodsDistribution": @json($paymentMethodsDistribution)
}
</script>
@vite(['resources/js/pages/admin-revenue.js'])
@endpush
