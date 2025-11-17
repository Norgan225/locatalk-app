<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class EncryptionService
{
    /**
     * Algorithme de cryptage utilisé
     */
    private const ALGORITHM = 'AES-256-CBC';

    /**
     * Générer une clé de cryptage unique pour une conversation
     *
     * @return string
     */
    public function generateConversationKey(): string
    {
        return base64_encode(random_bytes(32)); // 256 bits
    }

    /**
     * Générer un ID unique pour identifier la clé
     *
     * @return string
     */
    public function generateKeyId(): string
    {
        return 'key_' . Str::uuid();
    }

    /**
     * Crypter un message avec une clé spécifique
     *
     * @param string $message
     * @param string $key
     * @return array ['encrypted' => string, 'iv' => string]
     */
    public function encrypt(string $message, string $key): array
    {
        $key = base64_decode($key);
        $iv = random_bytes(openssl_cipher_iv_length(self::ALGORITHM));

        $encrypted = openssl_encrypt(
            $message,
            self::ALGORITHM,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return [
            'encrypted' => base64_encode($encrypted),
            'iv' => base64_encode($iv),
        ];
    }

    /**
     * Décrypter un message
     *
     * @param string $encryptedMessage
     * @param string $key
     * @param string $iv
     * @return string|false
     */
    public function decrypt(string $encryptedMessage, string $key, string $iv): string|false
    {
        $key = base64_decode($key);
        $encryptedMessage = base64_decode($encryptedMessage);
        $iv = base64_decode($iv);

        return openssl_decrypt(
            $encryptedMessage,
            self::ALGORITHM,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    /**
     * Crypter la clé de conversation avec la clé maître de Laravel
     * (pour stockage sécurisé en base de données)
     *
     * @param string $conversationKey
     * @return string
     */
    public function encryptKey(string $conversationKey): string
    {
        return Crypt::encryptString($conversationKey);
    }

    /**
     * Décrypter la clé de conversation
     *
     * @param string $encryptedKey
     * @return string
     */
    public function decryptKey(string $encryptedKey): string
    {
        return Crypt::decryptString($encryptedKey);
    }

    /**
     * Créer un hash sécurisé pour vérifier l'intégrité du message
     *
     * @param string $message
     * @param string $key
     * @return string
     */
    public function createHash(string $message, string $key): string
    {
        return hash_hmac('sha256', $message, $key);
    }

    /**
     * Vérifier l'intégrité d'un message
     *
     * @param string $message
     * @param string $hash
     * @param string $key
     * @return bool
     */
    public function verifyHash(string $message, string $hash, string $key): bool
    {
        return hash_equals($hash, $this->createHash($message, $key));
    }

    /**
     * Générer une paire de clés publique/privée (pour future implémentation RSA)
     *
     * @return array
     */
    public function generateKeyPair(): array
    {
        $config = [
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $resource = openssl_pkey_new($config);
        openssl_pkey_export($resource, $privateKey);
        $publicKey = openssl_pkey_get_details($resource)['key'];

        return [
            'public_key' => $publicKey,
            'private_key' => $privateKey,
        ];
    }

    // ========================================
    // MÉTHODES POUR APPELS DE GROUPE
    // ========================================

    /**
     * Générer une clé de session pour un appel de groupe
     * Utilise AES-256-GCM pour streaming temps réel
     *
     * @return string
     */
    public function generateCallSessionKey(): string
    {
        return base64_encode(random_bytes(32)); // 256 bits
    }

    /**
     * Générer un salt pour dérivation de clés
     *
     * @return string
     */
    public function generateSalt(): string
    {
        return random_bytes(16); // 128 bits
    }

    /**
     * Dériver une clé unique pour un participant (HKDF - Key Derivation Function)
     *
     * @param string $masterKey Clé maître de la session
     * @param int $userId ID de l'utilisateur
     * @param string $salt Salt de la session
     * @return string
     */
    public function deriveParticipantKey(string $masterKey, int $userId, string $salt): string
    {
        $info = 'participant_' . $userId; // Info contextuelle

        // HKDF (Hash-based Key Derivation Function)
        $derivedKey = hash_hkdf(
            'sha256',
            base64_decode($masterKey),
            32, // 256 bits
            $info,
            $salt
        );

        return base64_encode($derivedKey);
    }

    /**
     * Crypter un paquet média (audio/vidéo) pour streaming temps réel
     * Utilise AES-256-GCM pour AEAD (Authenticated Encryption with Associated Data)
     *
     * @param string $mediaData Données média brutes
     * @param string $key Clé de cryptage
     * @param string $nonce Nonce unique (numéro de séquence)
     * @return array ['encrypted' => string, 'tag' => string]
     */
    public function encryptMediaPacket(string $mediaData, string $key, string $nonce): array
    {
        $key = base64_decode($key);
        $nonce = base64_decode($nonce);

        $tag = '';
        $encrypted = openssl_encrypt(
            $mediaData,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '', // Associated Data (optionnel)
            16  // Tag length
        );

        return [
            'encrypted' => base64_encode($encrypted),
            'tag' => base64_encode($tag),
        ];
    }

    /**
     * Décrypter un paquet média
     *
     * @param string $encryptedData Données cryptées
     * @param string $key Clé de décryptage
     * @param string $nonce Nonce unique
     * @param string $tag Tag d'authentification
     * @return string|false
     */
    public function decryptMediaPacket(string $encryptedData, string $key, string $nonce, string $tag): string|false
    {
        $key = base64_decode($key);
        $encryptedData = base64_decode($encryptedData);
        $nonce = base64_decode($nonce);
        $tag = base64_decode($tag);

        return openssl_decrypt(
            $encryptedData,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );
    }

    /**
     * Générer un nonce unique pour chaque paquet média
     * Le nonce doit être unique et peut être basé sur un compteur + timestamp
     *
     * @param int $counter Compteur de paquets
     * @param int|null $timestamp Timestamp (optionnel)
     * @return string
     */
    public function generateNonce(int $counter, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();

        // Combiner timestamp (4 bytes) + counter (8 bytes) = 12 bytes pour GCM
        $nonce = pack('N', $timestamp) . pack('J', $counter);

        return base64_encode($nonce);
    }
}

