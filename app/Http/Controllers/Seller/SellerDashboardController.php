<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    /**
     * Display seller homepage with store profile, operational overview, and recent performance.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            if ($user->isSeller()) {
                $store = Store::create([
                    'user_id' => $user->id,
                    'name' => $user->name.' Store',
                    'slug' => Str::slug($user->name.'-store-'.uniqid()),
                    'description' => 'Gian hàng chính hãng trên ShopMart',
                    'status' => 'active',
                    'is_mall' => false,
                    'rating' => 5.0,
                    'response_rate' => '100%',
                    'online_status' => 'Đang hoạt động',
                ]);
            } else {
                return redirect()->route('seller.register');
            }
        }

        $overview = $store->getPerformanceOverview(30);

        return view('seller.dashboard', array_merge([
            'user' => $user,
            'store' => $store,
        ], $overview));
    }

    /**
     * Display seller revenue & operations analytics page.
     */
    public function revenue(): View|RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('seller.register')->with('info', 'Bạn chưa có gian hàng trên ShopMart. Hãy hoàn tất đăng ký để bắt đầu kinh doanh!');
        }

        $storeProductIds = Product::where('store_id', $store->id)->pluck('id');

        // Revenue & Orders query
        $ordersQuery = Order::whereHas('items', function ($q) use ($storeProductIds) {
            $q->whereIn('product_id', $storeProductIds);
        });

        // Status counts grouped into a single query
        $statusCounts = (clone $ordersQuery)
            ->selectRaw('status, count(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        $totalOrders = (clone $ordersQuery)->count();
        $pendingOrders = (int) ($statusCounts['pending'] ?? 0);
        $processingOrders = (int) ($statusCounts['processing'] ?? 0);
        $shippingOrders = (int) ($statusCounts['shipping'] ?? 0);
        $completedOrders = (int) ($statusCounts['completed'] ?? 0);
        $cancelledOrders = (int) ($statusCounts['cancelled'] ?? 0);
        $refundedOrders = (int) ($statusCounts['refunded'] ?? 0);

        // Calculate store revenue (sum of item subtotals for completed/shipping orders)
        $totalRevenue = OrderItem::whereIn('product_id', $storeProductIds)
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['completed', 'delivered', 'shipping']))
            ->sum('subtotal');

        if ($totalRevenue <= 0) {
            $totalRevenue = OrderItem::whereIn('product_id', $storeProductIds)
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
                ->sum('subtotal');
        }

        $todayRevenue = OrderItem::whereIn('product_id', $storeProductIds)
            ->whereHas('order', fn ($q) => $q->whereDate('created_at', now()->toDateString())->where('status', '!=', 'cancelled'))
            ->sum('subtotal');

        $monthRevenue = OrderItem::whereIn('product_id', $storeProductIds)
            ->whereHas('order', function ($q) {
                $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', '!=', 'cancelled');
            })
            ->sum('subtotal');

        // Products stats
        $totalProducts = Product::where('store_id', $store->id)->count();
        $lowStockProducts = Product::where('store_id', $store->id)->where('stock', '<=', 5)->count();

        // 7-day trend data for Chart.js directly from Database (single query aggregation)
        $sevenDaysStart = now()->subDays(6)->startOfDay();

        $dailyOrders = (clone $ordersQuery)
            ->where('created_at', '>=', $sevenDaysStart)
            ->selectRaw('DATE(created_at) as order_date, count(*) as aggregate_count')
            ->groupBy('order_date')
            ->pluck('aggregate_count', 'order_date');

        $dailyRevenue = OrderItem::whereIn('product_id', $storeProductIds)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', $sevenDaysStart)
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('DATE(orders.created_at) as order_date, sum(order_items.subtotal) as aggregate_revenue')
            ->groupBy('order_date')
            ->pluck('aggregate_revenue', 'order_date');

        $chartLabels = [];
        $chartRevenues = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->toDateString();
            $chartLabels[] = $date->format('d/m');
            $chartRevenues[] = (float) ($dailyRevenue[$dateStr] ?? 0);
            $chartOrders[] = (int) ($dailyOrders[$dateStr] ?? 0);
        }

        // Recent orders
        $recentOrders = (clone $ordersQuery)->with(['items.product', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Top products
        $topProducts = Product::where('store_id', $store->id)
            ->orderBy('sold_count', 'desc')
            ->take(5)
            ->get();

        return view('seller.revenue', compact(
            'store',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippingOrders',
            'completedOrders',
            'cancelledOrders',
            'refundedOrders',
            'totalRevenue',
            'todayRevenue',
            'monthRevenue',
            'totalProducts',
            'lowStockProducts',
            'chartLabels',
            'chartRevenues',
            'chartOrders',
            'recentOrders',
            'topProducts'
        ));
    }
}
