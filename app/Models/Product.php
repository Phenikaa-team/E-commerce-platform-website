<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percent' => 'integer',
        'rating' => 'decimal:1',
        'reviews_count' => 'integer',
        'sold_count' => 'integer',
        'is_mall' => 'boolean',
        'is_flash_sale' => 'boolean',
        'flash_sale_percent' => 'integer',
        'specs' => 'array',
        'features' => 'array',
        'variants' => 'array',
        'faqs' => 'array',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->price, 0, ',', '.').'₫',
        );
    }

    protected function formattedOriginalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->original_price ? number_format((float) $this->original_price, 0, ',', '.').'₫' : null,
        );
    }

    protected function formattedSold(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->sold_count >= 1000) {
                    $val = round($this->sold_count / 1000, 1);

                    return rtrim(rtrim((string) $val, '0'), '.').'k';
                }

                return (string) $this->sold_count;
            },
        );
    }

    protected function formattedReviews(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->reviews_count >= 1000) {
                    $val = round($this->reviews_count / 1000, 1);

                    return rtrim(rtrim((string) $val, '0'), '.').'k';
                }

                return (string) $this->reviews_count;
            },
        );
    }

    /**
     * Get list of combined or individual selectable variants for dropdowns.
     */
    protected function availableVariants(): Attribute
    {
        return Attribute::make(
            get: function () {
                $variants = $this->variants ?? [];
                $colorLabels = [];
                if (! empty($variants['colors'])) {
                    foreach ($variants['colors'] as $c) {
                        if (is_array($c) && isset($c['label'])) {
                            $colorLabels[] = $c['label'];
                        } elseif (is_string($c)) {
                            $colorLabels[] = $c;
                        }
                    }
                }

                $options = [];
                if (! empty($variants['options'])) {
                    foreach ($variants['options'] as $opt) {
                        if (is_string($opt)) {
                            $options[] = $opt;
                        } elseif (is_array($opt) && isset($opt['name'])) {
                            $options[] = $opt['name'];
                        }
                    }
                }

                $combinations = [];
                if (! empty($colorLabels) && ! empty($options)) {
                    foreach ($colorLabels as $color) {
                        foreach ($options as $opt) {
                            $combinations[] = $color.' | '.$opt;
                        }
                    }
                } elseif (! empty($colorLabels)) {
                    $combinations = $colorLabels;
                } elseif (! empty($options)) {
                    $combinations = $options;
                }

                if (empty($combinations)) {
                    $combinations = [
                        'Bản Tiêu Chuẩn',
                        'Bản Nâng Cấp Cao Cấp',
                    ];
                }

                return $combinations;
            }
        );
    }
}
