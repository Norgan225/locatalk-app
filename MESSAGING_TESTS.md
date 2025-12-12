# 🧪 Guide de Test - Interface de Messagerie

## Tests Manuels Complets

### ✅ 1. Test des Conversations

#### Charger les conversations
1. Accéder à `/messages/modern`
2. ✓ La liste des conversations s'affiche
3. ✓ Les avatars sont visibles
4. ✓ Les statuts (🟢 en ligne, 🟡 absent, 🔴 occupé, ⚪ hors ligne) s'affichent
5. ✓ Les derniers messages sont prévisualisés
6. ✓ Les badges "non lus" apparaissent

#### Rechercher une conversation
1. Taper dans la barre de recherche
2. ✓ Filtrage instantané
3. ✓ Pas de rechargement de page

#### Sélectionner une conversation
1. Cliquer sur une conversation
2. ✓ La conversation devient active (surlignée)
3. ✓ L'historique des messages s'affiche
4. ✓ Le header montre le nom et statut de l'utilisateur

---

### ✅ 2. Test des Messages

#### Envoyer un message texte
1. Taper un message dans le textarea
2. ✓ Le compteur de caractères s'actualise (X/5000)
3. ✓ Appuyer sur Entrée envoie le message
4. ✓ Le message apparaît dans la conversation
5. ✓ Le statut ✓ (envoyé) s'affiche
6. ✓ Après quelques secondes, ✓✓ (délivré) s'affiche

#### Auto-expand du textarea
1. Taper plusieurs lignes
2. ✓ Le textarea s'agrandit automatiquement
3. ✓ Max 120px de hauteur

#### Scroll automatique
1. Envoyer plusieurs messages
2. ✓ Scroll automatique vers le bas
3. ✓ Bouton "Scroll to bottom" apparaît si on scrolle vers le haut

---

### ✅ 3. Test du Chiffrement E2E

#### Vérifier le chiffrement
1. Envoyer un message
2. Ouvrir la base de données
3. ✓ Table `messages` : `encrypted_content` est illisible
4. ✓ Champ `is_encrypted` = 1
5. ✓ `encryption_key_id` est renseigné
6. ✓ Le message s'affiche correctement déchiffré dans l'interface

#### Test avec nouveau contact
1. Démarrer conversation avec nouvel utilisateur
2. ✓ Nouvelle clé générée automatiquement dans `encryption_keys`
3. ✓ Les deux utilisateurs peuvent lire les messages

---

### ✅ 4. Test des Réactions Emoji

#### Ajouter une réaction
1. Hover sur un message
2. Cliquer sur "➕" ou un emoji rapide
3. ✓ La réaction apparaît sous le message
4. ✓ Le compteur s'affiche (ex: ❤️ 1)
5. ✓ L'autre utilisateur voit la réaction en temps réel

#### Retirer une réaction
1. Cliquer sur l'emoji déjà réagi
2. ✓ La réaction disparaît
3. ✓ Le compteur diminue

#### Réactions multiples
1. Plusieurs utilisateurs ajoutent la même réaction
2. ✓ Le compteur s'incrémente (❤️ 3)
3. ✓ Hover montre la liste des utilisateurs

---

### ✅ 5. Test des Messages Épinglés

#### Épingler un message
1. Clic droit sur un message → "Épingler"
2. ✓ Un indicateur 📌 apparaît sur le message
3. ✓ Le badge dans le header s'incrémente
4. ✓ L'autre utilisateur voit le message épinglé

#### Voir les messages épinglés
1. Cliquer sur le badge "📌 Épinglés"
2. ✓ Un panel s'ouvre avec la liste
3. ✓ Cliquer sur un message épinglé scrolle vers lui

#### Désépingler
1. Clic droit → "Désépingler"
2. ✓ L'indicateur disparaît
3. ✓ Le badge diminue

---

### ✅ 6. Test des Pièces Jointes

#### Upload d'image
1. Cliquer sur 📎
2. Sélectionner une image (JPG, PNG, GIF)
3. ✓ Preview s'affiche avant envoi
4. ✓ Après envoi, l'image est visible dans le message
5. ✓ Cliquer ouvre en plein écran

#### Upload de fichier
1. Sélectionner un PDF, DOC, ZIP
2. ✓ Icône appropriée s'affiche (📄 📝 📊)
3. ✓ Nom et taille affichés
4. ✓ Cliquer télécharge le fichier

#### Upload multiple
1. Sélectionner plusieurs fichiers
2. ✓ Tous les fichiers sont prévisualisés
3. ✓ Possibilité de retirer un fichier (×)

