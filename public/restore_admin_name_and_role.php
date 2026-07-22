<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Decouple Employee ID 55 by changing its email to a separate test email
$employee = \App\Models\Empleado::find(55);
if ($employee) {
    $employee->email = 'prueba1@utrecar.com';
    $employee->save();
    echo "Employee ID 55 email updated to prueba1@utrecar.com\n";
}

// 2. Restore Admin user details (User ID 1)
$adminUser = \App\Models\User::find(1);
if ($adminUser) {
    $adminUser->name = 'jarodriguezbonilla';
    $adminUser->email = 'jarodriguezbonilla@gmail.com';
    // Remove Empleado role from admin so he is clean
    if ($adminUser->hasRole('Empleado')) {
        $adminUser->removeRole('Empleado');
        echo "Removed 'Empleado' role from admin user\n";
    }
    $adminUser->save();
    echo "Admin user ID 1 restored to name: jarodriguezbonilla, email: jarodriguezbonilla@gmail.com\n";
}

echo "Decoupling and Admin recovery complete!\n";
