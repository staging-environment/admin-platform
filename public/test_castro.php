<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$empleado = App\Models\Empleado::find(48);
if (!$empleado) {
    echo "Empleado 48 no encontrado!\n";
    exit;
}

echo "Empleado: {$empleado->nombre} {$empleado->apellidos} | Tipo Contrato: {$empleado->tipo_contrato} | Vencimiento: {$empleado->fecha_vencimiento_contrato}\n";

$docs = $empleado->documentos;
echo "Documentos (" . count($docs) . "):\n";
foreach ($docs as $d) {
    echo "ID: {$d->id} | Tipo: {$d->tipo} | Nombre: {$d->nombre} | Tipo Contrato: {$d->tipo_contrato} | Vencimiento: {$d->fecha_vencimiento_contrato} | Path: {$d->file_path}\n";
}
