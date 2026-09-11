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

    // Alerta 1: Utrera (Diesel) - Se toman las estaciones reales en cache
    $utreraData = $service->getLocalityData('utrera');
    $utreraDiesel = $utreraData['diesel'] ?? [];

    $utreraStations = [];
    if (!empty($utreraDiesel)) {
        foreach ($utreraDiesel as $idx => $st) {
            $rank = $idx + 1;
            $isChanged = $rank <= 2; // 2 primeras cambiadas
            $currentPrice = (float) ($st['price'] ?? 1.619);
            $oldPrice = $isChanged ? ($rank === 1 ? round($currentPrice - 0.010, 3) : round($currentPrice + 0.010, 3)) : $currentPrice;
            $diff = round($currentPrice - $oldPrice, 3);
            $direction = $diff > 0 ? 'sube' : ($diff < 0 ? 'baja' : null);
            $diffText = $diff > 0 ? ('+' . number_format($diff, 3)) : number_format($diff, 3);

            $utreraStations[] = [
                'id'         => $st['id'] ?? null,
                'rank'       => $rank,
                'name'       => $st['name'] ?? "Estacion {$rank}",
                'address'    => $st['address'] ?? '',
                'price'      => $currentPrice,
                'old_price'  => $oldPrice,
                'diff'       => $isChanged ? $diff : 0,
                'diff_text'  => $isChanged ? $diffText : '',
                'direction'  => $isChanged ? $direction : null,
                'is_changed' => $isChanged,
            ];
        }
    } else {
        $utreraStations = [
            ['rank' => 1, 'name' => 'E.S.VISTALEGRE', 'address' => 'CALLE ECIJA-JEREZ, 11', 'price' => 1.629, 'old_price' => 1.619, 'diff' => 0.010, 'diff_text' => '+0.010', 'direction' => 'sube', 'is_changed' => true],
            ['rank' => 2, 'name' => 'FAMILY ENERGY', 'address' => 'CARRETERA C.CIAL ALMAZARA PLAZA', 'price' => 1.615, 'old_price' => 1.625, 'diff' => -0.010, 'diff_text' => '-0.010', 'direction' => 'baja', 'is_changed' => true],
            ['rank' => 3, 'name' => 'BALLENOIL', 'address' => 'PLAZA DE LA TRIANILLA, S/N', 'price' => 1.619, 'old_price' => 1.619, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
            ['rank' => 4, 'name' => 'PETROUYA', 'address' => 'CARRETERA UTRERA SEVILLA KM. 1', 'price' => 1.639, 'old_price' => 1.639, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
            ['rank' => 5, 'name' => 'PLENERGY', 'address' => 'CALLE ALMAZARA, 2', 'price' => 1.649, 'old_price' => 1.649, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
        ];
    }

    $service->recordPriceAlert([
        'id'                     => uniqid('price_alert_', true),
        'locality_key'           => 'utrera',
        'locality_name'          => 'Utrera',
        'fuel_type'              => 'diesel',
        'fuel_label'             => 'DIESEL',
        'stations'               => $utreraStations,
        'changed_stations_count' => 2,
        'created_at'             => now()->timestamp,
        'formatted_time'         => now('Europe/Madrid')->format('d/m/Y H:i'),
    ]);

    // Alerta 2: Sevilla (Gasolina 95) - Se toman las estaciones reales en cache
    $sevillaData = $service->getLocalityData('sevilla');
    $sevillaGas = $sevillaData['gas95'] ?? [];

    $sevillaStations = [];
    if (!empty($sevillaGas)) {
        foreach ($sevillaGas as $idx => $st) {
            $rank = $idx + 1;
            $isChanged = $rank === 1; // 1 primera cambiada
            $currentPrice = (float) ($st['price'] ?? 1.669);
            $oldPrice = $isChanged ? round($currentPrice - 0.010, 3) : $currentPrice;
            $diff = round($currentPrice - $oldPrice, 3);
            $direction = $diff > 0 ? 'sube' : ($diff < 0 ? 'baja' : null);
            $diffText = $diff > 0 ? ('+' . number_format($diff, 3)) : number_format($diff, 3);

            $sevillaStations[] = [
                'id'         => $st['id'] ?? null,
                'rank'       => $rank,
                'name'       => $st['name'] ?? "Estacion {$rank}",
                'address'    => $st['address'] ?? '',
                'price'      => $currentPrice,
                'old_price'  => $oldPrice,
                'diff'       => $isChanged ? $diff : 0,
                'diff_text'  => $isChanged ? $diffText : '',
                'direction'  => $isChanged ? $direction : null,
                'is_changed' => $isChanged,
            ];
        }
    } else {
        $sevillaStations = [
            ['rank' => 1, 'name' => 'RONDA NORTE', 'address' => 'AUTOVIA SE30 NUDO CALONGE KM....', 'price' => 1.669, 'old_price' => 1.679, 'diff' => -0.010, 'diff_text' => '-0.010', 'direction' => 'baja', 'is_changed' => true],
            ['rank' => 2, 'name' => 'BALLENOIL', 'address' => 'AVENIDA SAN JERONIMO, S/N', 'price' => 1.669, 'old_price' => 1.669, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
            ['rank' => 3, 'name' => 'ALCAMPO S.A.', 'address' => 'CALLE RONDA DEL TAMARGUILLO, S...', 'price' => 1.675, 'old_price' => 1.675, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
            ['rank' => 4, 'name' => 'APJ', 'address' => 'CALLE COJINETE, SN', 'price' => 1.675, 'old_price' => 1.675, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
            ['rank' => 5, 'name' => 'MOOVE', 'address' => 'CALLE ASTRONOMIA, 3', 'price' => 1.678, 'old_price' => 1.678, 'diff' => 0, 'diff_text' => '', 'direction' => null, 'is_changed' => false],
        ];
    }

    $service->recordPriceAlert([
        'id'                     => uniqid('price_alert_', true),
        'locality_key'           => 'sevilla',
        'locality_name'          => 'Sevilla',
        'fuel_type'              => 'gas95',
        'fuel_label'             => 'GASOLINA 95',
        'stations'               => $sevillaStations,
        'changed_stations_count' => 1,
        'created_at'             => now()->timestamp,
        'formatted_time'         => now('Europe/Madrid')->format('d/m/Y H:i'),
    ]);

    $this->info('Alertas de prueba registradas con exito (Utrera y Sevilla). Validas durante 2 horas.');
})->purpose('Simular o limpiar alertas de cambio de precio de la competencia');
