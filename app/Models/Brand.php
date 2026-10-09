<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $guarded = [];

    public function getLogoUrlAttribute(?string $value): ?string
    {
        return project_asset_value($value);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(BrandAlias::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
