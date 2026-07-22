<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $lines = file($logPath);
    $errors = array_filter($lines, fn($l) => str_contains($l, 'production.ERROR'));
    echo "Total ERROR lines: " . count($errors) . "\n";
    $recentErrors = array_slice($errors, -5);
    foreach ($recentErrors as $e) {
        echo $e;
    }
}
