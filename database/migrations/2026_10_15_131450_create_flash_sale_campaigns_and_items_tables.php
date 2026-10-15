<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_sale_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);
            $table->string('schedule_type', 20);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('recurring_dates')->nullable();
            $table->time('daily_starts_at')->nullable();
            $table->time('daily_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        Schema::create('flash_sale_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('flash_sale_campaign_id')
                ->constrained('flash_sale_campaigns')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->decimal('regular_price', 12, 2);
            $table->decimal('flash_sale_price', 12, 2);
            $table->timestamps();

            $table->unique(['flash_sale_campaign_id', 'product_id']);
            $table->index(['product_id', 'flash_sale_campaign_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
        Schema::dropIfExists('flash_sale_campaigns');
    }
};
