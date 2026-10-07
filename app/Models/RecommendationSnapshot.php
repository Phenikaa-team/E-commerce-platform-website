<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationSnapshot extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
