<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::find(1);
if ($user) {
    echo "ID: {$user->id} | Email: {$user->email} | Name: {$user->name}\n";
} else {
    echo "User 1 not found.\n";
}
