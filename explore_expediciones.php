<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = DB::connection('virtusgesnet')
    ->select("SHOW TABLES LIKE '%exped%'");
print_r($tables);

$hasExpediciones = false;
foreach ($tables as $t) {
    $tArray = (array)$t;
    $tableName = array_values($tArray)[0];
    if ($tableName === 'expediciones') {
        $hasExpediciones = true;
    }
}

if ($hasExpediciones) {
    $columns = DB::connection('virtusgesnet')->select("SHOW COLUMNS FROM expediciones");
    print_r($columns);
} else {
    echo "No expediciones table found.\n";
}
