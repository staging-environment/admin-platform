<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Columns in local 'empleados':\n";
print_r(Schema::getColumnListing('empleados'));

try {
    echo "Columns in 'virtusgesnet' table 'estaciones':\n";
    print_r(Schema::connection('virtusgesnet')->getColumnListing('estaciones'));
} catch (\Exception $e) {
    echo "Error virtusgesnet connection: " . $e->getMessage() . "\n";
}
