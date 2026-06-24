<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::connection('virtusgesnet')->select("SHOW COLUMNS FROM productos");
for ($i = 0; $i < min(5, count($columns)); $i++) {
    print_r($columns[$i]);
}
