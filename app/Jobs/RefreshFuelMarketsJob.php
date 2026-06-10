<?php

namespace App\Jobs;

use App\Services\FuelMarketsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshFuelMarketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 30;

    /**
     * Actualiza la caché de precios de mercados energéticos (Yahoo Finance).
     */
    public function handle(FuelMarketsService $service): void
    {
        $service->refresh();
    }
}
