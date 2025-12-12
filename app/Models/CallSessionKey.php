<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallSessionKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'call_id',
        'master_key',
        'algorithm',
        'salt',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public $timestamps = false; // On utilise created_at uniquement

    /**
     * L'appel associé à cette session
     */
    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    /**
     * Les clés des participants
     */
    public function participantKeys(): HasMany
    {
        return $this->hasMany(CallParticipantKey::class);
    }

    /**
     * Créer une nouvelle session de clés pour un appel
     *
     * @param int $callId
     * @return self
     */
    public static function createForCall(int $callId): self
    {
        $encryptionService = app(\App\Services\EncryptionService::class);

        $masterKey = $encryptionService->generateCallSessionKey();
        $salt = $encryptionService->generateSalt();

        return self::create([
            'session_id' => 'session_' . \Illuminate\Support\Str::uuid(),
            'call_id' => $callId,
            'master_key' => $encryptionService->encryptKey($masterKey),
            'algorithm' => 'AES-256-GCM',
            'salt' => base64_encode($salt),
            'is_active' => true,
        ]);
    }

    /**
     * Obtenir la clé maître décryptée
     *
     * @return string
     */
    public function getDecryptedMasterKey(): string
    {
        return app(\App\Services\EncryptionService::class)->decryptKey($this->master_key);
    }

    /**
     * Générer une clé pour un participant spécifique
     *
     * @param int $userId
     * @return string
     */
    public function generateParticipantKey(int $userId): string
    {
        $encryptionService = app(\App\Services\EncryptionService::class);
        $masterKey = $this->getDecryptedMasterKey();
        $salt = base64_decode($this->salt);

        // Dériver une clé unique pour ce participant (HKDF - Key Derivation)
        return $encryptionService->deriveParticipantKey($masterKey, $userId, $salt);
    }

    /**
     * Ajouter un participant à la session
     *
     * @param int $userId
     * @return CallParticipantKey
     */
    public function addParticipant(int $userId): CallParticipantKey
    {
        $participantKey = $this->generateParticipantKey($userId);
        $encryptionService = app(\App\Services\EncryptionService::class);

        return CallParticipantKey::create([
            'call_session_key_id' => $this->id,
            'user_id' => $userId,
            'participant_key' => $encryptionService->encryptKey($participantKey),
            'key_version' => '1',
            'joined_at' => now(),
        ]);
    }

    /**
     * Désactiver la session (fin d'appel)
     */
    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'expires_at' => now()->addHours(24), // Garder 24h pour logs
        ]);
    }

    /**
     * Vérifier si la session a expiré
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Rotation de la clé maître (pour sécurité renforcée)
     *
     * @return self
     */
    public function rotateKey(): self
    {
        $encryptionService = app(\App\Services\EncryptionService::class);

        $newMasterKey = $encryptionService->generateCallSessionKey();
        $newSalt = $encryptionService->generateSalt();

        $this->update([
            'master_key' => $encryptionService->encryptKey($newMasterKey),
            'salt' => base64_encode($newSalt),
        ]);

        // Incrémenter la version des clés de tous les participants
        $this->participantKeys()->each(function ($participantKey) {
            $participantKey->rotateKey($this);
        });

        return $this;
    }
}
