<?php

namespace App\Filament\Resources\EmpleadoVacacions\Pages;

use App\Filament\Resources\EmpleadoVacacions\EmpleadoVacacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEmpleadoVacacions extends ManageRecords
{
    protected static string $resource = EmpleadoVacacionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
