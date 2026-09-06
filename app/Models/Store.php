<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_mall' => 'boolean',
        'rating' => 'decimal:1',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
