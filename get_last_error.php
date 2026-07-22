<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $lines = file($logPath);
    $last100 = array_slice($lines, -150);
    echo implode("", $last100);
} else {
    echo "No laravel.log found.\n";
}
