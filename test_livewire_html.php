<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Empleado;
use App\Filament\Resources\Empleados\Pages\ViewEmpleado;
use Livewire\Livewire;

Auth::loginUsingId(1);
$emp = Empleado::find(48);

echo "Starting Livewire render test...\n";

try {
    // We can simulate mounting the Livewire component
    $html = Livewire::test(ViewEmpleado::class, ['record' => 48])->html();
    
    // Search for tiene_incapacidad or the eye icon or Sí badge in the HTML
    echo "=== HTML contains 'tiene_incapacidad'? ===\n";
    echo str_contains($html, 'tiene_incapacidad') ? "YES\n" : "NO\n";
    
    echo "=== HTML contains the SVG path or eye icon? ===\n";
    echo str_contains($html, 'ver-archivo') ? "YES\n" : "NO\n";
    
    // Search for ¿Tiene Incapacidad? in the HTML
    $pos = strpos($html, '¿Tiene Incapacidad?');
    if ($pos !== false) {
        echo "=== HTML excerpt for ¿Tiene Incapacidad? ===\n";
        echo substr($html, $pos - 500, 2000) . "\n";
    } else {
        echo "Could not find '¿Tiene Incapacidad?' text in HTML.\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
