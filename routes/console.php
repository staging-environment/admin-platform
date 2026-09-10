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

// ── Alerta de Fichajes Faltantes (Comprobación diaria) ────────────────────────
// Envía un correo a los empleados que no completaron sus registros de entrada/salida.
Schedule::command('fichajes:send-missing-reminders')->dailyAt('21:00');




// ── Alertas de Empleados (Comprobación diaria de vencimientos) ───────────────
Schedule::call(function () {
    \App\Models\Empleado::all()->each->actualizarAlertas();
})->dailyAt('00:05');

// ── Sincronización Anual Feria de Utrera (Portal Web Oficial / BD) ─────────────
// Consulta por internet las fechas oficiales de la Feria y actualiza la BD.
// Se ejecuta el 1 de Agosto de cada año a las 04:00 (y reintento el 20 de Agosto).
Schedule::command('app:sync-feria-utrera-dates')->yearlyOn(8, 1, '04:00');
Schedule::command('app:sync-feria-utrera-dates')->yearlyOn(8, 20, '04:00');

// ?? Comando de Prueba / Simulaci?n de Alertas de Competencia ??????????????????
Artisan::command('minetur:test-alert {--clear : Limpiar alertas existentes}', function () {
    $service = app(\App\Services\MineturService::class);
    if ($this->option('clear')) {
        $service->clearPriceAlerts();
        $this->info('Alertas de competencia limpiadas.');
        return;
    }

    $service->recordPriceAlert([
        'id'                     => uniqid('price_alert_', true),
        'locality_key'           => 'utrera',
        'locality_name'          => 'Utrera',
        'fuel_type'              => 'diesel',
        'fuel_label'             => 'DI?SEL',
        'stations'               => [
            [
                'rank'       => 1,
                'name'       => 'E.S. VISTALEGRE',
                'address'    => 'CALLE ECIJA-JEREZ, 11',
                'price'      => 1.629,
                'old_price'  => 1.619,
                'diff'       => 0.010,
                'diff_text'  => '+0.010',
                'direction'  => 'sube',
                'is_changed' => true,
            ],
            [
                'rank'       => 2,
                'name'       => 'FAMILY ENERGY',
                'address'    => 'CARRETERA C.CIAL ALMAZARA PLAZA',
                'price'      => 1.615,
                'old_price'  => 1.625,
                'diff'       => -0.010,
                'diff_text'  => '-0.010',
                'direction'  => 'baja',
                'is_changed' => true,
            ],
            [
                'rank'       => 3,
                'name'       => 'BALLENOIL',
                'address'    => 'PLAZA DE LA TRIANILLA, S/N',
                'price'      => 1.619,
                'old_price'  => 1.619,
                'diff'       => 0,
                'diff_text'  => '',
                'direction'  => null,
                'is_changed' => false,
            ],
            [
                'rank'       => 4,
                'name'       => 'PLENERGY',
                'address'    => 'CALLE ALMAZARA, 2',
                'price'      => 1.619,
                'old_price'  => 1.619,
                'diff'       => 0,
                'diff_text'  => '',
                'direction'  => null,
                'is_changed' => false,
            ],
            [
                'rank'       => 5,
                'name'       => 'PLENERGY',
                'address'    => 'CALLE MIRLO, 1',
                'price'      => 1.619,
                'old_price'  => 1.619,
                'diff'       => 0,
                'diff_text'  => '',
                'direction'  => null,
                'is_changed' => false,
            ],
        ],
        'changed_stations_count' => 2,
        'created_at'             => now()->timestamp,
        'formatted_time'         => now('Europe/Madrid')->format('d/m/Y H:i'),
    ]);

    $this->info('Alerta de prueba registrada con ?xito. V?lida durante 2 horas.');
})->purpose('Simular o limpiar una alerta de cambio de precio de la competencia');
