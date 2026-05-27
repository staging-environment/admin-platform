<?php

namespace App\Filament\Resources\ContactoMensajes\Pages;

use App\Filament\Resources\ContactoMensajes\ContactoMensajeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactoMensajes extends ListRecords
{
    protected static string $resource = ContactoMensajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
