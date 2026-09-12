<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    /**
     * List all orders containing seller's products.
     */
    public function index(Request $request): View
    {
        $store = auth()->user()->store;
        $status = $request->query('status', 'all');

        $storeProductIds = Product::where('store_id', $store->id)->pluck('id');

        $query = Order::with(['items.product', 'user'])
            ->whereHas('items', function ($q) use ($storeProductIds) {
                $q->whereIn('product_id', $storeProductIds);
            })
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))->count(),
            'pending' => Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))->where('status', 'pending')->count(),
            'processing' => Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))->where('status', 'processing')->count(),
            'shipping' => Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))->where('status', 'shipping')->count(),
            'completed' => Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))->where('status', 'completed')->count(),
            'cancelled' => Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))->where('status', 'cancelled')->count(),
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
        $storeProductIds = Product::where('store_id', $store->id)->pluck('id');

        $order = Order::whereHas('items', function ($q) use ($storeProductIds) {
            $q->whereIn('product_id', $storeProductIds);
        })->findOrFail($id);

        $newStatus = $request->input('status');
        $order->status = $newStatus;

        // If marked completed, mark payment as paid if COD
        if ($newStatus === 'completed' && $order->payment_method === 'cod') {
            $order->payment_status = 'paid';
        }

        $order->save();

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng #'.$order->order_code.' thành công!');
    }
}
