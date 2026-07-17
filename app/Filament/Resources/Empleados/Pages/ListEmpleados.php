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

    public ?string $tableSortColumn = 'apellidos';
    public ?string $tableSortDirection = 'asc';

    public function mount(): void
    {
        parent::mount();

        if (!$this->tableSortColumn) {
            $this->tableSortColumn = 'apellidos';
            $this->tableSortDirection = 'asc';
        }
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();
        \Illuminate\Support\Facades\Log::warning('TABLE QUERY SQL: ' . $query->toSql() . ' | Orders: ' . json_encode($query->getQuery()->orders));
        return $query;
    }
}
