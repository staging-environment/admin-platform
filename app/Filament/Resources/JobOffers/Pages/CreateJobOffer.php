<?php

namespace App\Filament\Resources\JobOffers\Pages;

use App\Filament\Resources\JobOffers\JobOfferResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreateJobOffer extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = JobOfferResource::class;
}
