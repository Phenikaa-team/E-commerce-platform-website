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

        // 7 Days Revenue & Orders for Bar + Line Chart (Mockup 3 & 4)
        $sevenDaysLabels = [];
        $sevenDaysRevenue = [];
        $sevenDaysOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $sevenDaysLabels[] = $day->format('d/m');

            $dayRev = Order::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $day->toDateString())
                ->sum('total');

            $dayOrd = Order::whereDate('created_at', $day->toDateString())->count();

            $sevenDaysRevenue[] = (float) $dayRev;
            $sevenDaysOrders[] = $dayOrd;
        }

        // Order Status Distribution for Donut Chart (Real database counts)
        $statusCounts = [
            'completed' => Order::where('status', 'completed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'refunded' => Order::where('status', 'refunded')->count(),
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

        return view('admin.dashboard', compact(
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
