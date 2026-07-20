<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (Spatie\Permission\Models\Role::all() as $role) {
    echo "Role: " . $role->name . PHP_EOL;
    echo "Permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . PHP_EOL;
    echo "----------------------------------------" . PHP_EOL;
}
