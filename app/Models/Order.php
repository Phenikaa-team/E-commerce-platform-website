<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'shipping_address' => 'array',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->total, 0, ',', '.').'₫',
        );
    }

    protected function formattedSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->subtotal, 0, ',', '.').'₫',
        );
    }

    protected function formattedShippingFee(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->shipping_fee, 0, ',', '.').'₫',
        );
    }

    protected function formattedDiscount(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->discount_amount, 0, ',', '.').'₫',
        );
    }

    /**
     * Human-friendly status label in Vietnamese.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ duyệt',
            'processing' => 'Chờ lấy hàng',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Chờ xử lý',
        };
    }

    /**
     * Badge CSS classes for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
            'shipping' => 'bg-purple-50 text-purple-700 border-purple-200',
            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    /**
     * Payment method label in Vietnamese.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'vnpay' => 'Cổng thanh toán VNPay',
            'momo' => 'Ví điện tử MoMo',
            default => 'Thanh toán trực tuyến',
        };
    }

    /**
     * Stepper stage index (1-4) for progress bar visual.
     */
    public function getTimelineStepAttribute(): int
    {
        return match ($this->status) {
            'pending' => 1,
            'processing' => 2,
            'shipping' => 3,
            'completed' => 4,
            'cancelled' => 0,
            default => 1,
        };
    }
}
