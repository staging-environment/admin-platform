<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreateEmpleado extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = EmpleadoResource::class;
}
