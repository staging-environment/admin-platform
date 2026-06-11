<?php

namespace App\Jobs;

use App\Services\MitecoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadPricesToMitecoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    /**
     * Envía los precios actuales de nuestras 4 gasolineras a MITECO.
     */
    public function handle(MitecoService $service): void
    {
        $service->uploadPrices();
    }
}
