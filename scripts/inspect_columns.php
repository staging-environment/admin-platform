<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Raw DESCRIBE estaciones:\n";
    $results = DB::connection('virtusgesnet')->select("DESCRIBE estaciones");
    foreach ($results as $row) {
        $row = (array)$row;
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (\Exception $e) {
    echo "Error describing: " . $e->getMessage() . "\n";
}
