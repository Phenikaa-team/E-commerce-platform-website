<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('ai_metadata')->nullable()->after('faqs');
            $table->string('ai_analysis_status')->default('pending')->after('ai_metadata');
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_analysis_status');
            $table->text('ai_analysis_error')->nullable()->after('ai_analyzed_at');
            $table->index(['ai_analysis_status', 'category_id']);
        });

        Schema::create('navigation_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('section_key', 80);
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('recommendation_enabled')->default(true);
            $table->unsignedInteger('item_limit')->default(6);
            $table->timestamps();
            $table->index(['category_id', 'is_active', 'sort_order']);
        });

        Schema::create('recommendation_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('section_key', 80);
            $table->json('payload');
            $table->string('source', 30)->default('fallback');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['category_id', 'section_key', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_snapshots');
        Schema::dropIfExists('navigation_sections');

        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['ai_analysis_status', 'category_id']);
            $table->dropColumn(['ai_metadata', 'ai_analysis_status', 'ai_analyzed_at', 'ai_analysis_error']);
        });
    }
};
