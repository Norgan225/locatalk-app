<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class RoleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer une organisation existante ou en créer une
        $organization = Organization::first();

        if (!$organization) {
            $organization = Organization::create([
                'name' => 'Entreprise Demo',
                'slug' => 'entreprise-demo',
                'email' => 'contact@demo.com',
                'phone' => '+228 90 00 00 00',
                'address' => 'Lomé, Togo',
                'plan' => 'pro',
                'subscription_status' => 'active',
                'max_users' => 50,
                'max_projects' => 100,
            ]);
        }

        // Récupérer ou créer des départements
        $itDept = Department::firstOrCreate(
            ['name' => 'IT', 'organization_id' => $organization->id],
            ['description' => 'Département Informatique']
        );

        $hrDept = Department::firstOrCreate(
            ['name' => 'RH', 'organization_id' => $organization->id],
            ['description' => 'Ressources Humaines']
        );

        // Créer un compte OWNER (Propriétaire)
        $owner = User::updateOrCreate(
            ['email' => 'owner@demo.com'],
            [
                'name' => 'Patrick Owner',
                'email' => 'owner@demo.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'organization_id' => $organization->id,
                'department_id' => null, // Le owner n'est pas limité à un département
                'phone' => '+228 90 00 00 01',
            ]
        );

        // Créer un compte RESPONSABLE
        $responsable = User::updateOrCreate(
            ['email' => 'responsable@demo.com'],
            [
                'name' => 'Jean Responsable',
                'email' => 'responsable@demo.com',
                'password' => Hash::make('password'),
                'role' => 'responsable',
                'organization_id' => $organization->id,
                'department_id' => $itDept->id,
                'phone' => '+228 90 11 11 11',
            ]
        );

        // Créer un compte EMPLOYÉ
        $employe = User::updateOrCreate(
            ['email' => 'employe@demo.com'],
            [
                'name' => 'Marie Employée',
                'email' => 'employe@demo.com',
                'password' => Hash::make('password'),
                'role' => 'employe',
                'organization_id' => $organization->id,
                'department_id' => $hrDept->id,
                'phone' => '+228 90 22 22 22',
            ]
        );

        $this->command->info('✅ Comptes créés avec succès:');
        $this->command->info('📧 Owner: owner@demo.com | Password: password');
        $this->command->info('📧 Responsable: responsable@demo.com | Password: password');
        $this->command->info('📧 Employé: employe@demo.com | Password: password');
        $this->command->info('🏢 Organisation: ' . $organization->name);
    }
}
