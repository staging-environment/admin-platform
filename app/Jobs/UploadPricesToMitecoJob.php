<?php

namespace App\Jobs;

use App\Services\MitecoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadPricesToMitecoJob
{
    use Dispatchable;

    /**
     * Envía los precios actuales de nuestras 4 gasolineras a MITECO.
     */
    public function handle(MitecoService $service): void
    {
        $service->uploadPrices();
    }
}
