<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$telegram = app(App\Services\TelegramService::class);
$users = App\Models\User::whereNotNull('telegram_chat_id')->get();

$msg = "⚡ <b>Prueba de Sistema</b>\nHola, esta es una notificación de prueba indicando que estás testeando si funciona correctamente.";

foreach ($users as $user) {
    echo "Enviando a " . $user->name . "... ";
    $res = $telegram->sendMessage($user->telegram_chat_id, $msg);
    echo ($res ? "OK" : "ERROR") . PHP_EOL;
}
