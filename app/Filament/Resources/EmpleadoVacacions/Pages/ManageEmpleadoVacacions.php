<?php

namespace App\Filament\Resources\EmpleadoVacacions\Pages;

use App\Filament\Resources\EmpleadoVacacions\EmpleadoVacacionResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageEmpleadoVacacions extends ManageRecords
{
    protected static string $resource = EmpleadoVacacionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'nuevas' => Tab::make('Nuevas (Pendientes)')
                ->badge(\App\Models\EmpleadoVacacion::where('estado', 'Pendiente')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'Pendiente')),
            'aprobadas' => Tab::make('Aprobadas')
                ->badge(\App\Models\EmpleadoVacacion::whereIn('estado', ['Aceptada', 'Aprobada'])->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('estado', ['Aceptada', 'Aprobada'])),
            'denegadas' => Tab::make('Denegadas')
                ->badge(\App\Models\EmpleadoVacacion::whereIn('estado', ['Rechazada', 'Denegada'])->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('estado', ['Rechazada', 'Denegada'])),
            'todas' => Tab::make('Todas'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'nuevas';
    }
}
