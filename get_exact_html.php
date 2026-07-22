<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empleado;
use App\Filament\Resources\Empleados\Pages\ViewEmpleado;
use Livewire\Livewire;

Auth::loginUsingId(1);
$emp = Empleado::find(48);

try {
    $html = Livewire::test(ViewEmpleado::class, ['record' => 48])->html();
    
    // Find ver-archivo in HTML
    $pos = strpos($html, 'ver-archivo');
    if ($pos !== false) {
        echo "=== EXACT HTML CONTAINING EYE ICON ===\n";
        echo substr($html, $pos - 300, 800) . "\n";
    } else {
        echo "ver-archivo not found in HTML\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
