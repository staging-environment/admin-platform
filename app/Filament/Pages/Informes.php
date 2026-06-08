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

    public ?string $reportType = null;
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
                        // Los informes se irán añadiendo aquí
                    ])
                    ->placeholder('Selecciona un informe')
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
                                1 => 'Enero',    2 => 'Febrero',   3 => 'Marzo',
                                4 => 'Abril',    5 => 'Mayo',      6 => 'Junio',
                                7 => 'Julio',    8 => 'Agosto',    9 => 'Septiembre',
                                10 => 'Octubre', 11 => 'Noviembre',12 => 'Diciembre',
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
                                10 => 'Octubre', 11 => 'Noviembre',12 => 'Diciembre',
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

    public function generateReport(ReportService $reportService): void
    {
        $data = $this->form->getState();

        $this->chartData = null;

        // Los casos se irán añadiendo conforme se creen los informes
        switch ($data['reportType']) {
            default:
                break;
        }

        if ($this->chartData) {
            $this->dispatch('update-chart', data: $this->chartData);
        }
    }
}
