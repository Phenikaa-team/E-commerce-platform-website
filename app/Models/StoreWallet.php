<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StoreWallet extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'total_earned' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    /**
     * Ghi nhận tiền vào ví tạm giữ (Escrow) khi có đơn hàng mới phát sinh.
     */
    public function depositPending(float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $this->increment('pending_balance', $amount);
    }

    /**
     * Quyết toán dòng tiền từ Escrow sang số dư khả dụng khi đơn hàng hoàn tất.
     */
    public function settlePending(float $amount, ?int $orderId = null, string $description = ''): WalletTransaction
    {
        $amount = round($amount, 2);
        $balanceBefore = (float) $this->balance;
        $newPending = max(0.0, (float) $this->pending_balance - $amount);
        $newBalance = $balanceBefore + $amount;

        $this->update([
            'pending_balance' => $newPending,
            'balance' => $newBalance,
            'total_earned' => (float) $this->total_earned + $amount,
        ]);

        return $this->transactions()->create([
            'order_id' => $orderId,
            'transaction_code' => 'TXN-'.strtoupper(Str::random(10)),
            'type' => 'order_settlement',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
            'description' => $description ?: "Quyết toán đơn hàng #{$orderId}",
            'status' => 'completed',
        ]);
    }

    /**
     * Hủy khoản tiền đang tạm giữ khi đơn hàng bị hủy trước khi giao.
     */
    public function cancelPending(float $amount): void
    {
        $amount = round($amount, 2);
        $newPending = max(0.0, (float) $this->pending_balance - $amount);
        $this->update(['pending_balance' => $newPending]);
    }
}
