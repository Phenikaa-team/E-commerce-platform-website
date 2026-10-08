<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $guarded = [];

    public function aliases(): HasMany
    {
        return $this->hasMany(BrandAlias::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
