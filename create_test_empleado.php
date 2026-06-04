<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $e = \App\Models\Empleado::create([
        'nombre' => 'Test',
        'apellidos' => 'Validation',
        'dni' => '12345678A',
        'fecha_nacimiento' => '1990-01-01',
        'direccion' => 'Test Street',
        'localidad' => 'Test',
        'codigo_postal' => '41000',
        'provincia' => 'Test',
        'telefono_principal' => '123456789',
        'email' => 'test@test.com'
    ]);
    echo "Created employee ID: " . $e->id . "\n";
} catch (\Exception $ex) {
    echo "Error: " . $ex->getMessage() . "\n";
}
