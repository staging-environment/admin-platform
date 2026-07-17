<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = App\Models\Empleado::whereNull('deleted_at')
    ->where('gasolinera_codigo', 1)
    ->orderBy('apellidos', 'asc')
    ->get(['id', 'nombre', 'apellidos']);

foreach ($results as $r) {
    echo "ID: {$r->id} | Nombre: {$r->nombre} | Apellidos: {$r->apellidos}\n";
}
