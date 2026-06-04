<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'gestor@utrecar.com')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: " . $user->email . " (ID: " . $user->id . ")\n";
echo "Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
echo "Permissions via Roles: " . implode(', ', $user->getPermissionsViaRoles()->pluck('name')->toArray()) . "\n";
echo "Direct Permissions: " . implode(', ', $user->permissions->pluck('name')->toArray()) . "\n";
echo "Can view recursos humanos? " . ($user->can('gestion_recursos_humanos') ? 'YES' : 'NO') . "\n";
echo "Can view documentacion? " . ($user->can('gestion_documentacion_empleados') ? 'YES' : 'NO') . "\n";
