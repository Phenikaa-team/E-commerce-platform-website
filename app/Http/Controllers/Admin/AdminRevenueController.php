<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRevenueController extends Controller
{
    /**
     * Display comprehensive platform financial analytics and revenue breakdown.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', '30days'); // 7days, 30days, this_month, all
        $paymentMethod = $request->input('payment_method');

        $query = Order::query();

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        // Apply period filter for ledger list if specified
        $now = now();
        if ($period === '7days') {
            $startDate = $now->copy()->subDays(7);
        } elseif ($period === 'this_month') {
            $startDate = $now->copy()->startOfMonth();
        } elseif ($period === 'all') {
            $startDate = null;
        } else {
            // default: 30 days
            $startDate = $now->copy()->subDays(30);
        }

        // Overall GMV & Revenue (All non-cancelled orders)
        $totalGmv = Order::where('status', '!=', 'cancelled')->sum('total');
        $platformFeeRate = 0.05; // 5% platform commission
        $platformEarnings = $totalGmv * $platformFeeRate;
        $totalDiscountSponsored = Order::sum('discount_amount');
        $totalShippingFees = Order::sum('shipping_fee');
        $completedOrdersCount = Order::where('status', 'completed')->count();
        $averageOrderValue = $completedOrdersCount > 0 ? ($totalGmv / $completedOrdersCount) : 0;
        $cancelledAmount = Order::where('status', 'cancelled')->sum('total');

        // 30-Day Daily Chart Data
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];
        $daysCount = ($period === '7days') ? 7 : 30;

        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');

            $dayRev = Order::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total');
            $dayOrd = Order::whereDate('created_at', $date->toDateString())->count();

            $chartRevenue[] = (float) $dayRev;
            $chartOrders[] = $dayOrd;
        }

        // Revenue by Payment Method
        $paymentMethodsDistribution = [
            'cod' => Order::where('status', '!=', 'cancelled')->where('payment_method', 'cod')->sum('total'),
            'vnpay' => Order::where('status', '!=', 'cancelled')->where('payment_method', 'vnpay')->sum('total'),
            'momo' => Order::where('status', '!=', 'cancelled')->where('payment_method', 'momo')->sum('total'),
            'other' => Order::where('status', '!=', 'cancelled')->whereNotIn('payment_method', ['cod', 'vnpay', 'momo'])->sum('total'),
        ];

        // Top Revenue Generating Stores
        $topStores = Store::withCount(['products'])
            ->get()
            ->map(function ($store) use ($platformFeeRate) {
                // Calculate store revenue from their product orders
                $storeGmv = OrderItem::whereHas('product', fn ($q) => $q->where('store_id', $store->id))
                    ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
                    ->sum('subtotal');
                $ordersCount = OrderItem::whereHas('product', fn ($q) => $q->where('store_id', $store->id))
                    ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
                    ->distinct('order_id')
                    ->count('order_id');

                $store->gmv = (float) $storeGmv;
                $store->orders_count = $ordersCount;
                $store->platform_commission = $storeGmv * $platformFeeRate;
                $store->net_payout = $storeGmv * (1 - $platformFeeRate);

                return $store;
            })
            ->sortByDesc('gmv')
            ->take(5)
            ->values();

        // Transaction & Ledger Orders List with Pagination
        $transactionsQuery = Order::with(['user', 'items.product.store'])
            ->latest();

        if ($startDate) {
            $transactionsQuery->where('created_at', '>=', $startDate);
        }

        if ($paymentMethod) {
            $transactionsQuery->where('payment_method', $paymentMethod);
        }

        $transactions = $transactionsQuery->paginate(12)->withQueryString();

        return view('admin.revenue.index', compact(
            'period',
            'paymentMethod',
            'totalGmv',
            'platformFeeRate',
            'platformEarnings',
            'totalDiscountSponsored',
            'totalShippingFees',
            'averageOrderValue',
            'cancelledAmount',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'paymentMethodsDistribution',
            'topStores',
            'transactions'
        ));
    }
}
