<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
foreach ($users as $u) {
    echo "User: {$u->email}\n";
    echo "Roles: " . $u->roles->pluck('name')->join(', ') . "\n";
    echo "Permissions: " . $u->getAllPermissions()->pluck('name')->join(', ') . "\n";
    echo "Has gestion_usuarios_roles? " . ($u->can('gestion_usuarios_roles') ? 'Yes' : 'No') . "\n\n";
}
