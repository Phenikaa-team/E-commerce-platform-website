<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    /**
     * Display buyer's order history filtered by status tabs.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $query = Order::with(['items.product', 'reviews'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(8)->withQueryString();

        // Count for status tab badges
        $counts = [
            'all' => Order::where('user_id', auth()->id())->count(),
            'pending' => Order::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'processing' => Order::where('user_id', auth()->id())->where('status', 'processing')->count(),
            'shipping' => Order::where('user_id', auth()->id())->where('status', 'shipping')->count(),
            'completed' => Order::where('user_id', auth()->id())->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', auth()->id())->where('status', 'cancelled')->count(),
        ];

        return view('user.orders.index', compact('orders', 'status', 'counts'));
    }

    /**
     * Display visual order detail with status stepper.
     */
    public function show(string $order_code): View
    {
        $order = Order::with(['items.product.store', 'reviews'])
            ->where('order_code', $order_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('user.orders.show', compact('order'));
    }

    /**
     * Cancel a pending order and restore stock.
     */
    public function cancel(string $order_code): RedirectResponse
    {
        $order = Order::where('order_code', $order_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return back()->with('error', 'Đơn hàng đã được người bán xác nhận hoặc đang vận chuyển, không thể tự hủy.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // Restore product stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));
                }
            }
        });

        return back()->with('success', 'Đã hủy đơn hàng thành công.');
    }

    /**
     * Re-order items by pushing them into current cart.
     */
    public function reorder(Request $request, string $order_code): RedirectResponse
    {
        $order = Order::with('items')->where('order_code', $order_code)->firstOrFail();
        $sessionId = $request->session()->getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);

        foreach ($order->items as $item) {
            $cart->items()->updateOrCreate(
                [
                    'product_id' => $item->product_id,
                    'selected_variant' => $item->selected_variant,
                ],
                [
                    'quantity' => DB::raw("quantity + {$item->quantity}"),
                    'unit_price' => $item->unit_price,
                    'is_selected' => true,
                ]
            );
        }

        return redirect()->route('cart')->with('success', 'Đã thêm các sản phẩm từ đơn hàng vào giỏ của bạn!');
    }
}
