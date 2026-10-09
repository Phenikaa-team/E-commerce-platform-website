<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderFinancial;
use App\Models\Store;
use App\Models\StoreWallet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinancialSettlementService
{
    /**
     * Tỷ lệ phí thanh toán mặc định (2.5%).
     */
    public const DEFAULT_PAYMENT_FEE_RATE = 2.50;

    /**
     * Tỷ lệ hoa hồng / phí cố định sàn mặc định (3.0%).
     */
    public const DEFAULT_COMMISSION_RATE = 3.00;

    /**
     * Tỷ lệ trích hoa hồng để hoàn xu cho người mua (10%).
     */
    public const DEFAULT_CASHBACK_RATE = 0.10;

    /**
     * Tỷ lệ shop cùng gánh voucher sàn (alpha = 0.00 tức sàn tài trợ 100%).
     */
    public const DEFAULT_PLATFORM_VOUCHER_SHOP_SHARE_RATE = 0.00;

    /**
     * Ghi nhận hạch toán tài chính khi một đơn hàng vừa được tạo (Checkout).
     *
     * @param  array<string, float>  $breakdown
     */
    public function recordOrderCreation(Order $order, array $breakdown = []): OrderFinancial
    {
        return DB::transaction(function () use ($order, $breakdown) {
            $store = $order->store ?? Store::find($order->store_id);
            if (! $store) {
                throw new \RuntimeException("Đơn hàng #{$order->id} không thuộc cửa hàng nào.");
            }

            // Đảm bảo cửa hàng đã có ví
            $wallet = $this->getOrCreateStoreWallet($store);

            $grossMerchandise = (float) ($breakdown['gross_merchandise_amount'] ?? $order->subtotal);
            $shopDiscount = (float) ($breakdown['shop_discount'] ?? 0.0);
            $netMerchandise = max(0.0, $grossMerchandise - $shopDiscount);

            $platformDiscount = (float) ($breakdown['platform_discount'] ?? 0.0);
            $freeshipDiscount = (float) ($breakdown['freeship_discount'] ?? 0.0);
            $pointsDiscount = (float) ($breakdown['points_discount'] ?? 0.0);
            $shippingFeePaid = (float) ($breakdown['shipping_fee_paid'] ?? $order->shipping_fee);
            $totalBuyerPaid = (float) ($breakdown['total_buyer_paid'] ?? $order->total);

            // Các loại phí sàn thu từ người bán
            $paymentFeeRate = (float) ($breakdown['payment_fee_rate'] ?? self::DEFAULT_PAYMENT_FEE_RATE);
            $paymentFee = round($netMerchandise * ($paymentFeeRate / 100), 2);

            $commissionRate = (float) ($breakdown['commission_rate'] ?? self::DEFAULT_COMMISSION_RATE);
            $commissionFee = round($netMerchandise * ($commissionRate / 100), 2);

            $serviceFee = (float) ($breakdown['service_fee'] ?? 0.0);

            $platformVoucherShopShareRate = (float) ($breakdown['platform_voucher_shop_share_rate'] ?? self::DEFAULT_PLATFORM_VOUCHER_SHOP_SHARE_RATE);
            $platformVoucherShopShare = round($platformDiscount * ($platformVoucherShopShareRate / 100), 2);

            // Hạch toán dòng tiền
            // Shop nhận: Tiền hàng sau voucher shop - Phí thanh toán - Phí hoa hồng - Phí dịch vụ - Phần voucher sàn shop gánh
            $shopEarning = max(0.0, $netMerchandise - $paymentFee - $commissionFee - $serviceFee - $platformVoucherShopShare);

            // Sàn thu từ shop
            $platformGrossFee = $paymentFee + $commissionFee + $serviceFee;

            // Chi phí voucher sàn tài trợ = Platform Voucher sàn chịu + Freeship voucher sàn tài trợ
            $platformVoucherCost = ($platformDiscount - $platformVoucherShopShare) + $freeshipDiscount;

            // Lợi nhuận ròng của sàn
            $platformNetEarning = $platformGrossFee - $platformVoucherCost;

            // Xu tích lũy thưởng cho khách hàng (trích % từ hoa hồng sàn)
            $cashbackPoints = (int) round($commissionFee * self::DEFAULT_CASHBACK_RATE);

            $financial = OrderFinancial::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'store_id' => $store->id,
                    'user_id' => $order->user_id,
                    'gross_merchandise_amount' => $grossMerchandise,
                    'shop_discount' => $shopDiscount,
                    'net_merchandise_amount' => $netMerchandise,
                    'platform_discount' => $platformDiscount,
                    'freeship_discount' => $freeshipDiscount,
                    'points_discount' => $pointsDiscount,
                    'shipping_fee_paid' => $shippingFeePaid,
                    'total_buyer_paid' => $totalBuyerPaid,
                    'payment_fee_rate' => $paymentFeeRate,
                    'payment_fee' => $paymentFee,
                    'commission_rate' => $commissionRate,
                    'commission_fee' => $commissionFee,
                    'service_fee' => $serviceFee,
                    'platform_voucher_shop_share_rate' => $platformVoucherShopShareRate,
                    'platform_voucher_shop_share' => $platformVoucherShopShare,
                    'shipping_cost_to_carrier' => $shippingFeePaid,
                    'shop_earning' => $shopEarning,
                    'platform_gross_fee' => $platformGrossFee,
                    'platform_voucher_cost' => $platformVoucherCost,
                    'platform_net_earning' => $platformNetEarning,
                    'cashback_points' => $cashbackPoints,
                    'escrow_status' => 'holding',
                    'notes' => 'Tiền đang được tạm giữ trong Escrow ShopMart.',
                ]
            );

            // Ghi nhận số dư chờ thanh toán vào Escrow của người bán
            $wallet->depositPending($shopEarning);

            return $financial;
        });
    }

    /**
     * Quyết toán đơn hàng khi chuyển trạng thái COMPLETED (Hết hạn khiếu nại).
     * Tiền từ Escrow chuyển vào ví khả dụng của Shop & Hoàn xu cho Buyer.
     */
    public function settleOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $financial = $order->financial ?? OrderFinancial::where('order_id', $order->id)->first();

            // Nếu đơn hàng cũ chưa có bản ghi tài chính, tự động tính toán
            if (! $financial) {
                $financial = $this->recordOrderCreation($order, [
                    'gross_merchandise_amount' => (float) $order->subtotal,
                    'shop_discount' => 0.0,
                    'platform_discount' => (float) $order->discount_amount,
                    'freeship_discount' => 0.0,
                    'shipping_fee_paid' => (float) $order->shipping_fee,
                    'total_buyer_paid' => (float) $order->total,
                ]);
            }

            // Nếu đã quyết toán rồi thì bỏ qua
            if ($financial->escrow_status === 'settled') {
                return true;
            }

            $store = $order->store ?? Store::find($order->store_id);
            if (! $store) {
                return false;
            }

            $wallet = $this->getOrCreateStoreWallet($store);

            // 1. Chuyển tiền từ pending_balance sang balance khả dụng của Shop
            $wallet->settlePending(
                amount: (float) $financial->shop_earning,
                orderId: $order->id,
                description: "Thanh toán doanh thu đơn hàng #{$order->order_code} (Sau trừ phí sàn)"
            );

            // 2. Cập nhật trạng thái hạch toán
            $financial->update([
                'escrow_status' => 'settled',
                'settled_at' => now(),
                'notes' => 'Đã quyết toán vào số dư ví khả dụng của Người bán.',
            ]);

            // 3. Hoàn xu / Tích điểm cho người mua nếu có
            if ($financial->cashback_points > 0 && $order->user_id) {
                $buyer = User::find($order->user_id);
                if ($buyer) {
                    $buyer->increment('coins', $financial->cashback_points);
                }
            }

            return true;
        });
    }

    /**
     * Hủy khoản giữ Escrow khi đơn hàng bị hủy (CANCELLED).
     */
    public function cancelOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $financial = $order->financial ?? OrderFinancial::where('order_id', $order->id)->first();
            if (! $financial) {
                return true;
            }

            if ($financial->escrow_status === 'holding') {
                $store = $order->store ?? Store::find($order->store_id);
                if ($store) {
                    $wallet = $this->getOrCreateStoreWallet($store);
                    $wallet->cancelPending((float) $financial->shop_earning);
                }

                $financial->update([
                    'escrow_status' => 'cancelled',
                    'notes' => 'Đơn hàng đã bị hủy, giải phóng tiền tạm giữ trong Escrow.',
                ]);
            }

            return true;
        });
    }

    /**
     * Lấy hoặc tạo mới ví cho cửa hàng.
     */
    public function getOrCreateStoreWallet(Store $store): StoreWallet
    {
        return StoreWallet::firstOrCreate(
            ['store_id' => $store->id],
            [
                'balance' => 0.0,
                'pending_balance' => 0.0,
                'total_withdrawn' => 0.0,
                'total_earned' => 0.0,
            ]
        );
    }

    /**
     * Thống kê tài chính sàn toàn diện (Dành cho Admin).
     *
     * @return array<string, mixed>
     */
    public function getPlatformFinancialStats(): array
    {
        $totals = OrderFinancial::selectRaw("
            COUNT(*) as total_orders_count,
            SUM(total_buyer_paid) as total_gmv,
            SUM(CASE WHEN escrow_status = 'holding' THEN shop_earning ELSE 0 END) as total_escrow_holding,
            SUM(payment_fee) as total_payment_fee,
            SUM(commission_fee) as total_commission_fee,
            SUM(platform_gross_fee) as total_platform_gross_revenue,
            SUM(platform_voucher_cost) as total_platform_voucher_cost,
            SUM(platform_net_earning) as total_platform_net_profit,
            SUM(cashback_points) as total_cashback_distributed
        ")->first();

        return [
            'total_orders' => (int) ($totals->total_orders_count ?? 0),
            'gmv' => (float) ($totals->total_gmv ?? 0.0),
            'escrow_holding' => (float) ($totals->total_escrow_holding ?? 0.0),
            'payment_fee' => (float) ($totals->total_payment_fee ?? 0.0),
            'commission_fee' => (float) ($totals->total_commission_fee ?? 0.0),
            'gross_revenue' => (float) ($totals->total_platform_gross_revenue ?? 0.0),
            'voucher_cost' => (float) ($totals->total_platform_voucher_cost ?? 0.0),
            'net_profit' => (float) ($totals->total_platform_net_profit ?? 0.0),
            'cashback_points' => (int) ($totals->total_cashback_distributed ?? 0),
        ];
    }
}
