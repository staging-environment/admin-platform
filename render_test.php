<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(1);
$html = Livewire::mount('manage-empleado-documentos', ['empleadoId' => 48, 'family' => 'contratos']);
echo "HTML LENGTH: " . strlen($html) . "\n";
if (str_contains($html, 'title="Eliminar"')) {
    echo "SUCCESS: title=Eliminar IS PRESENT IN RENDERED HTML!\n";
} else {
    echo "WARNING: title=Eliminar IS NOT IN RENDERED HTML!\n";
}
