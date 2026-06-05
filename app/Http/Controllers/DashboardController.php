<?php

namespace App\Http\Controllers;

use App\Services\VirtusgesnetService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request, VirtusgesnetService $virtusgesnetService): View
    {
        $tables = [];
        $stations = [];
        $monthlySales = [];

        $selectedYear = (int) $request->integer('year', (int) date('Y'));
        $selectedDocumentType = $request->string('document_type', 'all')->toString();
        $selectedStartMonth = $request->filled('start_month') ? (int) $request->integer('start_month') : null;
        $selectedEndMonth = $request->filled('end_month') ? (int) $request->integer('end_month') : null;
        $selectedStationCode = $request->filled('station_code') ? (int) $request->integer('station_code') : null;

        if (!in_array($selectedDocumentType, ['all', 'invoices', 'tickets'], true)) {
            $selectedDocumentType = 'all';
        }

        if ($selectedStartMonth !== null) {
            $selectedStartMonth = max(1, min(12, $selectedStartMonth));
        }

        if ($selectedEndMonth !== null) {
            $selectedEndMonth = max(1, min(12, $selectedEndMonth));
        }

        if ($selectedStartMonth !== null && $selectedEndMonth !== null && $selectedStartMonth > $selectedEndMonth) {
            [$selectedStartMonth, $selectedEndMonth] = [$selectedEndMonth, $selectedStartMonth];
        }

        if ($selectedStationCode !== null && $selectedStationCode <= 0) {
            $selectedStationCode = null;
        }

        try {
            $tables = $virtusgesnetService->getTables();
            $stations = $virtusgesnetService->getStations();

            $monthlySales = $virtusgesnetService->getMonthlySalesSummary(
                $selectedYear,
                $selectedDocumentType,
                $selectedStartMonth,
                $selectedEndMonth,
                $selectedStationCode
            );
        } catch (\Throwable $exception) {
            report($exception);
        }

        // --- WIDGET 1: WEATHER INFO ---
        // Weather is now fetched client-side to respect user's location and allow search persistence.
        $weatherInfo = null;

        // --- WIDGET 2: SERVER PERFORMANCE STATS ---
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
        $cpuLoad = isset($load[0]) ? round($load[0], 2) : 0;

        $diskTotal = @disk_total_space('/') ?: 0;
        $diskFree = @disk_free_space('/') ?: 0;
        $diskUsed = $diskTotal - $diskFree;
        $diskUsedPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $formatBytes = function($bytes) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, 2) . ' ' . $units[$pow];
        };

        $ramTotal = 0;
        $ramFree = 0;
        if (file_exists('/proc/meminfo')) {
            $data = @file_get_contents('/proc/meminfo');
            if ($data) {
                preg_match('/MemTotal:\s+(\d+)/', $data, $matchesTotal);
                preg_match('/MemAvailable:\s+(\d+)/', $data, $matchesAvailable);
                if (isset($matchesTotal[1])) {
                    $ramTotal = $matchesTotal[1] * 1024;
                }
                if (isset($matchesAvailable[1])) {
                    $ramFree = $matchesAvailable[1] * 1024;
                }
            }
        }
        $ramUsed = $ramTotal - $ramFree;
        $ramUsedPercent = $ramTotal > 0 ? round(($ramUsed / $ramTotal) * 100, 1) : 0;

        // DB Status Checks
        $dbStatus = [];
        foreach (['Local' => null, 'VirtusGesNet' => 'virtusgesnet', 'SII' => 'sii'] as $name => $conn) {
            try {
                DB::connection($conn)->getPdo();
                $dbStatus[$name] = true;
            } catch (\Exception $e) {
                $dbStatus[$name] = false;
            }
        }

        $serverStats = [
            'env' => config('app.env') === 'production' ? 'Producción' : 'Desarrollo/Local',
            'cpu' => $cpuLoad,
            'disk_used_percent' => $diskUsedPercent,
            'disk_free' => $formatBytes($diskFree),
            'disk_total' => $formatBytes($diskTotal),
            'ram_used_percent' => $ramUsedPercent,
            'ram_free' => $formatBytes($ramFree),
            'ram_total' => $formatBytes($ramTotal),
            'php_version' => PHP_VERSION,
            'db_connections' => $dbStatus,
        ];

        // --- COMPONENT: COMPETITORS PRICE INDEX (MINETUR API) ---
        $selectedRadius = (int) $request->input('radius', 30);
        if (!in_array($selectedRadius, [30, 50, 100], true)) {
            $selectedRadius = 30;
        }

        $competitorsData = Cache::remember('competitors_prices_' . $selectedRadius, 21600, function () use ($selectedRadius) {
            $fallbacks = [
                1 => ['lat' => 37.1824, 'lng' => -5.7954, 'name' => 'E.S. VISTALEGRE'],
                2 => ['lat' => 37.1944, 'lng' => -5.7770, 'name' => 'RONDA NORTE'],
                3 => ['lat' => 36.8480, 'lng' => -5.9224, 'name' => 'E.S RODALABOTA'],
                4 => ['lat' => 37.5348, 'lng' => -5.0934, 'name' => 'E.S. ATENAS'],
            ];

            try {
                $response = Http::timeout(10)->get('https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/FiltroProvincia/41');
                if ($response->failed()) {
                    return [];
                }
                $data = $response->json();
                $stations = $data['ListaEESSPrecio'] ?? [];

                $result = [];
                foreach ($fallbacks as $id => $orig) {
                    $dbStation = \App\Models\Gasolinera::with('contenido')->find($id);
                    $origLat = ($dbStation && $dbStation->contenido && $dbStation->contenido->latitud) ? $dbStation->contenido->latitud : $orig['lat'];
                    $origLng = ($dbStation && $dbStation->contenido && $dbStation->contenido->longitud) ? $dbStation->contenido->longitud : $orig['lng'];
                    $origName = $dbStation ? $dbStation->Nombre : $orig['name'];

                    $list = [];
                    foreach ($stations as $s) {
                        $lat = (float) str_replace(',', '.', $s['Latitud']);
                        $lng = (float) str_replace(',', '.', $s['Longitud (WGS84)']);
                        
                        if (!$lat || !$lng) continue;

                        // Calculate distance (Haversine formula inline)
                        $theta = $origLng - $lng;
                        $dist = sin(deg2rad($origLat)) * sin(deg2rad($lat)) +  cos(deg2rad($origLat)) * cos(deg2rad($lat)) * cos(deg2rad($theta));
                        $dist = acos($dist);
                        $dist = rad2deg($dist);
                        $kms = $dist * 60 * 1.1515 * 1.609344;
                        
                        if ($kms < 0.05) continue;
                        if ($kms > $selectedRadius) continue; // Filter by radius
                        
                        $list[] = [
                            'name' => $s['Rótulo'],
                            'address' => $s['Dirección'],
                            'distance' => $kms,
                            'diesel' => (float) str_replace(',', '.', $s['Precio Gasoleo A'] ?? 0),
                            'gas95' => (float) str_replace(',', '.', $s['Precio Gasolina 95 E5'] ?? 0),
                        ];
                    }
                    
                    usort($list, function($a, $b) {
                        return $a['distance'] <=> $b['distance'];
                    });

                    $result[$id] = [
                        'station_name' => $origName,
                        'competitors' => array_slice($list, 0, 5) // Show up to 5 closest
                    ];
                }

                return $result;
            } catch (\Exception $e) {
                report($e);
                return [];
            }
        });

        return view('dashboard', [
            'tables' => $tables,
            'stations' => $stations,
            'tableGroups' => $this->groupTablesByBusinessArea($tables),
            'monthlySales' => $monthlySales,
            'selectedYear' => $selectedYear,
            'selectedDocumentType' => $selectedDocumentType,
            'selectedStartMonth' => $selectedStartMonth,
            'selectedEndMonth' => $selectedEndMonth,
            'selectedStationCode' => $selectedStationCode,
            'months' => $this->months(),
            'weatherInfo' => $weatherInfo,
            'serverStats' => $serverStats,
            'competitorsData' => $competitorsData,
            'selectedRadius' => $selectedRadius,
        ]);
    }

    private function groupTablesByBusinessArea(array $tables): array
    {
        return [
            'ventas' => $this->filterTables($tables, [
                'deventa',
                'yticketsdeventa',
                'ventasencurso',
                'ticketsdelavado',
                'ventaexclusiva',
            ]),

            'compras' => $this->filterTables($tables, [
                'decompra',
                'proveedor',
                'proveedores',
                'pedidosdecompra',
            ]),

            'clientes' => $this->filterTables($tables, [
                'cliente',
                'clientes',
                'contacto',
                'contactos',
            ]),

            'proveedores' => $this->filterTables($tables, [
                'proveedor',
                'proveedores',
            ]),

            'articulos' => $this->filterTables($tables, [
                'articulo',
                'articulos',
                'producto',
                'productos',
                'familia',
                'familias',
                'stock',
                'almacen',
                'almacenes',
            ]),
        ];
    }

    private function filterTables(array $tables, array $keywords): array
    {
        return array_values(array_filter($tables, function (string $table) use ($keywords) {
            $normalizedTable = mb_strtolower($table);

            foreach ($keywords as $keyword) {
                if (str_contains($normalizedTable, mb_strtolower($keyword))) {
                    return true;
                }
            }

            return false;
        }));
    }

    private function months(): array
    {
        return [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
    }
}
