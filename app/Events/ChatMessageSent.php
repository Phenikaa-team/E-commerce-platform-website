<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @param array{id: int, sender_id: int, sender_name: string, body: string, created_at: string} $message */
    public function __construct(public string $channelName, public array $message) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->channelName),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    /** @return array{id: int, sender_id: int, sender_name: string, body: string, created_at: string} */
    public function broadcastWith(): array
    {
        return $this->message;
    }
}
