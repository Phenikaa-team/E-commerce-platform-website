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

        $query = Order::with(['items.product.store', 'store', 'reviews'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(8)->withQueryString();

        // Count for status tab badges (Single grouped query)
        $statusCounts = Order::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        $counts = [
            'all' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'processing' => (int) ($statusCounts['processing'] ?? 0),
            'shipping' => (int) ($statusCounts['shipping'] ?? 0),
            'completed' => (int) ($statusCounts['completed'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        ];

        return view('user.orders.index', compact('orders', 'status', 'counts'));
    }

    /**
     * Display visual order detail with status stepper.
     */
    public function show(string $order_code): View
    {
        $order = Order::with(['items.product.store', 'store', 'reviews'])
            ->where('order_code', $order_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('user.orders.show', compact('order'));
    }

    /**
     * Display printable order invoice.
     */
    public function invoice(string $order_code): View
    {
        $order = Order::with(['items.product', 'store', 'user'])
            ->where('order_code', $order_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('user.orders.invoice', compact('order'));
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
        $orderQuery = Order::with('items')->where('order_code', $order_code);
        if (auth()->check()) {
            $orderQuery->where('user_id', auth()->id());
        }
        $order = $orderQuery->firstOrFail();

        if (auth()->check()) {
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        } else {
            $sessionId = $request->session()->getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        foreach ($order->items as $item) {
            $existing = $cart->items()
                ->where('product_id', $item->product_id)
                ->where('selected_variant', $item->selected_variant)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
                $existing->update([
                    'is_selected' => true,
                    'unit_price' => $item->unit_price,
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $item->product_id,
                    'selected_variant' => $item->selected_variant,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'is_selected' => true,
                ]);
            }
        }

        return redirect()->route('cart')->with('success', 'Đã thêm các sản phẩm từ đơn hàng vào giỏ của bạn!');
    }
}
