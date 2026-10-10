<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreChatMessage extends Model
{
    protected $fillable = ['sender_id', 'body'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(StoreChatConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
