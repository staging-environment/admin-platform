<?php

namespace App\Http\Controllers;

use App\Services\VirtusgesnetService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
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

        return view('reports.index', [
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
