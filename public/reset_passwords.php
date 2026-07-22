<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
foreach($users as $u) {
    $u->password = bcrypt('12345678');
    $u->save();
    echo "Reset password for: {$u->email}\n";
}
echo "All passwords reset successfully to '12345678'\n";
