<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (\App\Models\Gasolinera::with('contenido')->get() as $g) {
    echo "ID: " . $g->Codigo . " | Name: " . $g->Nombre . PHP_EOL;
    if ($g->contenido) {
        echo "  Lat: " . var_export($g->contenido->latitud, true) . PHP_EOL;
        echo "  Lng: " . var_export($g->contenido->longitud, true) . PHP_EOL;
    } else {
        echo "  No contenido" . PHP_EOL;
    }
}
