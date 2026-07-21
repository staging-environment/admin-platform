<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = [];
$rows[] = ["ID", "Fecha y Hora", "IP Hash", "Usuario ID", "URL Solicitada", "Tipo de Peticion / Sospecha"];

if (Schema::hasTable('page_views')) {
    $views = DB::table('page_views')->orderBy('created_at', 'desc')->get();
    foreach ($views as $pv) {
        $url = $pv->url ?? '';
        $isSuspicious = false;
        $reason = "Navegacion Normal";

        if (str_contains($url, 'phpinfo') || str_contains($url, '.env') || str_contains($url, 'config') || str_contains($url, 'admin/login') || str_contains($url, 'wp-login') || str_contains($url, 'eval-')) {
            $isSuspicious = true;
            $reason = "Escaneo de Vulnerabilidad / Reconocimiento Parametrizado";
        } elseif (!$pv->user_id && str_contains($url, 'login')) {
            $reason = "Intento de Acceso a Login (Anonimo)";
        } elseif (!$pv->user_id) {
            $reason = "Visita Anonima / Sin Autenticar";
        }

        if ($isSuspicious || !$pv->user_id) {
            $rows[] = [
                $pv->id ?? 'N/A',
                $pv->created_at ?? 'N/A',
                $pv->ip_address ?? 'N/A',
                $pv->user_id ?? 'Invitado / Anonimo',
                $url,
                $reason
            ];
        }
    }
}

$fp = fopen('public/informe_movimientos_sospechosos.csv', 'w');
foreach ($rows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);

echo "EXPORTS COMPLETE. TOTAL SUSPICIOUS ROWS: " . (count($rows) - 1) . "\n";
