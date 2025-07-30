<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING DATABASE USERS ===\n";

$users = App\Models\User::all(['name', 'email']);

echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "- {$user->name} ({$user->email})\n";
}

echo "\n=== TESTING LOGIN ===\n";

$testCredentials = [
    ['admin@training.com', 'password123'],
    ['hr@training.com', 'password123'],
    ['john@training.com', 'password123'],
    ['jane@training.com', 'password123']
];

foreach ($testCredentials as $creds) {
    $user = App\Models\User::where('email', $creds[0])->first();
    if ($user) {
        $passwordMatch = Hash::check($creds[1], $user->password);
        echo "✓ {$creds[0]} - Password check: " . ($passwordMatch ? 'VALID' : 'INVALID') . "\n";
    } else {
        echo "✗ {$creds[0]} - User not found\n";
    }
}
