<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\Department;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $organizationId = 1;

        // Projet 1 : Marketing
        $marketingDept = Department::where('name', 'Marketing')->first();
        $marketingResp = User::where('department_id', $marketingDept->id)
            ->where('role', 'responsable')->first();

        $project1 = Project::create([
            'organization_id' => $organizationId,
            'department_id' => $marketingDept->id,
            'name' => 'Campagne Q4 2025',
            'description' => 'Campagne marketing pour le quatrième trimestre',
            'status' => 'active',
            'progress' => 45,
            'deadline' => now()->addDays(30),
            'created_by' => $marketingResp->id,
        ]);

        // Ajouter des membres au projet
        $marketingUsers = User::where('department_id', $marketingDept->id)->get();
        foreach ($marketingUsers as $user) {
            $project1->users()->attach($user->id, [
                'role' => $user->id === $marketingResp->id ? 'owner' : 'contributor'
            ]);
        }

        // Tâches du projet 1
        Task::create([
            'project_id' => $project1->id,
            'title' => 'Définir la stratégie de campagne',
            'description' => 'Brainstorming et définition des objectifs',
            'assigned_to' => $marketingResp->id,
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => now()->addDays(5),
            'created_by' => $marketingResp->id,
        ]);

        Task::create([
            'project_id' => $project1->id,
            'title' => 'Créer les visuels',
            'description' => 'Design des visuels pour réseaux sociaux',
            'assigned_to' => User::where('email', 'mariame@apec.com')->first()->id,
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(10),
            'created_by' => $marketingResp->id,
        ]);

        Task::create([
            'project_id' => $project1->id,
            'title' => 'Rédiger les copies publicitaires',
            'description' => 'Textes pour Facebook et Instagram',
            'assigned_to' => User::where('email', 'moussa@apec.com')->first()->id,
            'priority' => 'medium',
            'status' => 'todo',
            'due_date' => now()->addDays(15),
            'created_by' => $marketingResp->id,
        ]);

        // Projet 2 : IT
        $itDept = Department::where('name', 'IT / Technique')->first();
        $itResp = User::where('department_id', $itDept->id)
            ->where('role', 'responsable')->first();

        $project2 = Project::create([
            'organization_id' => $organizationId,
            'department_id' => $itDept->id,
            'name' => 'Refonte Site Web',
            'description' => 'Modernisation du site web corporate',
            'status' => 'active',
            'progress' => 75,
            'deadline' => now()->addDays(20),
            'created_by' => $itResp->id,
        ]);

        // Ajouter des membres
        $itUsers = User::where('department_id', $itDept->id)->get();
        foreach ($itUsers as $user) {
            $project2->users()->attach($user->id, [
                'role' => $user->id === $itResp->id ? 'owner' : 'contributor'
            ]);
        }

        // Tâches du projet 2
        Task::create([
            'project_id' => $project2->id,
            'title' => 'Design UI/UX',
            'description' => 'Maquettes Figma',
            'assigned_to' => User::where('email', 'raissa@apec.com')->first()->id,
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => now()->subDays(5),
            'created_by' => $itResp->id,
        ]);

        Task::create([
            'project_id' => $project2->id,
            'title' => 'Développement Frontend',
            'description' => 'Intégration HTML/CSS/JS',
            'assigned_to' => User::where('email', 'karim@apec.com')->first()->id,
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(10),
            'created_by' => $itResp->id,
        ]);

        Task::create([
            'project_id' => $project2->id,
            'title' => 'Tests et déploiement',
            'description' => 'Tests finaux et mise en production',
            'assigned_to' => $itResp->id,
            'priority' => 'medium',
            'status' => 'todo',
            'due_date' => now()->addDays(18),
            'created_by' => $itResp->id,
        ]);

        // Projet 3 : RH
        $rhDept = Department::where('name', 'Ressources Humaines')->first();
        $rhResp = User::where('department_id', $rhDept->id)
            ->where('role', 'responsable')->first();

        $project3 = Project::create([
            'organization_id' => $organizationId,
            'department_id' => $rhDept->id,
            'name' => 'Recrutement Commerciaux',
            'description' => 'Recruter 5 commerciaux avant fin d\'année',
            'status' => 'active',
            'progress' => 60,
            'deadline' => now()->addDays(45),
            'created_by' => $rhResp->id,
        ]);

        // Tâches
        Task::create([
            'project_id' => $project3->id,
            'title' => 'Publier les offres d\'emploi',
            'description' => 'Publier sur LinkedIn et sites d\'emploi',
            'assigned_to' => User::where('email', 'aicha@apec.com')->first()->id,
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => now()->addDays(5),
            'created_by' => $rhResp->id,
        ]);

        // Mettre à jour la progression des projets
        $project1->updateProgress();
        $project2->updateProgress();
        $project3->updateProgress();
    }
}
