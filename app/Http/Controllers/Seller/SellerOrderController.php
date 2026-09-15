<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ExcelExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerOrderController extends Controller
{
    /**
     * List all orders containing seller's products.
     */
    public function index(Request $request): View
    {
        $store = auth()->user()->store;
        $status = $request->query('status', 'all');

        $query = Order::with(['items.product', 'user'])
            ->where('store_id', $store->id)
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Order::where('store_id', $store->id)->count(),
            'pending' => Order::where('store_id', $store->id)->where('status', 'pending')->count(),
            'processing' => Order::where('store_id', $store->id)->where('status', 'processing')->count(),
            'shipping' => Order::where('store_id', $store->id)->where('status', 'shipping')->count(),
            'completed' => Order::where('store_id', $store->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('store_id', $store->id)->where('status', 'cancelled')->count(),
        ];

        return view('seller.orders.index', compact('orders', 'status', 'counts', 'store'));
    }

    /**
     * Update order shipment status.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:processing,shipping,completed,cancelled',
        ]);

        $store = auth()->user()->store;

        $order = Order::where('store_id', $store->id)->findOrFail($id);

        $newStatus = $request->input('status');
        $order->status = $newStatus;

        // If marked completed, mark payment as paid if COD
        if ($newStatus === 'completed' && $order->payment_method === 'cod') {
            $order->payment_status = 'paid';
        }

        $order->save();

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng #'.$order->order_code.' thành công!');
    }

    /**
     * Xuất danh sách đơn hàng của shop ra file Excel định dạng cao cấp.
     */
    public function export(Request $request, ExcelExportService $excelService): StreamedResponse
    {
        $store = auth()->user()->store;
        $status = $request->query('status', 'all');

        $query = Order::with(['items.product', 'user'])
            ->where('store_id', $store->id)
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->get();

        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'processing' => 'Chờ lấy hàng',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao thành công',
            'cancelled' => 'Đã hủy',
        ];

        $paymentLabels = [
            'cod' => 'COD (Tiền mặt)',
            'vnpay' => 'VNPAY QR',
            'wallet' => 'Ví ShopMart',
        ];

        $headers = [
            'Mã đơn hàng',
            'Thời gian đặt',
            'Người nhận',
            'Số điện thoại',
            'Địa chỉ giao hàng',
            'Sản phẩm đặt mua',
            'Tiền hàng',
            'Giảm giá',
            'Phí ship',
            'Tổng thu',
            'Phương thức TT',
            'Trạng thái đơn',
            'Ghi chú',
        ];

        $columnConfigs = [
            0 => ['type' => 'center', 'width' => 110],
            1 => ['type' => 'date', 'width' => 125],
            2 => ['type' => 'text', 'width' => 130],
            3 => ['type' => 'center', 'width' => 105],
            4 => ['type' => 'text', 'width' => 240],
            5 => ['type' => 'text', 'width' => 220],
            6 => ['type' => 'currency', 'width' => 105],
            7 => ['type' => 'currency', 'width' => 95],
            8 => ['type' => 'currency', 'width' => 90],
            9 => ['type' => 'currency', 'width' => 115],
            10 => ['type' => 'center', 'width' => 115],
            11 => ['type' => 'status', 'width' => 125],
            12 => ['type' => 'text', 'width' => 150],
        ];

        $rows = [];
        $totalSubtotal = 0;
        $totalDiscount = 0;
        $totalShipping = 0;
        $totalAmount = 0;

        foreach ($orders as $order) {
            $shipping = $order->shipping_address ?? [];
            $recipientName = $shipping['name'] ?? ($order->user->name ?? 'Khách hàng');
            $recipientPhone = $shipping['phone'] ?? ($order->user->phone ?? 'N/A');
            $recipientAddress = $shipping['address'] ?? 'N/A';

            // Lọc các sản phẩm thuộc store này
            $storeItems = $order->items->whereIn('product_id', $storeProductIds);
            $productSummary = $storeItems->map(function ($item) {
                $name = $item->product_name ?? ($item->product->name ?? 'Sản phẩm');
                $unitPrice = (float) ($item->unit_price ?? $item->price ?? 0);
                $price = number_format($unitPrice, 0, ',', '.').'đ';

                return "• {$name} (SL: {$item->quantity} x {$price})";
            })->implode("\n");

            $subtotal = (float) $order->subtotal;
            $discount = (float) $order->discount_amount;
            $shippingFee = (float) $order->shipping_fee;
            $total = (float) $order->total;

            $totalSubtotal += $subtotal;
            $totalDiscount += $discount;
            $totalShipping += $shippingFee;
            $totalAmount += $total;

            $rows[] = [
                $order->order_code ?? ('#'.$order->id),
                $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                $recipientName,
                $recipientPhone,
                $recipientAddress,
                $productSummary,
                $subtotal,
                $discount,
                $shippingFee,
                $total,
                $paymentLabels[$order->payment_method] ?? strtoupper($order->payment_method ?? 'COD'),
                $statusLabels[$order->status] ?? ucfirst($order->status),
                $order->notes ?? '',
            ];
        }

        $totals = [
            'TỔNG CỘNG ('.$orders->count().' đơn)',
            '',
            '',
            '',
            '',
            '',
            $totalSubtotal,
            $totalDiscount,
            $totalShipping,
            $totalAmount,
            '',
            '',
            '',
        ];

        $storeNameClean = Str::slug($store->name ?? 'Store');
        $filename = "ShopMart_DonHang_{$storeNameClean}_".now()->format('Ymd_His').'.xls';
        $title = 'BÁO CÁO CHI TIẾT ĐƠN HÀNG - '.mb_strtoupper($store->name ?? 'Gian hàng');
        $statusDesc = ($status && $status !== 'all') ? ($statusLabels[$status] ?? $status) : 'Tất cả trạng thái';
        $subtitle = "Bộ lọc: {$statusDesc} | Tổng: {$orders->count()} đơn | Ngày xuất: ".now()->format('d/m/Y H:i:s');

        return $excelService->download($filename, $title, $headers, $rows, [
            'theme' => 'seller',
            'sheet_name' => 'Đơn hàng',
            'subtitle' => $subtitle,
            'columns' => $columnConfigs,
            'totals' => $totals,
        ]);
    }
}
