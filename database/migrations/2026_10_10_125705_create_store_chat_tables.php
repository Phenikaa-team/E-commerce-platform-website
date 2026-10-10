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
        Schema::create('store_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('buyer_unread_count')->default(0);
            $table->unsignedInteger('seller_unread_count')->default(0);
            $table->timestamps();
            $table->unique(['store_id', 'buyer_id']);
            $table->index(['store_id', 'last_message_at']);
            $table->index(['buyer_id', 'last_message_at']);
        });

        Schema::create('store_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('store_chat_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->index(['conversation_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_chat_messages');
        Schema::dropIfExists('store_chat_conversations');
    }
};
