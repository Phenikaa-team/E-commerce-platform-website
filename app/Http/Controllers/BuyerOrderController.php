<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Services\FinancialSettlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    /**
     * Display buyer's order history filtered by status tabs or search code.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $query = Order::with(['items.product.store', 'store', 'reviews'])
            ->where('user_id', auth()->id())
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhereHas('items.product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
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

        return view('user.orders.index', compact('orders', 'status', 'counts', 'search'));
    }

    /**
     * Quick tracking endpoint for menu bar link.
     */
    public function track(Request $request): RedirectResponse
    {
        $orderCode = trim((string) $request->query('order_code', ''));

        if (! auth()->check()) {
            if (! empty($orderCode)) {
                return redirect()->route('login', ['redirect' => route('user.orders', ['search' => $orderCode])])
                    ->with('info', 'Vui lòng đăng nhập để theo dõi chi tiết đơn hàng #'.$orderCode);
            }

            return redirect()->route('login', ['redirect' => route('user.orders')])
                ->with('info', 'Vui lòng đăng nhập để xem và theo dõi danh sách đơn mua của bạn.');
        }

        if (! empty($orderCode)) {
            $existing = Order::where('user_id', auth()->id())
                ->where('order_code', $orderCode)
                ->first();

            if ($existing) {
                return redirect()->route('user.orders.show', $existing->order_code);
            }

            return redirect()->route('user.orders', ['search' => $orderCode]);
        }

        return redirect()->route('user.orders');
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
            app(FinancialSettlementService::class)->cancelOrder($order);

            // Restore product stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('sold_count', min($item->product->sold_count, $item->quantity));

                    if (! empty($item->selected_variant)) {
                        $variantModel = $item->product->productVariants()
                            ->where(function ($q) use ($item) {
                                $q->where('name', $item->selected_variant)
                                    ->orWhere('name', trim(str_replace(' | ', ' - ', $item->selected_variant)))
                                    ->orWhere('color', $item->selected_variant)
                                    ->orWhere('option', $item->selected_variant);
                            })->first();
                        if ($variantModel) {
                            $variantModel->increment('stock', $item->quantity);
                        }
                    }
                }
            }
        });

        return back()->with('success', 'Đã hủy đơn hàng thành công.');
    }

    /**
     * Buyer confirms delivery receipt, completing the order and releasing escrow funds to seller.
     */
    public function confirmReceipt(string $order_code): RedirectResponse
    {
        $order = Order::where('order_code', $order_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (! in_array($order->status, ['shipping', 'processing'])) {
            return back()->with('error', 'Chỉ có thể xác nhận đơn hàng đang giao hoặc đang xử lý.');
        }

        DB::transaction(function () use ($order) {
            $order->status = 'completed';
            if ($order->payment_method === 'cod') {
                $order->payment_status = 'paid';
            }
            $order->save();

            app(FinancialSettlementService::class)->settleOrder($order);
        });

        return back()->with('success', 'Đã xác nhận nhận hàng thành công! Xu thưởng đã được cộng vào ví của bạn.');
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
