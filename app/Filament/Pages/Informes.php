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

    public ?string $reportType = 'sales_vs_purchases';
    public ?int $startMonth = null;
    public ?int $startYear = null;
    public ?int $endMonth = null;
    public ?int $endYear = null;
    public ?int $stationCode = null;

    public ?array $chartData = null;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    protected static ?string $title = 'Informes';

    protected string $view = 'filament.pages.informes';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('ver_informes');
    }

    public function mount(): void
    {
        $this->reportType = 'sales_vs_purchases';
        $this->startMonth = 1;
        $this->startYear = (int) date('Y');
        $this->endMonth = (int) date('m');
        $this->endYear = (int) date('Y');

        $this->form->fill([
            'reportType' => $this->reportType,
            'startMonth' => $this->startMonth,
            'startYear' => $this->startYear,
            'endMonth' => $this->endMonth,
            'endYear' => $this->endYear,
            'stationCode' => null, // Added stationCode to form state
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
                        'sales_vs_purchases' => 'Margen Económico Mensual (Ventas vs Compras)',
                        'top_clients' => 'Top 10 Clientes por Facturación',
                        'top_suppliers' => 'Top 10 Proveedores por Volumen de Compra',
                        'sales_by_station' => 'Comparativa de Facturación por Estación de Servicio',
                        'average_ticket' => 'Evolución del Ticket Medio Mensual',
                        'sales_by_payment_method' => 'Ventas por Medio de Pago',
                        'top_employees' => 'Rendimiento de Empleados (Expendedores)',
                        'loyalty_points' => 'Análisis de Puntos y Fidelización',
                        'top_products' => 'Top Productos Más Vendidos',
                        'inventory_movements' => 'Flujo de Movimientos de Almacén',
                    ])
                    ->default('sales_vs_purchases')
                    ->selectablePlaceholder(false)
                    ->required()
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
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
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
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ])
                            ->default(date('n'))
                            ->required(),
                        TextInput::make('endYear')
                            ->label('Año Hasta')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                    ])
            ]);
    }

    private function getMonths(): array
    {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
    }

    public function generateReport(ReportService $reportService): void
    {
        $data = $this->form->getState();
        
        $type = $data['reportType'];
        $sm = $data['startMonth'];
        $sy = $data['startYear'];
        $em = $data['endMonth'];
        $ey = $data['endYear'];
        $stationCode = $data['stationCode'] ?? null;

        $this->chartData = [
            'type' => 'bar',
            'labels' => [],
            'datasets' => [],
            'options' => []
        ];

        switch ($type) {
            case 'sales_vs_purchases':
                $result = $reportService->getSalesVsPurchasesMargin($sm, $sy, $em, $ey, $stationCode);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                break;
            case 'top_clients':
                $result = $reportService->getTopClients($sm, $sy, $em, $ey, $stationCode);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                $this->chartData['options'] = ['indexAxis' => 'y'];
                break;
            case 'top_suppliers':
                $result = $reportService->getTopSuppliers($sm, $sy, $em, $ey, $stationCode);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                $this->chartData['options'] = ['indexAxis' => 'y'];
                break;
            case 'sales_by_station':
                $result = $reportService->getSalesByStation($sm, $sy, $em, $ey);
                $this->chartData['type'] = 'pie';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                break;
            case 'average_ticket':
                $result = $reportService->getAverageTicketEvolution($sm, $sy, $em, $ey);
                $this->chartData['type'] = 'line';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                break;
            case 'sales_by_payment_method':
                $result = $reportService->getSalesByPaymentMethod($sm, $sy, $em, $ey, $stationCode);
                $this->chartData['type'] = 'doughnut';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                break;
            case 'top_employees':
                $result = $reportService->getTopEmployees($sm, $sy, $em, $ey, $stationCode);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                $this->chartData['options'] = ['indexAxis' => 'y'];
                break;
            case 'loyalty_points':
                $result = $reportService->getLoyaltyPointsEvolution($sm, $sy, $em, $ey);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                break;
            case 'top_products':
                $result = $reportService->getTopProducts($sm, $sy, $em, $ey, $stationCode);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                $this->chartData['options'] = ['indexAxis' => 'y'];
                break;
            case 'inventory_movements':
                $result = $reportService->getInventoryMovements($sm, $sy, $em, $ey);
                $this->chartData['type'] = 'bar';
                $this->chartData['labels'] = $result['labels'];
                $this->chartData['datasets'] = $result['datasets'];
                break;
        }

        $this->dispatch('update-chart', data: $this->chartData);
    }
}
