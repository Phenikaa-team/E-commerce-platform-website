@extends('layouts.admin')

@section('title', 'Giám Sát Đơn Hàng Toàn Sàn - ShopMart Admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Overview Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-6 bg-primary rounded-full inline-block"></span>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Giám Sát & Điều Phối Đơn Hàng</h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">Theo dõi thời gian thực mọi giao dịch phát sinh giữa người mua và các gian hàng trên toàn sàn</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.export', request()->query()) }}" 
               class="px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/70 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2"
               title="Xuất danh sách đơn hàng toàn sàn ra file Excel">
                <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="8" y1="13" x2="16" y2="13"></line>
                    <line x1="8" y1="17" x2="16" y2="17"></line>
                </svg>
                <span>Xuất Excel</span>
            </a>

            <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 text-xs shadow-2xs">
                <span class="text-gray-400 block text-[10px] uppercase font-bold">Đơn hôm nay</span>
                <span class="font-black text-gray-900">{{ number_format($todayOrdersCount) }} đơn</span>
            </div>
            <div class="px-4 py-2 bg-white rounded-xl border border-gray-200 text-xs shadow-2xs">
                <span class="text-gray-400 block text-[10px] uppercase font-bold">Doanh số hôm nay</span>
                <span class="font-black text-primary">₫ {{ number_format($todayRevenue, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Status Filter Tabs (Matching E-commerce standards) -->
    <div class="bg-white rounded-2xl p-2 border border-gray-100 shadow-xs flex items-center gap-1 overflow-x-auto text-xs font-semibold">
        <a href="{{ route('admin.orders.index', ['status' => 'all', 'q' => $search, 'payment_method' => $paymentMethod]) }}" class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 {{ $status === 'all' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>Tất cả đơn</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $status === 'all' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">{{ $statusCounts['all'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'pending', 'q' => $search, 'payment_method' => $paymentMethod]) }}" class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 {{ $status === 'pending' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>Chờ xác nhận</span>
            @if($statusCounts['pending'] > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-500 text-white">{{ $statusCounts['pending'] }}</span>
            @endif
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'processing', 'q' => $search, 'payment_method' => $paymentMethod]) }}" class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 {{ $status === 'processing' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>Chờ lấy hàng</span>
            @if($statusCounts['processing'] > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-500 text-white">{{ $statusCounts['processing'] }}</span>
            @endif
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'shipping', 'q' => $search, 'payment_method' => $paymentMethod]) }}" class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 {{ $status === 'shipping' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>Đang giao hàng</span>
            @if($statusCounts['shipping'] > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-purple-500 text-white">{{ $statusCounts['shipping'] }}</span>
            @endif
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'completed', 'q' => $search, 'payment_method' => $paymentMethod]) }}" class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 {{ $status === 'completed' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>Đã hoàn thành</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ $statusCounts['completed'] }}</span>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'cancelled', 'q' => $search, 'payment_method' => $paymentMethod]) }}" class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0 {{ $status === 'cancelled' ? 'bg-primary-light text-primary font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>Đã hủy</span>
            @if($statusCounts['cancelled'] > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">{{ $statusCounts['cancelled'] }}</span>
            @endif
        </a>
    </div>

    <!-- Search & Advanced Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 text-xs">
            <input type="hidden" name="status" value="{{ $status }}">

            <!-- Search Input (5 cols) -->
            <div class="lg:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <x-icon name="search" class="w-4 h-4" />
                </div>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Tìm theo mã đơn #SHM, tên khách, SĐT, tên gian hàng..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-primary focus:outline-hidden transition-all"
                >
            </div>

            <!-- Payment Method (3 cols) -->
            <div class="lg:col-span-3">
                <select name="payment_method" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-primary focus:outline-hidden">
                    <option value="">Phương thức thanh toán: Tất cả</option>
                    <option value="cod" {{ $paymentMethod === 'cod' ? 'selected' : '' }}>COD (Tiền mặt)</option>
                    <option value="vnpay" {{ $paymentMethod === 'vnpay' ? 'selected' : '' }}>VNPay</option>
                    <option value="momo" {{ $paymentMethod === 'momo' ? 'selected' : '' }}>MoMo</option>
                </select>
            </div>

            <!-- Date From (2 cols) -->
            <div class="lg:col-span-2">
                <input 
                    type="date" 
                    name="date_from" 
                    value="{{ $dateFrom }}" 
                    class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 focus:bg-white focus:border-primary focus:outline-hidden"
                    title="Từ ngày"
                >
            </div>

            <!-- Submit & Reset Buttons (2 cols) -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl shadow-xs transition-colors cursor-pointer text-center">
                    Lọc đơn
                </button>
                @if($search || $paymentMethod || $dateFrom || $dateTo || $status !== 'all')
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors cursor-pointer text-center" title="Đặt lại bộ lọc">
                        <x-icon name="close" class="w-4 h-4" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Master Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-400 uppercase text-[10px] font-bold">
                        <th class="py-3.5 px-4">Mã đơn & Ngày đặt</th>
                        <th class="py-3.5 px-4">Khách hàng</th>
                        <th class="py-3.5 px-4">Gian hàng cung ứng</th>
                        <th class="py-3.5 px-4">Chi tiết mặt hàng</th>
                        <th class="py-3.5 px-4">Tổng tiền & Thanh toán</th>
                        <th class="py-3.5 px-4">Trạng thái</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        @php
                            $firstItem = $order->items->first();
                            $store = $firstItem?->product?->store;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <!-- Mã đơn & Ngày đặt -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-900 text-xs">
                                    #{{ $order->order_code ?? ('SHM'.str_pad($order->id, 5, '0', STR_PAD_LEFT)) }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </div>
                            </td>

                            <!-- Khách hàng -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-800">{{ $order->user->name ?? 'Khách vãng lai' }}</div>
                                <div class="text-[11px] text-gray-500">{{ $order->user->phone ?? ($order->shipping_address['phone'] ?? 'Chưa có SĐT') }}</div>
                                @if(isset($order->shipping_address['city']))
                                    <div class="text-[10px] text-gray-400 truncate max-w-[140px]">{{ $order->shipping_address['city'] }}</div>
                                @endif
                            </td>

                            <!-- Gian hàng cung ứng -->
                            <td class="py-3.5 px-4">
                                @if($store)
                                    <a href="{{ route('store.show', $store->slug ?? $store->id) }}" target="_blank" class="font-bold text-gray-900 hover:text-primary transition-colors flex items-center gap-1.5">
                                        <x-icon name="store" class="w-3.5 h-3.5 text-gray-400" />
                                        <span>{{ $store->name }}</span>
                                    </a>
                                    <span class="text-[10px] text-gray-400">ID: #{{ $store->id }}</span>
                                @else
                                    <span class="text-gray-400 italic">Hệ thống ShopMart</span>
                                @endif
                            </td>

                            <!-- Chi tiết mặt hàng -->
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-800">
                                    {{ $firstItem ? Str::limit($firstItem->product_name, 28) : 'Sản phẩm' }}
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    @if($order->items->count() > 1)
                                        <span class="font-semibold text-gray-600">+ {{ $order->items->count() - 1 }} sản phẩm khác</span>
                                    @else
                                        <span>Số lượng: {{ $firstItem->quantity ?? 1 }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Tổng tiền & Thanh toán -->
                            <td class="py-3.5 px-4">
                                <div class="font-black text-sm text-gray-900">
                                    ₫ {{ number_format($order->total, 0, ',', '.') }}
                                </div>
                                <div class="mt-0.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 uppercase">
                                        {{ strtoupper($order->payment_method ?? 'COD') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Trạng thái -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $order->status_badge }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- Thao tác xem chi tiết -->
                            <td class="py-3.5 px-4 text-right">
                                <button 
                                    type="button" 
                                    onclick='viewOrderDetail(@json($order))'
                                    class="px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-200 hover:border-primary text-gray-700 hover:text-primary rounded-xl text-xs font-semibold shadow-2xs transition-colors cursor-pointer"
                                >
                                    Xem chi tiết
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-2">
                                    <x-icon name="bag" class="w-6 h-6" />
                                </div>
                                <p class="text-sm font-bold text-gray-700">Không tìm thấy đơn hàng nào</p>
                                <p class="text-xs text-gray-400 mt-0.5">Hãy thử thay đổi điều kiện lọc hoặc từ khóa tìm kiếm</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination with white design -->
        <div class="p-4 border-t border-gray-100 flex justify-center">
            {{ $orders->links() }}
        </div>
    </div>

</div>

<!-- Order Detail Modal -->
<div id="order-detail-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden animate-fade-in">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
            <div>
                <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                    <span>Chi Tiết Đơn Hàng</span>
                    <span id="modal-order-code" class="text-primary">#SHM00001</span>
                </h3>
                <p id="modal-order-date" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeOrderDetailModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                <x-icon name="close" class="w-5 h-5" />
            </button>
        </div>

        <div class="space-y-4 text-xs">
            <!-- Status Pill Banner -->
            <div class="p-3.5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                <span class="text-gray-500 font-semibold">Trạng thái đơn hàng:</span>
                <span id="modal-order-status" class="px-3 py-1 rounded-full text-xs font-bold border"></span>
            </div>

            <!-- Customer & Delivery Info Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-100">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[10px] mb-2">Thông Tin Người Nhận</h4>
                    <p id="modal-recipient-name" class="font-semibold text-gray-800"></p>
                    <p id="modal-recipient-phone" class="text-gray-600 mt-1"></p>
                    <p id="modal-recipient-address" class="text-gray-500 mt-1 text-[11px]"></p>
                </div>
                <div class="p-4 rounded-2xl bg-gray-50/70 border border-gray-100">
                    <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[10px] mb-2">Thanh Toán & Vận Chuyển</h4>
                    <p class="text-gray-600">Phương thức: <strong id="modal-payment-method" class="text-gray-800"></strong></p>
                    <p class="text-gray-600 mt-1">Trạng thái thanh toán: <span id="modal-payment-status" class="font-bold text-emerald-600"></span></p>
                    <p class="text-gray-500 mt-1 text-[11px]">Ghi chú: <span id="modal-order-notes">Không có</span></p>
                </div>
            </div>

            <!-- Items List -->
            <div>
                <h4 class="font-bold text-gray-900 uppercase tracking-wider text-[10px] mb-2">Danh Sách Mặt Hàng Trong Đơn</h4>
                <div id="modal-order-items" class="space-y-2 border border-gray-100 rounded-2xl p-3 bg-white"></div>
            </div>

            <!-- Financial Breakdown -->
            <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100/70 space-y-1.5 text-right">
                <div class="flex justify-between text-gray-600">
                    <span>Tạm tính tiền hàng:</span>
                    <span id="modal-subtotal" class="font-semibold text-gray-900">0₫</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Phí vận chuyển:</span>
                    <span id="modal-shipping-fee" class="font-semibold text-gray-900">0₫</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Giảm giá Voucher:</span>
                    <span id="modal-discount" class="font-semibold text-primary">-0₫</span>
                </div>
                <div class="flex justify-between text-sm font-black text-gray-900 pt-2 border-t border-rose-100">
                    <span>Tổng thanh toán:</span>
                    <span id="modal-total" class="text-primary">0₫</span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closeOrderDetailModal()" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Đóng
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewOrderDetail(order) {
    document.getElementById('modal-order-code').textContent = '#' + (order.order_code || ('SHM' + String(order.id).padStart(5, '0')));
    document.getElementById('modal-order-date').textContent = 'Thời gian đặt: ' + (order.created_at ? new Date(order.created_at).toLocaleString('vi-VN') : 'N/A');

    const statusBadge = document.getElementById('modal-order-status');
    statusBadge.textContent = order.status_label || order.status;
    statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold border ' + (order.status_badge || 'bg-gray-100 text-gray-700');

    // Recipient
    const addr = order.shipping_address || {};
    document.getElementById('modal-recipient-name').textContent = addr.recipient_name || (order.user ? order.user.name : 'Khách Mua');
    document.getElementById('modal-recipient-phone').textContent = addr.phone || (order.user ? order.user.phone : 'Chưa có SĐT');
    document.getElementById('modal-recipient-address').textContent = [addr.address_line, addr.ward, addr.district, addr.city].filter(Boolean).join(', ') || 'Chưa cung cấp địa chỉ';

    // Payment
    document.getElementById('modal-payment-method').textContent = (order.payment_method || 'cod').toUpperCase();
    document.getElementById('modal-payment-status').textContent = order.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán (Thu khi giao)';
    document.getElementById('modal-order-notes').textContent = order.notes || 'Không có ghi chú';

    // Totals
    const fmt = (n) => new Intl.NumberFormat('vi-VN').format(Math.round(n || 0)) + '₫';
    document.getElementById('modal-subtotal').textContent = fmt(order.subtotal || order.total);
    document.getElementById('modal-shipping-fee').textContent = fmt(order.shipping_fee || 0);
    document.getElementById('modal-discount').textContent = '-' + fmt(order.discount_amount || 0);
    document.getElementById('modal-total').textContent = fmt(order.total || 0);

    // Items list
    const itemsBox = document.getElementById('modal-order-items');
    itemsBox.innerHTML = '';
    if (order.items && order.items.length) {
        order.items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between py-2 border-b border-gray-50 last:border-0';
            div.innerHTML = `
                <div class="min-w-0 pr-3">
                    <p class="font-bold text-gray-800 truncate">${item.product_name}</p>
                    <p class="text-[11px] text-gray-400">${item.selected_variant ? 'Phân loại: ' + item.selected_variant : ''} Số lượng: ${item.quantity}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-bold text-gray-900">${fmt(item.subtotal || (item.unit_price * item.quantity))}</p>
                    <p class="text-[10px] text-gray-400">Đơn giá: ${fmt(item.unit_price)}</p>
                </div>
            `;
            itemsBox.appendChild(div);
        });
    } else {
        itemsBox.innerHTML = '<p class="text-gray-400 py-2">Không có chi tiết sản phẩm</p>';
    }

    document.getElementById('order-detail-modal').classList.remove('hidden');
}

function closeOrderDetailModal() {
    document.getElementById('order-detail-modal').classList.add('hidden');
}
</script>
@endpush
