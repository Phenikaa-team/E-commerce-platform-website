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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('online_status');
            $table->string('address')->nullable()->after('phone');
            $table->string('banner_url')->nullable()->after('address');
            $table->string('bank_name')->nullable()->after('banner_url');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'banner_url',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
            ]);
        });
    }
};
