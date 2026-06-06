<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$stations = \App\Models\Gasolinera::with('contenido')->get();
echo "Stations count: " . $stations->count() . PHP_EOL;
foreach ($stations as $s) {
    echo "ID: " . $s->Codigo . " | Name: " . $s->Nombre . " | Content: " . ($s->contenido ? 'Yes' : 'No') . PHP_EOL;
}
