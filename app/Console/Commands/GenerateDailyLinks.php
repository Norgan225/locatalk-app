<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meeting;
use App\Services\DailyService;

class GenerateDailyLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:generate-daily-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Daily.co room links for meetings without links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dailyService = new DailyService();

        $meetings = Meeting::whereNull('meeting_link')
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->get();

        if ($meetings->isEmpty()) {
            $this->info('Aucune réunion sans lien trouvée.');
            return 0;
        }

        $this->info("Génération de liens Daily.co pour {$meetings->count()} réunion(s)...");

        foreach ($meetings as $meeting) {
            $room = $dailyService->createRoom($meeting->id, [
                'enable_recording' => $meeting->is_recorded ? 'cloud' : 'off',
            ]);

            if ($room) {
                $meeting->meeting_link = $room['url'];
                $meeting->save();
                $this->info("✅ {$meeting->title} - {$room['url']}");
            } else {
                $this->error("❌ Échec pour: {$meeting->title}");
            }
        }

        $this->info("\n✅ Génération terminée !");
        return 0;
    }
}
