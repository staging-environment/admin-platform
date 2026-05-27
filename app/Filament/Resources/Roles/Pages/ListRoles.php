<?php

namespace App\Filament\Resources\Roles\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// Cambiamos esta línea para que apunte a la carpeta Roles
use App\Filament\Resources\Roles\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver al Dashboard')
                ->color('gray')
                ->icon('heroicon-m-arrow-left')
                ->url(route('dashboard')),
            Actions\CreateAction::make(),
        ];
    }
}
