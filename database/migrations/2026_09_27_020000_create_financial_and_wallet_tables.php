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
        // 1. Store Wallets (Ví người bán)
        Schema::create('store_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 14, 2)->default(0); // Số dư khả dụng có thể rút
            $table->decimal('pending_balance', 14, 2)->default(0); // Tiền đang tạm giữ trong Escrow
            $table->decimal('total_withdrawn', 14, 2)->default(0); // Tổng tiền đã rút
            $table->decimal('total_earned', 14, 2)->default(0); // Tổng thực nhận tích lũy
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->timestamps();
        });

        // 2. Order Financials (Bảng hạch toán & ăn chia dòng tiền chi tiết cho từng đơn hàng)
        Schema::create('order_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Doanh thu gộp & Giảm giá
            $table->decimal('gross_merchandise_amount', 12, 2)->default(0); // Tiền hàng gốc của Shop (S_Goc)
            $table->decimal('shop_discount', 12, 2)->default(0); // Voucher Shop chịu
            $table->decimal('net_merchandise_amount', 12, 2)->default(0); // Tiền hàng sau voucher shop = S_Goc - Voucher_Shop (căn cứ tính phí)
            $table->decimal('platform_discount', 12, 2)->default(0); // Voucher Sàn tài trợ
            $table->decimal('freeship_discount', 12, 2)->default(0); // Giảm phí vận chuyển sàn tài trợ
            $table->decimal('points_discount', 12, 2)->default(0); // Giảm trừ xu
            $table->decimal('shipping_fee_paid', 12, 2)->default(0); // Phí vận chuyển khách trả thực tế
            $table->decimal('total_buyer_paid', 12, 2)->default(0); // Khách trả thực tế (vào Escrow sàn)

            // Phí sàn thu từ Shop
            $table->decimal('payment_fee_rate', 5, 2)->default(2.50); // Phí thanh toán (ví dụ 2.5%)
            $table->decimal('payment_fee', 12, 2)->default(0); // Tiền phí thanh toán shop trả sàn
            $table->decimal('commission_rate', 5, 2)->default(3.00); // Phí hoa hồng / cố định sàn (ví dụ 3.0%)
            $table->decimal('commission_fee', 12, 2)->default(0); // Tiền phí hoa hồng shop trả sàn
            $table->decimal('service_fee', 12, 2)->default(0); // Phí dịch vụ giá trị gia tăng khác
            $table->decimal('platform_voucher_shop_share_rate', 5, 2)->default(0.00); // Tỷ lệ shop chịu voucher sàn (alpha, mặc định 0%)
            $table->decimal('platform_voucher_shop_share', 12, 2)->default(0); // Tiền voucher sàn do shop gánh (nếu có)

            // Kết quả hạch toán phân chia dòng tiền
            $table->decimal('shipping_cost_to_carrier', 12, 2)->default(0); // Tiền chuyển trả cho đơn vị vận chuyển
            $table->decimal('shop_earning', 12, 2)->default(0); // Thực nhận của Shop
            $table->decimal('platform_gross_fee', 12, 2)->default(0); // Tổng phí sàn thu từ shop (payment_fee + commission_fee + service_fee)
            $table->decimal('platform_voucher_cost', 12, 2)->default(0); // Chi phí sàn bỏ ra tài trợ voucher = platform_discount*(1-alpha) + freeship_discount
            $table->decimal('platform_net_earning', 12, 2)->default(0); // Lợi nhuận ròng của Sàn từ đơn hàng
            $table->unsignedInteger('cashback_points')->default(0); // Số xu thưởng hoàn cho khách hàng

            // Trạng thái Escrow & Quyết toán
            $table->string('escrow_status')->default('holding'); // holding, settled, cancelled
            $table->timestamp('settled_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->index(['store_id', 'escrow_status']);
        });

        // 3. Wallet Transactions (Lịch sử biến động số dư ví Shop)
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_code')->unique();
            $table->string('type'); // order_settlement, withdrawal, adjustment, refund
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('description');
            $table->string('status')->default('completed'); // completed, pending, cancelled
            $table->timestamps();
            $table->index(['store_wallet_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('order_financials');
        Schema::dropIfExists('store_wallets');
    }
};
