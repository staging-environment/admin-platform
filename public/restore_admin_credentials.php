<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Restore User ID 1 (Admin)
$adminUser = \App\Models\User::find(1);
if ($adminUser) {
    $adminUser->email = 'jarodriguezbonilla@gmail.com';
    $adminUser->name = 'jarodriguezbonilla';
    $adminUser->password = bcrypt('Sevillano15!');
    $adminUser->save();
    echo "User ID 1 restored to jarodriguezbonilla@gmail.com / Sevillano15!\n";
} else {
    // If not found, create it
    $adminUser = \App\Models\User::create([
        'id' => 1,
        'name' => 'jarodriguezbonilla',
        'email' => 'jarodriguezbonilla@gmail.com',
        'password' => bcrypt('Sevillano15!'),
    ]);
    echo "User ID 1 created as jarodriguezbonilla@gmail.com / Sevillano15!\n";
}

// Ensure it has the Admin role
if (!$adminUser->hasRole('Admin')) {
    $adminUser->assignRole('Admin');
}

// 2. Sync corresponding Employee record
$employee = \App\Models\Empleado::where('id', 55)->first();
if ($employee) {
    $employee->email = 'jarodriguezbonilla@gmail.com';
    $employee->save();
    echo "Employee ID 55 email updated to jarodriguezbonilla@gmail.com\n";
} else {
    $employeeByEmail = \App\Models\Empleado::where('email', 'robe@gmail.com')->first();
    if ($employeeByEmail) {
        $employeeByEmail->email = 'jarodriguezbonilla@gmail.com';
        $employeeByEmail->save();
        echo "Employee by robe@gmail.com email updated to jarodriguezbonilla@gmail.com\n";
    }
}

// 3. If there is any duplicate user with robe@gmail.com created by accident, delete it
$robeUser = \App\Models\User::where('email', 'robe@gmail.com')->where('id', '!=', 1)->first();
if ($robeUser) {
    $robeUser->delete();
    echo "Deleted duplicate robe@gmail.com user\n";
}

echo "Admin credentials restore complete!\n";
