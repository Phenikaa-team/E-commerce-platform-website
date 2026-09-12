<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->is_active) {
            return 0.0;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 0.0;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 0.0;
        }

        if ($subtotal < (float) $this->min_order_value) {
            return 0.0;
        }

        if ($this->discount_type === 'percent') {
            $discount = $subtotal * ((float) $this->discount_value / 100);
            if ($this->max_discount_amount !== null && $discount > (float) $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }

            return round($discount, 2);
        }

        // Fixed discount
        return min($subtotal, (float) $this->discount_value);
    }
}
