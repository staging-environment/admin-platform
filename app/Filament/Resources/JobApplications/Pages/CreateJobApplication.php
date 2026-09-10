<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Resources\Pages\CreateRecord;

use App\Filament\Traits\HasMenuBreadcrumbs;
class CreateJobApplication extends CreateRecord
{
    use HasMenuBreadcrumbs;

    protected static string $resource = JobApplicationResource::class;
}
