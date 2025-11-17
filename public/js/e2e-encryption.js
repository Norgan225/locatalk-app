/**
 * Service de chiffrement E2E (End-to-End Encryption)
 * Utilise Web Crypto API pour un chiffrement sécurisé côté client
 */

class E2EEncryptionService {
    constructor() {
        this.keyAlgorithm = {
            name: 'RSA-OAEP',
            modulusLength: 2048,
            publicExponent: new Uint8Array([1, 0, 1]),
            hash: 'SHA-256'
        };

        this.symmetricAlgorithm = {
            name: 'AES-GCM',
            length: 256
        };

        // Stockage local des clés (en mémoire seulement pour la session)
        this.keys = new Map(); // userId -> {publicKey, privateKey, sharedSecret}
        this.initialized = false;
    }

    /**
     * Initialiser le service de chiffrement
     */
    async initialize() {
        if (this.initialized) return;

        console.log('🔐 Initialisation du service E2E Encryption...');

        try {
            // Vérifier le support Web Crypto API
            if (!window.crypto || !window.crypto.subtle) {
                throw new Error('Web Crypto API non supporté');
            }

            // Générer la paire de clés RSA pour l'utilisateur actuel
            await this.generateKeyPair();

            this.initialized = true;
            console.log('✅ Service E2E Encryption initialisé');
        } catch (error) {
            console.error('❌ Erreur lors de l\'initialisation E2E:', error);
            throw error;
        }
    }

    /**
     * Générer une paire de clés RSA pour l'utilisateur
     */
    async generateKeyPair() {
        try {
            const keyPair = await window.crypto.subtle.generateKey(
                this.keyAlgorithm,
                true, // extractable
                ['encrypt', 'decrypt']
            );

            // Exporter les clés pour le stockage
            const publicKey = await window.crypto.subtle.exportKey('spki', keyPair.publicKey);
            const privateKey = await window.crypto.subtle.exportKey('pkcs8', keyPair.privateKey);

            // Stocker en base64
            this.keys.set('currentUser', {
                publicKey: this.arrayBufferToBase64(publicKey),
                privateKey: this.arrayBufferToBase64(privateKey),
                publicKeyObj: keyPair.publicKey,
                privateKeyObj: keyPair.privateKey
            });

            console.log('🔑 Paire de clés RSA générée pour l\'utilisateur actuel');
        } catch (error) {
            console.error('❌ Erreur génération clés RSA:', error);
            throw error;
        }
    }

    /**
     * Importer une clé publique depuis base64
     */
    async importPublicKey(base64Key) {
        try {
            const keyData = this.base64ToArrayBuffer(base64Key);
            return await window.crypto.subtle.importKey(
                'spki',
                keyData,
                this.keyAlgorithm,
                false,
                ['encrypt']
            );
        } catch (error) {
            console.error('❌ Erreur import clé publique:', error);
            throw error;
        }
    }

    /**
     * Importer une clé privée depuis base64
     */
    async importPrivateKey(base64Key) {
        try {
            const keyData = this.base64ToArrayBuffer(base64Key);
            return await window.crypto.subtle.importKey(
                'pkcs8',
                keyData,
                this.keyAlgorithm,
                false,
                ['decrypt']
            );
        } catch (error) {
            console.error('❌ Erreur import clé privée:', error);
            throw error;
        }
    }

