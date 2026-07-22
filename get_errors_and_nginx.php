<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LARAVEL LOG LAST ERROR ===\n";
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $lines = file($logPath);
    $errorLines = [];
    foreach ($lines as $i => $line) {
        if (str_contains($line, 'production.ERROR') || str_contains($line, 'production.CRITICAL')) {
            $errorLines[] = [$i, $line];
        }
    }
    
    $lastErrors = array_slice($errorLines, -3);
    foreach ($lastErrors as $err) {
        $idx = $err[0];
        echo "Line $idx: " . $err[1];
        // print subsequent 15 lines for context
        for ($j = 1; $j <= 15; $j++) {
            if (isset($lines[$idx + $j])) {
                echo "  " . $lines[$idx + $j];
            }
        }
        echo "--------------------------------------\n";
    }
} else {
    echo "No laravel.log found.\n";
}

echo "=== NGINX LOG LAST 20 LINES ===\n";
if (file_exists('/var/log/nginx/error.log')) {
    echo shell_exec('tail -n 20 /var/log/nginx/error.log');
} else {
    echo "No /var/log/nginx/error.log found\n";
}
