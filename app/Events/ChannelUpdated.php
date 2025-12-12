<?php

namespace App\Events;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Channel $channel;
    public string $action;
    public ?User $actor;

    /**
     * Create a new event instance.
     *
     * @param Channel $channel Le canal concerné
     * @param string $action L'action effectuée (created, updated, deleted, member_joined, member_left)
     * @param User|null $actor L'utilisateur qui a effectué l'action
     */
    public function __construct(Channel $channel, string $action, ?User $actor = null)
    {
        $this->channel = $channel;
        $this->action = $action;
        $this->actor = $actor;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Diffuser à tous les membres du canal
        return [
            new PrivateChannel('channel.' . $this->channel->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'channel.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'channel_id' => $this->channel->id,
            'channel_name' => $this->channel->name,
            'channel_type' => $this->channel->type,
            'action' => $this->action,
            'actor' => $this->actor ? [
                'id' => $this->actor->id,
                'name' => $this->actor->name,
            ] : null,
            'timestamp' => now()->toISOString(),
            'message' => $this->getActionMessage(),
        ];
    }

    /**
     * Générer le message d'action pour les notifications
     */
    protected function getActionMessage(): string
    {
        $actorName = $this->actor ? $this->actor->name : 'Quelqu\'un';

        return match ($this->action) {
            'created' => "{$actorName} a créé le canal {$this->channel->name}",
            'updated' => "{$actorName} a modifié le canal {$this->channel->name}",
            'deleted' => "{$actorName} a supprimé le canal {$this->channel->name}",
            'member_joined' => "{$actorName} a rejoint le canal",
            'member_left' => "{$actorName} a quitté le canal",
            'member_added' => "{$actorName} a été ajouté au canal",
            'member_removed' => "{$actorName} a été retiré du canal",
            default => "Le canal a été mis à jour",
        };
    }
}
