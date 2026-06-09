<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$service = app(\App\Services\ReportService::class);
$res = $service->getEvolucionMensual(4, 2026, 6, 2026, ["3"]);
echo json_encode($res, JSON_PRETTY_PRINT);
