<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('logo_url')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            $table->index(['status', 'name']);
        });

        Schema::create('brand_aliases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('alias', 120);
            $table->string('slug', 140);
            $table->timestamps();
            $table->unique('slug');
            $table->index(['brand_id', 'slug']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('brand_id')->nullable()->after('brand')->constrained('brands')->nullOnDelete();
            $table->index(['brand_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropForeign(['brand_id']);
            $table->dropIndex(['brand_id', 'status']);
            $table->dropColumn('brand_id');
        });
        Schema::dropIfExists('brand_aliases');
        Schema::dropIfExists('brands');
    }
};
