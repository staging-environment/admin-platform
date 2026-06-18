<?php

namespace App\Filament\Pages;

use App\Services\FuelMarketsService;
use App\Services\MineturService;
use Illuminate\Support\Facades\Cache;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected string $view = 'filament.pages.dashboard';

    protected static ?string $title            = 'Dashboard Energético';
    protected static ?string $navigationLabel  = 'Panel';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    /** Datos de mercados internacionales (Yahoo Finance) */
    public array $gasoilData = [];
    public array $rbobData   = [];

    /** Datos de competencia local (MITECO) */
    public array $localityData = [];

    /** Estado del bot MITECO */
    public ?array $mitecoLastUpdate = null;

    public function mount(): void
    {
        // Si no hay datos en caché, los obtenemos inmediatamente (primer acceso)
        $fuelService    = app(FuelMarketsService::class);
        $mineturService = app(MineturService::class);

        if (! Cache::has('fuel_markets_gasoil')) {
            $fuelService->refresh();
        }
        if (! Cache::has('minetur_sevilla')) {
            $mineturService->refreshAll();
        }

        $this->mitecoLastUpdate = Cache::get('miteco_last_update_status');
        $this->loadData();
    }

    /** Carga (o recarga) todos los datos desde caché. */
    public function loadData(): void
    {
        $fuelService    = app(FuelMarketsService::class);
        $mineturService = app(MineturService::class);

        $this->gasoilData  = $fuelService->getGasoilLondres();
        $this->rbobData    = $fuelService->getRBOB();
        $this->localityData = $mineturService->getAllLocalitiesData();
        $this->mitecoLastUpdate = Cache::get('miteco_last_update_status');
    }

    /** Refresca sólo los datos de competencia local (llamado por wire:poll cada 5 min). */
    public function refreshCompetitors(): void
    {
        $mineturService     = app(MineturService::class);
        $this->localityData = $mineturService->getAllLocalitiesData();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        return $user->hasRole('Admin') || $user->can('ver_dashboard');
    }
}
