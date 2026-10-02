<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Store extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_mall' => 'boolean',
        'rating' => 'decimal:1',
        'registered_categories' => 'array',
        'registered_brands' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get orders belonging to this store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(StoreWallet::class);
    }

    public function getOrCreateWallet(): StoreWallet
    {
        return StoreWallet::firstOrCreate(
            ['store_id' => $this->id],
            [
                'balance' => 0.0,
                'pending_balance' => 0.0,
                'total_withdrawn' => 0.0,
                'total_earned' => 0.0,
            ]
        );
    }

    public function getBannerUrlAttribute(?string $value): string
    {
        return ! empty($value) ? $value : asset('images/placeholders/store-banner-placeholder.svg');
    }

    public function getLogoUrlAttribute(?string $value): string
    {
        return ! empty($value) ? $value : asset('images/placeholders/store-logo-placeholder.svg');
    }

    /**
     * Get performance overview metrics, trends, and recent records.
     *
     * @return array<string, mixed>
     */
    public function getPerformanceOverview(int $days = 30): array
    {
        $storeProductIds = $this->products()->pluck('id');

        $stats = [
            'total_products' => $this->products()->count(),
            'total_orders' => $this->orders()->count(),
            'rating' => (float) ($this->rating ?? 4.9),
            'followers' => $this->followers ?? '2.458',
            'response_rate' => $this->response_rate ?? '98%',
        ];

        // Period performance
        $ordersCount = $this->orders()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        $revenue = $this->orders()
            ->where('created_at', '>=', now()->subDays($days))
            ->where('status', '!=', 'cancelled')
            ->sum('subtotal');

        if ($revenue <= 0) {
            $revenue = $this->orders()
                ->where('status', '!=', 'cancelled')
                ->sum('subtotal');
        }

        $visits = ($ordersCount * 24) + 540;

        $chartLabels = [];
        $chartOrders = [];
        $chartRevenue = [];
        $chartVisits = [];

        $startDate = now()->subDays($days - 1)->startOfDay();
        $dailyRecords = $this->orders()
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE(created_at) as order_date, COUNT(*) as order_count, SUM(CASE WHEN status != 'cancelled' THEN subtotal ELSE 0 END) as day_revenue")
            ->groupBy('order_date')
            ->get()
            ->keyBy('order_date');

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->toDateString();
            $chartLabels[] = $date->format('d/m');

            $rec = $dailyRecords->get($dateStr);
            $dOrders = $rec ? (int) $rec->order_count : 0;
            $dRev = $rec ? (float) $rec->day_revenue : 0.0;

            $chartOrders[] = $dOrders;
            $chartRevenue[] = (float) $dRev;
            $chartVisits[] = $dOrders > 0 ? ($dOrders * 16 + 25) : 15;
        }

        $recentReviews = Review::whereIn('product_id', $storeProductIds)
            ->with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = $this->orders()
            ->with(['user', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        return [
            'stats' => $stats,
            'orders30d' => $ordersCount,
            'revenue30d' => $revenue,
            'visits30d' => $visits,
            'chartLabels' => $chartLabels,
            'chartOrders' => $chartOrders,
            'chartRevenue' => $chartRevenue,
            'chartVisits' => $chartVisits,
            'recentReviews' => $recentReviews,
            'recentOrders' => $recentOrders,
        ];
    }
}
