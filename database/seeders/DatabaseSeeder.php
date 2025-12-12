<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important !
        $this->call([
            OrganizationSeeder::class,
            DepartmentSeeder::class, // Créer les départements d'abord
            UserSeeder::class, // Puis les users
            ChannelSeeder::class,
            ProjectSeeder::class,
        ]);

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->info('');
        $this->command->info('📧 Identifiants de connexion :');
        $this->command->info('');
        $this->command->info('👤 OWNER:');
        $this->command->info('   Email: marie@apec.com');
        $this->command->info('   Mot de passe: password123');
        $this->command->info('');
        $this->command->info('👤 ADMIN:');
        $this->command->info('   Email: amadou@apec.com');
        $this->command->info('   Mot de passe: password123');
        $this->command->info('');
        $this->command->info('👤 RESPONSABLE RH:');
        $this->command->info('   Email: sophie@apec.com');
        $this->command->info('   Mot de passe: password123');
        $this->command->info('');
        $this->command->info('👤 EMPLOYÉ Marketing:');
        $this->command->info('   Email: mariame@apec.com');
        $this->command->info('   Mot de passe: password123');
    }
}
