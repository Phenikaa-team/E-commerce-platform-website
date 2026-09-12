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
}
