<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;


class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $organizationId = 1; // APEC LTD

        $departments = [
            ['name' => 'Direction', 'description' => 'Direction générale', 'color' => '#8B5CF6'],
            ['name' => 'Ressources Humaines', 'description' => 'Gestion du personnel', 'color' => '#10B981'],
            ['name' => 'Marketing', 'description' => 'Marketing et communication', 'color' => '#F59E0B'],
            ['name' => 'IT / Technique', 'description' => 'Développement IT', 'color' => '#3B82F6'],
            ['name' => 'Finance', 'description' => 'Comptabilité et finances', 'color' => '#EF4444'],
            ['name' => 'Commercial', 'description' => 'Ventes', 'color' => '#06B6D4'],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'organization_id' => $organizationId,
                'name' => $dept['name'],
                'description' => $dept['description'],
                'color' => $dept['color'],
                'is_active' => true,
                'created_by' => null, // Sera mis à jour après création des users
            ]);
        }
    }
}
