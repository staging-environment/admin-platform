<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cached = Cache::get('competitors_prices_30');
echo "Cached 30km: " . json_encode($cached, JSON_PRETTY_PRINT) . PHP_EOL;
