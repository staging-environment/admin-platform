<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Connected Database: " . \DB::connection()->getDatabaseName() . "\n";

$count = \App\Models\Empleado::count();
echo "Total Empleados in DB: " . $count . "\n";
foreach (\App\Models\Empleado::all() as $e) {
    echo "ID: " . $e->id . " Name: " . $e->nombre . " " . $e->apellidos . " Email: " . $e->email . "\n";
}
