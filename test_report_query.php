<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sql = "
SELECT 
    DATE(v.FechaYHora) as fecha,
    TIME(v.FechaYHora) as hora,
    df.Importe as importe,
    p.Descripcion as tipo_combustible,
    df.Cantidad as litros,
    e.CodigoDeMaquinaExpendedora as surtidor,
    v.Numero as ticket_numero
FROM detalledefacturasyticketsdeventa df
JOIN facturasyticketsdeventa v ON v.CodigoDeEmpresaPropia = df.CodigoDeEmpresaPropia
    AND v.Serie = df.Serie
    AND v.Numero = df.Numero
JOIN productos p ON p.Codigo = df.CodigoDeProducto
LEFT JOIN expediciones e ON e.id = df.IdExpedicion
WHERE v.CodigoDeEstacion = 1
  AND DATE(v.FechaYHora) = '2026-06-03'
ORDER BY v.FechaYHora ASC
";

$results = DB::connection('virtusgesnet')->select($sql);
echo "Total rows: " . count($results) . "\n";
echo "First 5 rows:\n";
print_r(array_slice($results, 0, 5));
