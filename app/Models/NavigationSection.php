<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationSection extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'recommendation_enabled' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class)->orderBy('sort_order')->orderBy('id');
    }
}
