<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. SEARCHING ALL DATABASE TABLES FOR 'DAVID' ===\n";
$tables = DB::select("SHOW TABLES");
foreach ($tables as $t) {
    $tableName = array_values((array)$t)[0];
    try {
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
        $query = DB::table($tableName);
        $hasStringCols = false;
        foreach ($columns as $col) {
            $type = DB::getSchemaBuilder()->getColumnType($tableName, $col);
            if (in_array($type, ['string', 'text', 'varchar', 'char'])) {
                if (!$hasStringCols) {
                    $query->where($col, 'LIKE', '%David%');
                    $hasStringCols = true;
                } else {
                    $query->orWhere($col, 'LIKE', '%David%');
                }
            }
        }
        if ($hasStringCols) {
            $results = $query->limit(10)->get();
            if ($results->count() > 0) {
                echo "Table [$tableName] matches (" . $results->count() . " rows):\n";
                foreach ($results as $r) {
                    print_r($r);
                }
            }
        }
    } catch (\Throwable $e) {
        // continue
    }
}

echo "\n=== 2. LARAVEL LOG FILES SEARCH FOR 'DAVID' ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $davidLines = array_filter($lines, fn($line) => stripos($line, 'david') !== false);
    echo "Found " . count($davidLines) . " matching log lines in laravel.log:\n";
    foreach (array_slice($davidLines, -20) as $dl) {
        echo $dl;
    }
} else {
    echo "laravel.log does not exist.\n";
}

echo "\n=== 3. CHECKING SYSTEM & NGINX LOGS FOR DAVID / VPN IPs ===\n";
$systemLogs = [
    '/var/log/auth.log',
    '/var/log/nginx/access.log',
    '/var/log/nginx/error.log',
    '/var/log/syslog'
];
foreach ($systemLogs as $sysLog) {
    if (file_exists($sysLog) && is_readable($sysLog)) {
        $content = shell_exec("grep -i 'david' " . escapeshellarg($sysLog) . " | tail -n 20");
        if ($content) {
            echo "Matches in $sysLog:\n$content\n";
        }
    }
}

echo "\n=== 4. CHECKING RECENT PAGE VIEWS / SESSIONS BY IP & USER ===\n";
if (Schema::hasTable('page_views')) {
    $recentViews = DB::table('page_views')->orderBy('created_at', 'desc')->limit(30)->get();
    echo "Recent Page Views (Top 30):\n";
    foreach ($recentViews as $pv) {
        echo "Time: {$pv->created_at} | User ID: " . ($pv->user_id ?? 'Guest') . " | IP: " . ($pv->ip_address ?? 'N/A') . " | URL: " . ($pv->url ?? 'N/A') . "\n";
    }
}
