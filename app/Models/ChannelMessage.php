<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'channel_id',
        'user_id',
        'content',
        'type',
        'attachments',
        'encrypted',
        'iv',
        'is_pinned',
        'pinned_at',
        'pinned_by',
        'reply_to',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_pinned' => 'boolean',
        'encrypted' => 'boolean',
        'pinned_at' => 'datetime',
    ];

    // Relations
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pinnedBy()
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChannelMessage::class, 'reply_to');
    }

    public function replies()
    {
        return $this->hasMany(ChannelMessage::class, 'reply_to');
    }

    public function reactions()
    {
        return $this->hasMany(ChannelMessageReaction::class);
    }

    // Scopes
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByChannel($query, $channelId)
    {
        return $query->where('channel_id', $channelId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    // Helper methods
    public function pin(User $user)
    {
        $this->update([
            'is_pinned' => true,
            'pinned_at' => now(),
            'pinned_by' => $user->id,
        ]);
    }

    public function unpin()
    {
        $this->update([
            'is_pinned' => false,
            'pinned_at' => null,
            'pinned_by' => null,
        ]);
    }

    public function isPinned()
    {
        return $this->is_pinned;
    }
}
