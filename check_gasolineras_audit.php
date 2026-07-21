<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. AUTHORIZED STATIONS IN VIRTUSGESNET.ESTACIONES ===\n";
$estaciones = DB::connection('virtusgesnet')->table('estaciones')->get();
$validCodigos = $estaciones->pluck('Codigo')->toArray();
echo "Total Valid Stations: " . count($validCodigos) . "\n";
foreach ($estaciones as $e) {
    echo "  - Station Codigo: {$e->Codigo} | Name: " . ($e->Nombre ?? 'N/A') . "\n";
}

echo "\n=== 2. AUDITING EMPLOYEES & CONTRACTS FOR UNKNOWN STATIONS ===\n";
$invalidContractEmp = DB::table('empleados')
    ->whereNotNull('gasolinera_codigo')
    ->whereNotIn('gasolinera_codigo', $validCodigos)
    ->get();

echo "Employees assigned to UNKNOWN stations: " . $invalidContractEmp->count() . "\n";
foreach ($invalidContractEmp as $emp) {
    echo "  [ALERT] Employee ID: {$emp->id} | Name: {$emp->nombre} {$emp->apellidos} | Unknown Gasolinera Codigo: {$emp->gasolinera_codigo}\n";
}

echo "\n=== 3. AUDITING CONTACT MESSAGES FOR UNKNOWN STATIONS ===\n";
if (Schema::hasTable('contacto_mensajes')) {
    $invalidMsgs = DB::table('contacto_mensajes')
        ->whereNotNull('gasolinera_codigo')
        ->whereNotIn('gasolinera_codigo', $validCodigos)
        ->get();
    echo "Contact Messages for UNKNOWN stations: " . $invalidMsgs->count() . "\n";
    foreach ($invalidMsgs as $msg) {
        echo "  [ALERT] Message ID: {$msg->id} | Name: {$msg->nombre} | Email: {$msg->email} | Unknown Station: {$msg->gasolinera_codigo}\n";
    }
}

echo "\n=== 4. AUDITING GASOLINERA CONTENTS FOR UNKNOWN STATIONS ===\n";
if (Schema::hasTable('gasolinera_contenidos')) {
    $invalidContents = DB::table('gasolinera_contenidos')
        ->whereNotNull('gasolinera_codigo')
        ->whereNotIn('gasolinera_codigo', $validCodigos)
        ->get();
    echo "Gasolinera Contents for UNKNOWN stations: " . $invalidContents->count() . "\n";
    foreach ($invalidContents as $gc) {
        echo "  [ALERT] Content ID: {$gc->id} | Unknown Station: {$gc->gasolinera_codigo}\n";
    }
}
