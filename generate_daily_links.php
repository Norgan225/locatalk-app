<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Meeting;
use App\Services\DailyService;

$dailyService = new DailyService();

// First, delete all existing meetings to recreate them fresh
$meetings = Meeting::all();

echo "Suppression des anciennes rooms Daily.co...\n";
foreach ($meetings as $meeting) {
    if ($meeting->meeting_link && str_contains($meeting->meeting_link, 'daily.co')) {
        $roomName = basename(parse_url($meeting->meeting_link, PHP_URL_PATH));
        $dailyService->deleteRoom($roomName);
        echo "🗑️  Room supprimée: {$roomName}\n";
    }
}

echo "\n" . str_repeat('=', 50) . "\n\n";

$meetings = Meeting::whereIn('status', ['scheduled', 'ongoing'])->get();

echo "Création de " . $meetings->count() . " nouvelle(s) room(s) Daily.co...\n\n";

foreach ($meetings as $meeting) {
    // Don't pass 'off' for recording, just omit it for free plan
    $options = [];
    if ($meeting->is_recorded) {
        $options['enable_recording'] = 'cloud';
    }

    $room = $dailyService->createRoom($meeting->id, $options);

    if ($room && isset($room['url'])) {
        $meeting->meeting_link = $room['url'];
        $meeting->save();
        echo "✅ {$meeting->title}\n   -> {$room['url']}\n\n";
    } else {
        echo "❌ Erreur pour: {$meeting->title}\n\n";
    }
}

echo "Terminé!\n";
