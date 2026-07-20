<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$user = DB::table('users')->where('email', 'alfonsogaro@gmail.com')->first();
if ($user) {
    echo "Found user via DB query directly!\n";
    echo "Name: " . $user->name . "\n";
    echo "Telegram Chat ID: " . ($user->telegram_chat_id ?? 'NULL') . "\n";
} else {
    echo "User alfonsogaro@gmail.com not found in the users table at all.\n";
    
    echo "\nAll emails in users table:\n";
    $emails = DB::table('users')->pluck('email');
    foreach ($emails as $email) {
        echo "- " . $email . "\n";
    }
}
