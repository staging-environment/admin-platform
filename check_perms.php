<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();
foreach ($users as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    echo "Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
    echo "Permissions: " . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) . "\n\n";
}
