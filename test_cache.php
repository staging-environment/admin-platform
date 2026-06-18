<?php

use Illuminate\Support\Facades\Cache;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$status = Cache::get('miteco_last_update_status');
echo "STATUS:\n";
print_r($status);
echo "\n";
