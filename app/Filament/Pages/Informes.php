<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use App\Services\ReportService;

class Informes extends Page implements HasForms
{
    use InteractsWithForms;

    public ?string $reportType  = null;
    public ?int    $startMonth  = null;
    public ?int    $startYear   = null;
    public ?int    $endMonth    = null;
    public ?int    $endYear     = null;
    public ?int    $stationCode = null;

    /** Datos completos para la tabla y exportación */
    public ?array  $tableData   = null;

    /** Datos del gráfico (top 20) para Chart.js */
    public ?array  $chartData   = null;

    /** Tipo de informe activo */
    public ?string $resultType  = null;

    /** Error / aviso */
    public ?string $errorMsg    = null;

    /** Paginación de la tabla */
    public int $tablePage    = 1;
    public int $tablePerPage = 30;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null   $navigationGroup = 'Administración';
    protected static ?string $title = 'Informes';
    protected string $view          = 'filament.pages.informes';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('ver_informes');
    }

    public function mount(): void
    {
        $this->startMonth = 1;
        $this->startYear  = (int) date('Y');
        $this->endMonth   = (int) date('m');
        $this->endYear    = (int) date('Y');

        $this->form->fill([
            'reportType'  => null,
            'startMonth'  => $this->startMonth,
            'startYear'   => $this->startYear,
            'endMonth'    => $this->endMonth,
            'endYear'     => $this->endYear,
            'stationCode' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        $virtusService = app(\App\Services\VirtusgesnetService::class);
        $stations = collect($virtusService->getStations())->pluck('name', 'code')->toArray();

        return $form
            ->schema([
                Select::make('reportType')
                    ->label('Tipo de Informe')
                    ->options([
                        'margen_mercaderia' => 'Margen Comercial de Mercancía (Grupo 3)',
                    ])
                    ->placeholder('Selecciona un informe...')
                    ->live()
                    ->columnSpanFull(),

                Select::make('stationCode')
                    ->label('Gasolinera')
                    ->options($stations)
                    ->placeholder('Todas las Gasolineras')
                    ->columnSpanFull(),

                Grid::make(4)
                    ->schema([
                        Select::make('startMonth')
                            ->label('Mes Desde')
                            ->options([
                                1 => 'Enero',    2 => 'Febrero',   3 => 'Marzo',
                                4 => 'Abril',    5 => 'Mayo',      6 => 'Junio',
                                7 => 'Julio',    8 => 'Agosto',    9 => 'Septiembre',
                                10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ])
                            ->default(1)
                            ->required(),
                        TextInput::make('startYear')
                            ->label('Año Desde')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                        Select::make('endMonth')
                            ->label('Mes Hasta')
                            ->options([
                                1 => 'Enero',    2 => 'Febrero',   3 => 'Marzo',
                                4 => 'Abril',    5 => 'Mayo',      6 => 'Junio',
                                7 => 'Julio',    8 => 'Agosto',    9 => 'Septiembre',
                                10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ])
                            ->default(date('n'))
                            ->required(),
                        TextInput::make('endYear')
                            ->label('Año Hasta')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                    ]),
            ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Generar informe
    // ──────────────────────────────────────────────────────────────────────────

    public function generateReport(ReportService $reportService): void
    {
        $this->chartData  = null;
        $this->tableData  = null;
        $this->resultType = null;
        $this->errorMsg   = null;
        $this->tablePage  = 1;

        $data = $this->form->getState();

        // Leer directamente de la propiedad Livewire (el form->getState puede llegar tarde)
        $reportType = $this->reportType ?? ($data['reportType'] ?? null);

        if (empty($reportType)) {
            $this->errorMsg = 'Por favor, selecciona un tipo de informe.';
            return;
        }

        switch ($reportType) {

            case 'margen_mercaderia':
                $rows = $reportService->getMargenMercaderia(
                    (int) ($data['startMonth'] ?? $this->startMonth),
                    (int) ($data['startYear']  ?? $this->startYear),
                    (int) ($data['endMonth']   ?? $this->endMonth),
                    (int) ($data['endYear']    ?? $this->endYear),
                    !empty($data['stationCode']) ? (int) $data['stationCode'] : null
                );

                if (empty($rows)) {
                    $this->errorMsg = 'No se encontraron artículos con compras en el período seleccionado.';
                    break;
                }

                $this->tableData  = $rows;
                $this->resultType = 'margen_mercaderia';

                // Top 20 para el gráfico
                $top = array_slice($rows, 0, 20);
                $this->chartData = [
                    'labels'   => array_map(fn($r) => mb_substr($r['descripcion'], 0, 22), $top),
                    'margenes' => array_map(fn($r) => $r['margen_pct'], $top),
                    'colors'   => array_map(fn($r) => $r['margen_pct'] >= 40
                        ? 'rgba(34,197,94,0.85)'
                        : ($r['margen_pct'] >= 20 ? 'rgba(234,179,8,0.85)' : 'rgba(239,68,68,0.85)'),
                        $top),
                    'borders'  => array_map(fn($r) => $r['margen_pct'] >= 40
                        ? 'rgb(22,163,74)'
                        : ($r['margen_pct'] >= 20 ? 'rgb(202,138,4)' : 'rgb(220,38,38)'),
                        $top),
                ];

                // Disparar evento Alpine para re-crear el gráfico
                $this->dispatch('chart-data-ready', chartData: $this->chartData);
                break;

            default:
                break;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Paginación de la tabla
    // ──────────────────────────────────────────────────────────────────────────

    public function getTableTotalPages(): int
    {
        if (!$this->tableData) return 0;
        return (int) ceil(count($this->tableData) / $this->tablePerPage);
    }

    public function getPagedTableData(): array
    {
        if (!$this->tableData) return [];
        return array_slice($this->tableData, ($this->tablePage - 1) * $this->tablePerPage, $this->tablePerPage);
    }

    public function nextPage(): void
    {
        if ($this->tablePage < $this->getTableTotalPages()) {
            $this->tablePage++;
        }
    }

    public function prevPage(): void
    {
        if ($this->tablePage > 1) {
            $this->tablePage--;
        }
    }

    public function goToPage(int $page): void
    {
        $total = $this->getTableTotalPages();
        $this->tablePage = max(1, min($page, $total));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // URLs de exportación (ruta dedicada, respeta auth middleware)
    // ──────────────────────────────────────────────────────────────────────────

    public function getExportUrl(string $format): string
    {
        if (!$this->tableData || !$this->resultType) return '#';
        $data = $this->form->getState();
        $reportType = $this->reportType ?? ($data['reportType'] ?? null);
        return route('informes.export', [
            'reportType'  => $reportType,
            'startMonth'  => $data['startMonth']  ?? $this->startMonth,
            'startYear'   => $data['startYear']   ?? $this->startYear,
            'endMonth'    => $data['endMonth']    ?? $this->endMonth,
            'endYear'     => $data['endYear']     ?? $this->endYear,
            'stationCode' => $data['stationCode'] ?? null,
            'format'      => $format,
        ]);
    }
}
