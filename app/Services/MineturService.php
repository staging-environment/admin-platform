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

    /**
     * Devuelve los datos cacheados de una localidad (diesel + gas95, Top 5).
     */
    public function getLocalityData(string $locality): array
    {
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

                $cached = Cache::get("minetur_{$key}");
                $hasChanged = true;

                if ($cached && isset($cached['diesel']) && isset($cached['gas95'])) {
                    if (json_encode($cached['diesel']) === json_encode($newDiesel) && 
                        json_encode($cached['gas95']) === json_encode($newGas95)) {
                        $hasChanged = false;
                    }
                }

                $finalUpdatedAt = $hasChanged ? $updatedAt : ($cached['updated_at'] ?? $updatedAt);

                Cache::put("minetur_{$key}", [
                    'diesel'     => $newDiesel,
                    'gas95'      => $newGas95,
                    'updated_at' => $finalUpdatedAt,
                    'checked_at' => now('Europe/Madrid')->format('d/m/Y H:i:s'),
                ], self::CACHE_TTL);
            }

            Log::info('MineturService: Refreshed ' . count(self::LOCALITIES) . ' localities. Stations total: ' . count($stations) . '. Date: ' . $updatedAt);

        } catch (\Throwable $e) {
            Log::error('MineturService: ' . $e->getMessage());
        }
    }
}
