<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table): void {
            $table->string('logo_url')->nullable()->after('description');
            $table->decimal('rating', 2, 1)->default(4.9)->after('logo_url');
            $table->string('response_rate')->default('99%')->after('rating');
            $table->string('followers')->default('1.2tr')->after('response_rate');
            $table->boolean('is_mall')->default(true)->after('followers');
            $table->string('online_status')->default('Online 5 phút trước')->after('is_mall');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->text('icon_svg')->nullable()->after('slug');
            $table->string('badge')->nullable()->after('icon_svg');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->string('brand')->nullable()->after('name');
            $table->string('badge_text')->nullable()->after('brand');
            $table->string('main_image_url')->nullable()->after('badge_text');
            $table->decimal('original_price', 12, 2)->nullable()->after('price');
            $table->unsignedInteger('discount_percent')->default(0)->after('original_price');
            $table->decimal('rating', 2, 1)->default(5.0)->after('discount_percent');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->unsignedInteger('sold_count')->default(0)->after('reviews_count');
            $table->boolean('is_mall')->default(false)->after('sold_count');
            $table->boolean('is_flash_sale')->default(false)->after('is_mall');
            $table->unsignedInteger('flash_sale_percent')->nullable()->after('is_flash_sale');
            $table->json('specs')->nullable()->after('description');
            $table->json('features')->nullable()->after('specs');
            $table->json('variants')->nullable()->after('features');
            $table->string('banner_image_url')->nullable()->after('variants');
            $table->string('warranty_info')->nullable()->after('banner_image_url');
            $table->text('policy')->nullable()->after('warranty_info');
            $table->json('faqs')->nullable()->after('policy');
        });

        Schema::create('product_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('url');
            $table->boolean('is_video')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'brand',
                'badge_text',
                'main_image_url',
                'original_price',
                'discount_percent',
                'rating',
                'reviews_count',
                'sold_count',
                'is_mall',
                'is_flash_sale',
                'flash_sale_percent',
                'specs',
                'features',
                'variants',
                'banner_image_url',
                'warranty_info',
                'policy',
                'faqs',
            ]);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn(['icon_svg', 'badge']);
        });

        Schema::table('stores', function (Blueprint $table): void {
            $table->dropColumn([
                'logo_url',
                'rating',
                'response_rate',
                'followers',
                'is_mall',
                'online_status',
            ]);
        });
    }
};
