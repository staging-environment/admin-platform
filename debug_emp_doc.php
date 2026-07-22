<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empleado;

$record = Empleado::find(48);
echo "Empleado: " . $record->nombre . "\n";
echo "Tiene Incapacidad: " . ($record->tiene_incapacidad ? 'Sí' : 'No') . "\n";

$docsRelation = $record->documentos();
echo "Relation class: " . get_class($docsRelation) . "\n";

$allDocs = $record->documentos;
echo "All docs count: " . $allDocs->count() . "\n";
foreach ($allDocs as $d) {
    echo "- ID: {$d->id}, Nombre: {$d->nombre}, Tipo: '{$d->tipo}', File: '{$d->file_path}'\n";
}

$doc = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
if ($doc) {
    echo "Found doc ID: {$doc->id}, Tipo: '{$doc->tipo}', File: '{$doc->file_path}'\n";
} else {
    echo "NO DOC FOUND with whereIn tipo!\n";
}
