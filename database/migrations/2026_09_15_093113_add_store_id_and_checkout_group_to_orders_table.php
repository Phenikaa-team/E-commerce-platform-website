<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->string('checkout_group_id', 30)->nullable()->index()->after('store_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn(['store_id', 'checkout_group_id']);
        });
    }
};
