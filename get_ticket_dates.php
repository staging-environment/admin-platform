<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$res = DB::connection('virtusgesnet')
    ->table('facturasyticketsdeventa')
    ->where('CodigoDeEstacion', 1)
    ->selectRaw('MIN(FechaYHora) as min_date, MAX(FechaYHora) as max_date, COUNT(*) as count')
    ->first();
print_r($res);

$res2 = DB::connection('virtusgesnet')
    ->table('facturasyticketsdeventa')
    ->where('CodigoDeEstacion', 1)
    ->whereRaw('MONTH(FechaYHora) = 6 AND DAY(FechaYHora) = 3')
    ->selectRaw('YEAR(FechaYHora) as year, COUNT(*) as count')
    ->groupByRaw('YEAR(FechaYHora)')
    ->get();
print_r($res2);
