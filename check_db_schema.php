<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- START DATABASE SCHEMA CHECK ---\n";

try {
    // 1. Obtener las estaciones desde la base de datos central
    $estaciones = DB::connection('virtusgesnet')->table('estaciones')->get();
    echo "Found " . count($estaciones) . " stations in central database.\n\n";

    // Mapeo manual de las estaciones y sus IPs conocidas de la VPN
    $vpn_ips = [
        '10.8.0.1'  => 'Central / Host',
        '10.8.0.2'  => 'Peer 2',
        '10.8.0.3'  => 'Peer 3',
        '10.8.0.4'  => 'Peer 4',
        '10.8.0.5'  => 'Peer 5',
        '10.8.0.6'  => 'Peer 6 (Ronda Norte / TPV?)',
        '10.8.0.7'  => 'Peer 7',
        '10.8.0.8'  => 'Peer 8',
        '10.8.0.9'  => 'Peer 9',
        '10.8.0.10' => 'Peer 10',
        '10.8.0.11' => 'Peer 11 (Vistalegre / TPV?)',
        '10.8.0.12' => 'Peer 12',
        '10.8.0.14' => 'Peer 14',
        '10.8.0.16' => 'Peer 16',
        '10.8.0.17' => 'Peer 17',
    ];

    foreach ($vpn_ips as $ip => $name) {
        echo "Testing connection to {$name} ({$ip})...\n";
        
        // Creamos una configuración de conexión dinámica sobre la marcha
        $port = ($ip === '10.8.0.1') ? '33061' : '3306';
        config(['database.connections.temp_check' => [
            'driver' => 'mysql',
            'host' => $ip,
            'port' => $port,
            'database' => 'virtusgesnet',
            'username' => 'root',
            'password' => '.root.',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => false,
            'options' => [
                PDO::ATTR_TIMEOUT => 2, // Timeout rápido de 2 segundos
            ]
        ]]);

        try {
            // Intentar conectar y describir la tabla
            $results = DB::connection('temp_check')->select("SHOW COLUMNS FROM seriesdefacturasyticketsdeventa LIKE 'TipoDeDocumento'");
            
            if (count($results) > 0) {
                echo "  ✅ Connected! Table 'seriesdefacturasyticketsdeventa' HAS column 'TipoDeDocumento'.\n";
            } else {
                echo "  ⚠️ Connected! Table 'seriesdefacturasyticketsdeventa' exists but is MISSING column 'TipoDeDocumento'!\n";
                // Si la columna falta, la añadimos:
                echo "  → Adding column 'TipoDeDocumento'...\n";
                DB::connection('temp_check')->statement("ALTER TABLE seriesdefacturasyticketsdeventa ADD COLUMN TipoDeDocumento enum('Facturas','Tickets') NOT NULL DEFAULT 'Facturas' AFTER Codigo");
                echo "  ✅ Column added successfully!\n";
            }
        } catch (\Exception $e) {
            // Si la conexión falla o la tabla no existe
            if (str_contains($e->getMessage(), "doesn't exist")) {
                echo "  ❌ Connected, but table 'seriesdefacturasyticketsdeventa' does not exist in this database.\n";
            } else {
                echo "  offline ({$e->getMessage()})\n";
            }
        }
        DB::purge('temp_check');
        echo "\n";
    }

} catch (\Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage() . "\n";
}

echo "--- END DATABASE SCHEMA CHECK ---\n";
