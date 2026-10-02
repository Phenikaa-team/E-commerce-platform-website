<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display platform-wide Admin Dashboard matching the design mockup.
     */
    public function index(): View
    {
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalStores = Store::count();
        $totalProducts = Product::count();
        $totalSoldUnits = OrderItem::sum('quantity');

        // 7 Days Revenue & Orders for Bar + Line Chart (Single query aggregation)
        $sevenDaysLabels = [];
        $sevenDaysRevenue = [];
        $sevenDaysOrders = [];

        $sevenDaysStart = now()->subDays(6)->startOfDay();
        $dailyData = Order::where('created_at', '>=', $sevenDaysStart)
            ->selectRaw("DATE(created_at) as order_date, COUNT(*) as order_count, SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END) as revenue")
            ->groupBy('order_date')
            ->get()
            ->keyBy('order_date');

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayStr = $day->toDateString();
            $sevenDaysLabels[] = $day->format('d/m');

            $rec = $dailyData->get($dayStr);
            $sevenDaysRevenue[] = $rec ? (float) $rec->revenue : 0.0;
            $sevenDaysOrders[] = $rec ? (int) $rec->order_count : 0;
        }

        // Order Status Distribution for Donut Chart (Single grouped query)
        $rawStatusCounts = Order::selectRaw('status, count(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        $statusCounts = [
            'completed' => (int) ($rawStatusCounts['completed'] ?? 0),
            'processing' => (int) ($rawStatusCounts['processing'] ?? 0),
            'shipping' => (int) ($rawStatusCounts['shipping'] ?? 0),
            'cancelled' => (int) ($rawStatusCounts['cancelled'] ?? 0),
            'refunded' => (int) ($rawStatusCounts['refunded'] ?? 0),
        ];
        $totalStatusOrders = max(1, array_sum($statusCounts));

        // Recent orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(6)
            ->get();

        // Top Selling Products
        $topProducts = Product::with('store')
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        // Admin info
        $admin = auth()->user();

        return view('admin.dashboard', compact(
            'admin',
            'totalRevenue',
            'totalOrders',
            'totalUsers',
            'totalStores',
            'totalProducts',
            'totalSoldUnits',
            'sevenDaysLabels',
            'sevenDaysRevenue',
            'sevenDaysOrders',
            'statusCounts',
            'totalStatusOrders',
            'recentOrders',
            'topProducts'
        ));
    }
}
