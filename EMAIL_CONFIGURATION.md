# 📧 Configuration Email - LocaTalk

## Vue d'ensemble

LocaTalk dispose d'un système d'envoi d'emails complet pour notifier les utilisateurs des événements importants. Ce document explique comment configurer et utiliser le système d'emails.

## 🎯 Types d'emails disponibles

### 1. **WelcomeMail** - Email de bienvenue
- **Quand** : Lors de la création d'un nouveau compte utilisateur
- **Contenu** : Informations de compte, mot de passe temporaire, présentation des fonctionnalités
- **Template** : `resources/views/emails/welcome.blade.php`

### 2. **MeetingInvitationMail** - Invitation à une réunion
- **Quand** : Lors de l'ajout d'un participant à une réunion
- **Contenu** : Détails de la réunion, date/heure, lien de connexion, boutons accepter/décliner
- **Template** : `resources/views/emails/meeting-invitation.blade.php`

### 3. **TaskAssignedMail** - Assignation de tâche
- **Quand** : Lors de l'assignation d'une tâche à un utilisateur
- **Contenu** : Détails de la tâche, priorité, date limite, lien vers la tâche
- **Template** : `resources/views/emails/task-assigned.blade.php`

### 4. **ProjectInvitationMail** - Invitation à un projet
- **Quand** : Lors de l'ajout d'un membre à un projet
- **Contenu** : Informations du projet, rôle attribué, membres de l'équipe
- **Template** : `resources/views/emails/project-invitation.blade.php`

### 5. **MessageNotificationMail** - Notification de message
- **Quand** : Lors de la réception d'un nouveau message (optionnel, selon préférences)
- **Contenu** : Extrait du message, expéditeur, canal si applicable
- **Template** : `resources/views/emails/message-notification.blade.php`

## 🔧 Configuration

### Option 1 : Mailtrap (Développement/Test)

**Idéal pour** : Tests en développement, pas d'envoi réel

