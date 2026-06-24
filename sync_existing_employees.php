<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$empleados = \App\Models\Empleado::all();
echo "Found " . $empleados->count() . " employees.\n";
foreach ($empleados as $empleado) {
    $user = \App\Models\User::where('email', $empleado->email)->first();
    if (!$user) {
        $user = new \App\Models\User();
        $user->password = bcrypt('12345678');
        $user->name = $empleado->nombre . ' ' . $empleado->apellidos;
        $user->email = $empleado->email;
        $user->telefono = $empleado->telefono_principal;
        $user->save();
        $user->assignRole('Empleado');
        echo "Created User for " . $empleado->email . "\n";
    } else {
        // Just make sure they have the role Empleado if they don't have it
        if (!$user->hasRole('Empleado') && !$user->hasRole('Admin') && !$user->hasRole('Gestor')) {
            $user->assignRole('Empleado');
            echo "Assigned Empleado role to " . $empleado->email . "\n";
        }
    }
}
echo "Sync completed successfully.\n";
