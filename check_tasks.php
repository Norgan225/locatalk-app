<?php

use App\Models\Task;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::first(); // Assuming the user is the first user or I can try to find by email if I knew it.
// Let's just list all tasks and their assignees.

echo "Total Tasks: " . Task::count() . "\n";
$tasks = Task::all();
foreach ($tasks as $task) {
    echo "Task ID: {$task->id}, Title: {$task->title}, Status: {$task->status}, Assigned To: {$task->assigned_to}\n";
}

if ($user) {
    echo "\nUser ID: {$user->id}, Name: {$user->name}\n";
    $myTasks = Task::where('assigned_to', $user->id)->count();
    echo "Tasks assigned to user {$user->id}: {$myTasks}\n";
}
