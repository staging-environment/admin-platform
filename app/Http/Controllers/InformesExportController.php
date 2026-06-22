<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;

class InformesExportController extends Controller
{
    public function export(Request $request, ReportService $reportService)
    {
        // Verificar permisos — mismo criterio que Informes::canAccess()
        $user = auth()->user();
        if (!$user) abort(403);
        $canAccess = ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1)
                     || $user->hasRole('Admin')
                     || $user->can('ver_informes');
        if (!$canAccess) abort(403, 'No tienes permiso para exportar informes.');

        $reportType  = $request->input('reportType');
        $startMonth  = (int) $request->input('startMonth', 1);
        $startYear   = (int) $request->input('startYear', date('Y'));
        $endMonth    = (int) $request->input('endMonth', date('n'));
        $endYear     = (int) $request->input('endYear', date('Y'));
        $stationCode = $request->filled('stationCode') ? (int) $request->input('stationCode') : null;
        $format      = $request->input('format', 'csv');

        $searchQuery = $request->input('searchQuery');
        $filterGroup = $request->input('filterGroup');
        $filterMargin = $request->input('filterMargin');
        $sortColumn  = $request->input('sortColumn');
        $sortDirection = $request->input('sortDirection', 'asc');

        switch ($reportType) {
            case 'margen_mercaderia':
                $rows = $reportService->getMargenMercaderia(
                    $startMonth, $startYear, $endMonth, $endYear, $stationCode
                );
                break;

            case 'tienda_margen':
            case 'lavado_margen':
                $rows = $reportService->getMargenSimple(
                    $startMonth,
                    $startYear,
                    $endMonth,
                    $endYear,
                    $reportType === 'tienda_margen' ? ['3'] : ['4'],
                    $stationCode
                );
                break;

            default:
                abort(400, 'Tipo de informe no soportado.');
        }

        // --- APLICAR FILTROS Y ORDENACIÓN IGUAL QUE EN LA INTERFAZ ---
        // 1. Buscador
        if (!empty($searchQuery)) {
            $q = mb_strtolower($searchQuery);
            $rows = array_filter($rows, function($row) use ($q) {
                return str_contains(mb_strtolower($row['descripcion'] ?? ''), $q)
                    || str_contains(mb_strtolower($row['codigo'] ?? ''), $q)
                    || str_contains(mb_strtolower($row['grupo_nombre'] ?? ''), $q);
            });
        }

        // 2. Filtro grupo
        if (!empty($filterGroup)) {
            $rows = array_filter($rows, function($row) use ($filterGroup) {
                return ($row['grupo_nombre'] ?? '') === $filterGroup;
            });
        }

        // 3. Filtro margen
        if (!empty($filterMargin)) {
            $rows = array_filter($rows, function($row) use ($filterMargin) {
                $m = $row['margen_pct'] ?? $row['margen_real_pct'] ?? null;
                $sinV = $row['sin_ventas'] ?? false;

                if ($filterMargin === 'high') return $m !== null && $m >= 40 && !$sinV;
                if ($filterMargin === 'mid') return $m !== null && $m >= 20 && $m < 40 && !$sinV;
                if ($filterMargin === 'low') return $m !== null && $m >= 0 && $m < 20 && !$sinV;
                if ($filterMargin === 'negative') return $m !== null && $m < 0 && !$sinV;
                if ($filterMargin === 'no_sales') return $sinV || $m === null;
                return true;
            });
        }

        // 4. Ordenación
        if (!empty($sortColumn)) {
            $col = $sortColumn;
            $dir = $sortDirection === 'desc' ? 'desc' : 'asc';
            usort($rows, function ($a, $b) use ($col, $dir) {
                $va = $a[$col] ?? null;
                $vb = $b[$col] ?? null;

                if ($va === null && $vb === null) return 0;
                if ($va === null) return 1;
                if ($vb === null) return -1;

                $cmp = is_numeric($va) && is_numeric($vb)
                    ? ($va <=> $vb)
                    : strcmp((string)$va, (string)$vb);

                return $dir === 'asc' ? $cmp : -$cmp;
            });
        }

