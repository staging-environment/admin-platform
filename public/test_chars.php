<?php
include __DIR__ . '/../vendor/autoload.php';
$app = include __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$e48 = App\Models\Empleado::find(48);
$e52 = App\Models\Empleado::find(52);

echo "ID 48 (José Antonio): \n";
echo "Apellidos: " . $e48->apellidos . "\n";
echo "Hex: " . bin2hex($e48->apellidos) . "\n\n";

echo "ID 52 (Cora Tian): \n";
echo "Apellidos: " . $e52->apellidos . "\n";
echo "Hex: " . bin2hex($e52->apellidos) . "\n";
