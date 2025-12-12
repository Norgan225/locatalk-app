<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $organizationId = 1; // APEC LTD

        // 1. OWNER
        $owner = User::create([
            'organization_id' => $organizationId,
            'department_id' => Department::where('name', 'Direction')->first()->id,
            'name' => 'Marie Diallo',
            'email' => 'marie@apec.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'status' => 'active',
            'password_changed' => true,
            'email_verified_at' => now(),
        ]);

        // 2. ADMIN
        User::create([
            'organization_id' => $organizationId,
            'department_id' => Department::where('name', 'Direction')->first()->id,
            'name' => 'Amadou Koné',
            'email' => 'amadou@apec.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'password_changed' => true,
            'email_verified_at' => now(),
            'created_by' => $owner->id,
        ]);

        // 3. RESPONSABLES (Chefs de département)
        $departments = Department::where('organization_id', $organizationId)
            ->whereNotIn('name', ['Direction'])
            ->get();

        $responsables = [
            ['name' => 'Sophie Koné', 'email' => 'sophie@apec.com', 'dept' => 'Ressources Humaines'],
            ['name' => 'Jean Traoré', 'email' => 'jean@apec.com', 'dept' => 'Marketing'],
            ['name' => 'Ibrahim Diarra', 'email' => 'ibrahim@apec.com', 'dept' => 'IT / Technique'],
            ['name' => 'Fatou Touré', 'email' => 'fatou@apec.com', 'dept' => 'Finance'],
            ['name' => 'Youssouf Bamba', 'email' => 'youssouf@apec.com', 'dept' => 'Commercial'],
        ];

        foreach ($responsables as $resp) {
            $dept = Department::where('name', $resp['dept'])->first();
            $user = User::create([
                'organization_id' => $organizationId,
                'department_id' => $dept->id,
                'name' => $resp['name'],
                'email' => $resp['email'],
                'password' => Hash::make('password123'),
                'role' => 'responsable',
                'status' => 'active',
                'password_changed' => true,
                'email_verified_at' => now(),
                'created_by' => $owner->id,
            ]);

            // Assigner comme chef du département
            $dept->update(['head_user_id' => $user->id]);
        }

        // 4. EMPLOYÉS
        $employes = [
            // RH
            ['name' => 'Aïcha Bakayoko', 'email' => 'aicha@apec.com', 'dept' => 'Ressources Humaines'],
            ['name' => 'Sekou Camara', 'email' => 'sekou@apec.com', 'dept' => 'Ressources Humaines'],

            // Marketing
            ['name' => 'Mariame Coulibaly', 'email' => 'mariame@apec.com', 'dept' => 'Marketing'],
            ['name' => 'Moussa Sangaré', 'email' => 'moussa@apec.com', 'dept' => 'Marketing'],
            ['name' => 'Awa Diop', 'email' => 'awa@apec.com', 'dept' => 'Marketing'],

            // IT
            ['name' => 'Karim Ouattara', 'email' => 'karim@apec.com', 'dept' => 'IT / Technique'],
            ['name' => 'Salif Keita', 'email' => 'salif@apec.com', 'dept' => 'IT / Technique'],
            ['name' => 'Raissa N\'Guessan', 'email' => 'raissa@apec.com', 'dept' => 'IT / Technique'],

            // Finance
            ['name' => 'Clarisse Yao', 'email' => 'clarisse@apec.com', 'dept' => 'Finance'],
            ['name' => 'Koffi Assouan', 'email' => 'koffi@apec.com', 'dept' => 'Finance'],

            // Commercial
            ['name' => 'Adjoua Kouassi', 'email' => 'adjoua@apec.com', 'dept' => 'Commercial'],
            ['name' => 'Brahima Fofana', 'email' => 'brahima@apec.com', 'dept' => 'Commercial'],
            ['name' => 'Nathalie Brou', 'email' => 'nathalie@apec.com', 'dept' => 'Commercial'],
        ];

        foreach ($employes as $emp) {
            User::create([
                'organization_id' => $organizationId,
                'department_id' => Department::where('name', $emp['dept'])->first()->id,
                'name' => $emp['name'],
                'email' => $emp['email'],
                'password' => Hash::make('password123'),
                'role' => 'employe',
                'status' => 'active',
                'password_changed' => true,
                'email_verified_at' => now(),
                'created_by' => $owner->id,
            ]);
        }
    }
}
