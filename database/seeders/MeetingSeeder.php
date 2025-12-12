<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer une organisation (ou la première)
        $organization = Organization::first();

        if (!$organization) {
            $this->command->error('Aucune organisation trouvée. Veuillez lancer OrganizationSeeder d\'abord.');
            return;
        }

        // Récupérer quelques utilisateurs de cette organisation
        $users = User::where('organization_id', $organization->id)->take(5)->get();

        if ($users->count() < 2) {
            $this->command->error('Pas assez d\'utilisateurs pour créer des réunions.');
            return;
        }

        $creator = $users->first();
        $participants = $users->slice(1);

        // Supprimer toutes les anciennes réunions
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Meeting::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Réunion TEST Daily.co - EN COURS
        $meeting1 = Meeting::create([
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'title' => 'Test Visio Daily.co - Réunion Active',
            'description' => 'Réunion de test pour l\'intégration Daily.co avec visioconférence en temps réel.',
            'start_time' => Carbon::now()->subMinutes(10),
            'end_time' => Carbon::now()->addHour(1),
            'status' => 'ongoing',
            'meeting_link' => null, // Sera généré automatiquement par Daily.co
            'is_recorded' => true,
        ]);

        $meeting1->participants()->attach($users->pluck('id'), ['joined_at' => now()]);

        // 2. Réunion planifiée (dans 2 heures)
        $meeting2 = Meeting::create([
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'title' => 'Revue Hebdomadaire - Équipe Produit',
            'description' => 'Discussion sur les KPIs et objectifs de la semaine prochaine.',
            'start_time' => Carbon::now()->addHours(2),
            'end_time' => Carbon::now()->addHours(3),
            'status' => 'scheduled',
            'meeting_link' => null, // Sera généré par Daily.co
            'is_recorded' => false,
        ]);

        $meeting2->participants()->attach($participants->pluck('id'));

        // 3. Réunion planifiée (demain matin)
        $meeting3 = Meeting::create([
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'title' => 'Daily Standup - Développement',
            'description' => 'Point quotidien rapide sur l\'avancement des tâches en cours.',
            'start_time' => Carbon::tomorrow()->setHour(9)->setMinute(0),
            'end_time' => Carbon::tomorrow()->setHour(9)->setMinute(15),
            'status' => 'scheduled',
            'meeting_link' => null,
            'is_recorded' => false,
        ]);

        $meeting3->participants()->attach($users->pluck('id'));

        // 4. Réunion terminée (hier)
        $meeting4 = Meeting::create([
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'title' => 'Rétrospective Sprint #23',
            'description' => 'Analyse des points positifs et axes d\'amélioration du dernier sprint.',
            'start_time' => Carbon::yesterday()->setHour(16)->setMinute(0),
            'end_time' => Carbon::yesterday()->setHour(17)->setMinute(30),
            'status' => 'completed',
            'meeting_link' => null,
            'is_recorded' => true,
        ]);

        $meeting4->participants()->attach($users->pluck('id'), [
            'joined_at' => Carbon::yesterday()->setHour(16),
            'left_at' => Carbon::yesterday()->setHour(17)->setMinute(30)
        ]);

        $this->command->info('✅ 4 réunions de test créées (1 en cours avec Daily.co) !');
    }
}
