<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Channel;
use App\Models\Department;
use App\Models\User;

class ChannelSeeder extends Seeder
{
    public function run(): void
    {
        $organizationId = 1;
        $owner = User::where('role', 'owner')->first();

        // 1. CANAUX GÉNÉRAUX (toute l'organisation)
        $generalChannels = [
            [
                'name' => '#annonces',
                'description' => 'Annonces officielles de la direction',
                'type' => 'public',
            ],
            [
                'name' => '#general',
                'description' => 'Discussion générale pour tous',
                'type' => 'public',
            ],
            [
                'name' => '#random',
                'description' => 'Discussions informelles et pause café',
                'type' => 'public',
            ],
            [
                'name' => '#questions',
                'description' => 'Posez vos questions ici',
                'type' => 'public',
            ],
        ];

        foreach ($generalChannels as $channel) {
            Channel::create([
                'organization_id' => $organizationId,
                'department_id' => null,
                'name' => $channel['name'],
                'description' => $channel['description'],
                'type' => $channel['type'],
                'created_by' => $owner->id,
            ]);
        }

        // 2. CANAUX PAR DÉPARTEMENT
        $departments = Department::where('organization_id', $organizationId)->get();

        foreach ($departments as $dept) {
            // Canal général du département
            Channel::create([
                'organization_id' => $organizationId,
                'department_id' => $dept->id,
                'name' => '#' . strtolower(str_replace(' ', '-', $dept->name)) . '-general',
                'description' => 'Canal général du département ' . $dept->name,
                'type' => 'department',
                'created_by' => $owner->id,
            ]);

            // Canal privé pour responsables (selon département)
            if ($dept->name === 'Ressources Humaines') {
                Channel::create([
                    'organization_id' => $organizationId,
                    'department_id' => $dept->id,
                    'name' => '#rh-confidentiel',
                    'description' => 'Discussions confidentielles RH',
                    'type' => 'private',
                    'created_by' => $owner->id,
                ]);
            }

            if ($dept->name === 'Marketing') {
                Channel::create([
                    'organization_id' => $organizationId,
                    'department_id' => $dept->id,
                    'name' => '#campagnes',
                    'description' => 'Planification des campagnes marketing',
                    'type' => 'department',
                    'created_by' => $owner->id,
                ]);
            }

            if ($dept->name === 'IT / Technique') {
                Channel::create([
                    'organization_id' => $organizationId,
                    'department_id' => $dept->id,
                    'name' => '#dev-backend',
                    'description' => 'Développement backend',
                    'type' => 'private',
                    'created_by' => $owner->id,
                ]);
            }
        }
    }
}
