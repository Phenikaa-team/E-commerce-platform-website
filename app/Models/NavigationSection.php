<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class NavigationSection extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'recommendation_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('shopmart_flyout_configs_v1');
        });

        static::deleted(function () {
            Cache::forget('shopmart_flyout_configs_v1');
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('sort_order')->orderBy('id');
    }
}
