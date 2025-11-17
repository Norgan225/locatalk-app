<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Organization;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\Project;
use App\Models\Message;
use App\Mail\WelcomeMail;
use App\Mail\MeetingInvitationMail;
use App\Mail\TaskAssignedMail;
use App\Mail\ProjectInvitationMail;
use App\Mail\MessageNotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {type?} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi d\'emails. Types: welcome, meeting, task, project, message, all';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type') ?? $this->choice(
            'Quel type d\'email voulez-vous tester?',
            ['welcome', 'meeting', 'task', 'project', 'message', 'all'],
            0
        );

        $recipient = $this->option('to') ?? $this->ask('Email du destinataire?', 'test@example.com');

        $this->info("📧 Envoi du/des email(s) à : {$recipient}");
        $this->newLine();

        try {
            switch ($type) {
                case 'welcome':
                    $this->sendWelcomeEmail($recipient);
                    break;
                case 'meeting':
                    $this->sendMeetingEmail($recipient);
                    break;
                case 'task':
                    $this->sendTaskEmail($recipient);
                    break;
                case 'project':
                    $this->sendProjectEmail($recipient);
                    break;
                case 'message':
                    $this->sendMessageEmail($recipient);
                    break;
                case 'all':
                    $this->sendWelcomeEmail($recipient);
                    $this->sendMeetingEmail($recipient);
                    $this->sendTaskEmail($recipient);
                    $this->sendProjectEmail($recipient);
                    $this->sendMessageEmail($recipient);
                    break;
                default:
                    $this->error("Type invalide: {$type}");
                    return 1;
            }

            $this->newLine();
            $this->info('✅ Email(s) envoyé(s) avec succès!');
            $this->info('📬 Vérifiez votre boîte de réception (ou Mailtrap si en test)');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function sendWelcomeEmail($recipient)
    {
        $this->line('📨 Envoi de l\'email de bienvenue...');

        $user = User::first() ?? User::factory()->make([
            'name' => 'Jean Dupont',
            'email' => $recipient,
            'role' => 'user'
        ]);

        $organization = Organization::first() ?? (object)[
            'name' => 'Entreprise Test',
            'settings' => null
        ];

        $temporaryPassword = 'TempPass123!';

        Mail::to($recipient)->send(new WelcomeMail($user, $organization, $temporaryPassword));
        $this->info('   ✓ Email de bienvenue envoyé');
    }

    private function sendMeetingEmail($recipient)
    {
        $this->line('📨 Envoi de l\'invitation à une réunion...');

        $meeting = Meeting::with('organizer')->first();

        if (!$meeting) {
            // Créer un meeting de test
            $organizer = User::first();
            if (!$organizer) {
                $this->warn('   ⚠ Aucun utilisateur en base. Email ignoré.');
                return;
            }

            $meeting = new Meeting([
                'title' => 'Réunion de Planification Sprint',
                'description' => 'Discussion sur les objectifs du prochain sprint et répartition des tâches.',
                'scheduled_at' => now()->addDays(2),
                'duration' => 60,
                'meeting_link' => 'https://meet.locatalk.app/meeting-123',
                'status' => 'scheduled',
                'organization_id' => $organizer->organization_id,
                'organizer_id' => $organizer->id
            ]);
            $meeting->organizer = $organizer;
        }

        $participant = User::first() ?? (object)['name' => 'Marie Martin', 'email' => $recipient];

        Mail::to($recipient)->send(new MeetingInvitationMail($meeting, $participant));
        $this->info('   ✓ Invitation réunion envoyée');
    }

    private function sendTaskEmail($recipient)
    {
        $this->line('📨 Envoi de la notification de tâche assignée...');

        $task = Task::with('project')->first();

        if (!$task) {
            $this->warn('   ⚠ Aucune tâche en base. Création d\'une tâche de test...');

            $project = Project::first();
            if (!$project) {
                $this->warn('   ⚠ Aucun projet en base. Email ignoré.');
                return;
            }

            $task = new Task([
                'title' => 'Développer la nouvelle fonctionnalité',
                'description' => 'Implémenter le système de notification en temps réel avec WebSockets.',
                'priority' => 'high',
                'status' => 'todo',
                'due_date' => now()->addDays(5),
                'project_id' => $project->id
            ]);
            $task->project = $project;
        }

        $assignee = User::first() ?? (object)['name' => 'Pierre Durand', 'email' => $recipient];

        Mail::to($recipient)->send(new TaskAssignedMail($task, $assignee));
        $this->info('   ✓ Notification de tâche envoyée');
    }

    private function sendProjectEmail($recipient)
    {
        $this->line('📨 Envoi de l\'invitation au projet...');

        $project = Project::first();

        if (!$project) {
            $this->warn('   ⚠ Aucun projet en base. Création d\'un projet de test...');

            $project = new Project([
                'name' => 'Refonte Site Web',
                'description' => 'Modernisation complète du site web de l\'entreprise avec React et Laravel.',
                'status' => 'in_progress',
                'progress' => 35,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'organization_id' => Organization::first()->id ?? 1
            ]);
        }

        $member = User::first() ?? (object)['name' => 'Sophie Bernard', 'email' => $recipient];
        $role = 'manager';

        Mail::to($recipient)->send(new ProjectInvitationMail($project, $member, $role));
        $this->info('   ✓ Invitation projet envoyée');
    }

    private function sendMessageEmail($recipient)
    {
        $this->line('📨 Envoi de la notification de message...');

        $message = Message::with(['sender', 'channel'])->first();

        if (!$message) {
            $this->warn('   ⚠ Aucun message en base. Création d\'un message de test...');

            $sender = User::first();
            if (!$sender) {
                $this->warn('   ⚠ Aucun utilisateur en base. Email ignoré.');
                return;
            }

            $message = new Message([
                'content' => 'Bonjour ! J\'ai terminé la revue du code. Le projet est prêt pour le déploiement. Pouvez-vous valider les derniers changements ?',
                'sender_id' => $sender->id,
                'receiver_id' => null,
                'channel_id' => null,
                'attachments' => json_encode(['document.pdf', 'screenshot.png']),
                'created_at' => now()
            ]);
            $message->sender = $sender;
        }

        $recipientUser = User::first() ?? (object)['name' => 'Luc Petit', 'email' => $recipient];

        Mail::to($recipient)->send(new MessageNotificationMail($message, $recipientUser));
        $this->info('   ✓ Notification de message envoyée');
    }
}

