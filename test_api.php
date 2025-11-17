<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate a request
$request = Illuminate\Http\Request::create('/api/messaging/conversations', 'GET');

// Get first user and authenticate
$user = App\Models\User::first();
$token = $user->createToken('test-diagnostic')->plainTextToken;

echo "=== TEST DIAGNOSTIC API ===\n";
echo "User: {$user->name} (ID: {$user->id})\n";
echo "Token: " . substr($token, 0, 30) . "...\n\n";

// Add Bearer token
$request->headers->set('Authorization', 'Bearer ' . $token);
$request->headers->set('Accept', 'application/json');

echo "=== Testing /api/messaging/conversations ===\n";

try {
    $response = $kernel->handle($request);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Response:\n";
    echo $response->getContent() . "\n\n";

    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        echo "Conversations count: " . count($data['conversations'] ?? []) . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
