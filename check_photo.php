<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Empleado::all() as $e) {
    echo "ID: {$e->id}, Nombre: {$e->nombre}, Foto: {$e->foto}\n";
}
