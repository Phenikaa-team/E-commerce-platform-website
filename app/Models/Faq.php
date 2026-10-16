<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'category',
        'question',
        'answer',
        'prompt',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return array<string, array{title: string, icon: string}>
     */
    public static function categories(): array
    {
        return [
            'orders' => [
                'title' => 'Đơn hàng & Mua sắm',
                'icon' => '📦',
            ],
            'shipping' => [
                'title' => 'Vận chuyển & Giao nhận',
                'icon' => '🚚',
            ],
            'payment' => [
                'title' => 'Thanh toán & Hoàn tiền',
                'icon' => '💳',
            ],
            'returns' => [
                'title' => 'Đổi trả & Bảo hành',
                'icon' => '🛡️',
            ],
            'account' => [
                'title' => 'Tài khoản & Gian hàng',
                'icon' => '🏪',
            ],
        ];
    }
}
