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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('password')->nullable()->change();
            $table->string('avatar_url')->nullable()->after('password');
            $table->string('cover_url')->nullable()->after('avatar_url');
            $table->string('membership_tier')->default('Thành viên Bạc')->after('cover_url');
            $table->string('joined_date')->default('Tham gia từ 06/2024')->after('membership_tier');
            $table->string('gender')->default('Chưa cập nhật')->after('joined_date');
            $table->string('birthday')->nullable()->after('gender');
            $table->integer('coins')->default(120)->after('birthday');
            $table->integer('voucher_count')->default(3)->after('coins');
            $table->integer('favorite_count')->default(4)->after('voucher_count');
            $table->integer('order_count')->default(12)->after('favorite_count');
            $table->integer('review_count')->default(3)->after('order_count');
            $table->string('provider')->nullable()->after('review_count');
            $table->string('provider_id')->nullable()->after('provider');
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address_line');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone',
                'avatar_url',
                'cover_url',
                'membership_tier',
                'joined_date',
                'gender',
                'birthday',
                'coins',
                'voucher_count',
                'favorite_count',
                'order_count',
                'review_count',
                'provider',
                'provider_id',
            ]);
        });
    }
};
