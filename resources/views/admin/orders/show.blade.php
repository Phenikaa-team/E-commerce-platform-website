@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . ($order->order_code ?? $order->id) . ' - ShopMart Admin')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.index') }}" 
               class="w-9 h-9 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-600 transition-colors shadow-2xs"
               title="Quay lại danh sách đơn hàng">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Đơn hàng #{{ $order->order_code ?? $order->id }}</h1>
                    @php
                        $statusStyles = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'shipping' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                        $statusNames = [
                            'pending' => 'Chờ duyệt',
                            'processing' => 'Chờ lấy hàng',
                            'shipping' => 'Đang vận chuyển',
                            'completed' => 'Đã hoàn thành',
                            'delivered' => 'Đã giao hàng',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg border {{ $statusStyles[$order->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                        {{ $statusNames[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Đặt lúc {{ $order->created_at ? $order->created_at->format('d/m/Y H:i:s') : 'N/A' }} ({{ $order->created_at ? $order->created_at->diffForHumans() : '' }})</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="px-3.5 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>In thông tin đơn</span>
            </button>
        </div>
    </div>

    <!-- Stepper Status Overview -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-2xs">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Tiến trình thực hiện đơn</h3>
        @php
            $steps = [
                ['key' => 'pending', 'label' => 'Đặt đơn', 'icon' => 'cart'],
                ['key' => 'processing', 'label' => 'Xác nhận & Chuẩn bị', 'icon' => 'box'],
                ['key' => 'shipping', 'label' => 'Đang giao hàng', 'icon' => 'truck'],
                ['key' => 'completed', 'label' => 'Giao thành công', 'icon' => 'check'],
            ];
            $currentOrderIdx = match($order->status) {
                'pending' => 0,
                'processing' => 1,
                'shipping' => 2,
                'completed', 'delivered' => 3,
                default => -1,
            };
        @endphp

        @if($order->status === 'cancelled')
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold">✕</div>
                <div>
                    <div class="text-sm font-bold text-rose-800">Đơn hàng đã bị hủy</div>
                    <div class="text-xs text-rose-600">Đơn hàng này đã được đánh dấu hủy và không tiếp tục xử lý.</div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($steps as $idx => $step)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 transition-colors {{ $idx <= $currentOrderIdx ? 'bg-primary text-white shadow-xs' : 'bg-gray-100 text-gray-400' }}">
                            {{ $idx + 1 }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold {{ $idx <= $currentOrderIdx ? 'text-gray-900' : 'text-gray-400' }} truncate">{{ $step['label'] }}</div>
                            <div class="text-[10px] text-gray-400">{{ $idx < $currentOrderIdx ? 'Hoàn thành' : ($idx === $currentOrderIdx ? 'Đang thực hiện' : 'Chờ xử lý') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 3-Column Info Cards (Người mua, Người bán, Địa chỉ nhận) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Khách mua hàng -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Khách mua hàng</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">#{{ $order->user_id }}</span>
                </div>
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $order->user->avatar_url ?? asset('images/placeholders/avatar-placeholder.svg') }}" 
                         alt="{{ $order->user->name ?? 'Buyer' }}" 
                         class="w-10 h-10 rounded-full object-cover border border-gray-100 bg-gray-50">
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-gray-900 truncate">{{ $order->user->name ?? 'Khách vãng lai' }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $order->user->email ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="text-xs text-gray-600 space-y-1 pt-2 border-t border-gray-50">
                    <p><span class="text-gray-400">Số điện thoại:</span> {{ $order->user->phone ?? 'Chưa cập nhật' }}</p>
                    <p><span class="text-gray-400">Ngày tham gia:</span> {{ $order->user->created_at ? $order->user->created_at->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Gian hàng đối tác -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Gian hàng phụ trách</span>
                    @if($order->store && $order->store->is_mall)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-primary border border-rose-100">ShopMall</span>
                    @endif
                </div>
                @if($order->store)
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ $order->store->logo_url ?? project_asset('images/placeholders/store-logo-placeholder.svg') }}"
                             alt="{{ $order->store->name }}" 
                             class="w-10 h-10 rounded-xl object-cover border border-gray-100 bg-gray-50">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-900 truncate">{{ $order->store->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $order->store->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 space-y-1 pt-2 border-t border-gray-50">
                        <p><span class="text-gray-400">Địa chỉ shop:</span> {{ Str::limit($order->store->address ?? 'N/A', 35) }}</p>
                        <p><span class="text-gray-400">Trạng thái:</span> <span class="font-semibold text-emerald-600">Đang hoạt động</span></p>
                    </div>
                @else
                    <div class="text-sm font-semibold text-gray-400 py-3">Không có gian hàng liên kết cụ thể</div>
                @endif
            </div>
        </div>

        <!-- Địa chỉ nhận hàng & Thanh toán -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Địa chỉ giao hàng</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                </div>
                @php
                    $shipping = $order->shipping_address ?? [];
                    $recipientName = $shipping['name'] ?? ($order->user->name ?? 'Người nhận');
                    $recipientPhone = $shipping['phone'] ?? ($order->user->phone ?? 'N/A');
                    $recipientAddress = $shipping['address'] ?? 'N/A';
                @endphp
                <div class="text-xs text-gray-700 space-y-1.5">
                    <div class="font-bold text-sm text-gray-900">{{ $recipientName }}</div>
                    <div class="text-gray-600">📞 {{ $recipientPhone }}</div>
                    <div class="text-gray-500 leading-relaxed">📍 {{ $recipientAddress }}</div>
                    <div class="pt-2 border-t border-gray-50 text-gray-500">
                        Phương thức: <span class="font-semibold text-gray-800 uppercase">{{ $order->payment_method ?? 'COD' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong đơn -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Chi tiết kiện hàng ({{ $order->items->count() }} mặt hàng)</h2>
            @if($order->checkout_group_id)
                <span class="text-xs text-gray-400 font-mono">Mã nhóm: {{ $order->checkout_group_id }}</span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/70 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-6">Sản phẩm</th>
                        <th class="py-3.5 px-4 text-center">Đơn giá</th>
                        <th class="py-3.5 px-4 text-center">Số lượng</th>
                        <th class="py-3.5 px-6 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($order->items as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->product->main_image_url ?? asset('images/placeholders/product-placeholder.svg') }}" 
                                         alt="{{ $item->product_name ?? ($item->product->name ?? 'Product') }}" 
                                         class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0 bg-gray-50">
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 text-sm line-clamp-1">
                                            {{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm') }}
                                        </div>
                                        @if($item->variant_name)
                                            <div class="text-[11px] text-gray-400 mt-0.5">Phân loại: {{ $item->variant_name }}</div>
                                        @endif
                                        <div class="text-[10px] text-gray-400 mt-0.5 font-mono">Mã SP: #{{ $item->product_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center font-medium text-gray-700">
                                ₫ {{ number_format((float) ($item->unit_price ?? $item->price ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-center font-bold text-gray-900">
                                x{{ $item->quantity }}
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-gray-900">
                                ₫ {{ number_format((float) ($item->subtotal ?? (($item->unit_price ?? 0) * $item->quantity)), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">Không có dữ liệu mặt hàng.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Order Financial Summary -->
        <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-xs text-gray-500">
                Ghi chú: Đơn hàng thanh toán bằng phương thức <strong class="uppercase text-gray-800">{{ $order->payment_method ?? 'COD' }}</strong>.
            </div>

            <div class="w-full sm:w-80 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Tổng tiền hàng:</span>
                    <span class="font-bold text-gray-900">₫ {{ number_format((float) $order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Phí vận chuyển:</span>
                    <span class="font-bold text-gray-900">₫ {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}</span>
                </div>
                @if((float) $order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-medium">
                        <span>Giảm giá voucher:</span>
                        <span>- ₫ {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="pt-2 border-t border-gray-200 flex justify-between items-baseline">
                    <span class="text-sm font-black text-gray-900">Tổng thanh toán:</span>
                    <span class="text-lg font-black text-primary">₫ {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
