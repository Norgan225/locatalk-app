<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Meeting;

$meetings = Meeting::whereIn('status', ['scheduled', 'ongoing'])->get();

echo "Configuration de " . $meetings->count() . " réunion(s) avec Jitsi Meet...\n\n";

foreach ($meetings as $meeting) {
    $roomName = 'locatalk-meeting-' . $meeting->id . '-' . time();
    $meeting->meeting_link = 'https://meet.jit.si/' . $roomName;
    $meeting->save();
    echo "✅ {$meeting->title}\n   -> {$meeting->meeting_link}\n\n";
}

echo "\nToutes les réunions utilisent maintenant Jitsi Meet (gratuit, illimité)!\n";
