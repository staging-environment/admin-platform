<?php
try {
    $dsn = "mysql:host=ddev-utrecar3-db;port=3306;dbname=virtusgesnet;charset=utf8mb4";
    $pdo = new PDO($dsn, 'db', 'db', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Query all distinct group codes of products sold in 2026
    $stmt = $pdo->query("SELECT p.CodigoDeGrupo, g.Nombre, COUNT(*) as qty, SUM(dv.Importe) as total 
                         FROM detalledeventasencurso dv
                         JOIN ventasencurso v ON v.Id = dv.IdDeVentaEnCurso
                         JOIN productos p ON p.Codigo = dv.CodigoDeProducto
                         LEFT JOIN gruposdeproductos g ON g.Codigo = p.CodigoDeGrupo
                         WHERE v.FechaYHora >= '2026-01-01'
                         GROUP BY p.CodigoDeGrupo, g.Nombre
                         ORDER BY total DESC");
    $groups = $stmt->fetchAll();
    echo "GRUPOS VENDIDOS EN 2026:\n";
    foreach ($groups as $g) {
        echo "- Group: {$g['CodigoDeGrupo']}, Name: {$g['Nombre']}, Qty: {$g['qty']}, Total: {$g['total']} €\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
