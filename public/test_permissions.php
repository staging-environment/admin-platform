<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'perdonero@gmail.com')->first();
if ($user) {
    echo "User found: {$user->email}\n";
    echo "Has role Empleado: " . ($user->hasRole('Empleado') ? 'YES' : 'NO') . "\n";
    echo "Can acceder_ficha_empleado: " . ($user->can('acceder_ficha_empleado') ? 'YES' : 'NO') . "\n";
    
    $panel = filament()->getPanel('admin');
    echo "Can access panel admin: " . ($user->canAccessPanel($panel) ? 'YES' : 'NO') . "\n";
    
    $role = \Spatie\Permission\Models\Role::where('name', 'Empleado')->first();
    echo "Role Empleado permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . "\n";
    
    // Check Dashboard access
    echo "Dashboard can access: " . (\App\Filament\Pages\Dashboard::canAccess() ? 'YES' : 'NO') . "\n";
} else {
    echo "User perdonero@gmail.com not found\n";
}
