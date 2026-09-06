<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalItemsCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function getDisplayCountAttribute(): string
    {
        $count = $this->total_items_count;
        if ($count > 99) {
            return '99+';
        }

        return (string) $count;
    }

    public function getSelectedItemsAttribute()
    {
        return $this->items->filter(fn ($item) => $item->is_selected);
    }

    public function getSelectedCountAttribute(): int
    {
        return (int) $this->selected_items->sum('quantity');
    }

    public function getSelectedTotalAttribute(): float
    {
        return (float) $this->selected_items->sum(fn ($item) => $item->quantity * $item->unit_price);
    }

    public function getFormattedSelectedTotalAttribute(): string
    {
        return number_format($this->selected_total, 0, ',', '.').'₫';
    }

    public function getOriginalSelectedTotalAttribute(): float
    {
        return (float) $this->selected_items->sum(function ($item) {
            $origPrice = $item->product?->original_price ?? $item->unit_price;

            return $item->quantity * $origPrice;
        });
    }

    public function getFormattedOriginalSelectedTotalAttribute(): string
    {
        return number_format($this->original_selected_total, 0, ',', '.').'₫';
    }

    public function getSavingsTotalAttribute(): float
    {
        return max(0, $this->original_selected_total - $this->selected_total);
    }

    public function getSavingsPercentAttribute(): int
    {
        if ($this->original_selected_total > 0 && $this->savings_total > 0) {
            return (int) round(($this->savings_total / $this->original_selected_total) * 100);
        }

        return 0;
    }
}
