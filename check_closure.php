<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empleado;

$record = Empleado::find(48);

// Emulate the tiene_incapacidad closure
$closure = function (?\App\Models\Empleado $record) {
    if (!$record || !$record->tiene_incapacidad) {
        return 'No';
    }
    
    $badge = 'Sí';
    
    $doc = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->first();
    $iconHtml = '';
    if ($doc && !empty($doc->file_path)) {
        $url = route('admin.recursos_humanos.ver_archivo', ['path' => $doc->file_path]);
        $iconHtml = ' [ICON_FOUND: ' . $url . ']';
    } else {
        $iconHtml = ' [NO_DOC_FOUND_IN_CLOSURE]';
    }
    
    return $badge . $iconHtml;
};

echo "Result: " . $closure($record) . "\n";
