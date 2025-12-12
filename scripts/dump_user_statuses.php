<?php
// Script de debug: afficher UserStatus et getOnlineUsers
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserStatus;

echo "--- Tous les statuts UserStatus ---\n";
echo "Now: " . \Carbon\Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
$all = UserStatus::with('user')->get();
foreach ($all as $s) {
    $name = $s->user ? $s->user->name : 'N/A';
    $last = $s->last_activity ? $s->last_activity->format('Y-m-d H:i:s') : 'NULL';
    echo sprintf("User %d (%s): status=%s, invisible=%s, last_activity=%s\n", $s->user_id, $name, $s->status, $s->is_invisible ? 'true' : 'false', $last);
}

echo "\n--- getOnlineUsers() ---\n";
$online = UserStatus::getOnlineUsers();
foreach ($online as $s) {
    $name = $s->user ? $s->user->name : 'N/A';
    $last = $s->last_activity ? $s->last_activity->format('Y-m-d H:i:s') : 'NULL';
    echo sprintf("Online User %d (%s): last_activity=%s\n", $s->user_id, $name, $last);
}
