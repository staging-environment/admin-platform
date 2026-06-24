<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$row = DB::connection('virtusgesnet')
    ->table('facturasyticketsdeventa')
    ->where('CodigoDeEstacion', 1)
    ->whereDate('FechaYHora', '2026-06-03')
    ->first();
echo "FACTURA/TICKET ROW:\n";
print_r($row);

if ($row) {
    $detail = DB::connection('virtusgesnet')
        ->table('detalledefacturasyticketsdeventa')
        ->where('CodigoDeEmpresaPropia', $row->CodigoDeEmpresaPropia)
        ->where('Serie', $row->Serie)
        ->where('Numero', $row->Numero)
        ->first();
    echo "\nDETAIL ROW:\n";
    print_r($detail);
}