        // Retornar formato
        return match ($format) {
            'excel' => $this->exportExcel($rows, $reportType),
            default => $this->exportCsv($rows, $reportType),
        };
    }

    // ─── CSV ──────────────────────────────────────────────────────────────────

    private function exportCsv(array $rows, string $reportType): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $reportType . '_' . date('Ymd_His') . '.csv';

        $headers = match ($reportType) {
            'margen_mercaderia' => [
                'Código', 'Descripción', 'Grupo', 'P.Compra (€)', '% IVA Compra', 'P.Compra con IVA (€)', 'PVP con IVA (€)',
                '% IVA', 'PVP sin IVA (€)', '% Margen', 'Uds. Compradas',
            ],
            'tienda_margen', 'lavado_margen' => [
                'Código', 'Descripción', 'P.Compra (€)', '% IVA Compra', 'P.Compra con IVA (€)', 'Últ. Compra', 'PVP sin IVA (€)',
                '% IVA', 'PVP con IVA (€)', 'Uds. Compradas', 'Uds. Vendidas',
                'Total Comprado (€)', 'Total Facturado (€)', 'Beneficio (€)', '% Margen',
            ],
            default => [],
        };

        return response()->streamDownload(function () use ($rows, $headers, $reportType) {
            // BOM para UTF-8 correcto en Excel español
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers, ';');

            foreach ($rows as $row) {
                $line = match ($reportType) {
                    'margen_mercaderia' => [
                        $row['codigo'],
                        $row['descripcion'],
                        $row['grupo_nombre'],
                        number_format($row['precio_compra'], 4, ',', '.'),
                        number_format($row['pct_iva_compra'] ?? 0, 1, ',', '.') . '%',
                        number_format($row['precio_compra_con_iva'] ?? $row['precio_compra'], 4, ',', '.'),
                        number_format($row['pvp_con_iva'], 2, ',', '.'),
                        number_format($row['pct_iva'], 2, ',', '.') . '%',
                        number_format($row['pvp_sin_iva'], 4, ',', '.'),
                        number_format($row['margen_pct'], 2, ',', '.') . '%',
                        number_format($row['unidades_compradas'], 3, ',', '.'),
                    ],
                    'tienda_margen', 'lavado_margen' => [
                        $row['codigo'],
                        $row['descripcion'],
                        number_format($row['precio_compra'], 4, ',', '.'),
                        number_format($row['pct_iva_compra'] ?? 0, 1, ',', '.') . '%',
                        number_format($row['precio_compra_con_iva'] ?? $row['precio_compra'], 4, ',', '.'),
                        $row['fecha_ultima_compra'] ?? '—',
                        $row['precio_venta_sin_iva'] !== null ? number_format($row['precio_venta_sin_iva'], 4, ',', '.') : '—',
                        $row['precio_venta'] !== null ? number_format($row['pct_iva'], 1, ',', '.') . '%' : '—',
                        $row['precio_venta'] !== null ? number_format($row['precio_venta'], 2, ',', '.') : '—',
                        number_format($row['uds_compradas'], 3, ',', '.'),
                        number_format($row['uds_vendidas'], 3, ',', '.'),
                        number_format($row['total_comprado'], 2, ',', '.'),
                        number_format($row['total_facturado'], 2, ',', '.'),
                        $row['beneficio'] !== null ? number_format($row['beneficio'], 2, ',', '.') : '—',
                        $row['margen_pct'] !== null ? number_format($row['margen_pct'], 2, ',', '.') . '%' : '—',
                    ],
                    default => [],
                };
                fputcsv($out, $line, ';');
            }
            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─── Excel (HTML table — abre directo en Excel) ───────────────────────────

    private function exportExcel(array $rows, string $reportType): \Symfony\Component\HttpFoundation\Response
    {
        $filename = $reportType . '_' . date('Ymd_His') . '.xls';

        $meses = [
            1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
            7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
        ];

        $html = '<?xml version="1.0" encoding="UTF-8"?>';
        $html .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
                    <Worksheet ss:Name="Informe">
                    <Table>';

        if ($reportType === 'margen_mercaderia') {
            $cols = ['Código','Descripción','Grupo','P.Compra (€)','% IVA Compra','P.Compra con IVA (€)','PVP con IVA (€)','% IVA','PVP sin IVA (€)','% Margen','Uds. Compradas'];
            $html .= '<Row>';
            foreach ($cols as $col) {
                $html .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . htmlspecialchars($col) . '</Data></Cell>';
            }
            $html .= '</Row>';

            foreach ($rows as $row) {
                $html .= '<Row>';
                $html .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['codigo']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['descripcion']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['grupo_nombre']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['precio_compra'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . ($row['pct_iva_compra'] ?? 0) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . ($row['precio_compra_con_iva'] ?? $row['precio_compra']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['pvp_con_iva'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['pct_iva'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['pvp_sin_iva'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['margen_pct'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['unidades_compradas'] . '</Data></Cell>';
                $html .= '</Row>';
            }
        } elseif (in_array($reportType, ['tienda_margen', 'lavado_margen'])) {
            $cols = ['Código', 'Descripción', 'P.Compra (€)', '% IVA Compra', 'P.Compra con IVA (€)', 'Últ. Compra', 'PVP sin IVA (€)', '% IVA', 'PVP con IVA (€)', 'Uds. Compradas', 'Uds. Vendidas', 'Total Comprado (€)', 'Total Facturado (€)', 'Beneficio (€)', '% Margen'];
            $html .= '<Row>';
            foreach ($cols as $col) {
                $html .= '<Cell ss:StyleID="header"><Data ss:Type="String">' . htmlspecialchars($col) . '</Data></Cell>';
            }
            $html .= '</Row>';

            foreach ($rows as $row) {
                $html .= '<Row>';
                $html .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['codigo']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['descripcion']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['precio_compra'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . ($row['pct_iva_compra'] ?? 0) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . ($row['precio_compra_con_iva'] ?? $row['precio_compra']) . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['fecha_ultima_compra'] ?? '') . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="' . ($row['precio_venta_sin_iva'] !== null ? 'Number' : 'String') . '">' . ($row['precio_venta_sin_iva'] ?? '') . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="' . ($row['precio_venta'] !== null ? 'Number' : 'String') . '">' . ($row['precio_venta'] !== null ? $row['pct_iva'] : '') . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="' . ($row['precio_venta'] !== null ? 'Number' : 'String') . '">' . ($row['precio_venta'] ?? '') . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['uds_compradas'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['uds_vendidas'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['total_comprado'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['total_facturado'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="' . ($row['beneficio'] !== null ? 'Number' : 'String') . '">' . ($row['beneficio'] ?? '') . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="' . ($row['margen_pct'] !== null ? 'Number' : 'String') . '">' . ($row['margen_pct'] ?? '') . '</Data></Cell>';
                $html .= '</Row>';
            }
        }

        $html .= '</Table></Worksheet></Workbook>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
