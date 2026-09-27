<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFinancial extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'gross_merchandise_amount' => 'decimal:2',
        'shop_discount' => 'decimal:2',
        'net_merchandise_amount' => 'decimal:2',
        'platform_discount' => 'decimal:2',
        'freeship_discount' => 'decimal:2',
        'points_discount' => 'decimal:2',
        'shipping_fee_paid' => 'decimal:2',
        'total_buyer_paid' => 'decimal:2',
        'payment_fee_rate' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'platform_voucher_shop_share_rate' => 'decimal:2',
        'platform_voucher_shop_share' => 'decimal:2',
        'shipping_cost_to_carrier' => 'decimal:2',
        'shop_earning' => 'decimal:2',
        'platform_gross_fee' => 'decimal:2',
        'platform_voucher_cost' => 'decimal:2',
        'platform_net_earning' => 'decimal:2',
        'cashback_points' => 'integer',
        'settled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
