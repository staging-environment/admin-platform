<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empleado;
use App\Filament\Resources\Empleados\EmpleadoResource;

Auth::loginUsingId(1);
$emp = Empleado::find(48);
echo "Testing Empleado 48 Infolist...\n";

try {
    $schema = new \Filament\Schemas\Schema();
    $schema->record($emp);
    $res = EmpleadoResource::infolist($schema);
    echo "Infolist created successfully. Components count: " . count($res->getComponents()) . "\n";
    echo "SUCCESS: Page view logic for Empleado 48 executes without any error!\n";
} catch (\Throwable $e) {
    echo "ERROR IN INFOLIST: " . $e->getMessage() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
