<?php

namespace App\Filament\Resources\Gasolineras\Pages;

use App\Filament\Resources\Gasolineras\GasolineraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Traits\HasMenuBreadcrumbs;
class ListGasolineras extends ListRecords
{
    use HasMenuBreadcrumbs;

    protected static string $resource = GasolineraResource::class;


}
