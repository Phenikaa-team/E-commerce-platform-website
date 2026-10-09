<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class NavigationItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(NavigationSection::class, 'navigation_section_id');
    }
}
