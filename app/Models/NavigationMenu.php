<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class NavigationMenu extends Model
{
    protected $table = 'navigation_menus';

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('shopmart_menu_ids_v1');
            Cache::forget('shopmart_flyout_configs_v1');
        });

        static::deleted(function () {
            Cache::forget('shopmart_menu_ids_v1');
            Cache::forget('shopmart_flyout_configs_v1');
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
