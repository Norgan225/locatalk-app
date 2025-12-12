<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;

    public function __construct(int $messageId)
    {
        $this->messageId = $messageId;
    }

    public function broadcastOn(): array
    {
        // On ne peut pas récupérer les IDs depuis le message supprimé
        // Il faudra broadcast sur un canal général ou stocker les IDs avant suppression
        return [
            new Channel('messages'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->messageId,
        ];
    }
}
