<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ExcelExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderController extends Controller
{
    /**
     * Display all orders across the entire marketplace with filtering and search.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');
        $search = $request->input('q');
        $paymentMethod = $request->input('payment_method');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Status counts for tab navigation
        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        // Overall stats
        $todayOrdersCount = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::where('status', '!=', 'cancelled')->whereDate('created_at', today())->sum('total');

        $query = Order::with(['user', 'items.product.store'])->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product.store', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact(
            'orders',
            'status',
            'search',
            'paymentMethod',
            'dateFrom',
            'dateTo',
            'statusCounts',
            'todayOrdersCount',
            'todayRevenue'
        ));
    }

    /**
     * Show detailed order modal / page data.
     */
    public function show(int|string $id): JsonResponse|View
    {
        $order = Order::with(['user', 'items.product.store'])
            ->where('id', $id)
            ->orWhere('order_code', $id)
            ->firstOrFail();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $order,
            ]);
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Xuất toàn bộ đơn hàng toàn sàn ra file Excel định dạng cao cấp.
     */
    public function export(Request $request, ExcelExportService $excelService): StreamedResponse
    {
        $status = $request->input('status', 'all');
        $search = $request->input('q');
        $paymentMethod = $request->input('payment_method');

        $query = Order::with(['user', 'items.product.store'])->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product.store', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $orders = $query->get();

        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'processing' => 'Đã xác nhận',
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
            'Khách hàng',
            'Số điện thoại',
            'Địa chỉ giao hàng',
            'Gian hàng bán',
            'Sản phẩm',
            'Tiền hàng',
            'Giảm giá',
            'Phí ship',
            'Tổng thanh toán',
            'Phương thức TT',
            'Trạng thái TT',
            'Trạng thái đơn',
        ];

        $columnConfigs = [
            0 => ['type' => 'center', 'width' => 110],
            1 => ['type' => 'date', 'width' => 125],
            2 => ['type' => 'text', 'width' => 140],
            3 => ['type' => 'center', 'width' => 105],
            4 => ['type' => 'text', 'width' => 220],
            5 => ['type' => 'text', 'width' => 160],
            6 => ['type' => 'text', 'width' => 220],
            7 => ['type' => 'currency', 'width' => 105],
            8 => ['type' => 'currency', 'width' => 95],
            9 => ['type' => 'currency', 'width' => 90],
            10 => ['type' => 'currency', 'width' => 120],
            11 => ['type' => 'center', 'width' => 115],
            12 => ['type' => 'center', 'width' => 110],
            13 => ['type' => 'status', 'width' => 125],
        ];

        $rows = [];
        $totalSubtotal = 0;
        $totalDiscount = 0;
        $totalShipping = 0;
        $totalAmount = 0;

        foreach ($orders as $order) {
            $shipping = $order->shipping_address ?? [];
            $recipientName = $shipping['name'] ?? ($order->user->name ?? 'Khách vãng lai');
            $recipientPhone = $shipping['phone'] ?? ($order->user->phone ?? 'N/A');
            $recipientAddress = $shipping['address'] ?? 'N/A';

            $stores = $order->items->map(fn ($item) => $item->product->store->name ?? null)->filter()->unique()->implode(', ');
            if (empty($stores)) {
                $stores = 'N/A';
            }

            $productsSummary = $order->items->map(function ($item) {
                $name = $item->product->name ?? 'Sản phẩm';

                return "• {$name} (x{$item->quantity})";
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
                $stores,
                $productsSummary,
                $subtotal,
                $discount,
                $shippingFee,
                $total,
                $paymentLabels[$order->payment_method] ?? strtoupper($order->payment_method ?? 'COD'),
                $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán',
                $statusLabels[$order->status] ?? ucfirst($order->status),
            ];
        }

        $totals = [
            'TỔNG CỘNG ('.$orders->count().' đơn)',
            '',
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

        $filename = 'ShopMart_Admin_DonHang_'.now()->format('Ymd_His').'.xls';
        $title = 'BÁO CÁO TOÀN DIỆN ĐƠN HÀNG HỆ THỐNG - SHOPMART';
        $subtitle = "Tổng cộng: {$orders->count()} đơn hàng | Xuất ngày: ".now()->format('d/m/Y H:i:s').' bởi Ban Quản Trị';

        return $excelService->download($filename, $title, $headers, $rows, [
            'theme' => 'admin',
            'sheet_name' => 'Đơn hàng toàn sàn',
            'subtitle' => $subtitle,
            'columns' => $columnConfigs,
            'totals' => $totals,
        ]);
    }
}
