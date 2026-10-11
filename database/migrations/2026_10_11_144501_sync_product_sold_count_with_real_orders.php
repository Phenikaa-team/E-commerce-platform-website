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
        // Reset all products sold_count to 0
        DB::table('products')->update(['sold_count' => 0]);

        // Aggregate actual sold quantity from order items
        $sales = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->get();

        foreach ($sales as $sale) {
            if ($sale->product_id) {
                DB::table('products')
                    ->where('id', $sale->product_id)
                    ->update(['sold_count' => (int) $sale->total_sold]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op or keep as 0
    }
};
