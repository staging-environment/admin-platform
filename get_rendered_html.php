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

$schema = new Schema();
$schema->record($emp);
$res = EmpleadoResource::infolist($schema);

// Find the tiene_incapacidad component in the schema
function findComponent($components, $name) {
    foreach ($components as $c) {
        if (method_exists($c, 'getName') && $c->getName() === $name) {
            return $c;
        }
        if (method_exists($c, 'getChildComponents')) {
            $found = findComponent($c->getChildComponents(), $name);
            if ($found) return $found;
        }
    }
    return null;
}

$comp = findComponent($res->getComponents(), 'tiene_incapacidad');
if ($comp) {
    echo "Component found!\n";
    $state = $comp->getState();
    echo "State: " . var_export($state, true) . "\n";
    echo "Is HTML allowed: " . ($comp->isHtml() ? 'Yes' : 'No') . "\n";
} else {
    echo "Component NOT found!\n";
}
