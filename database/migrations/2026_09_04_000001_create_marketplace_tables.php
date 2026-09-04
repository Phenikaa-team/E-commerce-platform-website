<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('slug')->unique(); $table->text('description')->nullable();
            $table->string('status')->default('active'); $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table): void {
            $table->id(); $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name'); $table->string('slug')->unique(); $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table): void {
            $table->id(); $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); $table->string('slug')->unique(); $table->text('description')->nullable();
            $table->decimal('price', 12, 2); $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('active'); $table->timestamps(); $table->index(['status', 'category_id']);
        });
        Schema::create('carts', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index(); $table->timestamps();
        });
        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id(); $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity'); $table->decimal('unit_price', 12, 2); $table->unique(['cart_id', 'product_id']);
        });
        Schema::create('orders', function (Blueprint $table): void {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); $table->decimal('total', 12, 2)->default(0);
            $table->json('shipping_address'); $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete(); $table->string('product_name');
            $table->unsignedInteger('quantity'); $table->decimal('unit_price', 12, 2); $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    { foreach (['order_items','orders','cart_items','carts','products','categories','stores'] as $table) Schema::dropIfExists($table); }
};
