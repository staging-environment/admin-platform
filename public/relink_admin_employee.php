<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Relink Employee ID 55 back to jarodriguezbonilla@gmail.com
$employee = \App\Models\Empleado::find(55);
if ($employee) {
    $employee->email = 'jarodriguezbonilla@gmail.com';
    $employee->save();
    echo "Employee ID 55 email updated back to jarodriguezbonilla@gmail.com\n";
}

// 2. Double check and restore Admin user ID 1 name
$adminUser = \App\Models\User::find(1);
if ($adminUser) {
    $adminUser->name = 'jarodriguezbonilla';
    $adminUser->email = 'jarodriguezbonilla@gmail.com';
    $adminUser->save();
    echo "Admin user ID 1 name verified as jarodriguezbonilla\n";
}

echo "Relinking complete!\n";
