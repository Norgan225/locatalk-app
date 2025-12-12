<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PresenceService;

class CheckInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presence:check-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les utilisateurs inactifs et mettre à jour leur statut (away/offline)';

    /**
     * Execute the console command.
     */
    public function handle(PresenceService $presenceService)
    {
        $this->info('Vérification des utilisateurs inactifs...');

        $presenceService->checkInactiveUsers();

        $stats = $presenceService->getPresenceStats();

        $this->info('Statuts mis à jour avec succès !');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['En ligne', $stats['online']],
                ['Absent', $stats['away']],
                ['Occupé', $stats['busy']],
                ['Ne pas déranger', $stats['do_not_disturb']],
                ['Hors ligne', $stats['offline']],
                ['Total actifs', $stats['total_active']],
            ]
        );

        return Command::SUCCESS;
    }
}
