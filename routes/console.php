<?php

use App\Jobs\RefreshFuelMarketsJob;
use App\Jobs\RefreshMineturJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Mercados Energéticos (Yahoo Finance) ─────────────────────────────────────
// Se ejecuta cada 30 segundos; el Job actualiza la caché con los últimos
// precios de BZ=F (Gasoil Londres) y RB=F (Gasolina RBOB).
Schedule::job(new RefreshFuelMarketsJob)->everyThirtySeconds();

// ── Competencia Local (API MITECO) ───────────────────────────────────────────
// La API del Ministerio se actualiza ~1 vez/hora. Refrescamos cada hora.
Schedule::job(new RefreshMineturJob)->hourly();