    /**
     * Générer un secret partagé avec un autre utilisateur
     */
    async generateSharedSecret(otherUserId, otherPublicKeyBase64) {
        try {
            const otherPublicKey = await this.importPublicKey(otherPublicKeyBase64);

            // Générer un secret symétrique aléatoire
            const symmetricKey = await window.crypto.subtle.generateKey(
                this.symmetricAlgorithm,
                true,
                ['encrypt', 'decrypt']
            );

            // L'exporter pour l'échange
            const exportedKey = await window.crypto.subtle.exportKey('raw', symmetricKey);
            const keyBase64 = this.arrayBufferToBase64(exportedKey);

            // Chiffrer la clé symétrique avec la clé publique du destinataire
            const encryptedKey = await window.crypto.subtle.encrypt(
                { name: 'RSA-OAEP' },
                otherPublicKey,
                this.stringToArrayBuffer(keyBase64)
            );

            // Stocker le secret partagé localement
            this.keys.set(otherUserId, {
                sharedSecret: symmetricKey,
                sharedSecretBase64: keyBase64
            });

            console.log(`🔐 Secret partagé généré avec l'utilisateur ${otherUserId}`);

            return {
                encryptedKey: this.arrayBufferToBase64(encryptedKey),
                keyId: `key_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
            };
        } catch (error) {
            console.error('❌ Erreur génération secret partagé:', error);
            throw error;
        }
    }

    /**
     * Déchiffrer et stocker un secret partagé reçu
     */
    async decryptSharedSecret(otherUserId, encryptedKeyBase64) {
        try {
            const currentUserKeys = this.keys.get('currentUser');
            if (!currentUserKeys || !currentUserKeys.privateKeyObj) {
                throw new Error('Clés privées non disponibles');
            }

            // Déchiffrer la clé symétrique
            const encryptedKey = this.base64ToArrayBuffer(encryptedKeyBase64);
            const decryptedKey = await window.crypto.subtle.decrypt(
                { name: 'RSA-OAEP' },
                currentUserKeys.privateKeyObj,
                encryptedKey
            );

            const keyBase64 = this.arrayBufferToString(decryptedKey);

            // Importer la clé symétrique
            const symmetricKey = await window.crypto.subtle.importKey(
                'raw',
                this.stringToArrayBuffer(keyBase64),
                this.symmetricAlgorithm,
                false,
                ['encrypt', 'decrypt']
            );

            // Stocker le secret partagé
            this.keys.set(otherUserId, {
                sharedSecret: symmetricKey,
                sharedSecretBase64: keyBase64
            });

            console.log(`🔓 Secret partagé déchiffré avec l'utilisateur ${otherUserId}`);
        } catch (error) {
            console.error('❌ Erreur déchiffrement secret partagé:', error);
            throw error;
        }
    }

    /**
     * Chiffrer un message pour un destinataire
     */
    async encryptMessage(message, recipientUserId) {
        try {
            const recipientKeys = this.keys.get(recipientUserId);
            if (!recipientKeys || !recipientKeys.sharedSecret) {
                throw new Error(`Aucun secret partagé avec l'utilisateur ${recipientUserId}`);
            }

            // Générer un IV aléatoire
            const iv = window.crypto.getRandomValues(new Uint8Array(12));

            // Chiffrer le message
            const encrypted = await window.crypto.subtle.encrypt(
                {
                    name: 'AES-GCM',
                    iv: iv
                },
                recipientKeys.sharedSecret,
                this.stringToArrayBuffer(message)
            );

            const result = {
                encrypted: this.arrayBufferToBase64(encrypted),
                iv: this.arrayBufferToBase64(iv),
                keyId: `shared_${recipientUserId}_${Date.now()}`
            };

            console.log(`🔒 Message chiffré pour l'utilisateur ${recipientUserId}`);
            return result;
        } catch (error) {
            console.error('❌ Erreur chiffrement message:', error);
            throw error;
        }
    }

    /**
     * Déchiffrer un message reçu
     */
    async decryptMessage(encryptedData, senderUserId) {
        try {
            const senderKeys = this.keys.get(senderUserId);
            if (!senderKeys || !senderKeys.sharedSecret) {
                throw new Error(`Aucun secret partagé avec l'utilisateur ${senderUserId}`);
            }

            const iv = this.base64ToArrayBuffer(encryptedData.iv);
            const encrypted = this.base64ToArrayBuffer(encryptedData.encrypted);

            const decrypted = await window.crypto.subtle.decrypt(
                {
                    name: 'AES-GCM',
                    iv: iv
                },
                senderKeys.sharedSecret,
                encrypted
            );

            const message = this.arrayBufferToString(decrypted);
            console.log(`🔓 Message déchiffré de l'utilisateur ${senderUserId}`);
            return message;
        } catch (error) {
            console.error('❌ Erreur déchiffrement message:', error);
            throw error;
        }
    }

    /**
     * Obtenir la clé publique de l'utilisateur actuel (pour l'échange)
     */
    getPublicKey() {
        const keys = this.keys.get('currentUser');
        return keys ? keys.publicKey : null;
    }

    /**
     * Vérifier si un secret partagé existe avec un utilisateur
     */
    hasSharedSecret(userId) {
        const keys = this.keys.get(userId);
        return keys && keys.sharedSecret;
    }

    // Utilitaires de conversion
    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }

    base64ToArrayBuffer(base64) {
        const binary = window.atob(base64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes.buffer;
    }

    stringToArrayBuffer(str) {
        const encoder = new TextEncoder();
        return encoder.encode(str);
    }

    arrayBufferToString(buffer) {
        const decoder = new TextDecoder();
        return decoder.decode(buffer);
    }
}

// Instance globale
window.E2EEncryptionService = E2EEncryptionService;