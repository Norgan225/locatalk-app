<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelUserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        protected int $channelId,
        protected User $user,
        protected bool $isTyping
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('channel.' . $this->channelId)];
    }

    public function broadcastAs(): string
    {
        return 'channel-user.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'channel_id' => $this->channelId,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'is_typing' => $this->isTyping,
        ];
    }
}
