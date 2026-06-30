<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$contents = \App\Models\GasolineraContenido::all();
foreach ($contents as $c) {
    echo "Código Gasolinera: " . $c->gasolinera_codigo . PHP_EOL;
    echo "Texto Inicio: " . var_export($c->texto_inicio, true) . PHP_EOL;
    echo "Quienes Somos: " . var_export($c->quienes_somos, true) . PHP_EOL;
    echo "---------------------------------" . PHP_EOL;
}
