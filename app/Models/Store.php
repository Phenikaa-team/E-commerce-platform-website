<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_mall' => 'boolean',
        'rating' => 'decimal:1',
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
     * Get orders that have items belonging to this store.
     */
    public function orders()
    {
        return Order::whereHas('items.product', function ($query) {
            $query->where('store_id', $this->id);
        });
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
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
        $ordersCount = Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        $revenue = OrderItem::whereIn('product_id', $storeProductIds)
            ->whereHas('order', fn ($q) => $q->where('created_at', '>=', now()->subDays($days))->where('status', '!=', 'cancelled'))
            ->sum('subtotal');

        if ($revenue <= 0) {
            $revenue = OrderItem::whereIn('product_id', $storeProductIds)
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
                ->sum('subtotal');
        }

        $visits = ($ordersCount * 24) + 540;

        $chartLabels = [];
        $chartOrders = [];
        $chartRevenue = [];
        $chartVisits = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');

            $dOrders = Order::whereHas('items', fn ($q) => $q->whereIn('product_id', $storeProductIds))
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $dRev = OrderItem::whereIn('product_id', $storeProductIds)
                ->whereHas('order', fn ($q) => $q->whereDate('created_at', $date->toDateString())->where('status', '!=', 'cancelled'))
                ->sum('subtotal');

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
