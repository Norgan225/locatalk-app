<?php

namespace App\Events;

use App\Models\UserStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userStatus;
    public $userId;

    public function __construct(UserStatus $userStatus)
    {
        $this->userStatus = $userStatus;
        $this->userId = $userStatus->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user-status'),
            new PresenceChannel('user.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'status' => $this->userStatus->getVisibleStatus(),
            'last_activity' => $this->userStatus->last_activity?->toIso8601String(),
            'custom_message' => $this->userStatus->custom_message,
            'device_type' => $this->userStatus->device_type,
            'status_details' => $this->userStatus->getStatusWithColor(),
        ];
    }
}
