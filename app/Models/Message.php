<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'sender_id',
        'receiver_id',
        'channel_id',
        'content',
        'encrypted_content',
        'encryption_key_id',
        'is_encrypted',
        'type',
        'is_read',
        'read_at',
        'is_pinned',
        'pinned_at',
        'pinned_by',
        'reply_to',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_encrypted' => 'boolean',
        'is_pinned' => 'boolean',
        'read_at' => 'datetime',
        'delivered_at' => 'datetime',
        'pinned_at' => 'datetime',
    ];

    protected $appends = ['decrypted_content'];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function encryptionKey()
    {
        return $this->belongsTo(EncryptionKey::class, 'encryption_key_id', 'key_id');
    }

    // Accesseurs
    public function getDecryptedContentAttribute()
    {
        if (!$this->is_encrypted || !$this->encrypted_content) {
            return $this->content;
        }

        try {
            $encryptionService = app(\App\Services\EncryptionService::class);
            $encryptionKey = $this->encryptionKey;

            if (!$encryptionKey) {
                return '[Message crypté - clé introuvable]';
            }

            // Extraire IV et contenu crypté
            $parts = explode(':', $this->encrypted_content);
            if (count($parts) !== 2) {
                return '[Message crypté - format invalide]';
            }

            [$iv, $encrypted] = $parts;
            $key = $encryptionKey->getDecryptedKey();

            return $encryptionService->decrypt($encrypted, $key, $iv);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erreur décryptage message: ' . $e->getMessage());
            return '[Message crypté - erreur de décryptage]';
        }
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeDirectMessages($query)
    {
        return $query->whereNotNull('receiver_id')->whereNull('channel_id');
    }

    public function scopeChannelMessages($query)
    {
        return $query->whereNotNull('channel_id');
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id', 'id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to');
    }

    public function pinnedBy()
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    // Scopes supplémentaires
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'is_delivered' => true,
            'delivered_at' => now(),
        ]);
    }

    public function pin(int $userId)
    {
        $this->update([
            'is_pinned' => true,
            'pinned_at' => now(),
            'pinned_by' => $userId,
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

    public function getReactionSummary(): array
    {
        return MessageReaction::getReactionCounts($this->id);
    }
}

