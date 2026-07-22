<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- ROLES IN DATABASE ---\n";
foreach(\Spatie\Permission\Models\Role::all() as $r) {
    $perms = $r->permissions()->pluck('name')->toArray();
    echo "Role ID: {$r->id} | Name: {$r->name} | Guard: {$r->guard_name} | Permissions: " . implode(', ', $perms) . "\n";
}

echo "\n--- ALL PERMISSIONS ---\n";
foreach(\Spatie\Permission\Models\Permission::all() as $p) {
    echo "Perm ID: {$p->id} | Name: {$p->name} | Guard: {$p->guard_name}\n";
}

echo "\n--- USER perdonero@gmail.com ---\n";
$user = \App\Models\User::where('email', 'perdonero@gmail.com')->first();
if ($user) {
    echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    echo "Direct Permissions: " . implode(', ', $user->permissions->pluck('name')->toArray()) . "\n";
    echo "All Permissions: " . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) . "\n";
}
