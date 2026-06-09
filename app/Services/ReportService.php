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
            ->where(function ($q) use ($groupCodes) {
                foreach ($groupCodes as $gc) {
                    $q->orWhere('CodigoDeGrupo', 'like', $gc . '%');
                }
            })
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
                DB::raw('MAX(d.PorcentajeDeIVA) as pct_iva_compra'),
                DB::raw('SUM(d.Cantidad) as uds_compradas'),
                DB::raw('MAX(f.FechaYHoraDeFactura) as fecha_ultima_compra'),
            ])
            ->whereIn('d.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(f.FechaYHoraDeFactura)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $comprasQuery->where('f.CodigoDeEstacion', $stationCode);
        }

        $compras = $comprasQuery->groupBy('d.CodigoDeProducto')->get()->keyBy('CodigoDeProducto');

        // ── 2. Precio de VENTA real (TPV) por artículo ───────────────────────
        $ventasQuery = $db->table('detalledefacturasyticketsdeventa as dv')
            ->join('facturasyticketsdeventa as v', function ($j) {
                $j->on('v.CodigoDeEmpresaPropia', '=', 'dv.CodigoDeEmpresaPropia')
                  ->on('v.Serie', '=', 'dv.Serie')
                  ->on('v.Numero', '=', 'dv.Numero');
            })
            ->select([
                'dv.CodigoDeProducto',
                DB::raw('SUM(dv.Cantidad * dv.Precio) / SUM(dv.Cantidad) as precio_venta'),
                DB::raw('MAX(dv.PorcentajeDeIva) as pct_iva'),
                DB::raw('SUM(dv.Cantidad) as uds_vendidas'),
                DB::raw('SUM(dv.Importe) as total_facturado'),
            ])
            ->whereIn('dv.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(v.FechaYHora)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $ventasQuery->where('v.CodigoDeEstacion', $stationCode);
        }

        $ventas = $ventasQuery->groupBy('dv.CodigoDeProducto')->get()->keyBy('CodigoDeProducto');

        $codigosActivos = array_unique(array_merge($compras->keys()->toArray(), $ventas->keys()->toArray()));

        if (empty($codigosActivos)) return [];

        // ── 3. Nombre de productos ────────────────────────────────────────────
        $productos = $db->table('productos as p')
            ->join('gruposdeproductos as g', 'g.Codigo', '=', 'p.CodigoDeGrupo')
            ->select(['p.Codigo', 'p.Descripcion', 'g.Nombre as GrupoNombre'])
            ->whereIn('p.Codigo', $codigosActivos)
            ->get()
            ->keyBy('Codigo');

        // ── 4. Construir resultado ────────────────────────────────────────────
        $result = [];

        foreach ($codigosActivos as $codigo) {
            $producto = $productos[$codigo] ?? null;
            if (!$producto) continue;

            $compra         = $compras[$codigo] ?? null;
            $precioCompra   = $compra ? (float) $compra->precio_compra : 0.0;
            $pctIvaCompra   = $compra ? (float) $compra->pct_iva_compra : 0.0;
            $udsCompradas   = $compra ? (float) $compra->uds_compradas : 0.0;
            $fechaUltCompra = $compra ? $compra->fecha_ultima_compra : null;

            $precioCompraConIva = $precioCompra * (1 + $pctIvaCompra / 100);

            $venta          = $ventas[$codigo] ?? null;
            $precioVenta    = $venta ? (float) $venta->precio_venta    : null;
            $udsVendidas    = $venta ? (float) $venta->uds_vendidas    : 0;
            $totalFacturado = $venta ? (float) $venta->total_facturado : 0;
            $pctIva         = $venta ? (float) $venta->pct_iva         : 0;

            // Coste de lo vendido = precio compra × unidades vendidas
            $totalComprado  = $udsVendidas > 0 ? $precioCompra * $udsVendidas : 0;

            // Beneficio y margen
            $beneficio  = $totalFacturado > 0 ? $totalFacturado - $totalComprado : ($udsVendidas > 0 ? 0 - $totalComprado : null);
            
            $margenPct  = null;
            if ($totalComprado > 0) {
                $margenPct = ($beneficio / $totalComprado) * 100;
            } elseif ($totalFacturado > 0 && $precioCompra <= 0) {
                $margenPct = 100.0; // 100% de margen si no hay coste
            }

            $result[] = [
                'codigo'          => $codigo,
                'descripcion'     => $producto->Descripcion,
                'grupo_nombre'    => $producto->GrupoNombre,

                'precio_compra'          => round($precioCompra, 4),
                'pct_iva_compra'         => round($pctIvaCompra, 1),
                'precio_compra_con_iva'  => round($precioCompraConIva, 4),
                'precio_venta'    => $precioVenta !== null ? round($precioVenta, 4) : null,
                'uds_compradas'   => $udsCompradas,
                'uds_vendidas'    => $udsVendidas,

                'total_comprado'  => round($totalComprado, 2),
                'total_facturado' => round($totalFacturado, 2),
                'beneficio'       => $beneficio !== null ? round($beneficio, 2) : null,
                'margen_pct'      => $margenPct !== null ? round($margenPct, 2) : null,

                'pct_iva'              => round($pctIva, 1),
                'precio_venta_sin_iva' => $precioVenta !== null && $pctIva > 0
                    ? round($precioVenta / (1 + $pctIva / 100), 4)
                    : $precioVenta,
                'fecha_ultima_compra'  => $fechaUltCompra
                    ? \Carbon\Carbon::parse($fechaUltCompra)->locale('es')->isoFormat('MMM YYYY')
                    : null,

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
            ->where(function ($q) use ($groupCodes) {
                foreach ($groupCodes as $gc) {
                    $q->orWhere('p.CodigoDeGrupo', 'like', $gc . '%');
                }
            })
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
        $ventasQuery = $db->table('detalledefacturasyticketsdeventa as dv')
            ->join('facturasyticketsdeventa as v', function ($j) {
                $j->on('v.CodigoDeEmpresaPropia', '=', 'dv.CodigoDeEmpresaPropia')
                  ->on('v.Serie', '=', 'dv.Serie')
                  ->on('v.Numero', '=', 'dv.Numero');
            })
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
            $ventasQuery->where('v.CodigoDeEstacion', $stationCode);
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
                'precio_compra'          => round($precioCompra, 4),
                'pct_iva_compra'         => round($pctIvaC, 1),
                'precio_compra_con_iva'  => round($precioCompra * (1 + $pctIvaC / 100), 4),
                'uds_compradas'          => (float) $compra->uds_compradas,
                'coste_total'            => round((float) $compra->coste_total, 2),

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

            $precioCompraConIva = $precioCompra * (1 + $pctIva / 100);

            $divisorIva = 1 + ($pctIva / 100);
            $pvpSinIva  = $pvpConIva / $divisorIva;
            $margenPct  = (($pvpSinIva - $precioCompra) / $precioCompra) * 100;

            $result[] = [
                'codigo'             => $codigo,
                'descripcion'        => $producto->Descripcion,
                'grupo_codigo'       => $producto->GrupoCodigo,
                'grupo_nombre'       => $producto->GrupoNombre,
                'precio_compra'          => round($precioCompra, 4),
                'pct_iva_compra'         => round($pctIva, 1),
                'precio_compra_con_iva'  => round($precioCompraConIva, 4),
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
     * Obtiene los datos crudos de futuros y tipo de cambio desde Yahoo Finance.
     * Cache de 30 minutos.
     */
    public function getRawFuturesData(?string $futuresRange = '30d'): array
    {
        $rangesConf = [
            '30d' => ['range' => '1mo', 'interval' => '1d'],
            '6m'  => ['range' => '6mo', 'interval' => '1wk'],
            '1y'  => ['range' => '1y', 'interval' => '1wk'],
        ];
        $conf = $rangesConf[$futuresRange] ?? $rangesConf['30d'];

        return Cache::remember("futures_raw_data_v3_{$futuresRange}", 1800, function () use ($conf) {
            $usdToEurRate = 0.92; // default fallback
            try {
                $rateUrl = "https://query2.finance.yahoo.com/v8/finance/chart/EUR=X?interval=1d&range=1d";
                $rateResponse = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                    ->get($rateUrl);
                if ($rateResponse->successful()) {
                    $ratePrice = $rateResponse->json()['chart']['result'][0]['meta']['regularMarketPrice'] ?? null;
                    if ($ratePrice > 0) {
                        $usdToEurRate = (float) $ratePrice;
                    }
                }
            } catch (\Exception $e) {
                report($e);
            }

            $symbols = [
                'RB=F' => ['nombre' => 'Gasolina RBOB', 'unidad' => 'USD/gal', 'icono' => '⛽', 'tipo' => 'gas95'],
                'HO=F' => ['nombre' => 'Gasoil (Diésel ref.)', 'unidad' => 'USD/gal', 'icono' => '🛢️', 'tipo' => 'diesel'],
            ];

            $results = [
                'usd_to_eur' => $usdToEurRate,
                'symbols' => [],
            ];

            foreach ($symbols as $symbol => $meta) {
                try {
                    $url = "https://query2.finance.yahoo.com/v8/finance/chart/{$symbol}?range={$conf['range']}&interval={$conf['interval']}";
                    $response = \Illuminate\Support\Facades\Http::timeout(5)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                        ->get($url);

                    if ($response->failed()) {
                        continue;
                    }

                    $resData = $response->json();
                    $metaInfo = $resData['chart']['result'][0]['meta'] ?? null;
                    $closePrices = $resData['chart']['result'][0]['indicators']['quote'][0]['close'] ?? [];
                    $timestamps = $resData['chart']['result'][0]['timestamp'] ?? [];

                    $validClosePrices = [];
                    $validTimestamps = [];
                    foreach ($closePrices as $idx => $p) {
                        if ($p !== null) {
                            $validClosePrices[] = $p;
                            $validTimestamps[] = $timestamps[$idx] ?? null;
                        }
                    }

                    if (!$metaInfo || empty($validClosePrices)) {
                        continue;
                    }

                    $precio = (float) ($metaInfo['regularMarketPrice'] ?? end($validClosePrices));
                    $prevClose = (float) ($metaInfo['chartPreviousClose'] ?? 0);

                    $results['symbols'][$symbol] = [
                        'nombre' => $meta['nombre'],
                        'icono' => $meta['icono'],
                        'tipo' => $meta['tipo'],
                        'precio_usd_gal' => $precio,
                        'prev_close_usd_gal' => $prevClose,
                        'close_prices_usd_gal' => $validClosePrices,
                        'timestamps' => $validTimestamps,
                        'first_timestamp' => !empty($validTimestamps) ? $validTimestamps[0] : null,
                        'last_timestamp' => !empty($validTimestamps) ? end($validTimestamps) : null,
                    ];
                } catch (\Exception $e) {
                    report($e);
                }
            }

            return $results;
        });
    }

    /**
     * Obtiene los precios de futuros de combustible estimando el PVP en España (€/L).
     */
    public function getFuturesPrices(?string $selectedLocality = 'espana', ?array $currentPrices = null, ?string $futuresRange = '30d'): array
    {
        $rawData = $this->getRawFuturesData($futuresRange);
        $usdToEurRate = $rawData['usd_to_eur'] ?? 0.92;
        $symbolsData = $rawData['symbols'] ?? [];

        // Impuestos especiales de España (€/L)
        // Gasolina 95: 0.47269 €/L
        // Diésel A: 0.37900 €/L
        $taxes = [
            'gas95' => 0.47269,
            'diesel' => 0.37900,
        ];

        $results = [];

        foreach ($symbolsData as $symbol => $data) {
            $tipo = $data['tipo'];
            $specialTax = $taxes[$tipo] ?? 0.379;

            // 1. Convertir precios de USD/gal a EUR/L
            $galToLiter = 3.785411784;
            $convFactor = $usdToEurRate / $galToLiter;

            $currentPriceUsdGal = $data['precio_usd_gal'];
            $currentPriceEurL = $currentPriceUsdGal * $convFactor;

            $closePricesUsdGal = $data['close_prices_usd_gal'];
            $closePricesEurL = array_map(fn($p) => $p * $convFactor, $closePricesUsdGal);

            // 2. Calcular el Margen Implícito actual
            // PVP Medio Surtidor Real hoy en la localidad
            $realRetailPrice = (float) ($currentPrices[$tipo] ?? 0);
            if ($realRetailPrice <= 0) {
                // Fallback razonable de PVP real si no hay estaciones en la zona
                $realRetailPrice = $tipo === 'gas95' ? 1.60 : 1.55;
            }

            // Margin = (PVP_real / 1.21) - Future_EurL - SpecialTax
            $margin = ($realRetailPrice / 1.21) - $currentPriceEurL - $specialTax;
            if ($margin < 0.05) {
                $margin = 0.15; // fallback de margen mínimo (15 céntimos)
            }

            // 3. Proyectar serie de precios estimados en surtidor (€/L)
            $estimatedPrices = array_map(function($fPrice) use ($specialTax, $margin) {
                return ($fPrice + $specialTax + $margin) * 1.21;
            }, $closePricesEurL);

            // Precio estimado actual en surtidor
            $currentEstRetail = end($estimatedPrices);
            
            // Variaciones del precio estimado en surtidor
            $count = count($estimatedPrices);
            
            // Cambio Diario (último vs anterior)
            $cambioDiario = 0;
            $cambioDiarioPct = 0;
            if ($count > 1) {
                $prevDay = $estimatedPrices[$count - 2];
                $cambioDiario = $currentEstRetail - $prevDay;
                $cambioDiarioPct = (($currentEstRetail - $prevDay) / $prevDay) * 100;
            }

            // Cambio Semanal (7d, o sea, hace 7 días de mercado)
            $weeklyChangePct = 0;
            if ($count >= 7) {
                $prev7d = $estimatedPrices[$count - 7];
                $weeklyChangePct = (($currentEstRetail - $prev7d) / $prev7d) * 100;
            }

            // Cambio Mensual (30d, o sea, primer elemento de la serie vs último)
            $monthlyChangePct = 0;
            if ($count > 1) {
                $prev30d = $estimatedPrices[0];
                $monthlyChangePct = (($currentEstRetail - $prev30d) / $prev30d) * 100;
            }

            // 4. Predicción de tendencia a corto plazo (próximos 3-5 días)
            // Calculamos la diferencia entre el PVP estimado por el futuro y el PVP real actual de los surtidores de la zona.
            // Esta brecha indica cuánto debe corregir el precio de venta local para alinearse con el precio internacional.
            $predictedAdjustment = $currentEstRetail - $realRetailPrice;
            $formattedAdj = number_format(abs($predictedAdjustment), 3, ',', '.') . ' €/L';

            if ($predictedAdjustment > 0.005) {
                $prediccionClase = 'up';
                $prediccionIcono = '▲';
                $prediccionColor = 'text-amber-700 bg-amber-50 border border-amber-200/60';
                $prediccionLabel = 'Subida Probable: +' . $formattedAdj . ' (3-5d)';
            } elseif ($predictedAdjustment < -0.005) {
                $prediccionClase = 'down';
                $prediccionIcono = '▼';
                $prediccionColor = 'text-green-700 bg-green-50 border border-green-200/60';
                $prediccionLabel = 'Bajada Probable: -' . $formattedAdj . ' (3-5d)';
            } else {
                $prediccionClase = 'stable';
                $prediccionIcono = '◀▶';
                $prediccionColor = 'text-slate-600 bg-slate-50 border border-slate-200/60';
                $prediccionLabel = 'Tendencia Estable (3-5d)';
            }

            // 5. Generar Sparkline SVG interactivo
            $sparklineInfo = $this->getSparklineData($estimatedPrices, $data['timestamps'] ?? [], 120, 36);

            $fechaInicio = isset($data['first_timestamp']) && $data['first_timestamp'] ? Carbon::createFromTimestamp($data['first_timestamp'])->locale('es')->isoFormat('D [de] MMMM') : '';
            $fechaFin = isset($data['last_timestamp']) && $data['last_timestamp'] ? Carbon::createFromTimestamp($data['last_timestamp'])->locale('es')->isoFormat('D [de] MMMM') : '';
            $rangoFechas = $fechaInicio && $fechaFin ? "{$fechaInicio} al {$fechaFin}" : 'Histórico';

            $results[$symbol] = [
                'nombre' => $data['nombre'] . ($tipo === 'gas95' ? ' (Super 95)' : ' (Diésel A)'),
                'icono' => $data['icono'],
                'tipo' => $tipo,
                'precio_futuro' => $currentPriceEurL, // €/L
                'precio_estimado_surtidor' => $currentEstRetail, // €/L estimado
                'cambio' => $cambioDiario,
                'cambioPct' => $cambioDiarioPct,
                'positivo' => $cambioDiario >= 0,
                'weeklyChangePct' => $weeklyChangePct,
                'monthlyChangePct' => $monthlyChangePct,
                'sparklinePoints' => $sparklineInfo['points_string'],
                'sparklinePointsArray' => $sparklineInfo['points_array'],
                'rango_fechas' => $rangoFechas,
                'prediccion_clase' => $prediccionClase,
                'prediccion_icono' => $prediccionIcono,
                'prediccion_color' => $prediccionColor,
                'prediccion_label' => $prediccionLabel,
                'currency' => 'EUR',
                'unidad' => '€/L',
            ];
        }

        return $results;
    }

    /**
     * Genera los puntos de la sparkline SVG y un array con la información de cada punto para interactividad.
     */
    private function getSparklineData(array $prices, array $timestamps, int $width = 120, int $height = 36, int $padding = 2): array
    {
        $prices = array_values($prices);
        $timestamps = array_values($timestamps);
        $count = count($prices);
        if ($count < 2) {
            return [
                'points_string' => '',
                'points_array' => [],
            ];
        }

        $min = min($prices);
        $max = max($prices);
        $range = $max - $min;
        if ($range == 0) {
            $range = 1;
        }

        $pointsString = [];
        $pointsArray = [];
        $xStep = ($width - 2 * $padding) / ($count - 1);

        for ($i = 0; $i < $count; $i++) {
            $x = $padding + ($i * $xStep);
            $y = ($height - $padding) - (($prices[$i] - $min) / $range) * ($height - 2 * $padding);
            $xRound = round($x, 1);
            $yRound = round($y, 1);

            $pointsString[] = $xRound . ',' . $yRound;

            $timestamp = $timestamps[$i] ?? null;
            $dateFormatted = $timestamp
                ? \Carbon\Carbon::createFromTimestamp($timestamp)->locale('es')->isoFormat('D MMM YY')
                : '';

            $pointsArray[] = [
                'x' => $xRound,
                'y' => $yRound,
                'price' => number_format($prices[$i], 3, ',', '.'),
                'date' => $dateFormatted,
            ];
        }

        return [
            'points_string' => implode(' ', $pointsString),
            'points_array' => $pointsArray,
        ];
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
            ->where(function ($q) use ($groupCodes) {
                foreach ($groupCodes as $gc) {
                    $q->orWhere('CodigoDeGrupo', 'like', $gc . '%');
                }
            })
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
        $ventasQuery = $db->table('detalledefacturasyticketsdeventa as dv')
            ->join('facturasyticketsdeventa as v', function ($j) {
                $j->on('v.CodigoDeEmpresaPropia', '=', 'dv.CodigoDeEmpresaPropia')
                  ->on('v.Serie', '=', 'dv.Serie')
                  ->on('v.Numero', '=', 'dv.Numero');
            })
            ->select([
                DB::raw('YEAR(v.FechaYHora) as anio'),
                DB::raw('MONTH(v.FechaYHora) as mes'),
                DB::raw('SUM(dv.Importe) as ingreso_total'),
            ])
            ->whereIn('dv.CodigoDeProducto', $productosDeGrupo)
            ->whereBetween(DB::raw('DATE(v.FechaYHora)'), [$dateFrom, $dateTo]);

        if ($stationCode !== null) {
            $ventasQuery->where('v.CodigoDeEstacion', $stationCode);
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
