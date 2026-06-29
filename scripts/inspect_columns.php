<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Gasolinera;

echo "All gasolineras in database:\n";
$gasolineras = Gasolinera::all();
foreach ($gasolineras as $g) {
    echo "Codigo: " . $g->Codigo . " | Nombre: " . $g->Nombre . " | Poblacion: " . $g->Poblacion . " | Provincia: " . $g->Provincia . "\n";
}
