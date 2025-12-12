<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // Si c'est un message privé
        if ($this->message->receiver_id) {
            $channels[] = new PrivateChannel('user.' . $this->message->receiver_id);
            $channels[] = new PrivateChannel('user.' . $this->message->sender_id);
        }

        // Si c'est un message de channel
        if ($this->message->channel_id) {
            $channels[] = new PrivateChannel('channel.' . $this->message->channel_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->is_encrypted
                ? $this->message->decrypted_content
                : $this->message->content,
            // Prioriser la colonne moderne `type` si définie
            'type' => $this->message->type ?? $this->message->message_type,
            // Joindre les attachments (utile pour affichage en temps réel)
            'attachments' => $this->message->attachments ? $this->message->attachments->map(function($att) {
                return [
                    'id' => $att->id,
                    'file_name' => $att->file_name,
                    'file_type' => $att->file_type,
                    'mime_type' => $att->mime_type,
                    'file_size' => $att->formatted_size,
                    'file_url' => $att->file_url,
                    'thumbnail_url' => $att->thumbnail_url,
                    'duration' => $att->duration,
                    'formatted_duration' => $att->formatted_duration,
                    'icon' => $att->icon,
                ];
            })->toArray() : [],
            'receiver_id' => $this->message->receiver_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->avatar,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
