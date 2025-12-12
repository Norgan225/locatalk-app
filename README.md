# LocaTalk - Application de Messagerie Moderne

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Une application de messagerie moderne développée avec Laravel, offrant des fonctionnalités temps réel, le chiffrement E2E, et une interface utilisateur intuitive.

## ✨ Fonctionnalités

### 💬 Messagerie
- **Messages directs** entre utilisateurs
- **Messages vocaux** avec enregistrement intégré
- **Pièces jointes** (images, documents, audio)
- **Chiffrement E2E** pour la sécurité
- **Réactions aux messages** (👍, ❤️, 😂, etc.)
- **Messages épinglés** pour un accès rapide

### 🔴 Temps Réel
- **WebSocket** avec Laravel Echo
- **Statut de présence** (en ligne/hors ligne/occupé)
- **Notifications temps réel**
- **Indicateurs de frappe** ("est en train d'écrire...")
- **Mises à jour instantanées** des conversations

### 👥 Gestion Utilisateur
- **Authentification** avec Laravel Sanctum
- **Profils utilisateurs** avec avatars
- **Gestion des appareils** et sécurité
- **Rôles et permissions** (Super Admin, Admin, Employé)
- **Organisations et départements**

### 🎨 Interface Moderne
- **Design responsive** et moderne
- **Thème sombre/clair**
- **Interface intuitive** avec animations fluides
- **Support mobile** optimisé
- **Notifications toast** élégantes

### 🔧 Fonctionnalités Avancées
- **Appels vidéo/audio** (architecture préparée)
- **Chiffrement E2E** pour les appels de groupe
- **Gestion de projets** et tâches
- **Réunions** avec invitations
- **Analytics** et rapports
- **API REST** complète avec documentation Swagger

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Redis (optionnel, pour les files d'attente)

### Installation rapide

1. **Cloner le repository**
   ```bash
   git clone https://github.com/TON_USERNAME/locatalk-app.git
   cd locatalk-app
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances JavaScript**
   ```bash
   npm install
   ```

4. **Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurer la base de données**
   - Créer une base de données MySQL/PostgreSQL
   - Modifier `.env` avec vos credentials DB
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Compiler les assets**
   ```bash
   npm run build
   # ou pour le développement
   npm run dev
   ```

7. **Démarrer le serveur**
   ```bash
   php artisan serve
   ```

## 📱 Utilisation

### Comptes de test
Après le seeding, vous pouvez utiliser :
- **Super Admin**: admin@locatalk.com / password
- **Admin**: manager@locatalk.com / password
- **Employé**: employee@locatalk.com / password

### Fonctionnalités principales
1. **Connexion** et gestion du profil
2. **Navigation** dans les messages
3. **Création de conversations** avec d'autres utilisateurs
4. **Envoi de messages** texte, vocal, ou avec pièces jointes
5. **Réactions** et réponses aux messages
6. **Épinglage** des messages importants

## 🛠️ Technologies Utilisées

### Backend
- **Laravel 11** - Framework PHP
- **Laravel Sanctum** - Authentification API
- **Laravel Echo** - WebSocket broadcasting
- **MySQL/PostgreSQL** - Base de données
- **Redis** - Cache et files d'attente

### Frontend
- **JavaScript ES6+** - Logique métier
- **Tailwind CSS** - Framework CSS
- **Alpine.js** - Composants interactifs
- **WebRTC** - Appels audio/vidéo (futur)

### Sécurité
- **Chiffrement E2E** pour les messages
- **Clés de chiffrement** rotatives
- **Authentification multi-facteurs** (futur)
- **Gestion des sessions** sécurisée

## 📁 Structure du Projet

```
locatalk-app/
├── app/                    # Code applicatif Laravel
│   ├── Http/Controllers/   # Contrôleurs API
│   ├── Models/            # Modèles Eloquent
│   ├── Services/          # Services métier
│   └── Events/            # Événements temps réel
├── resources/             # Views et assets
│   ├── views/            # Templates Blade
│   ├── css/              # Styles personnalisés
│   └── js/               # JavaScript frontend
├── routes/               # Définition des routes
├── database/             # Migrations et seeders
├── public/               # Assets publics
└── tests/               # Tests unitaires et fonctionnels
```

## 🔧 Configuration Avancée

### WebSocket (Laravel Echo)
Pour activer les fonctionnalités temps réel :

1. Installer Laravel Echo Server ou Socket.io
2. Configurer les variables d'environnement dans `.env`
3. Démarrer le serveur WebSocket

### Chiffrement E2E
Le système de chiffrement est automatiquement activé pour tous les messages sensibles.

### API Documentation
Accédez à `/api/documentation` pour voir la documentation Swagger complète.

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👨‍💻 Auteur

**LocaTalk Team** - *Développement initial*

## 🙏 Remerciements

- Laravel Framework
- Laravel Sanctum
- Laravel Echo
- Tailwind CSS
- Tous les contributeurs open source

---

⭐ **Si ce projet vous plaît, n'hésitez pas à mettre une étoile !**

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
