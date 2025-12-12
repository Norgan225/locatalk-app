<?php

namespace App\Events;

use App\Models\ChannelMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChannelMessage $channelMessage;

    /**
     * Create a new event instance.
     */
    public function __construct(ChannelMessage $channelMessage)
    {
        $this->channelMessage = $channelMessage->load(['user', 'replyTo.user']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel.' . $this->channelMessage->channel_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'channel-message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->channelMessage->id,
            'channel_id' => $this->channelMessage->channel_id,
            'content' => $this->channelMessage->content,
            'type' => $this->channelMessage->type ?? 'text',
            'user_id' => $this->channelMessage->user_id,
            'attachments' => $this->channelMessage->attachments,
            'is_pinned' => $this->channelMessage->is_pinned ?? false,
            'reply_to' => $this->channelMessage->replyTo ? [
                'id' => $this->channelMessage->replyTo->id,
                'content' => $this->channelMessage->replyTo->content,
                'user_name' => optional($this->channelMessage->replyTo->user)->name,
                'encrypted' => (bool) ($this->channelMessage->replyTo->encrypted ?? false),
                'iv' => $this->channelMessage->replyTo->iv,
            ] : null,
            'created_at' => $this->channelMessage->created_at->toISOString(),
            'encrypted' => (bool) ($this->channelMessage->encrypted ?? false),
            'iv' => $this->channelMessage->iv,
            'sender' => $this->channelMessage->user ? [
                'id' => $this->channelMessage->user->id,
                'name' => $this->channelMessage->user->name,
                'email' => $this->channelMessage->user->email,
            ] : null,
            'reactions' => [],
        ];
    }
}
