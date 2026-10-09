@extends('layouts.seller')

@section('title', 'Tài Chính & Ví Người Bán - ShopMart Seller')
@section('page_title', 'Tài Chính & Ví Người Bán (Hạch Toán & Quyết Toán Dòng Tiền)')

@section('content')
<div class="space-y-6">

    <!-- 1. Top Financial Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Số dư khả dụng (Có thể rút) -->
        <div class="seller-wallet-card">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-medium text-emerald-100 block">Số dư khả dụng (Ví rút)</span>
                    <h3 class="text-2xl font-black mt-1 tracking-tight">
                        {{ number_format((float) $wallet->balance, 0, ',', '.') }}₫
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between">
                <span class="text-[11px] text-emerald-100">Sẵn sàng rút về ngân hàng</span>
                <button type="button" onclick="openWithdrawModal()" class="seller-withdraw-btn">
                    Rút tiền
                </button>
            </div>
        </div>

        <!-- Card 2: Tiền tạm giữ trong Escrow -->
        <div class="seller-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 block">Đang tạm giữ (Escrow)</span>
                    <h3 class="text-2xl font-black text-amber-600 mt-1 tracking-tight">
                        {{ number_format((float) $wallet->pending_balance, 0, ',', '.') }}₫
                    </h3>
                </div>
                <div class="seller-stat-icon bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100">
                <span class="text-[11px] text-gray-400 block leading-tight">
                    Tự động kết chuyển vào ví khả dụng ngay khi đơn giao thành công.
                </span>
            </div>
        </div>

        <!-- Card 3: Doanh thu đã quyết toán tích lũy -->
        <div class="seller-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 block">Tổng tiền đã quyết toán</span>
                    <h3 class="text-2xl font-black text-gray-900 mt-1 tracking-tight">
                        {{ number_format((float) ($stats->total_settled ?? $wallet->total_earned), 0, ',', '.') }}₫
                    </h3>
                </div>
                <div class="seller-stat-icon bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                <span>Đã rút: {{ number_format((float) $wallet->total_withdrawn, 0, ',', '.') }}₫</span>
                <span class="font-bold text-gray-600">{{ $stats->total_orders ?? 0 }} đơn</span>
            </div>
        </div>

        <!-- Card 4: Tổng phí sàn đã đóng -->
        <div class="seller-stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-500 block">Tổng phí sàn đã nộp</span>
                    <h3 class="text-2xl font-black text-gray-800 mt-1 tracking-tight">
                        {{ number_format((float) (($stats->total_payment_fee ?? 0) + ($stats->total_commission_fee ?? 0)), 0, ',', '.') }}₫
                    </h3>
                </div>
                <div class="seller-stat-icon bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                <span>Phí TT 2.5% + Hoa hồng 3.0%</span>
                <span class="font-bold text-emerald-600">Rõ ràng, minh bạch</span>
            </div>
        </div>

    </div>

    <!-- 2. Bảng kê đối soát tài chính chi tiết từng đơn hàng (Order Financials Ledger) -->
    <div class="seller-ledger-card">
        
        <!-- Header & Status Filter Tabs -->
        <div class="seller-ledger-header">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Bảng Kê Hạch Toán & Đối Soát Dòng Tiền Đơn Hàng</h3>
                <p class="text-xs text-gray-400 mt-0.5">Chi tiết bóc tách từng khoản: Tiền hàng, Voucher shop, Phí sàn thu và Thực nhận của Shop</p>
            </div>

            <!-- Filter Status -->
            <div class="seller-filter-bar self-start sm:self-auto">
                <a href="{{ route('seller.finances.index') }}" class="seller-filter-pill {{ empty($escrowStatus) ? 'is-active' : '' }}">
                    Tất cả
                </a>
                <a href="{{ route('seller.finances.index', ['status' => 'holding']) }}" class="seller-filter-pill {{ $escrowStatus === 'holding' ? 'is-active text-amber-700' : '' }}">
                    Đang tạm giữ (Escrow)
                </a>
                <a href="{{ route('seller.finances.index', ['status' => 'settled']) }}" class="seller-filter-pill {{ $escrowStatus === 'settled' ? 'is-active text-emerald-700' : '' }}">
                    Đã về ví (Settled)
                </a>
                <a href="{{ route('seller.finances.index', ['status' => 'cancelled']) }}" class="seller-filter-pill {{ $escrowStatus === 'cancelled' ? 'is-active text-rose-700' : '' }}">
                    Đã hủy
                </a>
            </div>
        </div>

        @if($financials->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto text-gray-400 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <h4 class="text-sm font-bold text-gray-800">Chưa có bản ghi hạch toán nào</h4>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Khi khách đặt hàng sản phẩm của shop, dòng tiền sẽ tự động được hạch toán và hiển thị chi tiết tại đây.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Mã đơn &amp; Ngày</th>
                            <th class="py-3 px-4">Tiền hàng gốc</th>
                            <th class="py-3 px-4">Voucher Shop</th>
                            <th class="py-3 px-4">Doanh thu tính phí</th>
                            <th class="py-3 px-4 text-center">Phí TT (2.5%)</th>
                            <th class="py-3 px-4 text-center">Hoa hồng (3.0%)</th>
                            <th class="py-3 px-4 text-right">Shop thực nhận</th>
                            <th class="py-3 px-4 text-center">Trạng thái Escrow</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($financials as $f)
                            @php
                                $ord = $f->order;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <!-- Order Code & Date -->
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-gray-900 block">
                                        {{ $ord->order_code ?? ('#'.$f->order_id) }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 block mt-0.5">
                                        {{ $f->created_at ? $f->created_at->format('d/m/Y H:i') : '' }}
                                    </span>
                                </td>

                                <!-- Gross Merchandise Amount -->
                                <td class="py-3.5 px-4 font-semibold text-gray-800">
                                    {{ number_format((float) $f->gross_merchandise_amount, 0, ',', '.') }}₫
                                </td>

                                <!-- Shop Voucher -->
                                <td class="py-3.5 px-4">
                                    @if($f->shop_discount > 0)
                                        <span class="text-rose-600 font-bold">
                                            -{{ number_format((float) $f->shop_discount, 0, ',', '.') }}₫
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Net Merchandise Amount -->
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ number_format((float) $f->net_merchandise_amount, 0, ',', '.') }}₫
                                </td>

                                <!-- Payment Fee 2.5% -->
                                <td class="py-3.5 px-4 text-center text-rose-600 font-semibold">
                                    -{{ number_format((float) $f->payment_fee, 0, ',', '.') }}₫
                                </td>

                                <!-- Commission Fee 3.0% -->
                                <td class="py-3.5 px-4 text-center text-rose-600 font-semibold">
                                    -{{ number_format((float) $f->commission_fee, 0, ',', '.') }}₫
                                </td>

                                <!-- Shop Earning (Thực nhận) -->
                                <td class="py-3.5 px-4 text-right">
                                    <span class="text-sm font-black text-emerald-600 block">
                                        +{{ number_format((float) $f->shop_earning, 0, ',', '.') }}₫
                                    </span>
                                    @if($f->platform_discount > 0)
                                        <span class="text-[10px] text-amber-600 block font-medium">
                                            (Sàn trợ giá {{ number_format((float) $f->platform_discount, 0, ',', '.') }}₫)
                                        </span>
                                    @endif
                                </td>

                                <!-- Escrow Status Badge -->
                                <td class="py-3.5 px-4 text-center">
                                    @if($f->escrow_status === 'settled')
                                        <span class="seller-badge-settled">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Đã về ví</span>
                                        </span>
                                    @elseif($f->escrow_status === 'holding')
                                        <span class="seller-badge-holding" title="Chờ giao hàng thành công & hết khiếu nại">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Tạm giữ Escrow</span>
                                        </span>
                                    @else
                                        <span class="seller-badge-cancelled">
                                            <span>Đã hủy</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($financials->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $financials->links() }}
                </div>
            @endif
        @endif

    </div>

    <!-- 3. Lịch sử biến động số dư ví Shop -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5">
        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Lịch Sử Biến Động Số Dư Ví (Gần Đây)</span>
        </h4>

        @if($transactions->isEmpty())
            <p class="text-xs text-gray-400 py-4 text-center">Chưa có giao dịch biến động số dư nào.</p>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($transactions as $tx)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $tx->type === 'withdrawal' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                                @if($tx->type === 'withdrawal')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                @endif
                            </span>
                            <div>
                                <span class="font-bold text-gray-900 block">{{ $tx->description }}</span>
                                <span class="text-[11px] text-gray-400 font-mono">{{ $tx->transaction_code }} • {{ $tx->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="font-black text-sm block {{ $tx->type === 'withdrawal' ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $tx->type === 'withdrawal' ? '-' : '+' }}{{ number_format((float) $tx->amount, 0, ',', '.') }}₫
                            </span>
                            <span class="text-[11px] text-gray-400">Số dư sau: {{ number_format((float) $tx->balance_after, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- Modal Rút Tiền Về Ngân Hàng -->
<div id="withdraw-modal" class="seller-modal-overlay hidden">
    <div class="seller-modal-dialog space-y-4">
        <div class="seller-modal-header">
            <h3 class="seller-modal-title">Rút Tiền Về Tài Khoản Ngân Hàng</h3>
            <button type="button" onclick="closeWithdrawModal()" class="seller-modal-close">&times;</button>
        </div>

        <form action="{{ route('seller.finances.withdraw') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500 font-medium">Số dư khả dụng:</span>
                <span class="text-sm font-black text-emerald-600">{{ number_format((float) $wallet->balance, 0, ',', '.') }}₫</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Số tiền muốn rút (VNĐ)</label>
                <input 
                    type="number" 
                    name="amount" 
                    min="50000" 
                    max="{{ (int) $wallet->balance }}" 
                    placeholder="Nhập số tiền (tối thiểu 50.000₫)..." 
                    class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-900 focus:border-primary focus:outline-hidden"
                    required
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Ngân hàng thụ hưởng</label>
                <select name="bank_name" class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-xl text-xs text-gray-900 focus:border-primary focus:outline-hidden" required>
                    <option value="Vietcombank" {{ $wallet->bank_name === 'Vietcombank' ? 'selected' : '' }}>Vietcombank (Ngoại Thương)</option>
                    <option value="MBBank" {{ $wallet->bank_name === 'MBBank' ? 'selected' : '' }}>MBBank (Quân Đội)</option>
                    <option value="Techcombank" {{ $wallet->bank_name === 'Techcombank' ? 'selected' : '' }}>Techcombank (Kỹ Thương)</option>
                    <option value="ACB" {{ $wallet->bank_name === 'ACB' ? 'selected' : '' }}>ACB (Á Châu)</option>
                    <option value="BIDV" {{ $wallet->bank_name === 'BIDV' ? 'selected' : '' }}>BIDV (Đầu Tư &amp; Phát Triển)</option>
                    <option value="VPBank" {{ $wallet->bank_name === 'VPBank' ? 'selected' : '' }}>VPBank (Việt Nam Thịnh Vượng)</option>
                    <option value="VietinBank" {{ $wallet->bank_name === 'VietinBank' ? 'selected' : '' }}>VietinBank (Công Thương)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Số tài khoản ngân hàng</label>
                <input 
                    type="text" 
                    name="bank_account_number" 
                    value="{{ $wallet->bank_account_number ?? '' }}" 
                    placeholder="Nhập số tài khoản..." 
                    class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-xl text-xs font-mono font-bold text-gray-900 focus:border-primary focus:outline-hidden"
                    required
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tên chủ tài khoản (Viết in hoa không dấu)</label>
                <input 
                    type="text" 
                    name="bank_account_name" 
                    value="{{ $wallet->bank_account_name ?? '' }}" 
                    placeholder="VÍ DỤ: NGUYEN VAN A" 
                    class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-xl text-xs font-bold uppercase text-gray-900 focus:border-primary focus:outline-hidden"
                    required
                >
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeWithdrawModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 cursor-pointer">
                    Hủy
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition-all shadow-xs cursor-pointer active:scale-95">
                    Xác nhận rút tiền
                </button>
            </div>
        </form>
    </div>
</div>



@push('scripts')
@vite(['resources/js/pages/seller-finances.js'])
@endpush
@endsection