#### Génération de thumbnail
1. Uploader une grande image (> 1MB)
2. Vérifier dans `storage/app/public/attachments/thumbnails/`
3. ✓ Thumbnail créé automatiquement
4. ✓ Taille réduite (max 300x300)

---

### ✅ 7. Test des Messages Vocaux 🎤

#### Enregistrer un message vocal
1. Cliquer sur le bouton 🎤
2. ✓ Permission micro demandée (accepter)
3. ✓ Indicateur d'enregistrement s'affiche
4. ✓ Timer commence (00:01, 00:02...)
5. ✓ Visualiseur de forme d'onde animé
6. Parler dans le micro
7. ✓ Les barres du visualiseur bougent

#### Arrêter l'enregistrement
1. Cliquer sur "⏹️ Arrêter" ou le bouton 🎤
2. ✓ L'enregistrement s'arrête
3. ✓ Preview audio s'affiche
4. ✓ Player audio fonctionnel
5. ✓ Durée et taille affichées

#### Annuler l'enregistrement
1. Pendant le preview, cliquer sur ×
2. ✓ Le preview disparaît
3. ✓ Pas d'envoi

#### Envoyer le message vocal
1. Cliquer sur "Envoyer"
2. ✓ Le message vocal apparaît dans la conversation
3. ✓ Icône 🎤 + durée visible
4. ✓ Player audio dans le message
5. ✓ L'autre utilisateur peut écouter

#### Limites
1. Enregistrer < 1 seconde
   ✓ Message d'erreur "Enregistrement trop court"
2. Enregistrer > 5 minutes
   ✓ Arrêt automatique
   ✓ Message "Durée maximale atteinte"

---

### ✅ 8. Test des Aperçus de Liens 🔗

