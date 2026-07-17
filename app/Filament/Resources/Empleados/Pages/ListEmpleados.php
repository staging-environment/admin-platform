<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmpleados extends ListRecords
{
    protected static string $resource = EmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public ?string $tableSort = 'apellidos:asc';

    public function mount(): void
    {
        parent::mount();

        if (!$this->tableSort) {
            $this->tableSort = 'apellidos:asc';
        }
    }
}
