<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MineturService
{
    const PROVINCE_SEVILLA = '41';
    const CACHE_TTL        = 3600; // 1 hora

    const LOCALITIES = [
        'sevilla'   => ['name' => 'Sevilla',              'match' => 'SEVILLA',               'exact' => true],
        'utrera'    => ['name' => 'Utrera',               'match' => 'UTRERA',                'exact' => true],
        'el_cuervo' => ['name' => 'El Cuervo de Sevilla', 'match' => 'CUERVO DE SEVILLA (EL)','exact' => false],
        'lebrija'   => ['name' => 'Lebrija',              'match' => 'LEBRIJA',               'exact' => true],
    ];

    const OUR_STATIONS = [6435, 7070, 13714, 13194];

    /**
     * Devuelve los datos cacheados de una localidad (diesel + gas95, Top 5).
     */
    public function getLocalityData(string $locality): array
    {
        $filePath = storage_path("app/minetur_{$locality}.json");
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return Cache::get("minetur_{$locality}", [
            'diesel'     => [],
            'gas95'      => [],
            'updated_at' => null,
        ]);
    }

    /**
     * Devuelve los datos de todas las localidades como array indexado.
     */
    public function getAllLocalitiesData(): array
    {
        $result = [];
        foreach (array_keys(self::LOCALITIES) as $key) {
            $result[$key] = $this->getLocalityData($key);
        }
        return $result;
    }

    /**
     * Refresca los datos de todas las localidades desde la API del Ministerio.
     * Hace un único request a la API provincial de Sevilla y filtra localmente.
     */
    public function refreshAll(): void
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'Accept'     => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (compatible; AdminPlatform/1.0)',
                ])
                ->get(
                    'https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/FiltroProvincia/' . self::PROVINCE_SEVILLA
                );

            if (! $response->successful()) {
                Log::warning('MineturService: API returned ' . $response->status());
                return;
            }

            $data      = $response->json();
            $stations  = $data['ListaEESSPrecio'] ?? [];
            $updatedAt = $data['Fecha'] ?? now('Europe/Madrid')->format('d/m/Y H:i:s');

            if (empty($stations)) {
                Log::warning('MineturService: Empty station list from API.');
                return;
            }

            $previousHistory = Cache::get('minetur_all_prices_history', []);
            $newHistory = $previousHistory;

            foreach (self::LOCALITIES as $key => $config) {
                $diesel = [];
                $gas95  = [];

                foreach ($stations as $s) {
                    $mun = strtoupper(trim($s['Municipio'] ?? ''));

                    $matches = $config['exact']
                        ? ($mun === $config['match'])
                        : str_contains($mun, $config['match']);

                    if (! $matches) continue;

                    $dPrice = (float) str_replace(',', '.', $s['Precio Gasoleo A'] ?? '0');
                    $gPrice = (float) str_replace(',', '.', $s['Precio Gasolina 95 E5'] ?? '0');
                    $name   = trim($s['Rótulo'] ?? 'Sin nombre');
                    $addr   = trim($s['Dirección'] ?? '');

                    $margen = $s['Margen'] ?? '';
                    $id     = (int) ($s['IDEESS'] ?? 0);

                    if ($id > 0) {
                        $newHistory[$id] = [
                            'diesel' => $dPrice > 0 ? $dPrice : ($previousHistory[$id]['diesel'] ?? null),
                            'gas95'  => $gPrice > 0 ? $gPrice : ($previousHistory[$id]['gas95'] ?? null),
                            'name'   => $name,
                        ];
                    }

                    if ($dPrice > 0) {
                        $diesel[] = [
                            'name'    => $name,
                            'address' => $addr,
                            'price'   => $dPrice,
                            'margen'  => $margen,
                            'id'      => $id,
                        ];
                    }
                    if ($gPrice > 0) {
                        $gas95[] = [
                            'name'    => $name,
                            'address' => $addr,
                            'price'   => $gPrice,
                            'margen'  => $margen,
                            'id'      => $id,
                        ];
                    }
                }

                // Ordenar exactamente igual que Miteco (Precio asc -> Margen N < D < I -> IDEESS asc)
                $mitecoSort = function ($a, $b) {
                    if ($a['price'] != $b['price']) {
                        return $a['price'] <=> $b['price'];
                    }

                    $margenOrder = ['N' => 1, 'D' => 2, 'I' => 3];
                    $mA = $margenOrder[$a['margen']] ?? 4;
                    $mB = $margenOrder[$b['margen']] ?? 4;
                    if ($mA != $mB) {
                        return $mA <=> $mB;
                    }

                    return $a['id'] <=> $b['id'];
                };

                usort($diesel, $mitecoSort);
                usort($gas95,  $mitecoSort);

                $newDiesel = array_slice($diesel, 0, 5);
                $newGas95  = array_slice($gas95, 0, 5);

                $cached = $this->getLocalityData($key);
                $hasChanged = false;

                if (!empty($cached['diesel']) || !empty($cached['gas95'])) {
                    $dieselChanged = json_encode($cached['diesel'] ?? []) !== json_encode($newDiesel);
                    $gas95Changed = json_encode($cached['gas95'] ?? []) !== json_encode($newGas95);
                    $hasChanged = $dieselChanged || $gas95Changed;

                    if ($hasChanged) {
                        try {
                            if ($dieselChanged && $this->hasCompetitorChanges($newDiesel, $cached['diesel'] ?? [], 'diesel')) {
                                $this->notifyChanges($key, 'diesel', $newDiesel, $cached['diesel'] ?? []);
                            }
                            if ($gas95Changed && $this->hasCompetitorChanges($newGas95, $cached['gas95'] ?? [], 'gas95')) {
                                $this->notifyChanges($key, 'gas95', $newGas95, $cached['gas95'] ?? []);
                            }
                        } catch (\Throwable $e) {
                            Log::error("MineturService notification error: " . $e->getMessage());
                        }
                    }
                }

                $finalUpdatedAt = $hasChanged ? $updatedAt : ($cached['updated_at'] ?? $updatedAt);

                $dataToStore = [
                    'diesel'     => $newDiesel,
                    'gas95'      => $newGas95,
                    'updated_at' => $finalUpdatedAt,
                    'checked_at' => now('Europe/Madrid')->format('d/m/Y H:i:s'),
                ];

                file_put_contents(storage_path("app/minetur_{$key}.json"), json_encode($dataToStore, JSON_PRETTY_PRINT));
                Cache::put("minetur_{$key}", $dataToStore, self::CACHE_TTL);
            }

            Cache::put('minetur_all_prices_history', $newHistory, now()->addDays(30));

            Log::info('MineturService: Refreshed ' . count(self::LOCALITIES) . ' localities. Stations total: ' . count($stations) . '. Date: ' . $updatedAt);

        } catch (\Throwable $e) {
            Log::error('MineturService: ' . $e->getMessage());
        }
    }

    /**
     * Send price change alerts to authorized users via Telegram.
     */
    public function notifyChanges(string $localityKey, string $fuelType, array $newPrices, array $oldPrices = []): void
    {
        $localityName = self::LOCALITIES[$localityKey]['name'] ?? ucfirst($localityKey);
        
        $text = "🔔 <b>Cambio de precios de la competencia</b>\n";
        $text .= "Localidad: <b>{$localityName}</b>\n\n";

        $previousHistory = Cache::get('minetur_all_prices_history', []);

        // Create lookups for old prices (fallback)
        $oldPricesLookup = [];
        foreach ($oldPrices as $s) {
            $oldPricesLookup[$s['id']] = $s['price'];
        }

        $fuelLabel = $fuelType === 'diesel' ? 'DIÉSEL' : 'GASOLINA 95';
        $fuelEmoji = '⛽';

        $text .= "<b>Precios actuales (Top Competidores):</b>\n";
        $text .= "{$fuelEmoji} <b>{$fuelLabel}:</b>\n";

        foreach (array_slice($newPrices, 0, 5) as $idx => $s) {
            $num = $idx + 1;
            
            // Check global history first, fallback to cached top 5
            $oldPrice = $previousHistory[$s['id']][$fuelType] ?? ($oldPricesLookup[$s['id']] ?? null);
            
            $priceText = "<b>" . number_format($s['price'], 3) . " €</b>";
            $highlight = "";
            $stationName = $s['name'];

            if ($oldPrice !== null && abs($oldPrice - $s['price']) > 0.0001) {
                $diff = $s['price'] - $oldPrice;
                $diffText = ($diff > 0 ? "+" : "") . number_format($diff, 3);
                $direction = $diff > 0 ? "sube" : "baja";
                $highlight = " ⚠️ <b>({$direction})</b> <i>(antes " . number_format($oldPrice, 3) . " € | {$diffText} €)</i>";
                $stationName = "<b>{$stationName}</b>";
            }

            $text .= "  {$num}. {$stationName}: {$priceText}{$highlight}\n";
        }

        // Find users with the required permission and active Telegram linked
        $usersToAlert = \App\Models\User::permission('recibir_notificaciones_competencia')
            ->whereNotNull('telegram_chat_id')
            ->get();

        $telegramService = app(TelegramService::class);
        foreach ($usersToAlert as $user) {
            $telegramService->sendMessage($user->telegram_chat_id, $text);
        }
    }

    /**
     * Send daily summary of competitor prices.
     */
    public function sendDailySummary(): void
    {
        $text = "📊 <b>[INFORME DIARIO] Precios de la Competencia</b>\n";
        $text .= "Fecha: <b>" . now('Europe/Madrid')->format('d/m/Y H:i') . "</b>\n\n";

        foreach (self::LOCALITIES as $key => $config) {
            $data = $this->getLocalityData($key);
            $diesel = $data['diesel'] ?? [];
            $gas95 = $data['gas95'] ?? [];

            if (empty($diesel) && empty($gas95)) {
                continue;
            }

            $text .= "📍 <b>" . strtoupper($config['name']) . "</b>\n";

            if (!empty($diesel)) {
                $text .= "  ⛽ <b>DIÉSEL:</b>\n";
                foreach (array_slice($diesel, 0, 3) as $idx => $s) {
                    $num = $idx + 1;
                    $text .= "    {$num}. {$s['name']}: <b>" . number_format($s['price'], 3) . " €</b>\n";
                }
            }

            if (!empty($gas95)) {
                $text .= "  ⛽ <b>GASOLINA 95:</b>\n";
                foreach (array_slice($gas95, 0, 3) as $idx => $s) {
                    $num = $idx + 1;
                    $text .= "    {$num}. {$s['name']}: <b>" . number_format($s['price'], 3) . " €</b>\n";
                }
            }
            $text .= "\n";
        }

        $usersToAlert = \App\Models\User::permission('recibir_notificaciones_competencia')
            ->whereNotNull('telegram_chat_id')
            ->get();

        $telegramService = app(TelegramService::class);
        foreach ($usersToAlert as $user) {
            $telegramService->sendMessage($user->telegram_chat_id, $text);
        }
    }

    /**
     * Check if a price change list contains changes on competitor stations.
     */
    private function hasCompetitorChanges(array $newPrices, array $oldPrices, string $fuelType): bool
    {
        $previousHistory = Cache::get('minetur_all_prices_history', []);

        $oldPricesLookup = [];
        foreach ($oldPrices as $s) {
            $oldPricesLookup[$s['id']] = $s['price'];
        }

        foreach ($newPrices as $s) {
            $id = $s['id'];
            if (in_array($id, self::OUR_STATIONS)) {
                continue;
            }

            $newPrice = $s['price'];
            $oldPrice = $previousHistory[$id][$fuelType] ?? ($oldPricesLookup[$id] ?? null);

            // Only notify if we have a previous price AND it actually changed
            if ($oldPrice !== null && abs($oldPrice - $newPrice) > 0.0001) {
                return true;
            }
        }
        return false;
    }
}