#### Détecter un lien automatiquement
1. Taper une URL dans le message (ex: https://github.com/laravel/laravel)
2. Attendre 1 seconde (debounce)
3. ✓ Un preview mini s'affiche sous le textarea
4. ✓ Image + titre + domaine visibles

#### Envoyer avec preview
1. Envoyer le message contenant l'URL
2. ✓ Le message s'affiche avec le texte
3. ✓ Le preview enrichi s'affiche en dessous
4. ✓ Image cliquable vers l'URL
5. ✓ Titre, description, site name visibles

#### Test de différentes plateformes
**YouTube :**
```
https://www.youtube.com/watch?v=dQw4w9WgXcQ
```
✓ Embed vidéo intégré
✓ Bordure rouge caractéristique

**GitHub :**
```
https://github.com/laravel/laravel
```
✓ Icône GitHub
✓ Description du repo
✓ Bordure grise

**Twitter/X :**
```
https://twitter.com/user/status/123456
```
✓ Preview du tweet
✓ Bordure bleue

#### Retirer un preview
1. Pendant la saisie, cliquer sur × du preview mini
2. ✓ Le preview disparaît
3. ✓ L'URL reste dans le texte

#### Cache
1. Envoyer le même lien 2 fois
2. ✓ Le 2ème preview se charge instantanément (cache)
3. Vérifier dans Redis/Cache
   ✓ Clé `link_preview_{md5(url)}` existe

---

### ✅ 9. Test des Indicateurs de Frappe

#### Voir l'indicateur
1. Utilisateur A tape un message
2. ✓ Utilisateur B voit "Alice est en train d'écrire..."
3. ✓ Animation pulse
4. A arrête de taper
5. ✓ Après 3 secondes, l'indicateur disparaît

#### Multi-conversations
1. Ouvrir conversation avec User A
2. User B tape dans une autre conversation
3. ✓ Pas d'indicateur (seulement dans la conversation active)

---

### ✅ 10. Test des Accusés de Lecture

#### États du message
**Envoyé (✓) :**
1. Envoyer un message
2. ✓ Une coche grise apparaît immédiatement

**Délivré (✓✓) :**
1. L'autre utilisateur reçoit le message (connection active)
2. ✓ Deux coches grises apparaissent
3. ✓ Horodatage `delivered_at` enregistré

**Lu (✓✓ bleu) :**
1. L'autre utilisateur ouvre la conversation
2. Le message est visible à l'écran
3. ✓ Les coches deviennent bleues
4. ✓ `is_read` = 1 en base

---

### ✅ 11. Test de Réponse aux Messages

#### Répondre à un message
1. Clic droit sur un message → "Répondre"
2. ✓ Un panel "Répondre à:" s'affiche au-dessus du textarea
3. ✓ Le message cité est visible
4. Taper une réponse
5. ✓ Envoyer le message
6. ✓ Le message affiché montre la citation

#### Navigation
1. Cliquer sur la citation
2. ✓ Scroll automatique vers le message original
3. ✓ Highlight temporaire

#### Annuler la réponse
1. Cliquer sur × dans le panel
2. ✓ Le panel disparaît
3. ✓ Le message devient normal

---

### ✅ 12. Test de Recherche dans la Conversation

#### Rechercher
1. Cliquer sur 🔍 dans le header
2. ✓ Panel de recherche s'ouvre
3. Taper un mot-clé (ex: "bonjour")
4. ✓ Requête envoyée au serveur
5. ✓ Messages correspondants affichés
6. ✓ Mots surlignés en jaune

#### Navigation dans les résultats
1. Cliquer sur un résultat
2. ✓ Scroll vers le message
3. ✓ Highlight du message

#### Fermer la recherche
1. Cliquer sur ×
2. ✓ Le panel se ferme
3. ✓ Retour à la vue normale

---

### ✅ 13. Test WebSocket Temps Réel

#### Pré-requis
```bash
# Démarrer Reverb
php artisan reverb:start
```

#### Nouveau message en temps réel
1. Utilisateur A envoie un message
2. ✓ Utilisateur B le reçoit instantanément
3. ✓ Pas de rechargement
4. ✓ Animation d'apparition

#### Réaction en temps réel
1. A ajoute une réaction ❤️
2. ✓ B voit la réaction apparaître
3. ✓ Le compteur s'incrémente

#### Indicateur de frappe
1. A tape un message
2. ✓ B voit "Alice est en train d'écrire..."
3. ✓ Latence < 500ms

#### Reconnexion
1. Couper la connexion réseau
2. ✓ Indicateur "Déconnecté" (optionnel)
3. Restaurer la connexion
4. ✓ Reconnexion automatique
5. ✓ Messages en attente synchronisés

---

### ✅ 14. Test de Performance

#### Charge de messages
1. Charger une conversation avec 1000+ messages
2. ✓ Pagination fonctionnelle (50 par page)
3. ✓ Scroll smooth
4. ✓ Pas de freeze UI

#### Upload gros fichier
1. Uploader un fichier de 50MB
2. ✓ Barre de progression (optionnel)
3. ✓ Upload réussi
4. ✓ Thumbnail généré pour images

#### Multiples conversations
1. Ouvrir 10+ conversations rapidement
2. ✓ Pas de ralentissement
3. ✓ Cache fonctionne
4. ✓ Mémoire stable

---

## 🐛 Cas d'Erreur à Tester

### Réseau
- ❌ Connexion perdue pendant l'envoi
  → Message mis en file d'attente
- ❌ WebSocket déconnecté
  → Tentative de reconnexion automatique
- ❌ Upload échoué
  → Message d'erreur + retry

### Validation
- ❌ Message > 5000 caractères
  → Erreur "Message trop long"
- ❌ Fichier > 50MB
  → Erreur "Fichier trop volumineux"
- ❌ Format non supporté
  → Erreur "Format non supporté"

### Permissions
- ❌ Token expiré
  → Redirection vers login
- ❌ Supprimer message d'un autre
  → Erreur 403 Forbidden

### Edge Cases
- ❌ Emoji dans le message
  → Affichage correct
- ❌ Caractères spéciaux (<script>)
  → Échappement HTML
- ❌ URLs multiples dans un message
  → Tous les previews extraits

---

## 📊 Checklist Finale

### Interface
- ✅ Design moderne et cohérent
- ✅ Responsive (mobile + tablette)
- ✅ Animations fluides
- ✅ Pas de bug visuel
- ✅ Accessibilité (contraste, focus)

### Fonctionnalités
- ✅ Tous les 14 features fonctionnels
- ✅ Chiffrement E2E actif
- ✅ WebSocket temps réel
- ✅ Messages vocaux opérationnels
- ✅ Link previews extraits

### Performance
- ✅ Temps de chargement < 2s
- ✅ Pas de memory leak
- ✅ Cache optimisé
- ✅ Requêtes minimisées

### Sécurité
- ✅ XSS protection
- ✅ CSRF tokens
- ✅ E2E encryption
- ✅ Permissions vérifiées
- ✅ Input validation

---

## 🚀 Déploiement

### Avant de déployer
```bash
# Compiler assets
npm run build

# Clear cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Migrations
php artisan migrate --force

# Permissions storage
chmod -R 775 storage bootstrap/cache

# Démarrer Reverb (production)
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Variables d'environnement
```env
BROADCAST_DRIVER=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

---

**Tests réussis ✅ → Prêt pour production ! 🎉**
