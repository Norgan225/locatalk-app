# 🧪 Tests API - LocaTalk

Guide complet pour tester tous les endpoints API avec curl ou Postman.

## 📋 Table des Matières

1. [Authentification](#authentification)
2. [Dashboard & Analytics](#dashboard--analytics)
3. [Profile](#profile)
4. [Users](#users)
5. [Organizations](#organizations)
6. [Departments](#departments)
7. [Projects & Tasks](#projects--tasks)
8. [Messages](#messages)
9. [Channels](#channels)
10. [Calls](#calls)
11. [Meetings](#meetings)
12. [Notifications](#notifications)

---

## 🔐 Authentification

### Login (Obtenir un token)

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "owner@example.com",
    "password": "password",
    "device_name": "test-device",
    "mac_address": "00:00:00:00:00:01"
  }'
```

**Réponse attendue** :
```json
{
  "token": "1|abcd1234...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "owner@example.com",
    "role": "owner"
  }
}
```

**💡 Important** : Copier le token pour les requêtes suivantes !

### Me (Utilisateur connecté)

```bash
curl http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 📊 Dashboard & Analytics

### Dashboard principal

```bash
curl http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

**Réponse pour owner** :
```json
{
  "personal_stats": {
    "my_projects": 3,
    "my_tasks": 7,
    "unread_messages": 5,
    "upcoming_meetings": 2
  },
  "organization_stats": {
    "total_users": 50,
    "total_departments": 5,
    "total_projects": 15,
    "active_tasks": 45
  },
  "platform_stats": {
    "total_organizations": 3,
    "total_users_all": 150,
    "total_projects_all": 45
  }
}
```

### Analytics (Graphiques)

```bash
curl http://localhost:8000/api/dashboard/analytics \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 👤 Profile

### Voir mon profil

```bash
curl http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Mettre à jour mon profil

```bash
curl -X PUT http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "phone": "+33612345678",
    "position": "Développeur Senior"
  }'
```

### Changer le mot de passe

```bash
curl -X POST http://localhost:8000/api/profile/change-password \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "oldpassword",
    "new_password": "NewPassword123!",
    "new_password_confirmation": "NewPassword123!"
  }'
```

### Upload avatar

```bash
curl -X POST http://localhost:8000/api/profile/avatar \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -F "avatar=@/path/to/image.jpg"
```

### Supprimer avatar

```bash
curl -X DELETE http://localhost:8000/api/profile/avatar \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Mes appareils

```bash
curl http://localhost:8000/api/profile/devices \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Révoquer un appareil

```bash
curl -X POST http://localhost:8000/api/profile/devices/2/revoke \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 👥 Users

### Lister les utilisateurs

```bash
curl http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer un utilisateur

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "organization_id": 1,
    "department_id": 1,
    "name": "Marie Martin",
    "email": "marie@example.com",
    "password": "Password123!",
    "role": "user",
    "phone": "+33612345678",
    "position": "Chef de Projet"
  }'
```

### Voir un utilisateur

```bash
curl http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Mettre à jour un utilisateur

```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Marie Dupont",
    "position": "Directrice de Projet"
  }'
```

### Supprimer un utilisateur (soft delete)

```bash
curl -X DELETE http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Restaurer un utilisateur

```bash
curl -X POST http://localhost:8000/api/users/1/restore \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 🏢 Organizations

### Lister les organisations (owner only)

```bash
curl http://localhost:8000/api/organizations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer une organisation

```bash
curl -X POST http://localhost:8000/api/organizations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Tech Corp",
    "settings": {
      "timezone": "Europe/Paris",
      "language": "fr"
    }
  }'
```

### Voir une organisation

```bash
curl http://localhost:8000/api/organizations/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Activer/Désactiver accès distant

```bash
curl -X POST http://localhost:8000/api/organizations/1/toggle-remote-access \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 🏬 Departments

### Lister les départements

```bash
curl http://localhost:8000/api/departments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer un département

```bash
curl -X POST http://localhost:8000/api/departments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "organization_id": 1,
    "name": "Développement",
    "description": "Équipe de développement logiciel"
  }'
```

### Voir un département

```bash
curl http://localhost:8000/api/departments/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Activer/Désactiver un département

```bash
curl -X POST http://localhost:8000/api/departments/1/toggle-status \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 📂 Projects & Tasks

### Lister les projets

```bash
curl http://localhost:8000/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer un projet

```bash
curl -X POST http://localhost:8000/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "organization_id": 1,
    "name": "Refonte Site Web",
    "description": "Modernisation du site web",
    "status": "in_progress",
    "start_date": "2025-11-01",
    "end_date": "2026-01-31"
  }'
```

### Assigner des utilisateurs au projet

```bash
curl -X POST http://localhost:8000/api/projects/1/assign-users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "user_ids": [2, 3, 4],
    "roles": {
      "2": "manager",
      "3": "member",
      "4": "member"
    }
  }'
```

### Lister mes tâches

```bash
curl http://localhost:8000/api/tasks/my-tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer une tâche

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "project_id": 1,
    "title": "Développer API REST",
    "description": "Créer les endpoints CRUD",
    "priority": "high",
    "status": "todo",
    "assigned_to": 2,
    "due_date": "2025-11-15"
  }'
```

### Marquer une tâche comme complétée

```bash
curl -X POST http://localhost:8000/api/tasks/1/complete \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Changer le statut d'une tâche

```bash
curl -X POST http://localhost:8000/api/tasks/1/change-status \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in_progress"
  }'
```

---

## 💬 Messages

### Lister mes conversations

```bash
curl http://localhost:8000/api/messages/conversations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Lister les messages (direct ou canal)

```bash
# Messages directs avec un utilisateur
curl "http://localhost:8000/api/messages?receiver_id=2" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"

# Messages dans un canal
curl "http://localhost:8000/api/messages?channel_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Envoyer un message direct

```bash
curl -X POST http://localhost:8000/api/messages \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "receiver_id": 2,
    "content": "Bonjour, comment vas-tu ?"
  }'
```

### Envoyer un message dans un canal

```bash
curl -X POST http://localhost:8000/api/messages \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "channel_id": 1,
    "content": "Réunion dans 10 minutes !"
  }'
```

### Envoyer un message avec pièces jointes

```bash
curl -X POST http://localhost:8000/api/messages \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -F "receiver_id=2" \
  -F "content=Voici les documents" \
  -F "attachments[]=@/path/to/file1.pdf" \
  -F "attachments[]=@/path/to/file2.png"
```

### Nombre de messages non lus

```bash
curl http://localhost:8000/api/messages/unread-count \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Marquer un message comme lu

```bash
curl -X POST http://localhost:8000/api/messages/1/mark-read \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Marquer tous les messages comme lus

```bash
curl -X POST http://localhost:8000/api/messages/mark-all-read \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Rechercher dans les messages

```bash
curl "http://localhost:8000/api/messages/search?query=réunion" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 📢 Channels

### Lister les canaux

```bash
# Tous les canaux
curl http://localhost:8000/api/channels \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"

# Mes canaux uniquement
curl "http://localhost:8000/api/channels?my_channels=true" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer un canal

```bash
curl -X POST http://localhost:8000/api/channels \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "organization_id": 1,
    "department_id": 1,
    "name": "Général",
    "description": "Canal principal de l'\''équipe",
    "type": "public",
    "user_ids": [2, 3, 4]
  }'
```

### Rejoindre un canal

```bash
curl -X POST http://localhost:8000/api/channels/1/join \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Quitter un canal

```bash
curl -X POST http://localhost:8000/api/channels/1/leave \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Ajouter des membres au canal

```bash
curl -X POST http://localhost:8000/api/channels/1/members \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "user_ids": [5, 6, 7]
  }'
```

---

## 📞 Calls

### Lister mes appels

```bash
curl "http://localhost:8000/api/calls?my_calls=true" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Initier un appel

```bash
# Appel direct
curl -X POST http://localhost:8000/api/calls \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "receiver_id": 2,
    "type": "video"
  }'

# Appel dans un canal
curl -X POST http://localhost:8000/api/calls \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "channel_id": 1,
    "type": "audio"
  }'
```

### Répondre à un appel

```bash
curl -X POST http://localhost:8000/api/calls/1/answer \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Terminer un appel

```bash
curl -X POST http://localhost:8000/api/calls/1/end \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Rejeter un appel

```bash
curl -X POST http://localhost:8000/api/calls/1/reject \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Historique des appels

```bash
curl http://localhost:8000/api/calls/history \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 📅 Meetings

### Lister les réunions

```bash
# Toutes les réunions
curl http://localhost:8000/api/meetings \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"

# Mes réunions uniquement
curl "http://localhost:8000/api/meetings?my_meetings=true" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Créer une réunion

```bash
curl -X POST http://localhost:8000/api/meetings \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "organization_id": 1,
    "title": "Sprint Planning",
    "description": "Planification du sprint 5",
    "scheduled_at": "2025-11-10 14:00:00",
    "duration": 60,
    "meeting_link": "https://meet.locatalk.app/abc123",
    "participant_ids": [2, 3, 4, 5]
  }'
```

### Accepter une invitation

```bash
curl -X POST http://localhost:8000/api/meetings/1/accept \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Décliner une invitation

```bash
curl -X POST http://localhost:8000/api/meetings/1/decline \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Démarrer une réunion

```bash
curl -X POST http://localhost:8000/api/meetings/1/start \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Terminer une réunion

```bash
curl -X POST http://localhost:8000/api/meetings/1/end \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Sauvegarder un résumé IA

```bash
curl -X POST http://localhost:8000/api/meetings/1/summary \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01" \
  -H "Content-Type: application/json" \
  -d '{
    "ai_summary": "Réunion productive. Décisions : adopter React pour le frontend, sprint de 2 semaines. Actions : Jean configure CI/CD, Marie fait la maquette."
  }'
```

---

## 🔔 Notifications

### Lister mes notifications

```bash
# Toutes
curl http://localhost:8000/api/notifications \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"

# Non lues uniquement
curl "http://localhost:8000/api/notifications?read=false" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Nombre de notifications non lues

```bash
curl http://localhost:8000/api/notifications/unread-count \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Marquer une notification comme lue

```bash
curl -X POST http://localhost:8000/api/notifications/1/mark-read \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Marquer toutes comme lues

```bash
curl -X POST http://localhost:8000/api/notifications/mark-all-read \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Supprimer une notification

```bash
curl -X DELETE http://localhost:8000/api/notifications/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

### Supprimer toutes les notifications lues

```bash
curl -X DELETE http://localhost:8000/api/notifications/delete-all-read \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-Mac-Address: 00:00:00:00:00:01"
```

---

## 🧪 Script de Test Automatisé

Créer un fichier `test-api.sh` :

```bash
#!/bin/bash

# Configuration
API_URL="http://localhost:8000/api"
EMAIL="owner@example.com"
PASSWORD="password"
MAC_ADDRESS="00:00:00:00:00:01"

echo "🚀 Test LocaTalk API"
echo "==================="
echo ""

# 1. Login
echo "1️⃣  Test Login..."
LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\",
    \"device_name\": \"test-script\",
    \"mac_address\": \"$MAC_ADDRESS\"
  }")

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | sed 's/"token":"//')

