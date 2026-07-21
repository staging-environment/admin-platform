<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- ALL USERS ---\n";
foreach (App\Models\User::all() as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
    echo "Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
    echo "Permissions: " . implode(', ', $u->getAllPermissions()->pluck('name')->toArray()) . "\n";
    echo "-------------------\n";
}
