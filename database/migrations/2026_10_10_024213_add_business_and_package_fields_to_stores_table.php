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
            $table->string('business_type', 30)->default('individual')->after('user_id'); // individual, business
            $table->string('tax_code', 50)->nullable()->after('phone');
            $table->string('representative_name', 150)->nullable()->after('tax_code');
            $table->string('id_card_number', 50)->nullable()->after('representative_name');
            $table->string('id_card_image')->nullable()->after('id_card_number');
            $table->string('business_license_image')->nullable()->after('id_card_image');
            $table->string('package_plan', 30)->default('free')->after('status'); // free, pro, enterprise
            $table->json('payment_methods')->nullable()->after('bank_account_name');
            $table->json('shipping_partners')->nullable()->after('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'business_type',
                'tax_code',
                'representative_name',
                'id_card_number',
                'id_card_image',
                'business_license_image',
                'package_plan',
                'payment_methods',
                'shipping_partners',
            ]);
        });
    }
};
