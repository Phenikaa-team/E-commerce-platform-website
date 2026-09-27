<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Bán Hàng #{{ $order->order_code }} - ShopMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/pages/invoice.css'])
</head>
<body>

    <div class="actions-bar">
        <a href="{{ route('user.orders.show', $order->order_code) }}" class="btn btn-back">
            ← Quay lại đơn hàng
        </a>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ In hóa đơn / Lưu PDF
        </button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
                <div class="logo-icon">SM</div>
                <div>
                    <div class="brand-name">Shop<span>Mart</span></div>
                    <div style="font-size: 11px; color: #64748b;">Sàn Thương Mại Điện Tử Đa Gian Hàng</div>
                </div>
            </div>
            <div class="invoice-title">
                <h1>HÓA ĐƠN BÁN HÀNG</h1>
                <div class="order-code">#{{ $order->order_code }}</div>
                <div class="invoice-meta">Ngày lập: {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        @php
            $shipping = $order->shipping_address ?? [];
            $recipientName = $shipping['name'] ?? ($order->user->name ?? 'Người nhận');
            $recipientPhone = $shipping['phone'] ?? ($order->user->phone ?? 'N/A');
            $recipientAddress = $shipping['address'] ?? 'N/A';
        @endphp

        <!-- 2 Column Customer & Seller Info -->
        <div class="info-grid">
            <div class="info-card">
                <h3>ĐƠN VỊ CUNG CẤP</h3>
                <p><strong>{{ $order->store->name ?? 'Gian Hàng ShopMart Official' }}</strong></p>
                <p>Hotline: {{ $order->store->phone ?? '1900 8888' }}</p>
                <p>Địa chỉ: {{ $order->store->address ?? 'Hà Nội, Việt Nam' }}</p>
                <p>Nền tảng: Hệ thống TMĐT ShopMart</p>
            </div>
            <div class="info-card">
                <h3>KHÁCH HÀNG / NGƯỜI NHẬN</h3>
                <p><strong>{{ $recipientName }}</strong></p>
                <p>Số điện thoại: {{ $recipientPhone }}</p>
                <p>Địa chỉ nhận: {{ $recipientAddress }}</p>
                <p>Email: {{ $order->user->email ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">STT</th>
                    <th>Tên Sản Phẩm / Quy Cách</th>
                    <th class="text-center" style="width: 80px;">Số Lượng</th>
                    <th class="text-right" style="width: 120px;">Đơn Giá</th>
                    <th class="text-right" style="width: 130px;">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <div class="prod-name">{{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm') }}</div>
                            @if($item->variant_name)
                                <div class="prod-variant">Phân loại: {{ $item->variant_name }}</div>
                            @endif
                        </td>
                        <td class="text-center" style="font-weight: 700;">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format((float) ($item->unit_price ?? $item->price ?? 0), 0, ',', '.') }} đ</td>
                        <td class="text-right" style="font-weight: 700;">{{ number_format((float) ($item->subtotal ?? (($item->unit_price ?? 0) * $item->quantity)), 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary & Payment Info -->
        <div class="summary-section">
            <div class="payment-info">
                <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">Thông tin thanh toán:</div>
                <div>Phương thức: <strong>{{ strtoupper($order->payment_method ?? 'COD') }}</strong></div>
                <div>Trạng thái: <strong>{{ $order->payment_status === 'paid' ? 'Đã thanh toán thành công' : 'Thu tiền khi giao hàng (COD)' }}</strong></div>
                <div style="margin-top: 6px; font-size: 11px;">Cảm ơn quý khách đã mua sắm tại ShopMart! Vui lòng kiểm tra kỹ hàng hóa trước khi nhận.</div>
            </div>

            <div class="summary-table">
                <div class="summary-row">
                    <span>Cộng tiền hàng:</span>
                    <strong style="color: #0f172a;">{{ number_format((float) $order->subtotal, 0, ',', '.') }} đ</strong>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <strong style="color: #0f172a;">{{ number_format((float) $order->shipping_fee, 0, ',', '.') }} đ</strong>
                </div>
                @if((float) $order->discount_amount > 0)
                    <div class="summary-row" style="color: #16a34a;">
                        <span>Giảm giá voucher:</span>
                        <strong>- {{ number_format((float) $order->discount_amount, 0, ',', '.') }} đ</strong>
                    </div>
                @endif
                <div class="summary-row total">
                    <span>Tổng thanh toán:</span>
                    <span class="price">{{ number_format((float) $order->total, 0, ',', '.') }} đ</span>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <p>Hóa đơn điện tử khởi tạo tự động từ hệ thống ShopMart. Tra cứu đơn hàng trực tuyến tại https://shopmart.vn/user/orders/{{ $order->order_code }}</p>
        </div>
    </div>

</body>
</html>
