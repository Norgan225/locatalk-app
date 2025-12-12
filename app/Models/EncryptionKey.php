<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncryptionKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key_id',
        'user1_id',
        'user2_id',
        'encrypted_key',
        'algorithm',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Utilisateur 1 de la conversation
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Utilisateur 2 de la conversation
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Récupérer ou créer une clé pour deux utilisateurs
     *
     * @param int $userId1
     * @param int $userId2
     * @return self
     */
    public static function getOrCreateKey(int $userId1, int $userId2): self
    {
        // S'assurer que user1_id < user2_id pour éviter les doublons
        if ($userId1 > $userId2) {
            [$userId1, $userId2] = [$userId2, $userId1];
        }

        return self::firstOrCreate(
            [
                'user1_id' => $userId1,
                'user2_id' => $userId2,
            ],
            [
                'key_id' => app(\App\Services\EncryptionService::class)->generateKeyId(),
                'encrypted_key' => app(\App\Services\EncryptionService::class)->encryptKey(
                    app(\App\Services\EncryptionService::class)->generateConversationKey()
                ),
                'algorithm' => 'AES-256-CBC',
            ]
        );
    }

    /**
     * Vérifier si la clé a expiré
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Obtenir la clé décryptée
     *
     * @return string
     */
    public function getDecryptedKey(): string
    {
        return app(\App\Services\EncryptionService::class)->decryptKey($this->encrypted_key);
    }
}
