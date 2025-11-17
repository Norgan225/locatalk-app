#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;

// Créer un message de test
$user1 = User::first();
$user2 = User::skip(1)->first();

if (!$user1 || !$user2) {
    echo "Pas assez d'utilisateurs pour tester\n";
    exit(1);
}

$message = Message::create([
    'organization_id' => $user1->organization_id,
    'sender_id' => $user1->id,
    'receiver_id' => $user2->id,
    'content' => 'Message de test en temps réel - ' . now(),
    'type' => 'text',
    'is_read' => false,
]);

// Dispatcher l'événement
MessageSent::dispatch($message);

echo "Message de test envoyé avec ID: {$message->id}\n";
echo "Événement MessageSent dispatché\n";
echo "Vérifiez les logs et la console du navigateur pour confirmer la réception\n";
