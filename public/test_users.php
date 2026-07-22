<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(\App\Models\User::all() as $u) {
    echo "ID: {$u->id} | Email: {$u->email} | Name: {$u->name} | Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
}
