<?php

namespace App\Jobs;

use App\Services\MineturService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshMineturJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    /**
     * Actualiza la caché de precios de gasolineras competidoras (API MITECO).
     */
    public function handle(MineturService $service): void
    {
        $service->refreshAll();
    }
}
