<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- PRODUCTION USERS ---\n";
foreach (App\Models\User::all() as $u) {
    $roles = implode(', ', $u->getRoleNames()->toArray());
    $perms = implode(', ', $u->getAllPermissions()->pluck('name')->toArray());
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Roles: [{$roles}] | Perms: [{$perms}]\n";
}
