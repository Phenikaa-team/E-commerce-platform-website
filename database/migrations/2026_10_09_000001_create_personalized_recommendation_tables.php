<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 40);
            $table->decimal('weight', 6, 2)->default(1);
            $table->json('metadata')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'event_type', 'created_at']);
            $table->index(['user_id', 'product_id', 'created_at']);
        });

        Schema::create('user_recommendation_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('context', 40)->default('home');
            $table->json('payload');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'context']);
            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_recommendation_snapshots');
        Schema::dropIfExists('recommendation_events');
    }
};
