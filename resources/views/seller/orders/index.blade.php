@extends('layouts.seller')

@section('title', 'Quản Lý Đơn Hàng - ShopMart Seller')
@section('page_title', 'Quản Lý & Xử Lý Đơn Hàng Của Shop')

@section('content')
<div class="space-y-6">

    <!-- Status Tabs -->
    <div class="bg-white rounded-2xl p-1.5 border border-gray-100 shadow-xs flex gap-1 overflow-x-auto">
        @php
            $tabs = [
                'all' => 'Tất cả đơn',
                'pending' => 'Chờ duyệt',
                'processing' => 'Chờ lấy hàng',
                'shipping' => 'Đang giao',
                'completed' => 'Đã giao',
                'cancelled' => 'Đã hủy',
            ];
        @endphp

        @foreach($tabs as $k => $label)
            <a href="{{ route('seller.orders.index', ['status' => $k]) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $status === $k ? 'bg-[#ea384c] text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span>{{ $label }}</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === $k ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-600' }}">
                    {{ $counts[$k] ?? 0 }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center text-gray-400 text-xs">
                Không có đơn hàng nào trong danh mục này.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50/75 text-gray-500 font-bold border-b border-gray-100">
                            <th class="py-3.5 px-6">Mã đơn & Ngày đặt</th>
                            <th class="py-3.5 px-4">Khách hàng & Địa chỉ</th>
                            <th class="py-3.5 px-4">Sản phẩm</th>
                            <th class="py-3.5 px-4">Tổng thu</th>
                            <th class="py-3.5 px-4">Trạng thái</th>
                            <th class="py-3.5 px-6 text-right">Xử lý vận chuyển</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-6">
                                    <span class="font-extrabold text-[#ea384c] block text-xs">{{ $order->order_code }}</span>
                                    <span class="text-[11px] text-gray-400">{{ $order->created_at->format('H:i - d/m/Y') }}</span>
                                </td>

                                <td class="py-3.5 px-4 max-w-[220px]">
                                    <p class="font-bold text-gray-900">{{ $order->shipping_address['name'] ?? 'Khách hàng' }} ({{ $order->shipping_address['phone'] ?? '' }})</p>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $order->shipping_address['address'] ?? '' }}</p>
                                </td>

                                <td class="py-3.5 px-4 max-w-[240px]">
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                            <div class="text-[11px] text-gray-700 truncate">
                                                <span class="font-bold">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                                @if($item->selected_variant)
                                                    <span class="text-gray-400">({{ $item->selected_variant }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-gray-900 block">{{ $order->formatted_total }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $order->payment_method_label }}</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $order->status_badge }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @php
                                            $slipData = [
                                                'code' => $order->order_code,
                                                'date' => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                                                'customer' => is_array($order->shipping_address) ? ($order->shipping_address['name'] ?? 'Khách hàng') : 'Khách hàng',
                                                'phone' => is_array($order->shipping_address) ? ($order->shipping_address['phone'] ?? '') : '',
                                                'address' => is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? '') : '',
                                                'store' => auth()->user()->store->name ?? 'ShopMart Seller',
                                                'store_phone' => auth()->user()->store->phone ?? auth()->user()->phone ?? '1900 8888',
                                                'store_address' => auth()->user()->store->address ?? 'Kho trung tâm ShopMart',
                                                'items' => $order->items->map(function($i) {
                                                    return [
                                                        'name' => $i->product_name,
                                                        'qty' => $i->quantity,
                                                        'variant' => $i->selected_variant,
                                                        'price' => number_format((float)$i->unit_price, 0, ',', '.') . '₫',
                                                    ];
                                                })->values()->all(),
                                                'total' => $order->formatted_total,
                                                'payment_method' => $order->payment_method_label,
                                            ];
                                        @endphp
                                        <button 
                                            type="button" 
                                            onclick="openSlipModal(JSON.parse(this.dataset.slip))"
                                            data-slip="{{ json_encode($slipData) }}"
                                            class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[11px] font-bold transition-colors cursor-pointer flex items-center gap-1"
                                            title="In Phiếu Đóng Gói / Vận Đơn"
                                        >
                                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>In vận đơn</span>
                                        </button>

                                        <form action="{{ route('seller.orders.status', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            @if($order->status === 'pending')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-bold shadow-xs cursor-pointer">
                                                    Xác nhận đơn
                                                </button>
                                            @elseif($order->status === 'processing')
                                                <input type="hidden" name="status" value="shipping">
                                                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold shadow-xs cursor-pointer">
                                                    Giao cho shipper
                                                </button>
                                            @elseif($order->status === 'shipping')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold shadow-xs cursor-pointer">
                                                    Xác nhận đã giao
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-[11px]">Đã kết thúc</span>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Printable Shipping Label / Packing Slip Modal -->
<div id="shipping-slip-modal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-gray-100 overflow-hidden my-8 p-6 text-gray-900" id="printable-slip-content">
        
        <!-- Header: Barcode & Brand -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-gray-900">
            <div>
                <h2 class="text-lg font-black tracking-tight">Shop<span class="text-[#ea384c]">Mart</span> Express</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Phiếu Giao Hàng & Đóng Gói Tiêu Chuẩn</p>
            </div>
            <div class="text-right">
                <p class="font-mono text-xs font-black" id="slip-order-code">---</p>
                <p class="text-[10px] text-gray-400" id="slip-order-date">---</p>
            </div>
        </div>

        <!-- Sender and Receiver Grid -->
        <div class="grid grid-cols-2 gap-4 py-4 text-xs border-b border-gray-200">
            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Người gửi (Shop):</span>
                <p class="font-bold text-gray-900" id="slip-store-name">---</p>
                <p class="text-gray-600 text-[11px]" id="slip-store-phone">---</p>
                <p class="text-gray-500 text-[11px] leading-tight" id="slip-store-address">---</p>
            </div>
            <div class="space-y-1 border-l border-gray-200 pl-4">
                <span class="text-[10px] uppercase font-bold text-gray-400 block">Người nhận (Khách hàng):</span>
                <p class="font-bold text-gray-900" id="slip-customer-name">---</p>
                <p class="text-gray-600 text-[11px]" id="slip-customer-phone">---</p>
                <p class="text-gray-500 text-[11px] leading-tight" id="slip-customer-address">---</p>
            </div>
        </div>

        <!-- Items Checklist -->
        <div class="py-4">
            <h4 class="text-xs font-black uppercase text-gray-700 mb-2">Danh sách sản phẩm đóng gói:</h4>
            <div id="slip-items-list" class="space-y-1.5 text-xs"></div>
        </div>

        <!-- Summary and Signature -->
        <div class="pt-3 border-t-2 border-gray-900 flex items-center justify-between text-xs">
            <div>
                <span class="text-[10px] text-gray-400 block">Phương thức thanh toán:</span>
                <span class="font-bold text-gray-800" id="slip-payment-method">---</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-gray-400 block">Tiền thu người nhận (COD/Tổng):</span>
                <span class="text-base font-black text-rose-600" id="slip-total-amount">---</span>
            </div>
        </div>

        <!-- Footer Actions (Hidden on Print) -->
        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between no-print">
            <button type="button" onclick="closeSlipModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold cursor-pointer transition-colors">
                Đóng
            </button>
            <button type="button" onclick="window.print()" class="px-5 py-2 bg-[#ea384c] hover:bg-[#d3273b] text-white rounded-xl text-xs font-bold cursor-pointer transition-all shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>In Phiếu Vận Đơn</span>
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSlipModal(data) {
        document.getElementById('slip-order-code').textContent = data.code;
        document.getElementById('slip-order-date').textContent = data.date;
        document.getElementById('slip-store-name').textContent = data.store;
        document.getElementById('slip-store-phone').textContent = data.store_phone;
        document.getElementById('slip-store-address').textContent = data.store_address;
        document.getElementById('slip-customer-name').textContent = data.customer;
        document.getElementById('slip-customer-phone').textContent = data.phone;
        document.getElementById('slip-customer-address').textContent = data.address;
        document.getElementById('slip-payment-method').textContent = data.payment_method;
        document.getElementById('slip-total-amount').textContent = data.total;

        const itemsList = document.getElementById('slip-items-list');
        itemsList.innerHTML = '';
        data.items.forEach((item, index) => {
            itemsList.innerHTML += `
                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border border-gray-300 flex items-center justify-center text-gray-500">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </span>
                        <div>
                            <span class="font-bold text-gray-900">${item.qty}x ${item.name}</span>
                            ${item.variant ? `<span class="text-gray-400 text-[10px] block">${item.variant}</span>` : ''}
                        </div>
                    </div>
                    <span class="font-bold text-gray-800">${item.price}</span>
                </div>
            `;
        });

        document.getElementById('shipping-slip-modal').classList.remove('hidden');
    }

    function closeSlipModal() {
        document.getElementById('shipping-slip-modal').classList.add('hidden');
    }
</script>
@endpush

