<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$stations = DB::connection('virtusgesnet')
    ->table('estaciones')
    ->get();
foreach ($stations as $s) {
    echo "Codigo: " . $s->Codigo . " - Nombre: " . $s->Nombre . " - Poblacion: " . $s->Poblacion . "\n";
}
