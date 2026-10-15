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
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('flash_sale_price', 12, 2)->nullable()->after('flash_sale_percent');
            $table->decimal('flash_sale_regular_price', 12, 2)->nullable()->after('flash_sale_price');
            $table->timestamp('flash_sale_starts_at')->nullable()->after('flash_sale_regular_price');
            $table->timestamp('flash_sale_ends_at')->nullable()->after('flash_sale_starts_at');
            $table->index(['is_flash_sale', 'flash_sale_starts_at', 'flash_sale_ends_at']);
        });

        // Preserve existing demo Flash Sale products while moving their sale and
        // restore prices into explicit columns with a real expiry time.
        $now = now();
        DB::table('products')
            ->where('is_flash_sale', true)
            ->orderBy('id')
            ->chunkById(100, function ($products) use ($now): void {
                foreach ($products as $product) {
                    $salePrice = (float) $product->price;
                    $regularPrice = $product->original_price !== null
                        && (float) $product->original_price > $salePrice
                        ? (float) $product->original_price
                        : $salePrice;

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'price' => $regularPrice,
                            'flash_sale_price' => $salePrice,
                            'flash_sale_regular_price' => $regularPrice,
                            'flash_sale_starts_at' => $now,
                            'flash_sale_ends_at' => $now->copy()->addDay(),
                            'flash_sale_percent' => $regularPrice > 0
                                ? round((($regularPrice - $salePrice) / $regularPrice) * 100)
                                : 0,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['is_flash_sale', 'flash_sale_starts_at', 'flash_sale_ends_at']);
            $table->dropColumn([
                'flash_sale_price',
                'flash_sale_regular_price',
                'flash_sale_starts_at',
                'flash_sale_ends_at',
            ]);
        });
    }
};
