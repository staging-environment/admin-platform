<?php

namespace App\Filament\Resources\Empleados\Pages;

use App\Filament\Resources\Empleados\EmpleadoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmpleado extends ViewRecord
{
    protected static string $resource = EmpleadoResource::class;

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                $this->getInfolistContentComponent()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'ficha-empleado-container']),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Modificar datos'),
            \Filament\Actions\Action::make('manageDocuments')
                ->label('Documentación')
                ->icon('heroicon-o-document-duplicate')
                ->color('warning')
                ->modalHeading('Documentación del Empleado')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record]))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
        ];
    }
}
