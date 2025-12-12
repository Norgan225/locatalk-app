<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelMessageReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_message_id',
        'user_id',
        'emoji',
    ];

    // Relations
    public function channelMessage()
    {
        return $this->belongsTo(ChannelMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByEmoji($query, $emoji)
    {
        return $query->where('emoji', $emoji);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
