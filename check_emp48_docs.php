<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ALL DOCUMENTS FOR EMPLOYEE 48 ===\n";
$docs = DB::table('empleado_documentos')->where('empleado_id', 48)->get();
foreach ($docs as $d) {
    echo "ID: {$d->id} | Nombre: {$d->nombre} | Tipo: '{$d->tipo}' | FilePath: {$d->file_path}\n";
}

echo "\n=== ALL INCAPACIDAD & DISCAPACIDAD DOCUMENT TYPES IN SYSTEM ===\n";
$allTypes = DB::table('empleado_documentos')->select('tipo', DB::raw('COUNT(*) as total'))->groupBy('tipo')->get();
foreach ($allTypes as $t) {
    echo "Tipo: '{$t->tipo}' | Count: {$t->total}\n";
}
