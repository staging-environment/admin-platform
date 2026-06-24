<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = \App\Models\User::where('email', 'jarodriguezbonilla@gmail.com')->first();
if ($u) {
    echo "User: " . $u->email . "\n";
    echo "Roles: " . $u->roles->pluck('name')->implode(', ') . "\n";
    echo "Permissions: " . $u->getAllPermissions()->pluck('name')->implode(', ') . "\n";
} else {
    echo "User not found\n";
}
