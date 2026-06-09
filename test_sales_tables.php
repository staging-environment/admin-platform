<?php
try {
    $dsn = "mysql:host=ddev-utrecar3-db;port=3306;dbname=virtusgesnet;charset=utf8mb4";
    $pdo = new PDO($dsn, 'db', 'db', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 1. Check sales in detalledeventasencurso / ventasencurso
    $stmt1 = $pdo->query("SELECT count(*) as qty, sum(Importe) as total FROM detalledeventasencurso dv 
                          JOIN ventasencurso v ON v.Id = dv.IdDeVentaEnCurso
                          JOIN productos p ON p.Codigo = dv.CodigoDeProducto
                          WHERE p.CodigoDeGrupo LIKE '4%' AND v.FechaYHora >= '2026-01-01'");
    $res1 = $stmt1->fetch();
    echo "ventasencurso (2026) Qty: {$res1['qty']}, Total: {$res1['total']} €\n";

    // 2. Check sales in detalledefacturasyticketsdeventa / facturasyticketsdeventa
    $stmt2 = $pdo->query("SELECT count(*) as qty, sum(df.Importe) as total FROM detalledefacturasyticketsdeventa df 
                          JOIN facturasyticketsdeventa f ON f.CodigoDeEmpresaPropia = df.CodigoDeEmpresaPropia 
                            AND f.Serie = df.Serie 
                            AND f.Numero = df.Numero
                          JOIN productos p ON p.Codigo = df.CodigoDeProducto
                          WHERE p.CodigoDeGrupo LIKE '4%' AND f.FechaYHora >= '2026-01-01'");
    $res2 = $stmt2->fetch();
    echo "facturasyticketsdeventa (2026) Qty: {$res2['qty']}, Total: {$res2['total']} €\n";

    // Let's also check general sales in facturasyticketsdeventa in 2026
    $stmt3 = $pdo->query("SELECT count(*) FROM facturasyticketsdeventa WHERE FechaYHora >= '2026-01-01'");
    $res3 = $stmt3->fetchColumn();
    echo "Total sales in facturasyticketsdeventa in 2026: {$res3}\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
