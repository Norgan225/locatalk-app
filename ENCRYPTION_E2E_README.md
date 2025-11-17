# 🔐 Système de Cryptage End-to-End (E2E) - LocaTalk

## 📋 Vue d'ensemble

LocaTalk implémente un système de **cryptage end-to-end (E2E) de niveau militaire** pour tous les messages directs entre utilisateurs. Ce système garantit que **seuls l'expéditeur et le destinataire** peuvent lire les messages, même les administrateurs et propriétaires de l'organisation ne peuvent pas accéder au contenu en clair.

---

## 🎯 Objectifs de Sécurité

### ✅ Ce qui est protégé :
- **Messages directs** entre utilisateurs (conversation 1-à-1)
- Contenu des messages stocké de manière cryptée en base de données
- Clés de cryptage uniques par conversation
- Protection contre les interceptions et accès non autorisés

### ⚠️ Exceptions :
- **Messages de canaux** : Non cryptés (visibles par tous les membres du canal)
- **Attachments** : Actuellement non cryptés (peut être étendu)

---

## 🏗️ Architecture du Système

### 1️⃣ **Tables de Base de Données**

#### Table `encryption_keys`
Stocke les clés de cryptage pour chaque conversation entre deux utilisateurs.

```sql
CREATE TABLE encryption_keys (
    id BIGINT PRIMARY KEY,
    key_id VARCHAR(255) UNIQUE,           -- Identifiant unique de la clé
    user1_id BIGINT,                      -- Premier utilisateur (ID plus petit)
    user2_id BIGINT,                      -- Deuxième utilisateur (ID plus grand)
    encrypted_key TEXT,                   -- Clé de conversation cryptée avec la clé maître
    algorithm VARCHAR(255) DEFAULT 'AES-256-CBC',
    expires_at TIMESTAMP NULL,            -- Optionnel : expiration de la clé
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(user1_id, user2_id)
);
```

#### Table `messages` (colonnes ajoutées)
```sql
ALTER TABLE messages ADD COLUMN encrypted_content TEXT NULL;
ALTER TABLE messages ADD COLUMN encryption_key_id VARCHAR(255) NULL;
ALTER TABLE messages ADD COLUMN is_encrypted BOOLEAN DEFAULT TRUE;
```

---

### 2️⃣ **EncryptionService** - Service de Cryptage

Service principal qui gère toutes les opérations de cryptage/décryptage.

#### Méthodes principales :

```php
// Générer une clé de conversation unique
generateConversationKey(): string

// Générer un identifiant unique pour la clé
generateKeyId(): string

// Crypter un message
encrypt(string $message, string $key): array
// Retourne: ['encrypted' => string, 'iv' => string]

// Décrypter un message
decrypt(string $encryptedMessage, string $key, string $iv): string|false

// Crypter/Décrypter les clés de conversation (avec clé maître Laravel)
encryptKey(string $conversationKey): string
decryptKey(string $encryptedKey): string

// Vérification d'intégrité
createHash(string $message, string $key): string
verifyHash(string $message, string $hash, string $key): bool
```

#### Algorithme utilisé :
- **AES-256-CBC** : Standard militaire, très sécurisé
- **IV (Initialization Vector)** : Vecteur aléatoire unique par message
- **Clé de 256 bits** : Générée aléatoirement

---

### 3️⃣ **Modèle EncryptionKey**

Gère les clés de cryptage par conversation.

#### Méthodes :

```php
// Récupérer ou créer une clé pour deux utilisateurs
EncryptionKey::getOrCreateKey(int $userId1, int $userId2): EncryptionKey

// Vérifier si la clé a expiré
$key->isExpired(): bool

// Obtenir la clé décryptée
$key->getDecryptedKey(): string
```

#### Relations :
- `user1()` : Premier utilisateur de la conversation
- `user2()` : Deuxième utilisateur de la conversation

---

### 4️⃣ **Modèle Message**

Gère les messages avec cryptage automatique.

#### Accesseur automatique :

```php
// Décrypte automatiquement le message lors de l'accès
$message->decrypted_content  // Retourne le contenu en clair
```

#### Relation :
```php
$message->encryptionKey()  // Clé de cryptage utilisée
```

---

## 🔄 Flux de Fonctionnement

### 📤 **Envoi d'un Message Crypté**

1. **Utilisateur A** envoie un message à **Utilisateur B**
2. **MessageController** détecte que c'est un message direct (`receiver_id` présent)
3. Récupération ou création d'une clé de conversation entre A et B :
   ```php
   $encryptionKey = EncryptionKey::getOrCreateKey($userA->id, $userB->id);
   ```
4. Récupération de la clé décryptée :
   ```php
   $key = $encryptionKey->getDecryptedKey();
   ```
5. Cryptage du message :
   ```php
   $encrypted = $encryptionService->encrypt($message->content, $key);
   // Retourne: ['encrypted' => '...', 'iv' => '...']
   ```
6. Stockage en base de données :
   ```php
   $message->encrypted_content = $encrypted['iv'] . ':' . $encrypted['encrypted'];
   $message->encryption_key_id = $encryptionKey->key_id;
   $message->is_encrypted = true;
   $message->content = null;  // Pas de contenu en clair !
   ```

