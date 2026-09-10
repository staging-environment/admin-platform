<?php

namespace App\Filament\Widgets;

use App\Models\Gasolinera;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class BeneficiosChart extends ChartWidget
{
    public ?Gasolinera $record = null;
    public ?int $gasolineraCodigo = null;

    protected ?string $heading = 'Beneficios (€)';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '260px';

    public ?string $filter = '6';

    public function mount($record = null): void
    {
        if ($record instanceof Gasolinera) {
            $this->record = $record;
            $this->gasolineraCodigo = (int) $record->Codigo;
        } elseif (is_numeric($record)) {
            $this->gasolineraCodigo = (int) $record;
            $this->record = Gasolinera::find($this->gasolineraCodigo);
        }
    }

    protected function getFilters(): ?array
    {
        return [
            '6' => 'Últimos 6 meses',
            '12' => 'Últimos 12 meses',
            'year' => 'Año actual (' . date('Y') . ')',
        ];
    }

    protected function getData(): array
    {
        $codigo = $this->gasolineraCodigo ?? ($this->record?->Codigo ?? null);

        if (!$codigo) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        try {
            $filter = $this->filter ?? '6';
            $now = Carbon::now();

            if ($filter === 'year') {
                $start = Carbon::create((int) $now->year, 1, 1);
                $end = Carbon::create((int) $now->year, (int) $now->month, 1);
            } elseif ($filter === '12') {
                $start = (clone $now)->subMonths(11)->startOfMonth();
                $end = (clone $now)->startOfMonth();
            } else { // 6 meses por defecto
                $start = (clone $now)->subMonths(5)->startOfMonth();
                $end = (clone $now)->startOfMonth();
            }

            /** @var ReportService $reportService */
            $reportService = app(ReportService::class);
            $evolucion = $reportService->getEvolucionMensual(
                (int) $start->format('m'),
                (int) $start->format('Y'),
                (int) $end->format('m'),
                (int) $end->format('Y'),
                ['3', '4'],
                (int) $codigo
            );

            $labels = [];
            $beneficios = [];

            foreach ($evolucion as $row) {
                $labels[] = $row['mes'];
                $beneficios[] = (float) ($row['beneficio'] ?? 0);
            }

            $bgColors = array_map(fn ($val) => $val >= 0 ? 'rgba(16, 185, 129, 0.75)' : 'rgba(239, 68, 68, 0.75)', $beneficios);
            $borderColors = array_map(fn ($val) => $val >= 0 ? '#10b981' : '#ef4444', $beneficios);

            return [
                'datasets' => [
                    [
                        'label' => 'Beneficio (€)',
                        'data' => $beneficios,
                        'backgroundColor' => $bgColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 1,
                        'borderRadius' => 4,
                    ],
                ],
                'labels' => $labels,
            ];
        } catch (\Throwable $e) {
            report($e);
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
