<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreateRole extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = RoleResource::class;
}
