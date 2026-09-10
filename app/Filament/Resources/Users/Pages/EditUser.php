<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class EditUser extends EditRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->color('success')
            ->extraAttributes([
                'style' => 'background-color: #16a34a !important; color: #ffffff !important; border-color: #16a34a !important;',
            ]);
    }
}
