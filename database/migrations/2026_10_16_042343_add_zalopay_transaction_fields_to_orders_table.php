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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('zalopay_app_trans_id')->nullable()->index()->after('payment_status');
            $table->string('zalopay_trans_id')->nullable()->after('zalopay_app_trans_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['zalopay_app_trans_id', 'zalopay_trans_id']);
        });
    }
};
