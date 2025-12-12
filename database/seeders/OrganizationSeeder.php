<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::create([
            'name' => 'APEC LTD',
            'slug' => 'apec-ltd',
            'plan' => 'business',
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addYear(),
            'max_users' => 50,
            'allow_remote_access' => false,
            'email' => 'contact@apec.com',
            'phone' => '+225 07 XX XX XX XX',
            'address' => 'Abidjan, Plateau',
        ]);

        // Organisation de test supplémentaire
        Organization::create([
            'name' => 'Tech Innovate CI',
            'slug' => 'tech-innovate-ci',
            'plan' => 'pro',
            'subscription_status' => 'trial',
            'subscription_expires_at' => now()->addDays(14),
            'max_users' => 10,
            'allow_remote_access' => true,
            'email' => 'contact@techinnovate.ci',
            'phone' => '+225 05 XX XX XX XX',
        ]);
    }
}
