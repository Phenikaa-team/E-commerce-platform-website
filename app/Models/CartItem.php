<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_selected' => 'boolean',
        'quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->quantity * $this->unit_price);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 0, ',', '.').'₫';
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 0, ',', '.').'₫';
    }
}
