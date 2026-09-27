<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Đen Nhám - 128GB" or "Đen Nhám" or "128GB"
            $table->string('sku')->nullable();
            $table->string('color')->nullable();
            $table->string('option')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('original_price', 15, 2)->nullable();
            $table->integer('stock')->default(0); // Mỗi variant có số lượng trong kho riêng
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'name']);
        });

        // Seed / populate variants for existing products that have variants in JSON
        $products = DB::table('products')->whereNotNull('variants')->get();
        foreach ($products as $product) {
            $variants = json_decode($product->variants, true);
            if (! is_array($variants)) {
                continue;
            }

            $colors = [];
            if (! empty($variants['colors'])) {
                foreach ($variants['colors'] as $c) {
                    if (is_array($c) && ! empty($c['label'])) {
                        $colors[] = [
                            'label' => $c['label'],
                            'image' => $c['image'] ?? null,
                            'stock' => isset($c['stock']) ? (int) $c['stock'] : null,
                        ];
                    } elseif (is_string($c) && trim($c) !== '') {
                        $colors[] = [
                            'label' => trim($c),
                            'image' => null,
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
                            'stock' => isset($o['stock']) ? (int) $o['stock'] : null,
                        ];
                    } elseif (is_string($o) && trim($o) !== '') {
                        $options[] = [
                            'name' => trim($o),
                            'stock' => null,
                        ];
                    }
                }
            }

            $productStock = (int) ($product->stock ?? 100);

            if (! empty($colors) && ! empty($options)) {
                $count = count($colors) * count($options);
                $baseStock = max(10, intval($productStock / $count));
                foreach ($colors as $cIdx => $c) {
                    foreach ($options as $oIdx => $o) {
                        $variantStock = $c['stock'] ?? $o['stock'] ?? ($baseStock + (($cIdx * 7 + $oIdx * 3) % 15));
                        DB::table('product_variants')->insert([
                            'product_id' => $product->id,
                            'name' => $c['label'].' - '.$o['name'],
                            'sku' => 'SKU-'.$product->id.'-'.($cIdx + 1).($oIdx + 1),
                            'color' => $c['label'],
                            'option' => $o['name'],
                            'price' => $product->price,
                            'original_price' => $product->original_price,
                            'stock' => max(0, $variantStock),
                            'image_url' => $c['image'] ?? $product->main_image_url,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } elseif (! empty($colors)) {
                $count = count($colors);
                $baseStock = max(10, intval($productStock / $count));
                foreach ($colors as $cIdx => $c) {
                    $variantStock = $c['stock'] ?? ($baseStock + (($cIdx * 5) % 12));
                    DB::table('product_variants')->insert([
                        'product_id' => $product->id,
                        'name' => $c['label'],
                        'sku' => 'SKU-'.$product->id.'-C'.($cIdx + 1),
                        'color' => $c['label'],
                        'option' => null,
                        'price' => $product->price,
                        'original_price' => $product->original_price,
                        'stock' => max(0, $variantStock),
                        'image_url' => $c['image'] ?? $product->main_image_url,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } elseif (! empty($options)) {
                $count = count($options);
                $baseStock = max(10, intval($productStock / $count));
                foreach ($options as $oIdx => $o) {
                    $variantStock = $o['stock'] ?? ($baseStock + (($oIdx * 6) % 14));
                    DB::table('product_variants')->insert([
                        'product_id' => $product->id,
                        'name' => $o['name'],
                        'sku' => 'SKU-'.$product->id.'-O'.($oIdx + 1),
                        'color' => null,
                        'option' => $o['name'],
                        'price' => $product->price,
                        'original_price' => $product->original_price,
                        'stock' => max(0, $variantStock),
                        'image_url' => $product->main_image_url,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
