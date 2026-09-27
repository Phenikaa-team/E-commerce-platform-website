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

    /**
     * Calculate freeship discount amount.
     */
    public function calculateFreeshipDiscount(float $subtotal, float $shippingFee): float
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
            $disc = $shippingFee * ((float) $this->discount_value / 100);
            if ($this->max_discount_amount !== null && $disc > (float) $this->max_discount_amount) {
                $disc = (float) $this->max_discount_amount;
            }

            return min($shippingFee, round($disc, 2));
        }

        return min($shippingFee, (float) $this->discount_value);
    }

    /**
     * Find best coupon from a collection for a given amount.
     *
     * @param  iterable<Coupon>  $coupons
     * @return array{coupon: ?Coupon, discount: float}
     */
    public static function findBestCoupon($coupons, float $applicableAmount): array
    {
        $bestCoupon = null;
        $maxDiscount = 0.0;

        foreach ($coupons as $coupon) {
            $discount = (float) $coupon->calculateDiscount($applicableAmount);
            if ($discount > $maxDiscount) {
                $maxDiscount = $discount;
                $bestCoupon = $coupon;
            } elseif ($discount > 0 && $discount === $maxDiscount && $bestCoupon !== null) {
                if ((float) $coupon->min_order_value < (float) $bestCoupon->min_order_value) {
                    $bestCoupon = $coupon;
                }
            }
        }

        return [
            'coupon' => $bestCoupon,
            'discount' => $maxDiscount,
        ];
    }

    /**
     * Find best freeship coupon from a collection.
     *
     * @param  iterable<Coupon>  $coupons
     * @return array{coupon: ?Coupon, discount: float}
     */
    public static function findBestFreeshipCoupon($coupons, float $subtotal, float $shippingFee): array
    {
        $bestCoupon = null;
        $maxDiscount = 0.0;

        foreach ($coupons as $coupon) {
            $discount = (float) $coupon->calculateFreeshipDiscount($subtotal, $shippingFee);
            if ($discount > $maxDiscount) {
                $maxDiscount = $discount;
                $bestCoupon = $coupon;
            } elseif ($discount > 0 && $discount === $maxDiscount && $bestCoupon !== null) {
                if ((float) $coupon->min_order_value < (float) $bestCoupon->min_order_value) {
                    $bestCoupon = $coupon;
                }
            }
        }

        return [
            'coupon' => $bestCoupon,
            'discount' => $maxDiscount,
        ];
    }

    /**
     * Recommend optimal bundle of coupons for an order.
     */
    public static function recommendOptimalVouchers(
        float $subtotal,
        float $shippingFee,
        $freeshipCoupons,
        $platformCoupons,
        array $shopCouponsByStore = [],
        array $storeSubtotals = []
    ): array {
        $bestFs = self::findBestFreeshipCoupon($freeshipCoupons, $subtotal, $shippingFee);
        $bestPlat = self::findBestCoupon($platformCoupons, $subtotal);

        $bestShops = [];
        $totalShopDiscount = 0.0;
        foreach ($shopCouponsByStore as $storeId => $sCoupons) {
            $storeSub = (float) ($storeSubtotals[$storeId] ?? 0);
            $bestStoreC = self::findBestCoupon($sCoupons, $storeSub);
            if ($bestStoreC['coupon']) {
                $bestShops[$storeId] = $bestStoreC;
                $totalShopDiscount += $bestStoreC['discount'];
            }
        }

        $totalSavings = $bestFs['discount'] + $bestPlat['discount'] + $totalShopDiscount;

        $recommendedCodes = array_values(array_filter([
            $bestFs['coupon']?->code,
            $bestPlat['coupon']?->code,
            ...collect($bestShops)->pluck('coupon.code')->all(),
        ]));

        return [
            'freeship' => $bestFs,
            'platform' => $bestPlat,
            'shops' => $bestShops,
            'total_savings' => $totalSavings,
            'recommended_codes' => $recommendedCodes,
        ];
    }
}
