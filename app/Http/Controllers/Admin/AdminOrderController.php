<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
