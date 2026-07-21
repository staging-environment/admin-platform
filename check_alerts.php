<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. CHECKING NOTIFICATIONS TABLE ===\n";
if (Schema::hasTable('notifications')) {
    $notifs = DB::table('notifications')->orderBy('created_at', 'desc')->limit(20)->get();
    echo "Found " . $notifs->count() . " notifications:\n";
    foreach ($notifs as $n) {
        print_r($n);
    }
} else {
    echo "No 'notifications' table found.\n";
}

echo "\n=== 2. CHECKING FAILED JOBS ===\n";
if (Schema::hasTable('failed_jobs')) {
    $failed = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->limit(20)->get();
    echo "Found " . $failed->count() . " failed jobs:\n";
    foreach ($failed as $fj) {
        echo "ID: {$fj->id} | Queue: {$fj->queue} | Exception: " . substr($fj->exception, 0, 150) . "\n";
    }
} else {
    echo "No 'failed_jobs' table found.\n";
}

echo "\n=== 3. CHECKING AUDIT / LOG TABLES ===\n";
$auditTables = ['audits', 'activity_log', 'alertas', 'logs'];
foreach ($auditTables as $at) {
    if (Schema::hasTable($at)) {
        $recs = DB::table($at)->orderBy('created_at', 'desc')->limit(10)->get();
        echo "Found " . $recs->count() . " records in '$at':\n";
        foreach ($recs as $r) {
            print_r($r);
        }
    }
}
