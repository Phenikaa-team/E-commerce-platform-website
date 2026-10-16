<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductQuestion extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'customer_name',
        'question',
        'answer',
        'answered_at',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
