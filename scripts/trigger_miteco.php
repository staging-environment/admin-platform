<?php
// Script to manually trigger the MITECO upload and populate the cache
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MitecoService;
use Illuminate\Support\Facades\Cache;

echo "Triggering MitecoService::uploadPrices()...\n";

try {
    $service = app(MitecoService::class);
    $service->uploadPrices();
    $status = Cache::get('miteco_last_update_status');
    echo "Cache status after: " . json_encode($status) . "\n";
    if ($status && isset($status['prices'])) {
        echo "Prices found: " . count($status['prices']) . " stations\n";
    } else {
        echo "No prices in cache\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
