<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DAVID VPN CONNECTION DETAILS ===\n";
echo "Profile Name: David - Portatil\n";
echo "VPN Internal IP: 10.8.0.4\n";
echo "External Endpoint IP: 88.31.162.132:49670\n";
echo "Last Handshake: 8 hours 34 mins ago (approx 12:27 PM today)\n";
echo "Data Received from David (Upload): 609.06 MiB\n";
echo "Data Sent to David (Download): 12.12 GiB\n\n";

echo "=== SEARCHING DATABASE PAGE VIEWS FOR DAVID'S VPN IP / ENDPOINT ===\n";
if (Schema::hasTable('page_views')) {
    $ip1 = hash('sha256', '10.8.0.4');
    $ip2 = hash('sha256', '88.31.162.132');
    
    $views = DB::table('page_views')
        ->where('ip_address', '10.8.0.4')
        ->orWhere('ip_address', '88.31.162.132')
        ->orWhere('ip_address', $ip1)
        ->orWhere('ip_address', $ip2)
        ->get();
    
    echo "Found " . $views->count() . " page_views matching David's IP / Hash:\n";
    foreach ($views as $v) {
        echo "  - Date: {$v->created_at} | User: " . ($v->user_id ?? 'Guest') . " | URL: {$v->url}\n";
    }
}
