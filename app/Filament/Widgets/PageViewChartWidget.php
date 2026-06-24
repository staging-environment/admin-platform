<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PageViewChartWidget extends ChartWidget
{
    protected ?string $heading = 'Historial de Visitas';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Últimos 7 días',
            '30' => 'Últimos 30 días',
            '90' => 'Últimos 90 días',
        ];
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 30);
        
        $data = PageView::selectRaw('DATE(created_at) as date, count(*) as count, count(distinct ip_address) as unique_count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates->put(now()->subDays($i)->format('Y-m-d'), ['visits' => 0, 'unique' => 0]);
        }

        foreach ($data as $row) {
            $dates->put($row->date, ['visits' => $row->count, 'unique' => $row->unique_count]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Páginas Vistas',
                    'data' => $dates->pluck('visits')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.05)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Visitantes Únicos',
                    'data' => $dates->pluck('unique')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates->keys()->map(fn ($date) => Carbon::parse($date)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
