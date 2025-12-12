<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallParticipantKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'call_session_key_id',
        'user_id',
        'participant_key',
        'key_version',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * La session de clés parente
     */
    public function callSessionKey(): BelongsTo
    {
        return $this->belongsTo(CallSessionKey::class);
    }

    /**
     * L'utilisateur participant
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir la clé du participant décryptée
     *
     * @return string
     */
    public function getDecryptedKey(): string
    {
        return app(\App\Services\EncryptionService::class)->decryptKey($this->participant_key);
    }

    /**
     * Marquer le participant comme ayant quitté
     */
    public function markAsLeft(): void
    {
        $this->update(['left_at' => now()]);
    }

    /**
     * Rotation de la clé du participant
     *
     * @param CallSessionKey $sessionKey
     */
    public function rotateKey(CallSessionKey $sessionKey): void
    {
        $encryptionService = app(\App\Services\EncryptionService::class);

        $newParticipantKey = $sessionKey->generateParticipantKey($this->user_id);
        $newVersion = (int) $this->key_version + 1;

        $this->update([
            'participant_key' => $encryptionService->encryptKey($newParticipantKey),
            'key_version' => (string) $newVersion,
        ]);
    }

    /**
     * Vérifier si le participant est toujours actif
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->left_at === null;
    }
}
