<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'gestor@utrecar.com')->first();
echo "User: " . $user->email . "\n";
echo "Can utilizar_explorador: " . ($user->can('utilizar_explorador') ? 'YES' : 'NO') . "\n";
echo "Has role Gestor: " . ($user->hasRole('Gestor') ? 'YES' : 'NO') . "\n";
echo "Guard name of permission: " . \Spatie\Permission\Models\Permission::where('name', 'utilizar_explorador')->first()->guard_name . "\n";
