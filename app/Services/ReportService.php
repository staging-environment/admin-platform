<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    private function getDateRange($startMonth, $startYear, $endMonth, $endYear)
    {
        $startMonth = max(1, min(12, (int) $startMonth));
        $endMonth = max(1, min(12, (int) $endMonth));
        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        // Ensure start is before end
        if ($startYear > $endYear || ($startYear === $endYear && $startMonth > $endMonth)) {
            $tmpM = $startMonth;
            $tmpY = $startYear;
            $startMonth = $endMonth;
            $startYear = $endYear;
            $endMonth = $tmpM;
            $endYear = $tmpY;
        }

        $startDate = Carbon::create($startYear, $startMonth, 1)->startOfMonth();
        $endDate = Carbon::create($endYear, $endMonth, 1)->endOfMonth();

        return [$startDate, $endDate];
    }

    /**
     * 1. Margen Económico Mensual (Ventas vs Compras)
     */
    public function getSalesVsPurchasesMargin($startMonth, $startYear, $endMonth, $endYear, $stationCode = null): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        // Ventas
        $sales = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->selectRaw('YEAR(FechaYHora) as year, MONTH(FechaYHora) as month, SUM(ImporteBruto) as total_sales')
            ->whereBetween('FechaYHora', [$startDate, $endDate])
            ->when($stationCode, fn($q) => $q->where('CodigoDeEstacion', $stationCode))
            ->groupByRaw('YEAR(FechaYHora), MONTH(FechaYHora)')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // Compras
        $purchases = DB::connection('virtusgesnet')
            ->table('facturasdecompra')
            ->selectRaw('YEAR(FechaYHoraDeFactura) as year, MONTH(FechaYHoraDeFactura) as month, SUM(ImporteBruto) as total_purchases')
            ->whereBetween('FechaYHoraDeFactura', [$startDate, $endDate])
            ->when($stationCode, fn($q) => $q->where('CodigoDeEstacion', $stationCode))
            ->groupByRaw('YEAR(FechaYHoraDeFactura), MONTH(FechaYHoraDeFactura)')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $labels = [];
        $salesData = [];
        $purchasesData = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m');
            $labels[] = $current->translatedFormat('M Y');
            
            $salesData[] = isset($sales[$key]) ? (float) $sales[$key]->total_sales : 0;
            $purchasesData[] = isset($purchases[$key]) ? (float) $purchases[$key]->total_purchases : 0;
            
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventas (Ingresos)',
                    'data' => $salesData,
                    'backgroundColor' => '#10B981', // green-500
                    'borderColor' => '#059669',
                ],
                [
                    'label' => 'Compras (Gastos)',
                    'data' => $purchasesData,
                    'backgroundColor' => '#EF4444', // red-500
                    'borderColor' => '#DC2626',
                ]
            ]
        ];
    }

    /**
     * 2. Top 10 Clientes por Facturación
     */
    public function getTopClients($startMonth, $startYear, $endMonth, $endYear, $stationCode = null): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $topClients = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->join('clientes', 'facturasyticketsdeventa.CodigoDeCliente', '=', 'clientes.Codigo')
            ->selectRaw('clientes.Nombre, SUM(facturasyticketsdeventa.ImporteTotal) as total')
            ->whereBetween('facturasyticketsdeventa.FechaYHora', [$startDate, $endDate])
            ->whereNotNull('facturasyticketsdeventa.CodigoDeCliente')
            ->when($stationCode, fn($q) => $q->where('facturasyticketsdeventa.CodigoDeEstacion', $stationCode))
            ->groupBy('clientes.Codigo', 'clientes.Nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $topClients->pluck('Nombre')->toArray(),
            'datasets' => [
                [
                    'label' => 'Facturación (€)',
                    'data' => $topClients->pluck('total')->map(fn($v) => round($v, 2))->toArray(),
                    'backgroundColor' => '#3B82F6', // blue-500
                ]
            ]
        ];
    }

    /**
     * 3. Top 10 Proveedores por Volumen de Compra
     */
    public function getTopSuppliers($startMonth, $startYear, $endMonth, $endYear, $stationCode = null): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $topSuppliers = DB::connection('virtusgesnet')
            ->table('facturasdecompra')
            ->join('proveedores', 'facturasdecompra.CodigoDeProveedor', '=', 'proveedores.Codigo')
            ->selectRaw('proveedores.Nombre, SUM(facturasdecompra.ImporteTotal) as total')
            ->whereBetween('facturasdecompra.FechaYHoraDeFactura', [$startDate, $endDate])
            ->when($stationCode, fn($q) => $q->where('facturasdecompra.CodigoDeEstacion', $stationCode))
            ->groupBy('proveedores.Codigo', 'proveedores.Nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $topSuppliers->pluck('Nombre')->toArray(),
            'datasets' => [
                [
                    'label' => 'Volumen de Compra (€)',
                    'data' => $topSuppliers->pluck('total')->map(fn($v) => round($v, 2))->toArray(),
                    'backgroundColor' => '#F59E0B', // amber-500
                ]
            ]
        ];
    }

    /**
     * 4. Comparativa de Facturación por Estación de Servicio
     */
    public function getSalesByStation($startMonth, $startYear, $endMonth, $endYear): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $stations = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->join('estaciones', 'facturasyticketsdeventa.CodigoDeEstacion', '=', 'estaciones.Codigo')
            ->selectRaw('estaciones.Nombre, SUM(facturasyticketsdeventa.ImporteTotal) as total')
            ->whereBetween('facturasyticketsdeventa.FechaYHora', [$startDate, $endDate])
            ->groupBy('estaciones.Codigo', 'estaciones.Nombre')
            ->orderByDesc('total')
            ->get();

        $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#14B8A6'];

        return [
            'labels' => $stations->pluck('Nombre')->map(fn($n) => $n ?: 'Desconocida')->toArray(),
            'datasets' => [
                [
                    'label' => 'Facturación por Estación (€)',
                    'data' => $stations->pluck('total')->map(fn($v) => round($v, 2))->toArray(),
                    'backgroundColor' => array_slice($colors, 0, count($stations)),
                ]
            ]
        ];
    }

    /**
     * 5. Evolución del Ticket Medio Mensual
     */
    public function getAverageTicketEvolution($startMonth, $startYear, $endMonth, $endYear): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $tickets = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->selectRaw('YEAR(FechaYHora) as year, MONTH(FechaYHora) as month, SUM(ImporteTotal) as total_amount, COUNT(*) as ticket_count')
            ->whereBetween('FechaYHora', [$startDate, $endDate])
            ->where('EsTicket', 1)
            ->groupByRaw('YEAR(FechaYHora), MONTH(FechaYHora)')
            ->get()
            ->keyBy(function ($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $labels = [];
        $averageData = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m');
            $labels[] = $current->translatedFormat('M Y');
            
            if (isset($tickets[$key]) && $tickets[$key]->ticket_count > 0) {
                $averageData[] = round($tickets[$key]->total_amount / $tickets[$key]->ticket_count, 2);
            } else {
                $averageData[] = 0;
            }
            
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ticket Medio Mensual (€)',
                    'data' => $averageData,
                    'backgroundColor' => '#8B5CF6', // purple-500
                    'borderColor' => '#7C3AED',
                    'tension' => 0.4,
                    'fill' => false,
                ]
            ]
        ];
    }

    /**
     * 6. Ventas por Medio de Pago (Efectivo vs Tarjeta)
     */
    public function getSalesByPaymentMethod($startMonth, $startYear, $endMonth, $endYear, $stationCode = null): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $methods = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->leftJoin('mediosdepago', 'facturasyticketsdeventa.CodigoDeMedioDePago', '=', 'mediosdepago.Codigo')
            ->selectRaw('COALESCE(mediosdepago.Descripcion, "Otros / Desconocido") as method_name, SUM(facturasyticketsdeventa.ImporteTotal) as total')
            ->whereBetween('facturasyticketsdeventa.FechaYHora', [$startDate, $endDate])
            ->when($stationCode, fn($q) => $q->where('facturasyticketsdeventa.CodigoDeEstacion', $stationCode))
            ->groupBy('mediosdepago.Descripcion')
            ->orderByDesc('total')
            ->get();

        $colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#14B8A6'];

        return [
            'labels' => $methods->pluck('method_name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Facturación por Medio de Pago (€)',
                    'data' => $methods->pluck('total')->map(fn($v) => round((float) $v, 2))->toArray(),
                    'backgroundColor' => array_slice($colors, 0, min(count($methods), count($colors))),
                ]
            ]
        ];
    }

    /**
     * 7. Rendimiento de Empleados (Expendedores)
     */
    public function getTopEmployees($startMonth, $startYear, $endMonth, $endYear, $stationCode = null): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $employees = DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->join('expendedores', 'facturasyticketsdeventa.CodigoDeExpendedor', '=', 'expendedores.Codigo')
            ->selectRaw('expendedores.Nombre, SUM(facturasyticketsdeventa.ImporteTotal) as total')
            ->whereBetween('facturasyticketsdeventa.FechaYHora', [$startDate, $endDate])
            ->when($stationCode, fn($q) => $q->where('facturasyticketsdeventa.CodigoDeEstacion', $stationCode))
            ->groupBy('expendedores.Codigo', 'expendedores.Nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $employees->pluck('Nombre')->toArray(),
            'datasets' => [
                [
                    'label' => 'Facturación (€)',
                    'data' => $employees->pluck('total')->map(fn($v) => round((float) $v, 2))->toArray(),
                    'backgroundColor' => '#14B8A6', // teal-500
                ]
            ]
        ];
    }

    /**
     * 8. Análisis de Puntos y Fidelización
     */
    public function getLoyaltyPointsEvolution($startMonth, $startYear, $endMonth, $endYear): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $points = DB::connection('virtusgesnet')
            ->table('operacionesdepuntos')
            ->selectRaw('YEAR(FechaYHora) as year, MONTH(FechaYHora) as month, SUM(Puntos) as total_points')
            ->whereBetween('FechaYHora', [$startDate, $endDate])
            ->groupByRaw('YEAR(FechaYHora), MONTH(FechaYHora), SIGN(Puntos)')
            ->get();

        $labels = [];
        $acumulados = [];
        $canjeados = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m');
            $labels[] = $current->translatedFormat('M Y');
            
            $monthlyPoints = $points->where('year', $current->year)->where('month', $current->month);
            
            $pos = $monthlyPoints->where('total_points', '>', 0)->sum('total_points');
            $neg = $monthlyPoints->where('total_points', '<', 0)->sum('total_points');

            $acumulados[] = round((float) $pos, 2);
            $canjeados[] = round(abs((float) $neg), 2);
            
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Puntos Emitidos',
                    'data' => $acumulados,
                    'backgroundColor' => '#3B82F6', // blue
                ],
                [
                    'label' => 'Puntos Canjeados',
                    'data' => $canjeados,
                    'backgroundColor' => '#F59E0B', // amber
                ]
            ]
        ];
    }

    /**
     * 9. Top Productos Más Vendidos
     */
    public function getTopProducts($startMonth, $startYear, $endMonth, $endYear, $stationCode = null): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $products = DB::connection('virtusgesnet')
            ->table('detalledefacturasyticketsdeventa')
            ->join('facturasyticketsdeventa', function ($join) {
                $join->on('detalledefacturasyticketsdeventa.CodigoDeEmpresaPropia', '=', 'facturasyticketsdeventa.CodigoDeEmpresaPropia')
                     ->on('detalledefacturasyticketsdeventa.Serie', '=', 'facturasyticketsdeventa.Serie')
                     ->on('detalledefacturasyticketsdeventa.Numero', '=', 'facturasyticketsdeventa.Numero');
            })
            ->join('productos', 'detalledefacturasyticketsdeventa.CodigoDeProducto', '=', 'productos.Codigo')
            ->selectRaw('productos.Descripcion, SUM(detalledefacturasyticketsdeventa.Importe) as total')
            ->whereBetween('facturasyticketsdeventa.FechaYHora', [$startDate, $endDate])
            ->when($stationCode, fn($q) => $q->where('facturasyticketsdeventa.CodigoDeEstacion', $stationCode))
            ->groupBy('productos.Codigo', 'productos.Descripcion')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $products->pluck('Descripcion')->toArray(),
            'datasets' => [
                [
                    'label' => 'Ingresos por Producto (€)',
                    'data' => $products->pluck('total')->map(fn($v) => round((float) $v, 2))->toArray(),
                    'backgroundColor' => '#8B5CF6', // purple-500
                ]
            ]
        ];
    }

    /**
     * 10. Control de Movimientos de Almacén (Valor de Inventario / Flujo)
     */
    public function getInventoryMovements($startMonth, $startYear, $endMonth, $endYear): array
    {
        [$startDate, $endDate] = $this->getDateRange($startMonth, $startYear, $endMonth, $endYear);

        $movements = DB::connection('virtusgesnet')
            ->table('movimientosdealmacen')
            ->selectRaw('YEAR(FechaYHora) as year, MONTH(FechaYHora) as month, SUM(Cantidad) as total_qty')
            ->whereBetween('FechaYHora', [$startDate, $endDate])
            ->groupByRaw('YEAR(FechaYHora), MONTH(FechaYHora), SIGN(Cantidad)')
            ->get();

        $labels = [];
        $entradas = [];
        $salidas = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m');
            $labels[] = $current->translatedFormat('M Y');
            
            $monthly = $movements->where('year', $current->year)->where('month', $current->month);
            
            $pos = $monthly->where('total_qty', '>', 0)->sum('total_qty');
            $neg = $monthly->where('total_qty', '<', 0)->sum('total_qty');

            $entradas[] = round((float) $pos, 2);
            $salidas[] = round(abs((float) $neg), 2);
            
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Entradas (Unidades/Litros)',
                    'data' => $entradas,
                    'backgroundColor' => '#10B981', // green
                ],
                [
                    'label' => 'Salidas (Unidades/Litros)',
                    'data' => $salidas,
                    'backgroundColor' => '#EF4444', // red
                ]
            ]
        ];
    }
}
