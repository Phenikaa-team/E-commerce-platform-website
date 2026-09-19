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
        Schema::table('products', function (Blueprint $table) {
            $table->index('store_id');
            $table->index('brand');
            $table->index(['status', 'is_flash_sale']);
            $table->index(['status', 'is_mall']);
            $table->index(['status', 'sold_count']);
            $table->index(['status', 'price']);
            $table->index(['status', 'rating']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['store_id', 'status', 'created_at']);
            $table->index(['store_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['payment_method', 'status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['product_id', 'status']);
            $table->index('user_id');
            $table->index('order_id');
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->index(['user_id', 'is_default']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index('store_id');
            $table->index(['is_active', 'expires_at']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['store_id']);
            $table->dropIndex(['is_active', 'expires_at']);
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_default']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['store_id', 'status', 'created_at']);
            $table->dropIndex(['store_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['payment_method', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['store_id']);
            $table->dropIndex(['brand']);
            $table->dropIndex(['status', 'is_flash_sale']);
            $table->dropIndex(['status', 'is_mall']);
            $table->dropIndex(['status', 'sold_count']);
            $table->dropIndex(['status', 'price']);
            $table->dropIndex(['status', 'rating']);
        });
    }
};
