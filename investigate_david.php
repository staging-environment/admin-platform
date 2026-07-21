<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== USER DAVID INFO ===\n";
$users = DB::table('users')->where('name', 'LIKE', '%David%')->orWhere('email', 'LIKE', '%david%')->get();
foreach ($users as $u) {
    print_r($u);
}

echo "\n=== EMPLEADO DAVID INFO ===\n";
$empleados = DB::table('empleados')->where('nombre', 'LIKE', '%David%')->orWhere('apellidos', 'LIKE', '%David%')->get();
foreach ($empleados as $e) {
    print_r($e);
}

echo "\n=== RECENT PAGE VIEWS / LOGS FOR DAVID ===\n";
if (Schema::hasTable('page_views')) {
    $userIds = $users->pluck('id')->toArray();
    $views = DB::table('page_views')
        ->whereIn('user_id', $userIds)
        ->orderBy('created_at', 'desc')
        ->limit(100)
        ->get();
    foreach ($views as $v) {
        print_r($v);
    }
}

echo "\n=== SESSIONS FOR DAVID ===\n";
if (Schema::hasTable('sessions')) {
    $userIds = $users->pluck('id')->toArray();
    $sessions = DB::table('sessions')
        ->whereIn('user_id', $userIds)
        ->get();
    foreach ($sessions as $s) {
        print_r($s);
    }
}
