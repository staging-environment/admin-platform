<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- EMPLOYEES IN DATABASE ---\n";
foreach(\App\Models\Empleado::all() as $e) {
    echo "ID: {$e->id} | Name: {$e->nombre} {$e->apellidos} | Email: {$e->email}\n";
}
