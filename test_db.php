<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Illuminate\Support\Facades\DB::connection('virtusgesnet')->select("SHOW COLUMNS FROM facturasyticketsdeventa");
print_r(array_column($columns, 'Field'));
