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
        $store = $user->store ?? Store::first();

        if (! $store) {
            return redirect()->route('seller.register');
        }

        $storeProductIds = Product::where('store_id', $store->id)->pluck('id');

        // Revenue & Orders query
        $ordersQuery = Order::whereHas('items', function ($q) use ($storeProductIds) {
            $q->whereIn('product_id', $storeProductIds);
        });

        $totalOrders = (clone $ordersQuery)->count();
        $pendingOrders = (clone $ordersQuery)->where('status', 'pending')->count();
        $processingOrders = (clone $ordersQuery)->where('status', 'processing')->count();
        $shippingOrders = (clone $ordersQuery)->where('status', 'shipping')->count();
        $completedOrders = (clone $ordersQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $refundedOrders = (clone $ordersQuery)->where('status', 'refunded')->count();

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

        // 7-day trend data for Chart.js directly from Database
        $chartLabels = [];
        $chartRevenues = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');

            $dayRevenue = OrderItem::whereIn('product_id', $storeProductIds)
                ->whereHas('order', fn ($q) => $q->whereDate('created_at', $date->toDateString())->where('status', '!=', 'cancelled'))
                ->sum('subtotal');

            $dayOrders = (clone $ordersQuery)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $chartRevenues[] = (float) $dayRevenue;
            $chartOrders[] = (int) $dayOrders;
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
