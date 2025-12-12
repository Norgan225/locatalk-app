<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privé pour les messages utilisateur
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privé pour les channels de groupe
Broadcast::channel('channel.{channelId}', function ($user, $channelId) {
    // Vérifier que l'utilisateur est membre du channel
    return \App\Models\Channel::where('id', $channelId)
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
});

// Canal de présence global
Broadcast::channel('presence', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar,
    ];
});
