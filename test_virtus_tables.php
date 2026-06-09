<?php
try {
    $dsn = "mysql:host=ddev-utrecar3-db;port=3306;dbname=virtusgesnet;charset=utf8mb4";
    $pdo = new PDO($dsn, 'db', 'db', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES IN VIRTUSGESNET:\n";
    foreach ($tables as $t) {
        if (str_contains(strtolower($t), 'lav') || str_contains(strtolower($t), 'vent') || str_contains(strtolower($t), 'prod')) {
            echo "- {$t}\n";
        }
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
