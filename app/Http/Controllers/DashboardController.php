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



        // --- COMPONENT: COMPETITORS PRICE INDEX BY LOCALITY (MINETUR API) ---
        $selectedLocality = $request->input('locality', 'utrera');
        $sortBy = $request->input('sort_by', 'diesel');

        $localityMapping = [
            'utrera' => [
                'name' => 'Utrera',
                'match' => 'UTRERA',
                'own_station_id' => 1,
                'own_name' => 'E.S. VISTALEGRE',
                'own_code' => '1',
            ],
            'sevilla' => [
                'name' => 'Sevilla',
                'match' => 'SEVILLA',
                'own_station_id' => 2,
                'own_name' => 'RONDA NORTE',
                'own_code' => '2',
            ],
            'el_cuervo' => [
                'name' => 'El Cuervo de Sevilla',
                'match' => 'CUERVO DE SEVILLA (EL)',
                'own_station_id' => 3,
                'own_name' => 'E.S RODALABOTA',
                'own_code' => '3',
            ],
            'lebrija' => [
                'name' => 'Lebrija',
                'match' => 'LEBRIJA',
                'own_station_id' => 4,
                'own_name' => 'E.S. ATENAS',
                'own_code' => '4',
            ],
        ];

        if (!array_key_exists($selectedLocality, $localityMapping)) {
            $selectedLocality = 'utrera';
        }

        if (!in_array($sortBy, ['diesel', 'gas95'], true)) {
            $sortBy = 'diesel';
        }

        $targetLoc = $localityMapping[$selectedLocality];

        // Fetch all stations in Sevilla province (41) with cache of 6 hours
        $sevillaStations = Cache::remember('minetur_sevilla_stations_raw', 21600, function () {
            try {
                $response = Http::timeout(10)->get('https://sedeaplicaciones.minetur.gob.es/ServiciosRESTCarburantes/PreciosCarburantes/EstacionesTerrestres/FiltroProvincia/41');
                if ($response->failed()) {
                    return [];
                }
                $data = $response->json();
                return $data['ListaEESSPrecio'] ?? [];
            } catch (\Exception $e) {
                report($e);
                return [];
            }
        });

        $filteredStations = [];
        foreach ($sevillaStations as $s) {
            $mun = strtoupper($s['Municipio'] ?? '');
            
            $matched = false;
            if ($selectedLocality === 'el_cuervo') {
                $matched = (strpos($mun, 'CUERVO') !== false);
            } else {
                $matched = ($mun === $targetLoc['match']);
            }

            if ($matched) {
                $dieselPrice = (float) str_replace(',', '.', $s['Precio Gasoleo A'] ?? '0');
                $gas95Price = (float) str_replace(',', '.', $s['Precio Gasolina 95 E5'] ?? '0');

                // Identify if it matches our own station in this locality
                $isOurs = false;
                $rotulo = strtoupper($s['Rótulo'] ?? '');
                $direccion = strtoupper($s['Dirección'] ?? '');
                
                if ($selectedLocality === 'utrera' && (strpos($rotulo, 'VISTALEGRE') !== false || strpos($rotulo, 'UTRECAR') !== false || strpos($direccion, 'ECIJA-JEREZ') !== false)) {
                    $isOurs = true;
                } elseif ($selectedLocality === 'sevilla' && (strpos($rotulo, 'RONDA NORTE') !== false || strpos($direccion, 'CALONGE') !== false)) {
                    $isOurs = true;
                } elseif ($selectedLocality === 'el_cuervo' && (strpos($rotulo, 'RODALABOTA') !== false || strpos($direccion, 'TORNERO') !== false)) {
                    $isOurs = true;
                } elseif ($selectedLocality === 'lebrija' && (strpos($rotulo, 'ATENAS') !== false || strpos($direccion, 'ATENAS') !== false)) {
                    $isOurs = true;
                }

                $latVal = str_replace(',', '.', $s['Latitud'] ?? '');
                $lngVal = str_replace(',', '.', $s['Longitud (WGS84)'] ?? '');

                $filteredStations[] = [
                    'name' => $s['Rótulo'] ?? 'SIN RÓTULO',
                    'address' => $s['Dirección'] ?? 'Sin Dirección',
                    'postal_code' => $s['C.P.'] ?? '',
                    'locality_name' => $s['Localidad'] ?? '',
                    'latitude' => $latVal,
                    'longitude' => $lngVal,
                    'horario' => $s['Horario'] ?? '',
                    'diesel' => $dieselPrice,
                    'gas95' => $gas95Price,
                    'is_ours' => $isOurs,
                    'ideess' => $s['IDEESS'] ?? null,
                ];
            }
        }

        // Sort by the selected fuel type from lowest to highest
        usort($filteredStations, function ($a, $b) use ($sortBy) {
            $priceA = $a[$sortBy];
            $priceB = $b[$sortBy];

            if ($priceA <= 0) $priceA = 999999;
            if ($priceB <= 0) $priceB = 999999;

            return $priceA <=> $priceB;
        });

        // Compute rank of our station
        $ourRank = null;
        foreach ($filteredStations as $index => $station) {
            if ($station['is_ours']) {
                $ourRank = $index + 1;
                break;
            }
        }

        // Exclude our own station from the list of stations to display
        $filteredStations = array_values(array_filter($filteredStations, function ($station) {
            return !$station['is_ours'];
        }));

        // Filter by name (rótulo) if search query is provided
        $searchName = trim($request->input('search_name', ''));
        if ($searchName !== '') {
            $filteredStations = array_values(array_filter($filteredStations, function ($station) use ($searchName) {
                return (strpos(strtoupper($station['name']), strtoupper($searchName)) !== false);
            }));
        }

        // Real-time own prices from the local database
        $ownDiesel = \App\Models\PreciosProducto::where('CodigoEstacion', $targetLoc['own_code'])->where('CodigoProducto', '1')->value('PVP');
        $ownGas95 = \App\Models\PreciosProducto::where('CodigoEstacion', $targetLoc['own_code'])->where('CodigoProducto', '2')->value('PVP');

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
            'filteredStations' => $filteredStations,
            'selectedLocality' => $selectedLocality,
            'sortBy' => $sortBy,
            'searchName' => $searchName,
            'localityMapping' => $localityMapping,
            'ownDiesel' => $ownDiesel ? (float) $ownDiesel : null,
            'ownGas95' => $ownGas95 ? (float) $ownGas95 : null,
            'ourRank' => $ourRank,
            'ourStationName' => $targetLoc['own_name'],
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
