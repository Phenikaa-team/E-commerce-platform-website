<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Bán Hàng #{{ $order->order_code }} - ShopMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            padding: 24px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            padding: 40px;
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid #f1f5f9;
        }
        .logo-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ef4444, #f43f5e);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 18px;
        }
        .brand-name {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
        .brand-name span { color: #ef4444; }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .order-code {
            font-size: 14px;
            font-weight: 700;
            color: #ef4444;
            font-family: monospace;
            margin-top: 4px;
        }
        .invoice-meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding: 24px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-card h3 {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .info-card p {
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
        }
        .info-card strong {
            font-weight: 700;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        .items-table th {
            background-color: #f8fafc;
            padding: 12px 14px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table th.text-center { text-align: center; }
        .items-table th.text-right { text-align: right; }
        .items-table td {
            padding: 14px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .items-table td.text-center { text-align: center; }
        .items-table td.text-right { text-align: right; }
        .items-table .prod-name {
            font-weight: 700;
            color: #0f172a;
        }
        .items-table .prod-variant {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .summary-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-top: 12px;
        }
        .payment-info {
            max-width: 320px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            background: #f8fafc;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }
        .summary-table {
            width: 280px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 0;
            color: #64748b;
        }
        .summary-row.total {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
            border-top: 2px solid #0f172a;
            padding-top: 10px;
            margin-top: 8px;
        }
        .summary-row.total .price {
            color: #ef4444;
        }
        .footer-note {
            text-align: center;
            padding-top: 36px;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            margin-top: 36px;
        }
        .actions-bar {
            max-width: 800px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-print {
            background-color: #ef4444;
            color: #ffffff;
        }
        .btn-back {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            .actions-bar {
                display: none !important;
            }
        }
    </style>
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
