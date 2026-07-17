<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$empleado = App\Models\Empleado::find(48);
if (!$empleado) {
    echo "Empleado 48 no encontrado!\n";
    exit;
}

$contratos = $empleado->contratos;
echo "Contratos de empleado_contratos table (" . count($contratos) . "):\n";
foreach ($contratos as $c) {
    echo "ID: {$c->id} | Tipo: {$c->tipo_contrato} | Inicio: {$c->fecha_inicio} | Fin: {$c->fecha_fin} | Jornada: {$c->jornada} | Salario: {$c->salario}\n";
}
