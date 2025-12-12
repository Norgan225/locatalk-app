<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Hash;

class MessagingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'utilisateur connecté ou créer des utilisateurs de test
        $currentUser = User::first();

        if (!$currentUser) {
            // Créer un utilisateur principal si aucun n'existe
            $currentUser = User::create([
                'name' => 'John Doe',
                'email' => 'john@locatalk.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'organization_id' => 1,
            ]);
        }

        // Créer des utilisateurs de test pour les conversations
        $users = [];

        $testUsers = [
            [
                'name' => 'Alice Martin',
                'email' => 'alice@locatalk.com',
                'avatar' => null,
                'status' => 'online'
            ],
            [
                'name' => 'Bob Dupont',
                'email' => 'bob@locatalk.com',
                'avatar' => null,
                'status' => 'away'
            ],
            [
                'name' => 'Claire Dubois',
                'email' => 'claire@locatalk.com',
                'avatar' => null,
                'status' => 'busy'
            ],
            [
                'name' => 'David Leroy',
                'email' => 'david@locatalk.com',
                'avatar' => null,
                'status' => 'online'
            ],
            [
                'name' => 'Emma Bernard',
                'email' => 'emma@locatalk.com',
                'avatar' => null,
                'status' => 'offline'
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'employe',
                    'organization_id' => $currentUser->organization_id ?? 1,
                ]
            );

            // Créer le statut
            UserStatus::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'status' => $userData['status'],
                    'last_seen' => now(),
                    'last_activity' => now(),
                ]
            );

            $users[] = $user;
        }

        // Créer des conversations avec messages
        $conversations = [
            [
                'user' => $users[0], // Alice
                'messages' => [
                    ['content' => 'Salut ! Comment ça va ?', 'sender' => 'other', 'time' => now()->subHours(2)],
                    ['content' => 'Ça va bien merci ! Et toi ?', 'sender' => 'me', 'time' => now()->subHours(2)->addMinutes(5)],
                    ['content' => 'Super ! Tu as vu le nouveau projet ?', 'sender' => 'other', 'time' => now()->subHours(1)],
                    ['content' => 'Oui, c\'est impressionnant ! 🚀', 'sender' => 'me', 'time' => now()->subMinutes(30)],
                    ['content' => 'On pourrait en discuter demain ?', 'sender' => 'other', 'time' => now()->subMinutes(15)],
                ]
            ],
            [
                'user' => $users[1], // Bob
                'messages' => [
                    ['content' => 'Tu es disponible pour une réunion ?', 'sender' => 'other', 'time' => now()->subHours(3)],
                    ['content' => 'Oui, à quelle heure ?', 'sender' => 'me', 'time' => now()->subHours(3)->addMinutes(2)],
                    ['content' => 'Disons 14h ?', 'sender' => 'other', 'time' => now()->subHours(2)],
                ]
            ],
            [
                'user' => $users[2], // Claire
                'messages' => [
                    ['content' => 'Le rapport est prêt', 'sender' => 'other', 'time' => now()->subDays(1)],
                    ['content' => 'Parfait, merci !', 'sender' => 'me', 'time' => now()->subDays(1)->addMinutes(10)],
                ]
            ],
            [
                'user' => $users[3], // David
                'messages' => [
                    ['content' => 'On se voit ce soir ?', 'sender' => 'other', 'time' => now()->subHours(5)],
                    ['content' => 'Oui, avec plaisir ! 🎉', 'sender' => 'me', 'time' => now()->subHours(4)],
                    ['content' => 'Super ! À ce soir alors', 'sender' => 'other', 'time' => now()->subHours(4)->addMinutes(5)],
                ]
            ],
            [
                'user' => $users[4], // Emma
                'messages' => [
                    ['content' => 'Merci pour ton aide !', 'sender' => 'other', 'time' => now()->subDays(2)],
                    ['content' => 'De rien, n\'hésite pas ! 😊', 'sender' => 'me', 'time' => now()->subDays(2)->addMinutes(3)],
                ]
            ],
        ];

        foreach ($conversations as $conv) {
            foreach ($conv['messages'] as $msgData) {
                $message = Message::create([
                    'organization_id' => $currentUser->organization_id ?? 1,
                    'sender_id' => $msgData['sender'] === 'me' ? $currentUser->id : $conv['user']->id,
                    'receiver_id' => $msgData['sender'] === 'me' ? $conv['user']->id : $currentUser->id,
                    'content' => $msgData['content'],
                    'is_read' => true,
                    'is_delivered' => true,
                    'delivered_at' => $msgData['time'],
                    'read_at' => $msgData['time']->addMinutes(1),
                    'created_at' => $msgData['time'],
                    'updated_at' => $msgData['time'],
                ]);

                // Ajouter quelques réactions aléatoires
                if (rand(1, 3) === 1) {
                    $emojis = ['👍', '❤️', '😂', '🎉', '🔥'];
                    MessageReaction::create([
                        'message_id' => $message->id,
                        'user_id' => $msgData['sender'] === 'me' ? $conv['user']->id : $currentUser->id,
                        'emoji' => $emojis[array_rand($emojis)],
                    ]);
                }
            }
        }

        $this->command->info('✅ Données de démonstration créées avec succès !');
        $this->command->info('📧 Utilisateurs de test :');
        foreach ($testUsers as $user) {
            $this->command->info("   - {$user['email']} (password: password)");
        }
    }
}
