<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll updates from Telegram Bot API (useful for local development)';

    public function handle(TelegramService $telegramService): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN is not configured in .env file.');
            return;
        }

        $this->info('Starting Telegram Bot Polling... Press Ctrl+C to stop.');

        $offset = 0;

        while (true) {
            try {
                $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 5,
                ]);

                if ($response->successful()) {
                    $updates = $response->json()['result'] ?? [];
                    foreach ($updates as $update) {
                        $updateId = $update['update_id'];
                        $offset = $updateId + 1;

                        $this->info("Processing update ID: {$updateId}");
                        $telegramService->handleUpdate($update);
                    }
                } else {
                    $this->error('Error calling getUpdates: ' . $response->body());
                    sleep(2);
                }
            } catch (\Throwable $e) {
                $this->error('Exception: ' . $e->getMessage());
                sleep(2);
            }
        }
    }
}
