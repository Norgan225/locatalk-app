# 🚀 Guide de Démarrage Rapide - Emails LocaTalk

## 📋 Configuration Rapide (5 minutes)

### Option 1 : Test avec Mailtrap (Recommandé pour débuter)

1. **Créer un compte Mailtrap** :
   - Aller sur [mailtrap.io](https://mailtrap.io)
   - S'inscrire gratuitement
   - Créer une inbox

2. **Copier les credentials** :
   - Dans votre inbox Mailtrap, onglet "SMTP Settings"
   - Choisir "Laravel 9+"
   - Copier les valeurs

3. **Mettre à jour `.env`** :
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=votre_username_mailtrap
   MAIL_PASSWORD=votre_password_mailtrap
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@locatalk.app"
   MAIL_FROM_NAME="LocaTalk"
   ```

4. **Tester** :
   ```bash
   php artisan email:test welcome --to=test@example.com
   ```

5. **Vérifier** :
   - Retourner sur Mailtrap
   - L'email devrait apparaître dans votre inbox
   - Cliquer pour voir le rendu HTML

✅ **C'est tout !** Vous pouvez maintenant développer et tester sans envoyer de vrais emails.

---

## 🧪 Commandes de Test

### Tester un type d'email spécifique

```bash
# Email de bienvenue
php artisan email:test welcome --to=votre@email.com

# Invitation à une réunion
php artisan email:test meeting --to=votre@email.com

# Assignation de tâche
php artisan email:test task --to=votre@email.com

# Invitation au projet
php artisan email:test project --to=votre@email.com

# Notification de message
php artisan email:test message --to=votre@email.com

# Tous les types d'emails
php artisan email:test all --to=votre@email.com
```

### Mode interactif (sans options)

```bash
php artisan email:test
# Vous serez invité à choisir le type et l'email
```

---

## 📧 Types d'Emails Disponibles

| Type | Quand envoyé | Template |
|------|--------------|----------|
| **WelcomeMail** | Création de compte | `emails/welcome.blade.php` |
| **MeetingInvitationMail** | Invitation réunion | `emails/meeting-invitation.blade.php` |
| **TaskAssignedMail** | Assignation tâche | `emails/task-assigned.blade.php` |
| **ProjectInvitationMail** | Ajout au projet | `emails/project-invitation.blade.php` |
| **MessageNotificationMail** | Nouveau message | `emails/message-notification.blade.php` |

---

## 🔄 Intégration dans les Contrôleurs

### Exemple 1 : UserController

```php
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

public function store(Request $request)
{
    $user = User::create($data);
    $organization = Organization::find($user->organization_id);
    
    // Envoyer l'email de bienvenue
    Mail::to($user->email)->send(
        new WelcomeMail($user, $organization, $temporaryPassword)
    );
    
    return response()->json(['data' => $user], 201);
}
```

### Exemple 2 : MeetingController

```php
use App\Mail\MeetingInvitationMail;
use Illuminate\Support\Facades\Mail;

public function store(Request $request)
{
    $meeting = Meeting::create($data);
    
    // Envoyer invitation à chaque participant
    foreach ($request->participant_ids as $participantId) {
        $participant = User::find($participantId);
        Mail::to($participant->email)->send(
            new MeetingInvitationMail($meeting, $participant)
        );
    }
    
    return response()->json(['data' => $meeting], 201);
}
```

### Exemple 3 : TaskController

```php
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Mail;

public function assign(Request $request, $id)
{
    $task = Task::findOrFail($id);
    $assignee = User::find($request->user_id);
    
    $task->update(['assigned_to' => $request->user_id]);
    
    // Notifier l'assigné
    Mail::to($assignee->email)->send(
        new TaskAssignedMail($task, $assignee)
    );
    
    return response()->json(['message' => 'Tâche assignée'], 200);
}
```

---

## 🎨 Personnalisation des Templates

### Modifier les couleurs

Éditer `resources/views/emails/layout.blade.php` :

```css
/* Ligne 43-44 : Gradient du header */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Ligne 68-70 : Couleur des boutons */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Ajouter votre logo

Dans `resources/views/emails/layout.blade.php`, remplacer ligne 95 :

```blade
<div class="email-logo">📍</div>
<!-- Par -->
<img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 50px;">
```

---

## 🚀 Passage en Production

### Avec Gmail (petit volume)

1. Activer l'authentification 2FA sur Gmail
2. Générer un App Password : [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
3. Mettre à jour `.env` :
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=votre-email@gmail.com
   MAIL_PASSWORD=votre-app-password
   MAIL_ENCRYPTION=tls
   ```

### Avec SendGrid (gros volume)

1. Créer un compte sur [sendgrid.com](https://sendgrid.com)
2. Créer une API Key
3. Mettre à jour `.env` :
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.sendgrid.net
   MAIL_PORT=587
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=votre-sendgrid-api-key
   MAIL_ENCRYPTION=tls
   ```

---

## 📊 Queue (Recommandé pour Production)

### Activer les queues

1. Vérifier `.env` :
   ```env
   QUEUE_CONNECTION=database
   ```

2. Démarrer le worker :
   ```bash
   php artisan queue:work
   ```

Les emails seront envoyés en arrière-plan automatiquement !

---

## 🆘 Dépannage Rapide

### Email non reçu

```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Tester la connexion SMTP
php artisan tinker
>>> Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });
```

### Erreur "Connection refused"

✅ Vérifier MAIL_HOST et MAIL_PORT dans `.env`  
✅ Vérifier que le firewall autorise le port  
✅ Tester avec Mailtrap pour isoler le problème

### Emails en spam

✅ Configurer SPF/DKIM pour votre domaine  
✅ Utiliser un service professionnel (SendGrid, Mailgun)  
✅ Éviter les mots comme "urgent", "gratuit" dans les sujets

---

## 📚 Documentation Complète

Pour plus de détails, voir : **[EMAIL_CONFIGURATION.md](EMAIL_CONFIGURATION.md)**

---

## ✅ Checklist Finale

- [ ] Configuration SMTP dans `.env`
- [ ] Test avec `php artisan email:test`
- [ ] Email reçu et affiché correctement
- [ ] Templates personnalisés si nécessaire
- [ ] Queue configurée pour production
- [ ] Monitoring/logs en place

---

**🎉 Félicitations !** Votre système d'emails est opérationnel !

Pour toute question : consultez la documentation complète ou contactez l'équipe.
