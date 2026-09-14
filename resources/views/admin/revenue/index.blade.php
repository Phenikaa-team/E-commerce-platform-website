@extends('layouts.admin')

@section('title', 'Báo Cáo Doanh Thu Toàn Sàn - ShopMart Admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-6 bg-[#F52245] rounded-full inline-block"></span>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Báo Cáo Doanh Thu & Tài Chính Sàn</h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">Phân tích dòng tiền toàn diện, tổng giá trị giao dịch (GMV) và hoa hồng dịch vụ nền tảng</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Period Filter Pills -->
            <div class="inline-flex p-1 bg-white border border-[#E9ECF1] rounded-xl shadow-2xs text-xs font-semibold self-start sm:self-auto">
                <a href="{{ route('admin.revenue', ['period' => '7days', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === '7days' ? 'bg-[#FFF0F2] text-[#F52245] font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    7 ngày qua
                </a>
                <a href="{{ route('admin.revenue', ['period' => '30days', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === '30days' ? 'bg-[#FFF0F2] text-[#F52245] font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    30 ngày qua
                </a>
                <a href="{{ route('admin.revenue', ['period' => 'this_month', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === 'this_month' ? 'bg-[#FFF0F2] text-[#F52245] font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Tháng này
                </a>
                <a href="{{ route('admin.revenue', ['period' => 'all', 'payment_method' => $paymentMethod]) }}" class="px-3 py-1.5 rounded-lg transition-colors {{ $period === 'all' ? 'bg-[#FFF0F2] text-[#F52245] font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
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
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-[#F52245] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-[#F52245] tracking-tight mt-2 leading-none">
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
                        <span class="w-3 h-3 rounded-full bg-[#F52245]"></span>
                        GMV Doanh thu
                    </span>
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <span class="w-3 h-3 rounded-full bg-[#3B82F6]"></span>
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
                <div class="space-y-2 text-xs pt-2">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="flex items-center gap-2 font-medium text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                            COD (Tiền mặt)
                        </span>
                        <span class="font-bold text-gray-900">₫ {{ number_format($paymentMethodsDistribution['cod'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="flex items-center gap-2 font-medium text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#3B82F6]"></span>
                            Cổng VNPay
                        </span>
                        <span class="font-bold text-gray-900">₫ {{ number_format($paymentMethodsDistribution['vnpay'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100">
                        <span class="flex items-center gap-2 font-medium text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#EC4899]"></span>
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
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-[#F52245] hover:underline">Xem tất cả gian hàng</a>
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
                                <span class="w-6 h-6 rounded-full {{ $idx === 0 ? 'bg-[#F52245] text-white' : ($idx === 1 ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700') }} font-bold text-xs flex items-center justify-center">
                                    {{ $idx + 1 }}
                                </span>
                            </td>
                            <td class="py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $st->logo_url ?? asset('images/placeholders/store-logo-placeholder.svg') }}" class="w-8 h-8 rounded-lg object-cover border border-gray-200">
                                    <div>
                                        <a href="{{ route('store.show', $st->slug ?? $st->id) }}" target="_blank" class="font-bold text-gray-900 hover:text-[#F52245] transition-colors">
                                            {{ $st->name }}
                                        </a>
                                        <p class="text-[10px] text-gray-400">Store ID: #{{ $st->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 font-semibold text-gray-600">{{ $st->products_count }} SP</td>
                            <td class="py-3.5 font-bold text-gray-900">{{ number_format($st->orders_count) }} đơn</td>
                            <td class="py-3.5 font-black text-gray-900">₫ {{ number_format($st->gmv, 0, ',', '.') }}</td>
                            <td class="py-3.5 font-bold text-[#F52245]">₫ {{ number_format($st->platform_commission, 0, ',', '.') }}</td>
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
                <select name="payment_method" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-hidden focus:border-[#F52245]">
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
                            <td class="py-3 font-bold text-[#F52245]">₫ {{ number_format($fee, 0, ',', '.') }}</td>
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
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Detailed Daily Revenue Chart
    const ctxRevenue = document.getElementById('detailedRevenueChart')?.getContext('2d');
    if (ctxRevenue) {
        new Chart(ctxRevenue, {
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        type: 'bar',
                        label: 'Doanh thu GMV (₫)',
                        data: {!! json_encode($chartRevenue) !!},
                        backgroundColor: '#F52245',
                        borderRadius: 6,
                        barThickness: 16,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Số lượng đơn hàng',
                        data: {!! json_encode($chartOrders) !!},
                        borderColor: '#3B82F6',
                        borderWidth: 2,
                        pointBackgroundColor: '#3B82F6',
                        pointRadius: 3,
                        tension: 0.3,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                    },
                    y: {
                        position: 'left',
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: function(value) {
                                return (value / 1000000).toFixed(0) + 'M';
                            }
                        }
                    },
                    y1: {
                        position: 'right',
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#3B82F6' }
                    }
                }
            }
        });
    }

    // 2. Payment Method Distribution Doughnut Chart
    const ctxPayment = document.getElementById('paymentMethodChart')?.getContext('2d');
    if (ctxPayment) {
        new Chart(ctxPayment, {
            type: 'doughnut',
            data: {
                labels: ['COD (Tiền mặt)', 'VNPay', 'MoMo', 'Khác'],
                datasets: [{
                    data: [
                        {{ $paymentMethodsDistribution['cod'] }},
                        {{ $paymentMethodsDistribution['vnpay'] }},
                        {{ $paymentMethodsDistribution['momo'] }},
                        {{ $paymentMethodsDistribution['other'] }}
                    ],
                    backgroundColor: ['#10B981', '#3B82F6', '#EC4899', '#9CA3AF'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
