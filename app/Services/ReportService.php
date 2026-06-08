<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportService
{
    // Las gráficas se irán añadiendo aquí progresivamente.

    /**
     * Informe Simple de Margen Compra vs Venta.
     *
     * Para un período y grupos dados, muestra por artículo:
     *   - precio_compra   : media ponderada de lo pagado al proveedor (sin IVA)
     *   - precio_venta    : media ponderada de lo cobrado en caja (con IVA)
     *   - uds_vendidas    : unidades realmente vendidas en TPV
     *   - total_comprado  : precio_compra × uds_vendidas  (coste de lo vendido)
     *   - total_facturado : precio_venta  × uds_vendidas  (ingreso bruto)
     *   - beneficio       : total_facturado - total_comprado
     *   - margen_pct      : beneficio / total_comprado × 100
     */
    public function getMargenSimple(
        int $startMonth,
        int $startYear,
        int $endMonth,
        int $endYear,
        array  $groupCodes,
        ?int   $stationCode = null
    ): array {
        $dateFrom = Carbon::create($startYear, $startMonth, 1)->startOfMonth()->format('Y-m-d');
        $dateTo   = Carbon::create($endYear, $endMonth, 1)->endOfMonth()->format('Y-m-d');

        $db = DB::connection('virtusgesnet');

        // ── Productos de los grupos seleccionados (incluyendo subgrupos) ──────
        $productosDeGrupo = $db->table('productos')
            ->whereIn('CodigoDeGrupo', $groupCodes)
            ->pluck('Codigo')
            ->toArray();

        if (empty($productosDeGrupo)) return [];

        // ── 1. Precio de COMPRA (factura proveedor) por artículo ─────────────
        $comprasQuery = $db->table('detalledefacturasdecompra as d')
            ->join('facturasdecompra as f', function ($j) {
                $j->on('f.CodigoDeEmpresaPropia', '=', 'd.CodigoDeEmpresaPropia')
                  ->on('f.Serie', '=', 'd.Serie')
                  ->on('f.Numero', '=', 'd.Numero');
            })
            ->select([
                'd.CodigoDeProducto',
                DB::raw('SUM(d.Cantidad * d.Precio) / SUM(d.Cantidad) as precio_compra'),
                DB::raw('SUM(d.Cantidad) as uds_compradas'),
            ])
            ->whereIn('d.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(f.FechaYHoraDeFactura)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $comprasQuery->where('f.CodigoDeEstacion', $stationCode);
        }

        $compras = $comprasQuery->groupBy('d.CodigoDeProducto')->get()->keyBy('CodigoDeProducto');

        if ($compras->isEmpty()) return [];

        $codigosConCompra = $compras->keys()->toArray();

        // ── 2. Precio de VENTA real (TPV) por artículo ───────────────────────
        $ventasQuery = $db->table('detalledeventasencurso as dv')
            ->join('ventasencurso as v', 'v.Id', '=', 'dv.IdDeVentaEnCurso')
            ->select([
                'dv.CodigoDeProducto',
                DB::raw('SUM(dv.Cantidad * dv.Precio) / SUM(dv.Cantidad) as precio_venta'),
                DB::raw('MAX(dv.PorcentajeDeIva) as pct_iva'),
                DB::raw('SUM(dv.Cantidad) as uds_vendidas'),
                DB::raw('SUM(dv.Importe) as total_facturado'),
            ])
            ->whereIn('dv.CodigoDeProducto', $codigosConCompra)
            ->whereBetween(DB::raw('DATE(v.FechaYHora)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $ventasQuery->where('v.CoDigoDeEstacion', $stationCode);
        }

        $ventas = $ventasQuery->groupBy('dv.CodigoDeProducto')->get()->keyBy('CodigoDeProducto');

        // ── 3. Nombre de productos ────────────────────────────────────────────
        $productos = $db->table('productos as p')
            ->join('gruposdeproductos as g', 'g.Codigo', '=', 'p.CodigoDeGrupo')
            ->select(['p.Codigo', 'p.Descripcion', 'g.Nombre as GrupoNombre'])
            ->whereIn('p.Codigo', $codigosConCompra)
            ->get()
            ->keyBy('Codigo');

        // ── 4. Construir resultado ────────────────────────────────────────────
        $result = [];

        foreach ($compras as $codigo => $compra) {
            $producto = $productos[$codigo] ?? null;
            if (!$producto) continue;

            $precioCompra = (float) $compra->precio_compra;
            if ($precioCompra <= 0) continue;

            $venta          = $ventas[$codigo] ?? null;
            $precioVenta    = $venta ? (float) $venta->precio_venta    : null;
            $udsVendidas    = $venta ? (float) $venta->uds_vendidas    : 0;
            $totalFacturado = $venta ? (float) $venta->total_facturado : 0;
            $pctIva         = $venta ? (float) $venta->pct_iva         : 0;

            // Coste de lo vendido = precio compra × unidades vendidas
            $totalComprado  = $udsVendidas > 0 ? $precioCompra * $udsVendidas : 0;

            // Beneficio y margen
            $beneficio  = $totalFacturado > 0 ? $totalFacturado - $totalComprado : null;
            $margenPct  = ($beneficio !== null && $totalComprado > 0)
                ? ($beneficio / $totalComprado) * 100
                : null;

            $result[] = [
                'codigo'          => $codigo,
                'descripcion'     => $producto->Descripcion,
                'grupo_nombre'    => $producto->GrupoNombre,

                'precio_compra'   => round($precioCompra, 4),
                'precio_venta'    => $precioVenta !== null ? round($precioVenta, 4) : null,
                'uds_compradas'   => (float) $compra->uds_compradas,
                'uds_vendidas'    => $udsVendidas,

                'total_comprado'  => round($totalComprado, 2),
                'total_facturado' => round($totalFacturado, 2),
                'beneficio'       => $beneficio !== null ? round($beneficio, 2) : null,
                'margen_pct'      => $margenPct !== null ? round($margenPct, 2) : null,

                'sin_ventas'      => $venta === null,
            ];
        }

        // Ordenar: artículos con ventas primero, luego por beneficio descendente
        usort($result, function ($a, $b) {
            if ($a['sin_ventas'] !== $b['sin_ventas']) return $a['sin_ventas'] ? 1 : -1;
            return ($b['beneficio'] ?? -999999) <=> ($a['beneficio'] ?? -999999);
        });

        return $result;
    }


    /**
     * Informe de Margen Real Compra vs Venta.
     *
     * Cruza el precio real de compra (factura de proveedor) con el precio
     * real de venta (ticket de TPV) en el mismo período, artículo a artículo.
     *
     * Devuelve por artículo:
     *  - precio_compra_medio  : media ponderada de lo que se pagó al proveedor (sin IVA)
     *  - precio_venta_medio   : media ponderada de lo que se cobró al cliente (con IVA)
     *  - pvp_sin_iva          : precio_venta_medio sin IVA
     *  - margen_real_pct      : ((pvp_sin_iva - precio_compra) / precio_compra) * 100
     *  - beneficio_bruto      : (pvp_sin_iva - precio_compra) * uds_vendidas
     *  - uds_compradas / uds_vendidas
     */

    public function getMargenConVentas(
        int $startMonth,
        int $startYear,
        int $endMonth,
        int $endYear,
        ?int   $stationCode = null,
        ?array $groupCodes  = null
    ): array {
        $dateFrom   = Carbon::create($startYear, $startMonth, 1)->startOfMonth()->format('Y-m-d');
        $dateTo     = Carbon::create($endYear, $endMonth, 1)->endOfMonth()->format('Y-m-d');
        $groupCodes = $groupCodes ?: ['3', '4'];

        $db = DB::connection('virtusgesnet');

        // Productos de los grupos seleccionados
        $productosDeGrupo = $db->table('productos as p')
            ->join('gruposdeproductos as g', 'g.Codigo', '=', 'p.CodigoDeGrupo')
            ->whereIn('p.CodigoDeGrupo', $groupCodes)
            ->pluck('p.Codigo')
            ->toArray();

        if (empty($productosDeGrupo)) return [];

        // ── 1. Precios de COMPRA (factura proveedor) en el período ───────────
        $comprasQuery = $db->table('detalledefacturasdecompra as d')
            ->join('facturasdecompra as f', function ($j) {
                $j->on('f.CodigoDeEmpresaPropia', '=', 'd.CodigoDeEmpresaPropia')
                  ->on('f.Serie', '=', 'd.Serie')
                  ->on('f.Numero', '=', 'd.Numero');
            })
            ->select([
                'd.CodigoDeProducto',
                DB::raw('SUM(d.Cantidad * d.Precio) / SUM(d.Cantidad) as precio_compra_medio'),
                DB::raw('MAX(d.PorcentajeDeIVA) as pct_iva_compra'),
                DB::raw('SUM(d.Cantidad) as uds_compradas'),
                DB::raw('SUM(d.Importe) as coste_total'),
            ])
            ->whereIn('d.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(f.FechaYHoraDeFactura)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $comprasQuery->where('f.CodigoDeEstacion', $stationCode);
        }

        $compras = $comprasQuery->groupBy('d.CodigoDeProducto')->get()->keyBy('CodigoDeProducto');

        if ($compras->isEmpty()) {
            return [];
        }

        $productCodes = $compras->keys()->toArray();

        // ── 2. Precios de VENTA real (ticket TPV) en el mismo período ────────
        $ventasQuery = $db->table('detalledeventasencurso as dv')
            ->join('ventasencurso as v', 'v.Id', '=', 'dv.IdDeVentaEnCurso')
            ->select([
                'dv.CodigoDeProducto',
                DB::raw('SUM(dv.Cantidad * dv.Precio) / SUM(dv.Cantidad) as precio_venta_medio'),
                DB::raw('MAX(dv.PorcentajeDeIva) as pct_iva_venta'),
                DB::raw('SUM(dv.Cantidad) as uds_vendidas'),
                DB::raw('SUM(dv.Importe) as ingreso_total'),
            ])
            ->whereIn('dv.CodigoDeProducto', $productCodes)
            ->whereBetween(DB::raw('DATE(v.FechaYHora)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $ventasQuery->where('v.CoDigoDeEstacion', $stationCode);
        }

        $ventas = $ventasQuery->groupBy('dv.CodigoDeProducto')->get()->keyBy('CodigoDeProducto');

        // ── 3. Información de productos ───────────────────────────────────────
        $productos = $db->table('productos as p')
            ->join('gruposdeproductos as g', 'g.Codigo', '=', 'p.CodigoDeGrupo')
            ->select(['p.Codigo', 'p.Descripcion', 'g.Nombre as GrupoNombre'])
            ->whereIn('p.Codigo', $productCodes)
            ->get()
            ->keyBy('Codigo');

        // ── 4. PVP teórico actual (de preciosdeproductos) ─────────────────────
        $pvpRows = $db->table('preciosdeproductos')
            ->select(['CodigoProducto', 'PVP', 'AmbitoDeEstaciones', 'CodigoEstacion'])
            ->whereIn('CodigoProducto', $productCodes)
            ->get();

        $pvpIndex = [];
        foreach ($pvpRows as $pvp) {
            $code = $pvp->CodigoProducto;
            if ($pvp->AmbitoDeEstaciones === 'Todas las estaciones' && !isset($pvpIndex[$code])) {
                $pvpIndex[$code] = (float) $pvp->PVP;
            }
            if ($stationCode !== null
                && $pvp->AmbitoDeEstaciones === 'Estacion'
                && (int) $pvp->CodigoEstacion === $stationCode
            ) {
                $pvpIndex[$code] = (float) $pvp->PVP;
            }
        }

        // ── 5. Construir resultado ────────────────────────────────────────────
        $result = [];

        foreach ($compras as $codigo => $compra) {
            $producto = $productos[$codigo] ?? null;
            if (!$producto) continue;

            $precioCompra = (float) $compra->precio_compra_medio;
            $pctIvaC      = (float) $compra->pct_iva_compra;
            if ($precioCompra <= 0) continue;

            // Datos de venta real
            $ventaRow       = $ventas[$codigo] ?? null;
            $pvpVentaConIva = $ventaRow ? (float) $ventaRow->precio_venta_medio : null;
            $pctIvaV        = $ventaRow ? (float) $ventaRow->pct_iva_venta : $pctIvaC;
            $udsVendidas    = $ventaRow ? (float) $ventaRow->uds_vendidas : 0;
            $ingresoTotal   = $ventaRow ? (float) $ventaRow->ingreso_total : 0;

            // PVP sin IVA (precio cobrado al cliente descontando IVA)
            $divisorIvaV  = 1 + ($pctIvaV / 100);
            $pvpVentaSinIva = $pvpVentaConIva !== null ? $pvpVentaConIva / $divisorIvaV : null;

            // PVP teórico actual (de preciosdeproductos)
            $pvpTeorico     = $pvpIndex[$codigo] ?? null;
            $pvpTeoricoSin  = $pvpTeorico !== null ? $pvpTeorico / $divisorIvaV : null;

            // Márgenes
            $margenRealPct   = $pvpVentaSinIva !== null
                ? (($pvpVentaSinIva - $precioCompra) / $precioCompra) * 100
                : null;

            $margenTeoPct    = $pvpTeoricoSin !== null
                ? (($pvpTeoricoSin - $precioCompra) / $precioCompra) * 100
                : null;

            // Beneficio bruto estimado = (PVP_sin_IVA - P_compra) × uds_vendidas
            $beneficioBruto = ($margenRealPct !== null && $udsVendidas > 0)
                ? ($pvpVentaSinIva - $precioCompra) * $udsVendidas
                : null;

            // Ingresos sin IVA total
            $ingresoSinIva = $ingresoTotal / $divisorIvaV;
            $costeTotal    = $udsVendidas > 0
                ? $precioCompra * $udsVendidas
                : (float) $compra->coste_total;

            $result[] = [
                'codigo'             => $codigo,
                'descripcion'        => $producto->Descripcion,
                'grupo_nombre'       => $producto->GrupoNombre,
                'pct_iva'            => $pctIvaV,

                // Compra
                'precio_compra'      => round($precioCompra, 4),
                'uds_compradas'      => (float) $compra->uds_compradas,
                'coste_total'        => round((float) $compra->coste_total, 2),

                // Venta real (TPV)
                'pvp_venta_con_iva'  => $pvpVentaConIva !== null ? round($pvpVentaConIva, 4) : null,
                'pvp_venta_sin_iva'  => $pvpVentaSinIva !== null ? round($pvpVentaSinIva, 4) : null,
                'uds_vendidas'       => $udsVendidas,
                'ingreso_total'      => round($ingresoTotal, 2),
                'ingreso_sin_iva'    => round($ingresoSinIva, 2),

                // PVP teórico actual (tarifa)
                'pvp_tarifa_con_iva' => $pvpTeorico !== null ? round($pvpTeorico, 4) : null,
                'pvp_tarifa_sin_iva' => $pvpTeoricoSin !== null ? round($pvpTeoricoSin, 4) : null,

                // Márgenes
                'margen_real_pct'    => $margenRealPct !== null ? round($margenRealPct, 2) : null,
                'margen_tarifa_pct'  => $margenTeoPct  !== null ? round($margenTeoPct, 2) : null,
                'beneficio_bruto'    => $beneficioBruto !== null ? round($beneficioBruto, 2) : null,

                // Flag: vendido sin datos de venta
                'sin_ventas'         => $ventaRow === null,
            ];
        }

        // Ordenar: primero artículos con ventas, luego por margen real descendente
        usort($result, function ($a, $b) {
            if ($a['sin_ventas'] !== $b['sin_ventas']) return $a['sin_ventas'] ? 1 : -1;
            return ($b['margen_real_pct'] ?? -999) <=> ($a['margen_real_pct'] ?? -999);
        });

        return $result;
    }


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
        ?int   $stationCode = null,
        ?array $groupCodes  = null
    ): array {
        $dateFrom   = Carbon::create($startYear, $startMonth, 1)->startOfMonth()->format('Y-m-d');
        $dateTo     = Carbon::create($endYear, $endMonth, 1)->endOfMonth()->format('Y-m-d');
        $groupCodes = $groupCodes ?: ['3', '4'];

        $db = DB::connection('virtusgesnet');

        // Productos de los grupos seleccionados (incluyendo subgrupos)
        $productosDeGrupo = $db->table('productos as p')
            ->where(function ($q) use ($groupCodes) {
                foreach ($groupCodes as $gc) {
                    $q->orWhere('p.CodigoDeGrupo', 'like', $gc . '%');
                }
            })
            ->pluck('p.Codigo')
            ->toArray();

        if (empty($productosDeGrupo)) return [];

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
            ->whereIn('d.CodigoDeProducto', $productosDeGrupo)
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

    /**
     * Devuelve la evolución histórica (agrupada por mes) de compras, ventas y beneficio.
     */
    public function getEvolucionMensual(
        int $startMonth,
        int $startYear,
        int $endMonth,
        int $endYear,
        array $groupCodes,
        ?int $stationCode = null
    ): array {
        $dateFrom = Carbon::create($startYear, $startMonth, 1)->startOfMonth()->format('Y-m-d');
        $dateTo   = Carbon::create($endYear, $endMonth, 1)->endOfMonth()->format('Y-m-d');

        $db = DB::connection('virtusgesnet');

        $productosDeGrupo = $db->table('productos')
            ->whereIn('CodigoDeGrupo', $groupCodes)
            ->pluck('Codigo')
            ->toArray();

        if (empty($productosDeGrupo)) return [];

        // Compras por mes
        $comprasQuery = $db->table('detalledefacturasdecompra as d')
            ->join('facturasdecompra as f', function ($j) {
                $j->on('f.CodigoDeEmpresaPropia', '=', 'd.CodigoDeEmpresaPropia')
                  ->on('f.Serie', '=', 'd.Serie')
                  ->on('f.Numero', '=', 'd.Numero');
            })
            ->select([
                DB::raw('YEAR(f.FechaYHoraDeFactura) as anio'),
                DB::raw('MONTH(f.FechaYHoraDeFactura) as mes'),
                DB::raw('SUM(d.Importe) as coste_total'),
            ])
            ->whereIn('d.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(f.FechaYHoraDeFactura)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $comprasQuery->where('f.CodigoDeEstacion', $stationCode);
        }

        $compras = $comprasQuery->groupBy('anio', 'mes')->get();

        // Ventas por mes
        $ventasQuery = $db->table('detalledeventasencurso as dv')
            ->join('ventasencurso as v', 'v.Id', '=', 'dv.IdDeVentaEnCurso')
            ->select([
                DB::raw('YEAR(v.FechaYHora) as anio'),
                DB::raw('MONTH(v.FechaYHora) as mes'),
                DB::raw('SUM(dv.Importe) as ingreso_total'),
            ])
            ->whereIn('dv.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(v.FechaYHora)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $ventasQuery->where('v.CoDigoDeEstacion', $stationCode);
        }

        $ventas = $ventasQuery->groupBy('anio', 'mes')->get();

        // Construir evolución
        $evolucion = [];

        // Generar todos los meses en el rango
        $current = Carbon::create($startYear, $startMonth, 1)->startOfMonth();
        $end = Carbon::create($endYear, $endMonth, 1)->startOfMonth();
        
        while ($current <= $end) {
            $key = $current->format('Y-m');
            $evolucion[$key] = [
                'mes' => ucfirst($current->translatedFormat('M Y')), // ej: Ene 2024
                'coste' => 0,
                'ingreso' => 0,
                'beneficio' => 0,
                'margen_pct' => 0,
            ];
            $current->addMonth();
        }

        foreach ($compras as $c) {
            $key = sprintf('%04d-%02d', $c->anio, $c->mes);
            if (isset($evolucion[$key])) {
                $evolucion[$key]['coste'] += (float) $c->coste_total;
            }
        }

        foreach ($ventas as $v) {
            $key = sprintf('%04d-%02d', $v->anio, $v->mes);
            if (isset($evolucion[$key])) {
                $evolucion[$key]['ingreso'] += (float) $v->ingreso_total;
            }
        }

        // Calcular beneficios y márgenes
        foreach ($evolucion as $key => &$data) {
            $data['beneficio'] = $data['ingreso'] - $data['coste'];
            $data['margen_pct'] = $data['coste'] > 0 ? ($data['beneficio'] / $data['coste']) * 100 : 0;
            
            // Rounding
            $data['coste'] = round($data['coste'], 2);
            $data['ingreso'] = round($data['ingreso'], 2);
            $data['beneficio'] = round($data['beneficio'], 2);
            $data['margen_pct'] = round($data['margen_pct'], 2);
        }

        return array_values($evolucion);
    }
}