1. Créer un compte sur [mailtrap.io](https://mailtrap.io)
2. Copier les credentials de votre inbox
3. Mettre à jour `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@locatalk.app"
MAIL_FROM_NAME="LocaTalk"
```

### Option 2 : Gmail (Production)

**Idéal pour** : Production avec volumes faibles (<500 emails/jour)

1. Activer l'authentification à 2 facteurs sur votre compte Gmail
2. Générer un "App Password" : [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
3. Mettre à jour `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="LocaTalk"
```

### Option 3 : SendGrid (Production)

**Idéal pour** : Production avec gros volumes, excellent deliverability

1. Créer un compte sur [sendgrid.com](https://sendgrid.com)
2. Créer une API Key dans Settings > API Keys
3. Vérifier votre domaine expéditeur
4. Mettre à jour `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="LocaTalk"
```

### Option 4 : Mailgun (Production)

**Idéal pour** : Production avec tracking avancé et analytics

1. Créer un compte sur [mailgun.com](https://mailgun.com)
2. Vérifier votre domaine
3. Copier vos credentials
4. Mettre à jour `.env` :

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your-mailgun-secret
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="LocaTalk"
```

## 🚀 Utilisation dans le code

### Exemple 1 : Envoyer un email de bienvenue

```php
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

// Dans UserController@store après création utilisateur
$user = User::create($data);
$organization = Organization::find($user->organization_id);
$temporaryPassword = 'temp123'; // Généré de manière sécurisée

Mail::to($user->email)->send(new WelcomeMail($user, $organization, $temporaryPassword));
```

### Exemple 2 : Envoyer une invitation à une réunion

```php
use App\Mail\MeetingInvitationMail;
use Illuminate\Support\Facades\Mail;

// Dans MeetingController@store après création
foreach ($meeting->participants as $participant) {
    Mail::to($participant->user->email)
        ->send(new MeetingInvitationMail($meeting, $participant->user));
}
```

### Exemple 3 : Notification d'assignation de tâche

```php
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Mail;

// Dans TaskController@assign
$assignee = User::find($userId);
Mail::to($assignee->email)->send(new TaskAssignedMail($task, $assignee));
```

### Exemple 4 : Invitation au projet

```php
use App\Mail\ProjectInvitationMail;
use Illuminate\Support\Facades\Mail;

// Dans ProjectController@assignUsers
foreach ($userIds as $userId) {
    $user = User::find($userId);
    $role = $request->input("roles.{$userId}", 'member');
    
    Mail::to($user->email)
        ->send(new ProjectInvitationMail($project, $user, $role));
}
```

## 📋 Queue (File d'attente)

Pour de meilleures performances, utilisez les queues pour envoyer les emails en arrière-plan :

### Configuration

1. Vérifier que `QUEUE_CONNECTION=database` dans `.env`

2. Créer les tables de queue si ce n'est pas fait :
```bash
php artisan queue:table
php artisan migrate
```

3. Démarrer le worker de queue :
```bash
php artisan queue:work
```

### En production

Utiliser Supervisor pour garder le worker actif :

```ini
[program:locatalk-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/locatalk-app/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/locatalk-worker.log
```

## 🧪 Tests

### Test en local avec Mailtrap

```php
// Dans tinker ou un test
php artisan tinker

>>> use App\Mail\WelcomeMail;
>>> use App\Models\User;
>>> use Illuminate\Support\Facades\Mail;
>>> 
>>> $user = User::first();
>>> $org = $user->organization;
>>> Mail::to($user->email)->send(new WelcomeMail($user, $org, 'test123'));
```

### Test avec Artisan

Créer une commande de test :

```bash
php artisan make:command TestEmailCommand
```

```php
// app/Console/Commands/TestEmailCommand.php
public function handle()
{
    $user = User::first();
    $org = $user->organization;
    
    Mail::to($this->ask('Email recipient?'))
        ->send(new WelcomeMail($user, $org, 'test123'));
    
    $this->info('Email sent successfully!');
}
```

Exécuter :
```bash
php artisan test:email
```

## 🎨 Personnalisation des templates

### Modifier le layout de base

Éditer `resources/views/emails/layout.blade.php` pour :
- Changer les couleurs (gradient, boutons)
- Ajouter votre logo
- Modifier le footer
- Ajuster les polices

### Modifier un template spécifique

Chaque template étend le layout :
```blade
@extends('emails.layout')

@section('content')
    <!-- Votre contenu personnalisé -->
@endsection
```

### Variables disponibles

Chaque Mailable expose ses propres variables publiques accessibles dans les templates.

## 📊 Monitoring et logs

### Logs Laravel

Les emails envoyés sont loggés automatiquement :
```bash
tail -f storage/logs/laravel.log | grep "mail"
```

### Tracking avec SendGrid/Mailgun

Ces services offrent des dashboards pour suivre :
- Taux d'ouverture
- Clics sur les liens
- Bounces
- Spam reports

## 🔒 Sécurité

### Bonnes pratiques

1. **Ne jamais** committer les credentials SMTP dans Git
2. Utiliser des **App Passwords** pour Gmail, pas le mot de passe principal
3. Activer **SPF, DKIM, DMARC** pour votre domaine en production
4. **Limiter** le rate de sending pour éviter le spam
5. **Valider** les emails avant envoi
6. **Utiliser HTTPS** pour tous les liens dans les emails

### Rate limiting

Limiter les emails par utilisateur :

```php
use Illuminate\Support\Facades\RateLimiter;

if (RateLimiter::tooManyAttempts('send-email:'.$user->id, 10)) {
    return response()->json(['error' => 'Too many emails sent'], 429);
}

RateLimiter::hit('send-email:'.$user->id, 3600); // 10 per hour

Mail::to($user->email)->send($mailable);
```

## 🆘 Dépannage

### Email non reçu

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier le dossier spam
3. Tester avec Mailtrap
4. Vérifier les credentials SMTP

### Erreur de connexion SMTP

```
Swift_TransportException: Connection could not be established
```

**Solutions** :
- Vérifier MAIL_HOST, MAIL_PORT, MAIL_ENCRYPTION
- Vérifier que le firewall autorise le port
- Tester avec telnet : `telnet smtp.gmail.com 587`

### Queue worker ne traite pas les emails

**Solutions** :
- Redémarrer le worker : `php artisan queue:restart`
- Vérifier les failed jobs : `php artisan queue:failed`
- Relancer les jobs échoués : `php artisan queue:retry all`

## 📚 Ressources supplémentaires

- [Documentation Laravel Mail](https://laravel.com/docs/11.x/mail)
- [Documentation Laravel Queues](https://laravel.com/docs/11.x/queues)
- [Mailtrap Documentation](https://mailtrap.io/docs)
- [SendGrid Laravel Integration](https://docs.sendgrid.com/for-developers/sending-email/laravel)

---

**Auteur** : LocaTalk Development Team  
**Dernière mise à jour** : 5 novembre 2025
