<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanObsoleteStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:clean-obsolete {--minutes=10 : Minutes d\'inactivité avant de considérer un statut comme obsolète}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les statuts utilisateur obsolètes (utilisateurs online sans activité récente)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->option('minutes');

        $this->info("🔍 Recherche des statuts obsolètes (pas d'activité depuis {$minutes} minutes)...");

        // Trouver les utilisateurs online sans activité récente
        $obsoleteStatuses = \App\Models\UserStatus::where('status', 'online')
            ->where(function($query) use ($minutes) {
                $query->whereNull('last_activity')
                      ->orWhere('last_activity', '<', now()->subMinutes($minutes));
            })
            ->with('user')
            ->get();

        if ($obsoleteStatuses->isEmpty()) {
            $this->info('✅ Aucun statut obsolète trouvé.');
            return;
        }

        $this->warn("📋 {$obsoleteStatuses->count()} statuts obsolètes trouvés :");

        foreach ($obsoleteStatuses as $status) {
            $this->line("  - {$status->user->name} (dernière activité: " .
                       ($status->last_activity ? $status->last_activity->diffForHumans() : 'jamais') . ")");
        }

        if ($this->confirm('Voulez-vous mettre ces utilisateurs hors ligne ?')) {
            $count = 0;
            foreach ($obsoleteStatuses as $status) {
                $status->update(['status' => 'offline']);
                $count++;
            }

            $this->info("✅ {$count} statuts mis à jour avec succès.");
        } else {
            $this->info('❌ Opération annulée.');
        }
    }
}
