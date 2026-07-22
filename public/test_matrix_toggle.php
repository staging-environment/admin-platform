<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = \Spatie\Permission\Models\Role::where('name', 'Empleado')->first();
$permission = \Spatie\Permission\Models\Permission::where('name', 'acceder_ficha_empleado')->first();

if ($role && $permission) {
    echo "Before toggle: " . ($role->hasPermissionTo($permission) ? 'YES' : 'NO') . "\n";
    
    // Toggle it on
    $role->givePermissionTo($permission);
    echo "After give: " . ($role->hasPermissionTo($permission) ? 'YES' : 'NO') . "\n";
    
    // Toggle it off
    $role->revokePermissionTo($permission);
    echo "After revoke: " . ($role->hasPermissionTo($permission) ? 'YES' : 'NO') . "\n";
} else {
    echo "Role or permission not found\n";
}
