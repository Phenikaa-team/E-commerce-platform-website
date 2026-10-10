<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StoreChatConversation extends Model
{
    protected $fillable = [
        'store_id',
        'buyer_id',
        'last_message_at',
        'buyer_unread_count',
        'seller_unread_count',
    ];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(StoreChatMessage::class, 'conversation_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(StoreChatMessage::class, 'conversation_id')->latestOfMany();
    }
}
