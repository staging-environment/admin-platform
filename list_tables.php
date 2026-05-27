<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = app(App\Services\VirtusgesnetService::class);
$tables = ["movimientosdealmacen"];
foreach($tables as $t) {
    try {
        echo strtoupper($t) . ":\n";
        print_r(array_map(function($c) { return $c->Field; }, $s->getTableSchema($t)));
    } catch (Exception $e) {
        echo "Table error\n";
    }
}
echo "\n";