if [ -z "$TOKEN" ]; then
  echo "❌ Login échoué"
  echo "$LOGIN_RESPONSE"
  exit 1
fi

echo "✅ Login réussi - Token: ${TOKEN:0:20}..."
echo ""

# 2. Dashboard
echo "2️⃣  Test Dashboard..."
DASHBOARD=$(curl -s "$API_URL/dashboard" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$DASHBOARD" | grep -q "personal_stats"; then
  echo "✅ Dashboard OK"
else
  echo "❌ Dashboard échoué"
fi
echo ""

# 3. Profile
echo "3️⃣  Test Profile..."
PROFILE=$(curl -s "$API_URL/profile" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$PROFILE" | grep -q "email"; then
  echo "✅ Profile OK"
else
  echo "❌ Profile échoué"
fi
echo ""

# 4. Users
echo "4️⃣  Test Users..."
USERS=$(curl -s "$API_URL/users" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$USERS" | grep -q "data"; then
  echo "✅ Users OK"
else
  echo "❌ Users échoué"
fi
echo ""

# 5. Projects
echo "5️⃣  Test Projects..."
PROJECTS=$(curl -s "$API_URL/projects" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$PROJECTS" | grep -q "data"; then
  echo "✅ Projects OK"
else
  echo "❌ Projects échoué"
fi
echo ""

# 6. Messages
echo "6️⃣  Test Messages..."
MESSAGES=$(curl -s "$API_URL/messages/conversations" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$MESSAGES" | grep -q "data"; then
  echo "✅ Messages OK"
else
  echo "❌ Messages échoué"
fi
echo ""

# 7. Notifications
echo "7️⃣  Test Notifications..."
NOTIFS=$(curl -s "$API_URL/notifications/unread-count" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$NOTIFS" | grep -q "count"; then
  echo "✅ Notifications OK"
else
  echo "❌ Notifications échoué"
fi
echo ""

# 8. Logout
echo "8️⃣  Test Logout..."
LOGOUT=$(curl -s -X POST "$API_URL/logout" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")

if echo "$LOGOUT" | grep -q "message"; then
  echo "✅ Logout OK"
else
  echo "❌ Logout échoué"
fi
echo ""

echo "==================="
echo "✨ Tests terminés !"
```

Rendre le script exécutable et le lancer :

```bash
chmod +x test-api.sh
./test-api.sh
```

---

## 📝 Collection Postman

Importer ce JSON dans Postman pour avoir tous les endpoints prêts :

1. Créer une nouvelle collection "LocaTalk API"
2. Ajouter une variable d'environnement `{{token}}` et `{{base_url}}`
3. Configurer `base_url = http://localhost:8000/api`
4. Après le login, copier le token dans la variable `{{token}}`

Tous les endpoints utilisent :
- **Authorization** : Bearer {{token}}
- **Header** : X-Mac-Address: 00:00:00:00:00:01

---

## ✅ Checklist de Tests

- [ ] Authentification (login/logout/me)
- [ ] Dashboard & Analytics
- [ ] Profile (CRUD + avatar + password + devices)
- [ ] Users (CRUD + restore)
- [ ] Organizations (CRUD + settings)
- [ ] Departments (CRUD + toggle)
- [ ] Projects (CRUD + assign users)
- [ ] Tasks (CRUD + my-tasks + complete + status)
- [ ] Messages (send/receive + attachments + search)
- [ ] Channels (CRUD + join/leave + members)
- [ ] Calls (initiate + answer + end + reject)
- [ ] Meetings (CRUD + accept/decline + start/end + summary)
- [ ] Notifications (list + mark-read + delete)

---

## 🆘 Dépannage

### Erreur 401 Unauthorized
- Vérifier que le token est valide
- Vérifier le header X-Mac-Address

### Erreur 403 Forbidden
- Vérifier les permissions de l'utilisateur
- Vérifier le rôle (owner/admin/user)

### Erreur 422 Validation
- Vérifier les champs requis
- Vérifier le format des données

### Erreur 500 Server Error
- Vérifier les logs : `tail -f storage/logs/laravel.log`
- Vérifier la connexion à la base de données

---

**🎉 Bonne chance avec les tests !**
