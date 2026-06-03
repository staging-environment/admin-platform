<?php

namespace App\Filament\Resources\JobOffers\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use App\Models\JobApplication;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Candidatos Inscritos';

    protected static ?string $modelLabel = 'Candidatura';

    protected static ?string $pluralModelLabel = 'Candidaturas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->disabled(),

                TextInput::make('last_name')
                    ->label('Apellidos')
                    ->disabled(),

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->disabled(),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->disabled(),

                TextInput::make('years_of_experience')
                    ->label('Años de experiencia')
                    ->disabled(),

                TextInput::make('incorporation_time')
                    ->label('Tiempo de incorporación')
                    ->disabled(),

                \Filament\Forms\Components\Toggle::make('travel_possibility')
                    ->label('Posibilidad de viajar')
                    ->disabled(),

                Textarea::make('profile_description')
                    ->label('Descripción / Perfil')
                    ->disabled()
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('cover_letter')
                    ->label('Carta de Presentación')
                    ->disabled()
                    ->rows(3)
                    ->columnSpanFull(),

                \Filament\Forms\Components\Placeholder::make('cv_download_link')
                    ->label('Currículum Adjunto')
                    ->content(function ($record) {
                        if (!$record || !$record->cv_path) {
                            return 'No hay currículum adjunto.';
                        }
                        return new \Illuminate\Support\HtmlString('<a href="' . route('admin.cv.download', $record->id) . '" target="_blank" style="color: #d97706; font-weight: bold; text-decoration: underline;">Descargar Currículum (' . basename($record->cv_path) . ')</a>');
                    })
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

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
                    ->searchable()
                    ->sortable(),

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

                \Filament\Tables\Columns\IconColumn::make('travel_possibility')
                    ->label('Viajar')
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Nueva petición' => 'Nueva petición',
                        'En estudio' => 'En estudio',
                        'Aceptada' => 'Aceptada',
                        'Rechazada' => 'Rechazada',
                    ])
                    ->label('Estado'),
            ])
            ->headerActions([
                // No permitimos crear inscripciones directamente desde el panel
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver Detalles')
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
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
