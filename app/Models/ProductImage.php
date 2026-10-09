<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_video' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getUrlAttribute(?string $value): ?string
    {
        return project_asset_value($value);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
