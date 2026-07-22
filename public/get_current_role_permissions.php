<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = \Spatie\Permission\Models\Role::where('name', 'Empleado')->first();
if ($role) {
    echo "Role Empleado found.\n";
    echo "Permissions count: " . $role->permissions()->count() . "\n";
    echo "Permissions: " . implode(', ', $role->permissions()->pluck('name')->toArray()) . "\n";
} else {
    echo "Role Empleado not found.\n";
}

$user = \App\Models\User::where('email', 'perdonero@gmail.com')->first();
if ($user) {
    echo "User perdonero has roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    echo "Can acceder_ficha_empleado: " . ($user->can('acceder_ficha_empleado') ? 'YES' : 'NO') . "\n";
}
