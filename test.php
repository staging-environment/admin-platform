<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Refreshing localities Minetur data...\n";
    $service = app(App\Services\MineturService::class);
    $service->refreshAll();
    echo "Refreshed! Checking cache for 'sevilla':\n";
    print_r(Illuminate\Support\Facades\Cache::get('minetur_sevilla'));
    echo "\nChecking cache for 'utrera':\n";
    print_r(Illuminate\Support\Facades\Cache::get('minetur_utrera'));
    echo "\nChecking cache for 'el_cuervo':\n";
    print_r(Illuminate\Support\Facades\Cache::get('minetur_el_cuervo'));
    echo "\nChecking cache for 'lebrija':\n";
    print_r(Illuminate\Support\Facades\Cache::get('minetur_lebrija'));
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