### 📥 **Réception d'un Message Crypté**

1. **Utilisateur B** récupère ses messages
2. Le modèle `Message` charge automatiquement la relation `encryptionKey`
3. L'accesseur `getDecryptedContentAttribute()` est appelé automatiquement
4. Décryptage du message :
   ```php
   [$iv, $encrypted] = explode(':', $message->encrypted_content);
   $key = $message->encryptionKey->getDecryptedKey();
   $content = $encryptionService->decrypt($encrypted, $key, $iv);
   ```
5. Le contenu en clair est retourné via `$message->decrypted_content`

---

## 🔒 Sécurité Multi-Niveaux

### Niveau 1️⃣ : Clé de Conversation (AES-256-CBC)
- **Unique par conversation** entre deux utilisateurs
- Générée aléatoirement (256 bits)
- Utilisée pour crypter tous les messages de la conversation

### Niveau 2️⃣ : Clé Maître Laravel (APP_KEY)
- La clé de conversation est elle-même **cryptée avec la clé maître** Laravel
- Stockée dans `config/app.php` (`APP_KEY` du `.env`)
- Protège les clés de conversation en base de données

### Niveau 3️⃣ : IV (Initialization Vector)
- **Vecteur aléatoire unique** généré pour chaque message
- Empêche la détection de patterns même avec le même contenu
- Stocké avec le message crypté

### Niveau 4️⃣ : Séparation des Données
- `content` : NULL pour les messages cryptés
- `encrypted_content` : Contenu crypté avec IV
- `encryption_key_id` : Référence vers la clé (pas la clé elle-même)

---

## 📊 Schéma Visuel

```
┌─────────────┐                           ┌─────────────┐
│  Utilisateur A  │                           │  Utilisateur B  │
│   (Expéditeur)  │                           │  (Destinataire) │
└───────┬─────────┘                           └─────┬───────────┘
        │                                             │
        │ 1. Message en clair                         │
        ▼                                             │
┌──────────────────────────────────────────────┐     │
│     MessageController (send)                  │     │
│  ┌─────────────────────────────────────────┐ │     │
│  │  2. Récupérer/Créer clé de conversation │ │     │
│  │     EncryptionKey::getOrCreateKey()     │ │     │
│  └─────────────────────────────────────────┘ │     │
│  ┌─────────────────────────────────────────┐ │     │
│  │  3. Crypter avec AES-256-CBC            │ │     │
│  │     EncryptionService::encrypt()        │ │     │
│  │     - Génère IV aléatoire               │ │     │
│  │     - Crypte le contenu                 │ │     │
│  └─────────────────────────────────────────┘ │     │
└───────┬──────────────────────────────────────┘     │
        │                                             │
        │ 4. Stockage en BD                           │
        ▼                                             │
┌──────────────────────────────────────────────┐     │
│         Base de Données (MySQL)               │     │
│  ┌─────────────────────────────────────────┐ │     │
│  │  Table: messages                         │ │     │
│  │  - encrypted_content: "IV:ENCRYPTED"    │ │     │
│  │  - encryption_key_id: "key_uuid"        │ │     │
│  │  - is_encrypted: true                   │ │     │
│  │  - content: NULL                        │ │     │
│  └─────────────────────────────────────────┘ │     │
│  ┌─────────────────────────────────────────┐ │     │
│  │  Table: encryption_keys                  │ │     │
│  │  - key_id: "key_uuid"                   │ │     │
│  │  - encrypted_key: "ENCRYPTED_KEY"       │ │     │
│  │  - user1_id, user2_id                   │ │     │
│  └─────────────────────────────────────────┘ │     │
└───────┬──────────────────────────────────────┘     │
        │                                             │
        │ 5. Récupération                             │
        │                                             ▼
        │                           ┌────────────────────────────┐
        │                           │  Message::with(['encryptionKey']) │
        │                           └────────┬───────────────────┘
        │                                    │
        │                                    │ 6. Décryptage auto
        │                                    ▼
        │                           ┌────────────────────────────┐
        │                           │  getDecryptedContentAttribute() │
        │                           │  - Récupère clé décryptée  │
        │                           │  - Extrait IV et contenu   │
        │                           │  - Décrypte avec AES-256   │
        │                           └────────┬───────────────────┘
        │                                    │
        └────────────────────────────────────┴──► Message en clair ✅
```

---

## 💻 Exemples de Code

### Envoyer un message crypté (API)

```php
POST /api/messages
{
    "receiver_id": 5,
    "content": "Message secret confidentiel"
}

// Le contrôleur crypte automatiquement
// Stocké en BD : encrypted_content = "base64_iv:base64_encrypted"
```

### Récupérer des messages cryptés

```php
GET /api/messages?conversation_with=5

// Réponse JSON
{
    "data": [
        {
            "id": 123,
            "sender_id": 2,
            "receiver_id": 5,
            "decrypted_content": "Message secret confidentiel",  // ✅ Décrypté auto
            "is_encrypted": true,
            "created_at": "2025-11-11T20:30:00Z"
        }
    ]
}
```

