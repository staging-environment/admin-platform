<?php

namespace App\Filament\Resources\ContactoMensajes\Pages;

use App\Filament\Resources\ContactoMensajes\ContactoMensajeResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreateContactoMensaje extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = ContactoMensajeResource::class;
}
