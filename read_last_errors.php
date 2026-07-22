<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $content = file_get_contents($logPath);
    // Let's get the last 5000 characters of the log file to see the most recent stack trace
    echo substr($content, -5000);
} else {
    echo "No laravel.log found.\n";
}
