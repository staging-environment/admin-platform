<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// Cambiamos esta línea para que apunte a la carpeta Users
use App\Filament\Resources\Users\UserResource;

use App\Filament\Traits\HasMenuBreadcrumbs;
class ListUsers extends ListRecords
{
    use HasMenuBreadcrumbs;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->color('success')
                ->extraAttributes([
                    'style' => 'background-color: #16a34a !important; color: #ffffff !important; border-color: #16a34a !important;',
                ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Users\Widgets\UsersInfoWidget::class,
        ];
    }
}
