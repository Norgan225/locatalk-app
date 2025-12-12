<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'caller_id',
        'receiver_id',
        'channel_id',
        'type',
        'status',
        'start_time',
        'end_time',
        'started_at',
        'ended_at',
        'duration',
        'meeting_link',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Session de clés de cryptage pour cet appel
     */
    public function sessionKey()
    {
        return $this->hasOne(CallSessionKey::class);
    }

    /**
     * Créer une session de cryptage pour cet appel
     *
     * @return CallSessionKey
     */
    public function createEncryptionSession(): CallSessionKey
    {
        return CallSessionKey::createForCall($this->id);
    }

    /**
     * Ajouter un participant à l'appel crypté
     *
     * @param int $userId
     * @return CallParticipantKey|null
     */
    public function addEncryptedParticipant(int $userId): ?CallParticipantKey
    {
        $sessionKey = $this->sessionKey;

        if (!$sessionKey) {
            $sessionKey = $this->createEncryptionSession();
        }

        return $sessionKey->addParticipant($userId);
    }

    /**
     * Vérifier si l'appel est crypté
     *
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->sessionKey !== null && $this->sessionKey->is_active;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeMissed($query)
    {
        return $query->where('status', 'missed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Methods
    public function getDurationFormattedAttribute()
    {
        if (!$this->duration) return '-';

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d min %d sec', $minutes, $seconds);
    }
}
