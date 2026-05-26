<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BeneficiosChart extends ChartWidget
{
    public ?\App\Models\Gasolinera $record = null;

    protected ?string $heading = 'Beneficios (Últimos 6 meses)';

    protected function getData(): array
    {
        if (!$this->record) {
            return [];
        }

        $data = \Illuminate\Support\Facades\DB::connection('virtusgesnet')
            ->table('facturasyticketsdeventa')
            ->selectRaw('YEAR(FechaYHora) as year, MONTH(FechaYHora) as month, SUM(ImporteTotal) as total')
            ->where('CodigoDeEstacion', $this->record->Codigo)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        $labels = [];
        $totals = [];

        foreach ($data as $row) {
            $monthName = \Carbon\Carbon::createFromDate($row->year, $row->month, 1)->locale('es')->translatedFormat('M Y');
            $labels[] = ucfirst($monthName);
            $totals[] = round($row->total, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas (€)',
                    'data' => $totals,
                    'backgroundColor' => '#3b82f6', // blue-500
                    'borderColor' => '#2563eb', // blue-600
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
