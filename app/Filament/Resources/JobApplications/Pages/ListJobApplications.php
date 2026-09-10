<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Traits\HasMenuBreadcrumbs;
class ListJobApplications extends ListRecords
{
    use HasMenuBreadcrumbs;

    protected static string $resource = JobApplicationResource::class;

    public function mount(): void
    {
        parent::mount();

        if (request()->has('job_offer_id')) {
            $this->tableFilters['job_offer_id'] = [
                'value' => request()->query('job_offer_id'),
            ];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
