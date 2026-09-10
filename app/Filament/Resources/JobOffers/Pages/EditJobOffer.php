<?php

namespace App\Filament\Resources\JobOffers\Pages;

use App\Filament\Resources\JobOffers\JobOfferResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class EditJobOffer extends EditRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = JobOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
