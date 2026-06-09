<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
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

    /** Datos de la evolución mensual */
    public ?array  $chartEvolucionData = null;

    /** Tipo de informe activo */
    public ?string $resultType  = null;

    /** Error / aviso */
    public ?string $errorMsg    = null;

    /** Búsqueda en la tabla de resultados */
    public string $searchQuery  = '';

    /** Filtros adicionales de la tabla */
    public ?string $filterGroup = null;
    public ?string $filterMargin = null;

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
                Wizard::make([
                    Wizard\Step::make('Paso 1: ¿Qué quieres analizar?')
                        ->description('Selecciona el área de negocio o tipo de margen')
                        ->schema([
                            Radio::make('reportType')
                                ->hiddenLabel()
                                ->options([
                                    'tienda_margen'     => '🛋️ La Tienda',
                                    'lavado_margen'     => '🚐 El Lavadero',
                                    'margen_mercaderia' => '📊 Margen Teórico (Toda la mercancía)',
                                ])
                                ->descriptions([
                                    'tienda_margen'     => 'Descubre qué margen real de beneficio te deja cada producto (Precio de venta en caja vs Coste real de factura).',
                                    'lavado_margen'     => 'Analiza la rentabilidad real de los servicios de lavado.',
                                    'margen_mercaderia' => 'Compara el precio de Tarifa actual en base de datos con el último precio de coste.',
                                ])
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) use ($groupOptions) {
                                    $keys = array_keys($groupOptions);
                                    if ($state === 'tienda_margen') {
                                        $set('groupCodes', array_values(array_filter($keys, fn($k) => str_starts_with((string)$k, '3'))));
                                    } elseif ($state === 'lavado_margen') {
                                        $set('groupCodes', array_values(array_filter($keys, fn($k) => str_starts_with((string)$k, '4'))));
                                    } elseif ($state === 'margen_mercaderia') {
                                        $set('groupCodes', array_values(array_filter($keys, fn($k) => str_starts_with((string)$k, '3') || str_starts_with((string)$k, '4'))));
                                    }
                                })
                                ->required(),
                        ]),

                    Wizard\Step::make('Paso 2: ¿Dónde y Cuándo?')
                        ->description('Filtra los datos por fechas y gasolinera')
                        ->schema([
                            Select::make('groupCodes')
                                ->label('Grupos a Incluir')
                                ->multiple()
                                ->options($groupOptions)
                                ->placeholder('Selecciona uno o más grupos...')
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
                        ]),
                ])
                ->nextAction(fn ($action) => $action->label('Siguiente Paso'))
                ->previousAction(fn ($action) => $action->label('Atrás'))
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="generateReport">
                            Generar Informe
                        </span>
                        <span wire:loading wire:target="generateReport">
                            Generando...
                        </span>
                    </x-filament::button>
                BLADE)))
                ->columnSpanFull()
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
        $this->searchQuery = '';
        $this->filterGroup   = null;
        $this->filterMargin  = null;
        $this->sortColumn    = '';
        $this->sortDirection = 'asc';
        $this->chartEvolucionData = null;

        $data = $this->form->getState();

        // Leer directamente de la propiedad Livewire (el form->getState puede llegar tarde)
        $reportType = $this->reportType ?? ($data['reportType'] ?? null);

        if (empty($reportType)) {
            $this->errorMsg = 'Por favor, selecciona un tipo de informe.';
            return;
        }

        switch ($reportType) {

            // ── Tienda (Grupo 3) ─────────────────────────────────────────────
            case 'tienda_margen':
            // ── Lavadero (Grupo 4) ───────────────────────────────────────────
            case 'lavado_margen':
                $selectedGroupCodes = !empty($data['groupCodes']) 
                    ? $data['groupCodes'] 
                    : ($reportType === 'tienda_margen' ? ['3'] : ['4']);
                    
                $rows = $reportService->getMargenSimple(
                    (int) ($data['startMonth'] ?? $this->startMonth),
                    (int) ($data['startYear']  ?? $this->startYear),
                    (int) ($data['endMonth']   ?? $this->endMonth),
                    (int) ($data['endYear']    ?? $this->endYear),
                    $selectedGroupCodes,
                    !empty($data['stationCode']) ? (int) $data['stationCode'] : null
                );
                
                $this->chartEvolucionData = $reportService->getEvolucionMensual(
                    (int) ($data['startMonth'] ?? $this->startMonth),
                    (int) ($data['startYear']  ?? $this->startYear),
                    (int) ($data['endMonth']   ?? $this->endMonth),
                    (int) ($data['endYear']    ?? $this->endYear),
                    $selectedGroupCodes,
                    !empty($data['stationCode']) ? (int) $data['stationCode'] : null
                );

                if (empty($rows)) {
                    $this->errorMsg = 'No se encontraron artículos con compras registradas en el período seleccionado.';
                    break;
                }

                $this->tableData  = $rows;
                $this->resultType = $reportType;

                // Gráfico: Top 20 por % margen (solo los que tienen ventas)
                $conVentas = array_filter($rows, fn($r) => !$r['sin_ventas'] && $r['margen_pct'] !== null);
                usort($conVentas, fn($a, $b) => $b['margen_pct'] <=> $a['margen_pct']);
                $top = array_slice($conVentas, 0, 20);
                $top = array_values($top);

                $this->chartData = [
                    'labels'   => array_map(fn($r) => mb_substr($r['descripcion'], 0, 22), $top),
                    'margenes' => array_map(fn($r) => $r['margen_pct'], $top),
                    'colors'   => array_map(fn($r) => $r['margen_pct'] >= 40
                        ? 'rgba(34,197,94,0.85)'
                        : ($r['margen_pct'] >= 20 ? 'rgba(234,179,8,0.85)' : 'rgba(239,68,68,0.85)'), $top),
                    'borders'  => array_map(fn($r) => $r['margen_pct'] >= 40
                        ? 'rgb(22,163,74)'
                        : ($r['margen_pct'] >= 20 ? 'rgb(202,138,4)' : 'rgb(220,38,38)'), $top),
                    'dual'     => false,
                ];

                $this->dispatch('chart-data-ready', chartData: $this->chartData);
                $this->dispatch('chart-ev-data-ready', chartEvolucionData: $this->chartEvolucionData);
                
                // Ordenar por defecto por beneficio (menor a mayor)
                $this->sortColumn = '';
                $this->sortBy('beneficio');
                
                break;

            // ── Margen Tarifa (PVP Tarifa vs Precio Compra) ──────────────────
            case 'margen_mercaderia':
                $rows = $reportService->getMargenMercaderia(
                    (int) ($data['startMonth'] ?? $this->startMonth),
                    (int) ($data['startYear']  ?? $this->startYear),
                    (int) ($data['endMonth']   ?? $this->endMonth),
                    (int) ($data['endYear']    ?? $this->endYear),
                    !empty($data['stationCode']) ? (int) $data['stationCode'] : null,
                    ['3', '4']
                );


                if (empty($rows)) {
                    $this->errorMsg = 'No se encontraron artículos con compras en el período seleccionado.';
                    break;
                }

                $this->tableData  = $rows;
                $this->resultType = 'margen_mercaderia';

                // Top 20 para el gráfico
                usort($rows, fn($a, $b) => $b['margen_pct'] <=> $a['margen_pct']);
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
                
                // Ordenar por defecto por margen_pct (menor a mayor)
                $this->sortColumn = '';
                $this->sortBy('margen_pct');
                
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
                usort($topConVentas, fn($a, $b) => $b['margen_real_pct'] <=> $a['margen_real_pct']);
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
                
                // Ordenar por defecto por margen_real_pct (menor a mayor)
                $this->sortColumn = '';
                $this->sortBy('margen_real_pct');
                
                break;

            default:
                break;
        }
    }


    // ──────────────────────────────────────────────────────────────────────────
    // Paginación y Filtrado de la tabla
    // ──────────────────────────────────────────────────────────────────────────

    public function updatedSearchQuery()
    {
        $this->tablePage = 1;
    }

    public function updatedFilterGroup()
    {
        $this->tablePage = 1;
    }

    public function updatedFilterMargin()
    {
        $this->tablePage = 1;
    }

    public function getActiveGroups(): array
    {
        if (!$this->tableData) return [];
        return array_values(array_unique(array_filter(array_column($this->tableData, 'grupo_nombre'))));
    }

    protected function getFilteredTableData(): array
    {
        if (!$this->tableData) return [];
        
        $data = $this->tableData;
        
        // Filtro buscador
        if (!empty($this->searchQuery)) {
            $q = mb_strtolower($this->searchQuery);
            $data = array_filter($data, function($row) use ($q) {
                return str_contains(mb_strtolower($row['descripcion'] ?? ''), $q)
                    || str_contains(mb_strtolower($row['codigo'] ?? ''), $q)
                    || str_contains(mb_strtolower($row['grupo_nombre'] ?? ''), $q);
            });
        }

        // Filtro grupo
        if (!empty($this->filterGroup)) {
            $fg = $this->filterGroup;
            $data = array_filter($data, function($row) use ($fg) {
                return ($row['grupo_nombre'] ?? '') === $fg;
            });
        }

        // Filtro margen
        if (!empty($this->filterMargin)) {
            $fm = $this->filterMargin;
            $data = array_filter($data, function($row) use ($fm) {
                $m = $row['margen_pct'] ?? $row['margen_real_pct'] ?? null;
                $sinV = $row['sin_ventas'] ?? false;

                if ($fm === 'high') return $m !== null && $m >= 40 && !$sinV;
                if ($fm === 'mid') return $m !== null && $m >= 20 && $m < 40 && !$sinV;
                if ($fm === 'low') return $m !== null && $m >= 0 && $m < 20 && !$sinV;
                if ($fm === 'negative') return $m !== null && $m < 0 && !$sinV;
                if ($fm === 'no_sales') return $sinV || $m === null;
                return true;
            });
        }

        return $data;
    }

    public function getTableTotalPages(): int
    {
        $data = $this->getFilteredTableData();
        if (empty($data)) return 0;
        return (int) ceil(count($data) / $this->tablePerPage);
    }

    public function getPagedTableData(): array
    {
        $data = $this->getFilteredTableData();
        if (empty($data)) return [];
        return array_slice($data, ($this->tablePage - 1) * $this->tablePerPage, $this->tablePerPage);
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
