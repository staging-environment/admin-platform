<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportService
{
    // Las gráficas se irán añadiendo aquí progresivamente.

    /**
     * Informe de Margen Comercial de Mercadería (Grupo 3).
     *
     * Compara el precio medio ponderado de compra (base imponible, sin IVA)
     * con el PVP de venta sin IVA, calculando el % margen comercial por artículo.
     *
     * @param int      $startMonth
     * @param int      $startYear
     * @param int      $endMonth
     * @param int      $endYear
     * @param int|null $stationCode  null = todas las estaciones
     * @return array   Lista de artículos con sus datos de margen
     */
    public function getMargenMercaderia(
        int $startMonth,
        int $startYear,
        int $endMonth,
        int $endYear,
        ?int $stationCode = null
    ): array {
        $dateFrom = Carbon::create($startYear, $startMonth, 1)->startOfMonth()->format('Y-m-d');
        $dateTo   = Carbon::create($endYear, $endMonth, 1)->endOfMonth()->format('Y-m-d');

        $db = DB::connection('virtusgesnet');

        // Precio medio ponderado de compra por artículo en el período
        $comprasQuery = $db->table('detalledefacturasdecompra as d')
            ->join('facturasdecompra as f', function ($join) {
                $join->on('f.CodigoDeEmpresaPropia', '=', 'd.CodigoDeEmpresaPropia')
                     ->on('f.Serie', '=', 'd.Serie')
                     ->on('f.Numero', '=', 'd.Numero');
            })
            ->select([
                'd.CodigoDeProducto',
                DB::raw('SUM(d.Cantidad * d.Precio) / SUM(d.Cantidad) as precio_compra_medio'),
                DB::raw('MAX(d.PorcentajeDeIVA) as pct_iva'),
                DB::raw('SUM(d.Cantidad) as total_unidades'),
                DB::raw('COUNT(d.ID) as num_lineas'),
            ])
            ->where('d.CodigoDeProducto', 'like', '3%')
            ->whereBetween(DB::raw('DATE(f.FechaYHoraDeFactura)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $comprasQuery->where('f.CodigoDeEstacion', $stationCode);
        }

        $compras = $comprasQuery
            ->groupBy('d.CodigoDeProducto')
            ->get()
            ->keyBy('CodigoDeProducto');

        if ($compras->isEmpty()) {
            return [];
        }

        $productCodes = $compras->keys()->toArray();

        // Obtener productos (nombre + grupo)
        $productos = $db->table('productos as p')
            ->join('gruposdeproductos as g', 'g.Codigo', '=', 'p.CodigoDeGrupo')
            ->select(['p.Codigo', 'p.Descripcion', 'g.Nombre as GrupoNombre', 'g.Codigo as GrupoCodigo'])
            ->whereIn('p.Codigo', $productCodes)
            ->get()
            ->keyBy('Codigo');

        // Obtener PVP de venta — prioridad: estación específica > grupo de estaciones > todas
        $pvpRows = $db->table('preciosdeproductos')
            ->select(['CodigoProducto', 'PVP', 'AmbitoDeEstaciones', 'CodigoEstacion'])
            ->whereIn('CodigoProducto', $productCodes)
            ->get();

        // Indexar PVP con prioridad correcta
        $pvpIndex = [];
        foreach ($pvpRows as $pvp) {
            $code = $pvp->CodigoProducto;
            // Inicializar con precio "todas las estaciones"
            if ($pvp->AmbitoDeEstaciones === 'Todas las estaciones' && !isset($pvpIndex[$code])) {
                $pvpIndex[$code] = (float) $pvp->PVP;
            }
            // Sobrescribir con precio específico de la estación seleccionada
            if ($stationCode !== null
                && $pvp->AmbitoDeEstaciones === 'Estacion'
                && (int) $pvp->CodigoEstacion === $stationCode
            ) {
                $pvpIndex[$code] = (float) $pvp->PVP;
            }
        }

        // Construir resultado
        $result = [];
        foreach ($compras as $codigo => $compra) {
            $producto = $productos[$codigo] ?? null;
            if (!$producto) {
                continue;
            }

            $precioCompra = (float) $compra->precio_compra_medio;
            $pctIva       = (float) $compra->pct_iva;
            $pvpConIva    = $pvpIndex[$codigo] ?? null;

            if ($pvpConIva === null || $pvpConIva <= 0 || $precioCompra <= 0) {
                continue;
            }

            $divisorIva = 1 + ($pctIva / 100);
            $pvpSinIva  = $pvpConIva / $divisorIva;
            $margenPct  = (($pvpSinIva - $precioCompra) / $precioCompra) * 100;

            $result[] = [
                'codigo'             => $codigo,
                'descripcion'        => $producto->Descripcion,
                'grupo_codigo'       => $producto->GrupoCodigo,
                'grupo_nombre'       => $producto->GrupoNombre,
                'precio_compra'      => round($precioCompra, 4),
                'pvp_con_iva'        => round($pvpConIva, 4),
                'pct_iva'            => $pctIva,
                'pvp_sin_iva'        => round($pvpSinIva, 4),
                'margen_pct'         => round($margenPct, 2),
                'unidades_compradas' => (float) $compra->total_unidades,
            ];
        }

        // Ordenar por margen descendente
        usort($result, fn($a, $b) => $b['margen_pct'] <=> $a['margen_pct']);

        return $result;
    }

    /**
     * Obtiene los precios de futuros de combustible desde Yahoo Finance.
     * Cache de 30 minutos para no saturar la API.
     */
    public function getFuturesPrices(): array
    {
        return Cache::remember('futures_prices_v1', 1800, function () {
            $symbols = [
                'RB=F' => ['nombre' => 'Gasolina RBOB', 'unidad' => 'USD/gal', 'icono' => '⛽'],
                'HO=F' => ['nombre' => 'Gasoil (Diésel ref.)', 'unidad' => 'USD/gal', 'icono' => '🛢️'],
            ];

            $results = [];
            foreach ($symbols as $symbol => $meta) {
                try {
                    $url      = "https://query2.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=2d";
                    $response = \Illuminate\Support\Facades\Http::timeout(5)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; PHP)'])
                        ->get($url);

                    if ($response->failed()) {
                        continue;
                    }

                    $metaInfo = $response->json()['chart']['result'][0]['meta'] ?? null;
                    if (!$metaInfo) {
                        continue;
                    }

                    $precio    = (float) ($metaInfo['regularMarketPrice'] ?? 0);
                    $prevClose = (float) ($metaInfo['chartPreviousClose'] ?? 0);
                    $cambio    = $prevClose > 0 ? $precio - $prevClose : 0;
                    $cambioPct = $prevClose > 0 ? ($cambio / $prevClose) * 100 : 0;

                    $results[$symbol] = [
                        'nombre'    => $meta['nombre'],
                        'unidad'    => $meta['unidad'],
                        'icono'     => $meta['icono'],
                        'precio'    => $precio,
                        'cambio'    => $cambio,
                        'cambioPct' => $cambioPct,
                        'positivo'  => $cambio >= 0,
                        'currency'  => $metaInfo['currency'] ?? 'USD',
                    ];
                } catch (\Exception $e) {
                    report($e);
                }
            }

            return $results;
        });
    }
}
