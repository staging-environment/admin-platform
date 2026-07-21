<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. AUDITING DOCUMENT DOWNLOAD & FILE ACCESS LOGS ===\n";
if (Schema::hasTable('page_views')) {
    $fileAccesses = DB::table('page_views')
        ->where('url', 'LIKE', '%ver_archivo%')
        ->orWhere('url', 'LIKE', '%descargar_archivo%')
        ->orWhere('url', 'LIKE', '%ver-archivo%')
        ->orWhere('url', 'LIKE', '%export%')
        ->orderBy('created_at', 'desc')
        ->get();

    echo "Total File Access / Export Requests Recorded: " . $fileAccesses->count() . "\n";
    $usersWhoAccessedFiles = [];
    foreach ($fileAccesses as $fa) {
        $uId = $fa->user_id ?? 'Guest';
        $usersWhoAccessedFiles[$uId] = ($usersWhoAccessedFiles[$uId] ?? 0) + 1;
        echo "  - Date: {$fa->created_at} | User ID: {$uId} | IP: {$fa->ip_address} | URL: {$fa->url}\n";
    }

    echo "\nSummary of File Accesses by User ID:\n";
    foreach ($usersWhoAccessedFiles as $uid => $count) {
        echo "  - User ID $uid: $count file accesses\n";
    }
}

echo "\n=== 2. AUDITING API & BULK DATA EXPORT ENDPOINTS ===\n";
if (Schema::hasTable('page_views')) {
    $apiExports = DB::table('page_views')
        ->where('url', 'LIKE', '%/api/%')
        ->where('url', 'NOT LIKE', '%fuel-markets-data%')
        ->where('url', 'NOT LIKE', '%competitor-data%')
        ->get();
    echo "Other API Requests: " . $apiExports->count() . "\n";
    foreach ($apiExports as $ae) {
        echo "  - Date: {$ae->created_at} | User ID: " . ($ae->user_id ?? 'Guest') . " | URL: {$ae->url}\n";
    }
}

echo "\n=== 3. CHECKING ALL USERS WITH EXPORT OR SENSITIVE PERMISSIONS ===\n";
$users = DB::table('users')->get();
foreach ($users as $u) {
    echo "User ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
}
