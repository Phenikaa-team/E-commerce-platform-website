<?php

use App\Models\StoreChatConversation;
use App\Models\SupportConversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('store-chat.{conversationId}', function (User $user, string $conversationId): bool {
    $conversation = StoreChatConversation::with('store')->find($conversationId);

    return $conversation !== null
        && ((int) $conversation->buyer_id === (int) $user->id
            || (int) $conversation->store?->user_id === (int) $user->id);
});

Broadcast::channel('support-chat.{conversationId}', function (User $user, string $conversationId): bool {
    $conversation = SupportConversation::find($conversationId);

    return $conversation !== null
        && ($user->isAdmin() || (int) $conversation->customer_id === (int) $user->id);
});
