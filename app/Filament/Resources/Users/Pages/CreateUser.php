<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreateUser extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = UserResource::class;
}
