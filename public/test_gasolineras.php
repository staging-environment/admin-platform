<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = App\Models\Gasolinera::all();
foreach ($results as $r) {
    echo "Codigo: {$r->Codigo} | Nombre: {$r->Nombre} | Direccion: {$r->Direccion} | Localidad: {$r->Localidad}\n";
}
