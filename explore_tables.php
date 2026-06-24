<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = DB::connection('virtusgesnet')
    ->select('SHOW TABLES');
print_r($tables);
