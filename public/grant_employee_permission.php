<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = \Spatie\Permission\Models\Role::where('name', 'Empleado')->first();
$permission = \Spatie\Permission\Models\Permission::where('name', 'acceder_ficha_empleado')->first();
if ($role && $permission) {
    $role->givePermissionTo($permission);
    echo "Granted acceder_ficha_empleado to Empleado role\n";
} else {
    echo "Role or permission not found\n";
}

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "Cache cleared!\n";
