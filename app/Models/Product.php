<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

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
        'ai_metadata' => 'array',
        'ai_analyzed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $product) {
            Cache::forget("product_id_slug_{$product->slug}");
            Cache::forget('shopmart_flash_sale_ids_v1');
            if ($product->category_id) {
                Cache::forget("product_same_cat_ids_{$product->category_id}_{$product->id}");
            }
        });

        static::deleted(function (self $product) {
            Cache::forget("product_id_slug_{$product->slug}");
            Cache::forget('shopmart_flash_sale_ids_v1');
            if ($product->category_id) {
                Cache::forget("product_same_cat_ids_{$product->category_id}_{$product->id}");
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function brandModel(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
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

    public function getMainImageUrlAttribute(?string $value): ?string
    {
        return project_asset_value($value);
    }

    public function getBannerImageUrlAttribute(?string $value): ?string
    {
        return project_asset_value($value);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
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

    /**
     * Sync ProductVariant records from product's variants attribute.
     */
    public function syncVariantsFromAttribute(): void
    {
        $variants = $this->variants;
        if (! is_array($variants)) {
            $this->productVariants()->delete();

            return;
        }

        $colors = [];
        if (! empty($variants['colors'])) {
            foreach ($variants['colors'] as $c) {
                if (is_array($c) && ! empty($c['label'])) {
                    $colors[] = [
                        'label' => $c['label'],
                        'image' => $c['image'] ?? null,
                        'price' => isset($c['price']) ? (float) $c['price'] : null,
                        'price_adjustment' => isset($c['price_adjustment']) ? (float) $c['price_adjustment'] : null,
                        'stock' => isset($c['stock']) ? (int) $c['stock'] : null,
                    ];
                } elseif (is_string($c) && trim($c) !== '') {
                    $colors[] = [
                        'label' => trim($c),
                        'image' => null,
                        'price' => null,
                        'price_adjustment' => null,
                        'stock' => null,
                    ];
                }
            }
        }

        $options = [];
        if (! empty($variants['options'])) {
            foreach ($variants['options'] as $o) {
                if (is_array($o) && ! empty($o['name'])) {
                    $options[] = [
                        'name' => $o['name'],
                        'price' => isset($o['price']) ? (float) $o['price'] : null,
                        'original_price' => isset($o['original_price']) ? (float) $o['original_price'] : null,
                        'stock' => isset($o['stock']) ? (int) $o['stock'] : null,
                    ];
                } elseif (is_string($o) && trim($o) !== '') {
                    $options[] = [
                        'name' => trim($o),
                        'price' => null,
                        'original_price' => null,
                        'stock' => null,
                    ];
                }
            }
        }

        // Check if explicit combination pricing matrix is provided in variants['combinations']
        $combinationsMap = collect($variants['combinations'] ?? [])->keyBy('name');

        if (empty($colors) && empty($options) && $combinationsMap->isEmpty()) {
            $this->productVariants()->delete();

            return;
        }

        $existingVariants = $this->productVariants()->get()->keyBy('name');
        $keepIds = [];
        $productStock = (int) ($this->stock ?? 100);

        if (! empty($colors) && ! empty($options)) {
            $count = count($colors) * count($options);
            $baseStock = max(1, intval($productStock / $count));
            foreach ($colors as $cIdx => $c) {
                foreach ($options as $oIdx => $o) {
                    $name = $c['label'].' - '.$o['name'];
                    $variant = $existingVariants->get($name);
                    $combo = $combinationsMap->get($name);

                    // Determine Price
                    $varPrice = $this->price;
                    if ($combo && isset($combo['price']) && $combo['price'] !== null && $combo['price'] !== '') {
                        $varPrice = (float) $combo['price'];
                    } elseif (isset($o['price']) && $o['price'] !== null) {
                        $varPrice = (float) $o['price'] + (float) ($c['price_adjustment'] ?? 0);
                    } elseif (isset($c['price']) && $c['price'] !== null) {
                        $varPrice = (float) $c['price'];
                    } elseif ($variant && $variant->price !== null) {
                        $varPrice = $variant->price;
                    }

                    // Determine Original Price
                    $varOriginalPrice = $this->original_price;
                    if ($combo && isset($combo['original_price']) && $combo['original_price'] !== null && $combo['original_price'] !== '') {
                        $varOriginalPrice = (float) $combo['original_price'];
                    } elseif (isset($o['original_price']) && $o['original_price'] !== null) {
                        $varOriginalPrice = (float) $o['original_price'] + (float) ($c['price_adjustment'] ?? 0);
                    } elseif ($variant && $variant->original_price !== null) {
                        $varOriginalPrice = $variant->original_price;
                    }

                    // Determine Stock
                    $stock = $combo['stock'] ?? $c['stock'] ?? $o['stock'] ?? ($variant ? $variant->stock : $baseStock);

                    $record = $this->productVariants()->updateOrCreate(
                        ['name' => $name],
                        [
                            'sku' => $combo['sku'] ?? ('SKU-'.$this->id.'-'.($cIdx + 1).($oIdx + 1)),
                            'color' => $c['label'],
                            'option' => $o['name'],
                            'price' => $varPrice,
                            'original_price' => $varOriginalPrice,
                            'stock' => max(0, (int) $stock),
                            'image_url' => $combo['image_url'] ?? ($c['image'] ?? $this->main_image_url),
                        ]
                    );
                    $keepIds[] = $record->id;
                }
            }
        } elseif (! empty($colors)) {
            $count = count($colors);
            $baseStock = max(1, intval($productStock / $count));
            foreach ($colors as $cIdx => $c) {
                $name = $c['label'];
                $variant = $existingVariants->get($name);
                $combo = $combinationsMap->get($name);

                $varPrice = $combo['price'] ?? $c['price'] ?? ($variant ? $variant->price : $this->price);
                $varOriginalPrice = $combo['original_price'] ?? ($variant ? $variant->original_price : $this->original_price);
                $stock = $combo['stock'] ?? $c['stock'] ?? ($variant ? $variant->stock : $baseStock);

                $record = $this->productVariants()->updateOrCreate(
                    ['name' => $name],
                    [
                        'sku' => $combo['sku'] ?? ('SKU-'.$this->id.'-C'.($cIdx + 1)),
                        'color' => $c['label'],
                        'option' => null,
                        'price' => $varPrice,
                        'original_price' => $varOriginalPrice,
                        'stock' => max(0, (int) $stock),
                        'image_url' => $combo['image_url'] ?? ($c['image'] ?? $this->main_image_url),
                    ]
                );
                $keepIds[] = $record->id;
            }
        } elseif (! empty($options)) {
            $count = count($options);
            $baseStock = max(1, intval($productStock / $count));
            foreach ($options as $oIdx => $o) {
                $name = $o['name'];
                $variant = $existingVariants->get($name);
                $combo = $combinationsMap->get($name);

                $varPrice = $combo['price'] ?? $o['price'] ?? ($variant ? $variant->price : $this->price);
                $varOriginalPrice = $combo['original_price'] ?? $o['original_price'] ?? ($variant ? $variant->original_price : $this->original_price);
                $stock = $combo['stock'] ?? $o['stock'] ?? ($variant ? $variant->stock : $baseStock);

                $record = $this->productVariants()->updateOrCreate(
                    ['name' => $name],
                    [
                        'sku' => $combo['sku'] ?? ('SKU-'.$this->id.'-O'.($oIdx + 1)),
                        'color' => null,
                        'option' => $o['name'],
                        'price' => $varPrice,
                        'original_price' => $varOriginalPrice,
                        'stock' => max(0, (int) $stock),
                        'image_url' => $combo['image_url'] ?? $this->main_image_url,
                    ]
                );
                $keepIds[] = $record->id;
            }
        }

        $this->productVariants()->whereNotIn('id', $keepIds)->delete();
    }
}
