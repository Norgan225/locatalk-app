<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublicKeyShared implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $publicKey;

    public function __construct(int $userId, string $publicKey)
    {
        $this->userId = $userId;
        $this->publicKey = $publicKey;
    }

    public function broadcastOn(): array
    {
        // Diffuser à tous les utilisateurs connectés (peut être filtré côté client)
        return [
            new Channel('public-keys'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'public.key.shared';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'public_key' => $this->publicKey,
        ];
    }
}
