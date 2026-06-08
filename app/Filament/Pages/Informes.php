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
    public int    $tablePage      = 1;
    public int    $tablePerPage   = 30;

    /** Ordenación de la tabla */
    public string $sortColumn    = '';
    public string $sortDirection = 'asc';

    /** Grupos de producto seleccionados para el informe */
    public ?array $groupCodes = null;

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
            'groupCodes'  => ['3', '4'],
        ]);
    }

    public function form(Schema $form): Schema
    {
        $virtusService = app(\App\Services\VirtusgesnetService::class);
        $stations = collect($virtusService->getStations())->pluck('name', 'code')->toArray();

        // Cargar grupos de productos desde virtusgesnet — todos los grupos salvo gastos/combustibles
        $groupOptions = \Illuminate\Support\Facades\DB::connection('virtusgesnet')
            ->table('gruposdeproductos')
            ->select('Codigo', 'Nombre')
            ->where('Codigo', 'not like', '9%')    // excluir gastos internos
            ->where('Codigo', 'not like', '0%')    // excluir combustibles
            ->where('Codigo', '!=', 'TODOS')
            ->orderBy('Codigo')
            ->get()
            ->mapWithKeys(fn($g) => [$g->Codigo => "({$g->Codigo}) {$g->Nombre}"])
            ->toArray();


        return $form
            ->schema([
                Select::make('reportType')
                    ->label('Tipo de Informe')
                    ->options([
                        'margen_mercaderia' => 'Margen Comercial — PVP Tarifa',
                        'margen_con_ventas' => 'Margen Real Compra vs Venta — Dato Real TPV',
                    ])
                    ->placeholder('Selecciona un informe...')
                    ->live()
                    ->columnSpanFull(),

                Select::make('groupCodes')
                    ->label('Grupos de Producto')
                    ->options($groupOptions)
                    ->multiple()
                    ->default(['3', '4'])
                    ->placeholder('Selecciona grupos...')
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
        $this->sortColumn    = '';
        $this->sortDirection = 'asc';

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
                    !empty($data['stationCode']) ? (int) $data['stationCode'] : null,
                    !empty($data['groupCodes'])  ? (array) $data['groupCodes'] : null
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

            case 'margen_con_ventas':
                $rows = $reportService->getMargenConVentas(
                    (int) ($data['startMonth'] ?? $this->startMonth),
                    (int) ($data['startYear']  ?? $this->startYear),
                    (int) ($data['endMonth']   ?? $this->endMonth),
                    (int) ($data['endYear']    ?? $this->endYear),
                    !empty($data['stationCode']) ? (int) $data['stationCode'] : null,
                    !empty($data['groupCodes'])  ? (array) $data['groupCodes'] : null
                );

                if (empty($rows)) {
                    $this->errorMsg = 'No se encontraron artículos con compras en el período seleccionado.';
                    break;
                }

                $this->tableData  = $rows;
                $this->resultType = 'margen_con_ventas';

                // Top 20 con ventas reales para el gráfico (comparativa compra vs venta)
                $topConVentas = array_filter($rows, fn($r) => !$r['sin_ventas'] && $r['margen_real_pct'] !== null);
                $top20 = array_slice(array_values($topConVentas), 0, 20);

                if (!empty($top20)) {
                    $this->chartData = [
                        'labels'          => array_map(fn($r) => mb_substr($r['descripcion'], 0, 22), $top20),
                        'margenes'        => array_map(fn($r) => $r['margen_real_pct'], $top20),
                        'margenesTarget'  => array_map(fn($r) => $r['margen_tarifa_pct'], $top20),
                        'colors'          => array_map(fn($r) => ($r['margen_real_pct'] ?? 0) >= 40
                            ? 'rgba(34,197,94,0.85)'
                            : (($r['margen_real_pct'] ?? 0) >= 20 ? 'rgba(234,179,8,0.85)' : 'rgba(239,68,68,0.85)'), $top20),
                        'borders'         => array_map(fn($r) => ($r['margen_real_pct'] ?? 0) >= 40
                            ? 'rgb(22,163,74)'
                            : (($r['margen_real_pct'] ?? 0) >= 20 ? 'rgb(202,138,4)' : 'rgb(220,38,38)'), $top20),
                        'dual'            => true,
                    ];
                    $this->dispatch('chart-data-ready', chartData: $this->chartData);
                }
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

    // ─────────────────────────────────────────────────────────────────────────
    // Ordenación de la tabla
    // ─────────────────────────────────────────────────────────────────────────

    public function sortBy(string $column): void
    {
        if (!$this->tableData) return;

        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn    = $column;
            $this->sortDirection = 'asc';
        }

        $this->tablePage = 1; // volver a la primera página al ordenar

        $col = $this->sortColumn;
        $dir = $this->sortDirection;

        usort($this->tableData, function ($a, $b) use ($col, $dir) {
            $va = $a[$col] ?? null;
            $vb = $b[$col] ?? null;

            // Nulos siempre al final
            if ($va === null && $vb === null) return 0;
            if ($va === null) return 1;
            if ($vb === null) return -1;

            $cmp = is_numeric($va) && is_numeric($vb)
                ? ($va <=> $vb)
                : strcmp((string)$va, (string)$vb);

            return $dir === 'asc' ? $cmp : -$cmp;
        });
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
