<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = App\Models\Gasolinera::where('Nombre', 'like', '%utrecar%')->get();
foreach ($results as $r) {
    echo "Nombre: {$r->Nombre} | Direccion: {$r->Direccion} | Localidad: {$r->Localidad}\n";
}
