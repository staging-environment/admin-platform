<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. SEARCHING FOR ACTIVE VPN SERVICES & INTERFACES ===\n";
$ifconfig = shell_exec("ip a | grep -E 'tun|wg|ppp|tap'");
echo "VPN Interfaces:\n" . ($ifconfig ?: "No tun/wg/ppp interfaces found.\n");

$vpnServices = shell_exec("systemctl list-units --type=service | grep -iE 'vpn|wireguard|openvpn|tailscale'");
echo "\nVPN Services running:\n" . ($vpnServices ?: "No active systemd VPN services found.\n");

echo "\n=== 2. SEARCHING FOR VPN CONFLICTS / UNUSUAL IP CONNECTIONS IN DB ===\n";
if (Schema::hasTable('page_views')) {
    $vpnIps = DB::table('page_views')
        ->select('ip_address', 'user_id', DB::raw('COUNT(*) as total_requests'), DB::raw('MAX(created_at) as last_seen'))
        ->groupBy('ip_address', 'user_id')
        ->orderBy('last_seen', 'desc')
        ->get();
    echo "Active IP / Session summary in platform:\n";
    foreach ($vpnIps as $ip) {
        echo "IP Hash: {$ip->ip_address} | User ID: " . ($ip->user_id ?? 'Guest') . " | Requests: {$ip->total_requests} | Last Seen: {$ip->last_seen}\n";
    }
}

echo "\n=== 3. CHECKING DATABASE MODIFICATIONS & SALES RECORDS FOR ANOMALIES ===\n";
$tablesToCheck = ['empleado_contratos', 'empleados', 'contacto_mensajes', 'gasolinera_contenidos', 'home_configs'];
foreach ($tablesToCheck as $tbl) {
    if (Schema::hasTable($tbl)) {
        $recent = DB::table($tbl)->orderBy('updated_at', 'desc')->limit(5)->get();
        echo "Recent changes in [$tbl]:\n";
        foreach ($recent as $r) {
            $updatedAt = $r->updated_at ?? ($r->created_at ?? 'N/A');
            echo "  - ID: {$r->id} | Updated: {$updatedAt}\n";
        }
    }
}

echo "\n=== 4. SYSTEM AUTH & SSH VPN ACCESS LOGS FOR 'DAVID' ===\n";
$authLogs = shell_exec("grep -iE 'david|vpn|accepted' /var/log/auth.log 2>/dev/null | tail -n 25");
echo "Auth Log Entries:\n" . ($authLogs ?: "No recent ssh/auth matches.\n");
