<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing product_variants with tiered pricing based on option/capacity/limited edition
        $variants = DB::table('product_variants')->get();

        foreach ($variants as $v) {
            $product = DB::table('products')->where('id', $v->product_id)->first();
            if (! $product) {
                continue;
            }

            $basePrice = (float) $product->price;
            $baseOriginal = $product->original_price ? (float) $product->original_price : ($basePrice * 1.2);
            $price = $basePrice;
            $originalPrice = $baseOriginal;

            $opt = mb_strtolower($v->option ?? '');
            $color = mb_strtolower($v->color ?? '');
            $name = mb_strtolower($v->name ?? '');

            // Option / Capacity based tiered pricing
            if (str_contains($opt, '1tb') || str_contains($name, '1tb')) {
                $price += 11000000;
                $originalPrice += 12000000;
            } elseif (str_contains($opt, '512gb') || str_contains($name, '512gb')) {
                $price += 5500000;
                $originalPrice += 6000000;
            } elseif (str_contains($opt, '256gb') || str_contains($name, '256gb')) {
                // If base is 128GB or 256GB
                if (str_contains($product->name, '128GB') || str_contains(json_encode($product->variants), '128GB')) {
                    $price += 2500000;
                    $originalPrice += 3000000;
                }
            } elseif (str_contains($opt, '64gb') || str_contains($name, '64gb')) {
                $price = max(100000, $price - 1500000);
                $originalPrice = max(100000, $originalPrice - 1500000);
            } elseif (str_contains($opt, '32gb') || str_contains($opt, '16gb ram') || str_contains($opt, '32gb ram')) {
                $price += 6000000;
                $originalPrice += 7000000;
            } elseif (str_contains($opt, 'xxl') || str_contains($opt, '2xl')) {
                $price += 30000;
                $originalPrice += 30000;
            } elseif (str_contains($opt, 'xl')) {
                $price += 15000;
                $originalPrice += 15000;
            }

            // Limited edition / Special color based pricing
            if (str_contains($color, 'giới hạn') || str_contains($color, 'limited') || str_contains($color, 'sa mạc') || str_contains($color, 'special')) {
                $price += 800000;
                $originalPrice += 1000000;
            }

            DB::table('product_variants')->where('id', $v->id)->update([
                'price' => $price,
                'original_price' => $originalPrice,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to product's base price
        $products = DB::table('products')->get();
        foreach ($products as $p) {
            DB::table('product_variants')->where('product_id', $p->id)->update([
                'price' => $p->price,
                'original_price' => $p->original_price,
            ]);
        }
    }
};
