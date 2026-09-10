<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreatePermission extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = PermissionResource::class;
}