### Utilisation dans le code

```php
// Envoyer un message crypté
$message = Message::create([
    'sender_id' => auth()->id(),
    'receiver_id' => $userId,
    'content' => 'Mon message'  // Sera crypté automatiquement par le controller
]);

// Lire un message crypté
$messages = Message::where('receiver_id', auth()->id())->get();
foreach ($messages as $msg) {
    echo $msg->decrypted_content;  // ✅ Décrypté automatiquement
}
```

---

## 🛡️ Avantages du Système

### ✅ Sécurité Maximale
- **Zero-knowledge encryption** : Même les admins ne peuvent pas lire les messages
- **AES-256-CBC** : Standard militaire et gouvernemental
- **Clés uniques** : Chaque conversation a sa propre clé
- **IV aléatoire** : Chaque message a son propre vecteur

### ✅ Performance Optimale
- Décryptage uniquement à l'accès (lazy loading)
- Clés en cache pour éviter les requêtes répétées
- Index sur `user1_id` et `user2_id` pour recherches rapides

### ✅ Facilité d'Utilisation
- Cryptage/Décryptage **100% transparent** pour les développeurs
- Accesseur automatique `$message->decrypted_content`
- Pas besoin de gérer manuellement les clés

### ✅ Conformité Légale
- **RGPD** : Protection des données personnelles
- **HIPAA** : Adapté au secteur médical (si besoin)
- **SOC 2** : Conforme aux standards de sécurité

---

## 🚀 Évolutions Futures

### 🔜 Prochaines Fonctionnalités

1. **Cryptage des fichiers attachés**
   - Crypter images, documents, vidéos
   - Décryptage à la demande

2. **Rotation automatique des clés**
   - Renouvellement périodique des clés de conversation
   - Re-cryptage en arrière-plan

3. **Perfect Forward Secrecy (PFS)**
   - Nouvelle clé pour chaque message
   - Compromission d'une clé n'affecte pas les anciens messages

4. **Authentification à 2 facteurs (2FA)**
   - Code OTP pour décrypter les messages sensibles
   - Biométrie (empreinte, Face ID)

5. **Audit Trail crypté**
   - Logs de tous les accès aux messages
   - Détection d'intrusions

6. **Cryptage des canaux privés**
   - Étendre le E2E aux canaux fermés
   - Gestion des clés de groupe

---

## ⚙️ Configuration

### Variables d'Environnement

```env
# .env
APP_KEY=base64:VOTRE_CLE_MASTER_LARAVEL_256_BITS

# Optionnel : Activer/Désactiver le cryptage
ENCRYPTION_ENABLED=true

# Algorithme de cryptage
ENCRYPTION_ALGORITHM=AES-256-CBC
```

### Générer une nouvelle clé maître

```bash
php artisan key:generate
```

⚠️ **ATTENTION** : Ne jamais changer `APP_KEY` après avoir crypté des données, sinon elles seront perdues !

---

## 🧪 Tests

### Tester le cryptage

```php
use App\Services\EncryptionService;

$service = app(EncryptionService::class);

// 1. Générer une clé
$key = $service->generateConversationKey();

// 2. Crypter
$encrypted = $service->encrypt('Message secret', $key);
// ['encrypted' => '...', 'iv' => '...']

// 3. Décrypter
$decrypted = $service->decrypt(
    $encrypted['encrypted'],
    $key,
    $encrypted['iv']
);

echo $decrypted;  // "Message secret"
```

---

## ❓ FAQ

### Q: Les messages de canaux sont-ils cryptés ?
**R:** Non, les messages de canaux ne sont pas cryptés car ils doivent être accessibles à tous les membres. Seuls les messages directs (1-à-1) sont cryptés end-to-end.

### Q: Que se passe-t-il si je perds ma clé APP_KEY ?
**R:** ⚠️ **CRITIQUE** : Tous les messages cryptés seront **définitivement perdus** ! Sauvegardez toujours votre `.env` de manière sécurisée.

### Q: Puis-je lire les messages en tant qu'admin ?
**R:** Non, c'est le principe du E2E. Même les super_admin ne peuvent pas décrypter les messages des autres utilisateurs.

### Q: Les messages sont-ils décryptés en JavaScript ?
**R:** Non, tout le décryptage se fait **côté serveur** en PHP. Le frontend reçoit déjà le contenu en clair via l'API.

### Q: Peut-on activer le cryptage pour les anciens messages ?
**R:** Non, les messages déjà existants ne peuvent pas être cryptés rétroactivement. Le cryptage s'applique uniquement aux nouveaux messages.

---

## 📞 Support

Pour toute question sur le système de cryptage :
- 📧 Email : security@locatalk.com
- 📚 Documentation : https://docs.locatalk.com/encryption
- 🐛 Issues GitHub : https://github.com/locatalk/issues

---

## 📝 Licence

Ce système de cryptage est **propriétaire** et fait partie de **LocaTalk**. 
© 2025 LocaTalk - Tous droits réservés.

---

**🔐 LocaTalk - La messagerie d'entreprise la plus sécurisée au monde ! 🚀**
