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
// La API del Ministerio se actualiza ~1 vez/hora. Consultamos cada 15 minutos para sincronizar los cambios rápido.
Schedule::job(new RefreshMineturJob)->everyFifteenMinutes();

// ── Envío de Precios a MITECO ─────────────────────────────────────────────────
// Envía los precios actuales de nuestras 4 gasolineras a MITECO cada 5 minutos si hay cambios.
Schedule::job(new \App\Jobs\UploadPricesToMitecoJob)->everyFiveMinutes();

// ── Resumen Diario de Competencia por Telegram ─────────────────────────────────
// Envía un resumen diario de precios a las 09:00 AM (Hora de Madrid).
Schedule::command('telegram:send-daily-summary')->dailyAt('09:00')->timezone('Europe/Madrid');

