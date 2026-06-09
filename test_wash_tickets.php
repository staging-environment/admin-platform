<?php
try {
    $dsn = "mysql:host=ddev-utrecar3-db;port=3306;dbname=virtusgesnet;charset=utf8mb4";
    $pdo = new PDO($dsn, 'db', 'db', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Check count and date range in ticketsdelavado
    $stmt2 = $pdo->query("SELECT MIN(FechaYHoraEmision) as min_date, MAX(FechaYHoraEmision) as max_date, COUNT(*) as qty FROM ticketsdelavado");
    $range = $stmt2->fetch();
    echo "\nTICKETSDELAVADO RANGE:\n";
    echo "- Min: {$range['min_date']}\n";
    echo "- Max: {$range['max_date']}\n";
    echo "- Count: {$range['qty']}\n";

    // Show top 10 ticketsdelavado in 2026 if any
    $stmt3 = $pdo->query("SELECT * FROM ticketsdelavado WHERE FechaYHoraEmision >= '2026-01-01' LIMIT 10");
    $tickets = $stmt3->fetchAll();
    echo "\nEXAMPLES IN 2026:\n";
    foreach ($tickets as $t) {
        echo "- ID: {$t['Id']}, Date: {$t['FechaYHoraEmision']}, Prod: {$t['CodigoProducto']}, Cantidad: {$t['Cantidad']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
