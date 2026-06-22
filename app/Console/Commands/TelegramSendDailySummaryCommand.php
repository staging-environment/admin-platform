<?php

namespace App\Console\Commands;

use App\Services\MineturService;
use Illuminate\Console\Command;

class TelegramSendDailySummaryCommand extends Command
{
    protected $signature = 'telegram:send-daily-summary';
    protected $description = 'Send a daily summary of competitor prices to linked users';

    public function handle(MineturService $mineturService): void
    {
        $this->info('Generating and sending daily summary...');
        $mineturService->sendDailySummary();
        $this->info('Done!');
    }
}
