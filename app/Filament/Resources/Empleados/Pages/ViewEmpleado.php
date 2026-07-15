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
            \Filament\Actions\Action::make('dniDocuments')
                ->label('DNI')
                ->icon('heroicon-o-identification')
                ->color('warning')
                ->modalHeading('Documentos DNI')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'dni']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('contratosDocuments')
                ->label('Contratos')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->modalHeading('Documentos Contratos')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'contratos']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('formacionDocuments')
                ->label('Formación')
                ->icon('heroicon-o-academic-cap')
                ->color('warning')
                ->modalHeading('Documentos Formación')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'formacion']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('discapacidadDocuments')
                ->label('Discapacidad')
                ->icon('heroicon-o-heart')
                ->color(function ($record) {
                    $hasDocs = $record->documentos()->whereIn('tipo', ['Resolución Discapacidad', 'Dictamen Técnico', 'Certificado Discapacidad'])->exists();
                    return $hasDocs ? 'warning' : 'danger';
                })
                ->modalHeading('Documentos Discapacidad')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'discapacidad']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            \Filament\Actions\Action::make('incapacidadDocuments')
                ->label('Incapacidad')
                ->icon('heroicon-o-shield-check')
                ->color(function ($record) {
                    $hasDocs = $record->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists();
                    return $hasDocs ? 'warning' : 'danger';
                })
                ->modalHeading('Documentos Incapacidad')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalContent(fn ($record) => view('filament.pages.documentos-modal', ['record' => $record, 'family' => 'incapacidad']))
                ->visible(fn () => auth()->user()->can('ver_documentacion_empleados')),
            EditAction::make()
                ->label('Modificar datos')
                ->color('info'),
        ];
    }
}
