<?php

use App\Services\MitecoService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "--- CONFIG DUMP ---\n";
    print_r(config('services.miteco'));
    
    echo "--- TESTING MITECO MULTI-STATION UPLOAD ---\n";
    $service = new MitecoService();
    $results = $service->uploadPrices();
    
    echo "Results:\n";
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
