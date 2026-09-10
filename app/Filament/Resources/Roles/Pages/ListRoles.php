<?php

namespace App\Filament\Resources\Roles\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// Cambiamos esta línea para que apunte a la carpeta Roles
use App\Filament\Resources\Roles\RoleResource;

use App\Filament\Traits\HasMenuBreadcrumbs;
class ListRoles extends ListRecords
{
    use HasMenuBreadcrumbs;

    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
