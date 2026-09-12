<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code')->nullable()->unique()->after('id');
            $table->string('payment_method')->default('cod')->after('status');
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->decimal('subtotal', 12, 2)->default(0)->after('payment_status');
            $table->decimal('shipping_fee', 12, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('shipping_fee');
            $table->string('coupon_code')->nullable()->after('discount_amount');
            $table->text('notes')->nullable()->after('shipping_address');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('selected_variant')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['selected_variant']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_code',
                'payment_method',
                'payment_status',
                'subtotal',
                'shipping_fee',
                'discount_amount',
                'coupon_code',
                'notes',
            ]);
        });
    }
};
