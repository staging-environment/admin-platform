<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "User: " . $user->email . " (ID: " . $user->id . ")\n";
    echo "  Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
    echo "  Direct Permissions: " . implode(', ', $user->permissions->pluck('name')->toArray()) . "\n";
    echo "  All Permissions: " . implode(', ', $user->getAllPermissions()->pluck('name')->toArray()) . "\n";
}

$roles = \Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "Role: " . $role->name . "\n";
    echo "  Permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . "\n";
}
