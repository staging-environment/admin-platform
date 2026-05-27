<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = \Illuminate\Support\Facades\DB::table('model_has_roles')->get();
echo "model_has_roles:\n";
print_r($roles->toArray());

$roles_table = \Illuminate\Support\Facades\DB::table('roles')->get();
echo "roles:\n";
print_r($roles_table->toArray());
