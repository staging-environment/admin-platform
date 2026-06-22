<?php

namespace App\Console\Commands;

use App\Models\Empleado;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramTestSendCommand extends Command
{
    protected $signature = 'telegram:test-send';
    protected $description = 'Send a test competitor price alert to all linked employees';

    public function handle(TelegramService $telegramService): void
    {
        $users = \App\Models\User::whereNotNull('telegram_chat_id')->get();

        if ($users->isEmpty()) {
            $this->error('No users have been linked to a Telegram chat ID yet.');
            $this->info('Please start the bot @utrecar_alertas_bot and share your contact first.');
            return;
        }

        $text = "🔔 <b>[PRUEBA] Alerta de cambio de precios de la competencia</b>\n";
        $text .= "Localidad: <b>Utrera (Prueba de Envío)</b>\n\n";
        $text .= "⛽ <b>DIÉSEL:</b>\n";
        $text .= "  1. Gasolinera Competencia A: <b>1.389 €</b>\n";
        $text .= "  2. Gasolinera Competencia B: <b>1.395 €</b>\n";
        $text .= "\n⛽ <b>GASOLINA 95:</b>\n";
        $text .= "  1. Gasolinera Competencia A: <b>1.549 €</b>\n";
        $text .= "  2. Gasolinera Competencia B: <b>1.555 €</b>\n";

        $this->info('Sending test message to ' . $users->count() . ' users...');

        foreach ($users as $user) {
            $this->info("Sending to {$user->name} (Chat ID: {$user->telegram_chat_id})...");
            $success = $telegramService->sendMessage($user->telegram_chat_id, $text);
            if ($success) {
                $this->info('Success!');
            } else {
                $this->error('Failed.');
            }
        }
    }
}
