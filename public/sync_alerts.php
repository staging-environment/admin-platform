<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (App\Models\Empleado::all() as $e) {
    $e->actualizarAlertas();
    echo "Sincronizadas alertas para: {$e->nombre} {$e->apellidos} (Alertas: " . $e->alertas()->count() . ")\n";
}
