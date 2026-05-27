<?php

namespace App\Filament\Resources\Gasolineras\Pages;

use App\Filament\Resources\Gasolineras\GasolineraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGasolineras extends ListRecords
{
    protected static string $resource = GasolineraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver al Dashboard')
                ->color('gray')
                ->icon('heroicon-m-arrow-left')
                ->url(route('dashboard')),
        ];
    }
}
