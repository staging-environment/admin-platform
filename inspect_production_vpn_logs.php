<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 1. LISTING ALL LOG FILES IN /var/log/ ===\n";
$varLogs = shell_exec("ls -la /var/log 2>&1");
echo $varLogs . "\n";

echo "=== 2. SEARCHING /var/log/ FOR 'VPN', 'WIREGUARD', 'OPENVPN', 'SSH' ===\n";
$vpnLogs = shell_exec("find /var/log -type f -exec grep -iE 'vpn|david|wireguard|openvpn' {} + 2>/dev/null | tail -n 40");
echo ($vpnLogs ?: "No direct matches found in /var/log files.\n") . "\n";

echo "=== 3. JOURNALCTL SYSTEMD LOGS FOR VPN SERVICES ===\n";
$journalLogs = shell_exec("journalctl -n 50 --no-pager 2>&1 | grep -iE 'vpn|openvpn|wireguard|auth|login|david'");
echo ($journalLogs ?: "No matches in recent journalctl logs.\n") . "\n";

echo "=== 4. SYSTEM NETWORK CONNECTIONS & ESTABLISHED SOCKETS ===\n";
$netstat = shell_exec("ss -tulpn 2>&1");
echo $netstat . "\n";

echo "=== 5. NGINX ACCESS LOGS - RECENT IP REQUESTS ===\n";
$nginxLogs = shell_exec("tail -n 30 /var/log/nginx/access.log 2>&1");
echo ($nginxLogs ?: "No nginx access log available.\n") . "\n";
