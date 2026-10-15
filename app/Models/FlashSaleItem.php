<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'flash_sale_price' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(FlashSaleCampaign::class, 'flash_sale_campaign_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
