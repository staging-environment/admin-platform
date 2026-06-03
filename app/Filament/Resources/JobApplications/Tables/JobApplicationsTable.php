<?php

namespace App\Filament\Resources\JobApplications\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\JobApplication;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jobOffer.title')
                    ->label('Oferta de Empleo')
                    ->searchable()
                    ->sortable()
                    ->url(fn (JobApplication $record) => $record->job_offer_id ? route('filament.admin.resources.job-offers.edit', ['record' => $record->job_offer_id]) : null)
                    ->color('primary'),

                TextColumn::make('first_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('Apellidos')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Teléfono'),

                TextColumn::make('years_of_experience')
                    ->label('Años de experiencia')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('incorporation_time')
                    ->label('Tiempo de incorporación')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('travel_possibility')
                    ->label('Viajar')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_read')
                    ->label('Leído')
                    ->boolean()
                    ->sortable(),

                SelectColumn::make('status')
                    ->label('Estado')
                    ->options([
                        'Nueva petición' => 'Nueva petición',
                        'En estudio' => 'En estudio',
                        'Aceptada' => 'Aceptada',
                        'Rechazada' => 'Rechazada',
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Inscripción')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('job_offer_id')
                    ->options(fn () => \App\Models\JobOffer::pluck('title', 'id')->toArray())
                    ->label('Oferta de Empleo'),
                SelectFilter::make('status')
                    ->options([
                        'Nueva petición' => 'Nueva petición',
                        'En estudio' => 'En estudio',
                        'Aceptada' => 'Aceptada',
                        'Rechazada' => 'Rechazada',
                    ])
                    ->label('Estado'),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                ViewAction::make()
                    ->before(function (JobApplication $record) {
                        if (!$record->is_read) {
                            $record->update(['is_read' => true]);
                        }
                    }),
                EditAction::make()
                    ->before(function (JobApplication $record) {
                        if (!$record->is_read) {
                            $record->update(['is_read' => true]);
                        }
                    }),
                Action::make('download_cv')
                    ->label('Descargar CV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (JobApplication $record) {
                        $disk = \Illuminate\Support\Facades\Storage::disk('private_cvs');
                        if (!$disk->exists($record->cv_path)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Archivo no encontrado')
                                ->body('El archivo del currículum no existe en el almacenamiento.')
                                ->danger()
                                ->send();
                            return;
                        }
                        return $disk->download($record->cv_path);
                    })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
