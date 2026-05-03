<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// Cambiamos esta línea para que apunte a la carpeta Users
use App\Filament\Resources\Users\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver al Dashboard')
                ->color('gray')
                ->icon('heroicon-m-arrow-left')
                ->url('/dashboard'),
            Actions\CreateAction::make(),
        ];
    }
}
