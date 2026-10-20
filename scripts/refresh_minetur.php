<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Refreshing Minetur prices...\n";
$service = app(App\Services\MineturService::class);
$service->refreshAll();

$data = $service->getLocalityData('sevilla');
echo "New top 5 for Sevilla Diesel:\n";
foreach ($data['diesel'] as $i => $s) {
    echo ($i + 1) . ". " . $s['name'] . " - " . $s['address'] . " - " . $s['price'] . " € (ID: " . $s['id'] . ")\n";
}
