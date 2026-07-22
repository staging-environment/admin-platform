<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empleado;
use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Schemas\Schema;

Auth::loginUsingId(1);
$emp = Empleado::find(48);

try {
    $schema = new Schema();
    $schema->record($emp);
    $res = EmpleadoResource::infolist($schema);
    echo "RENDER OK: " . count($res->getComponents()) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
