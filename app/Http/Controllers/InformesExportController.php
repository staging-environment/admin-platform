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

        switch ($reportType) {
            case 'margen_mercaderia':
                $rows = $reportService->getMargenMercaderia(
                    $startMonth, $startYear, $endMonth, $endYear, $stationCode
                );
                return match ($format) {
                    'excel' => $this->exportExcel($rows, 'margen_mercaderia'),
                    default => $this->exportCsv($rows, 'margen_mercaderia'),
                };

            default:
                abort(400, 'Tipo de informe no soportado.');
        }
    }

    // ─── CSV ──────────────────────────────────────────────────────────────────

    private function exportCsv(array $rows, string $reportType): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $reportType . '_' . date('Ymd_His') . '.csv';

        $headers = match ($reportType) {
            'margen_mercaderia' => [
                'Código', 'Descripción', 'Grupo', 'P.Compra (€)', 'PVP con IVA (€)',
                '% IVA', 'PVP sin IVA (€)', '% Margen', 'Uds. Compradas',
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
                        number_format($row['pvp_con_iva'], 4, ',', '.'),
                        number_format($row['pct_iva'], 2, ',', '.') . '%',
                        number_format($row['pvp_sin_iva'], 4, ',', '.'),
                        number_format($row['margen_pct'], 2, ',', '.') . '%',
                        number_format($row['unidades_compradas'], 3, ',', '.'),
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
            $cols = ['Código','Descripción','Grupo','P.Compra (€)','PVP con IVA (€)','% IVA','PVP sin IVA (€)','% Margen','Uds. Compradas'];
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
                $html .= '<Cell><Data ss:Type="Number">' . $row['pvp_con_iva'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['pct_iva'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['pvp_sin_iva'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['margen_pct'] . '</Data></Cell>';
                $html .= '<Cell><Data ss:Type="Number">' . $row['unidades_compradas'] . '</Data></Cell>';
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
