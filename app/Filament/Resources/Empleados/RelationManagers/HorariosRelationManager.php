<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class HorariosRelationManager extends RelationManager
{
    protected static string $relationship = 'horarios';

    protected static ?string $title = 'Horario Laboral';
    protected static ?string $modelLabel = 'Horario/Turno';
    protected static ?string $pluralModelLabel = 'Horarios Laborales';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('gestion_horarios_empleados');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('tipo_jornada')
                            ->label('Tipo de Jornada')
                            ->options([
                                'Completa' => 'Jornada Completa',
                                'Parcial' => 'Jornada Parcial',
                                'Reducida' => 'Jornada Reducida',
                                'Otros' => 'Otros',
                            ])
                            ->required(),
                        TextInput::make('turnos')
                            ->label('Turnos Asignados (Opcional)')
                            ->placeholder('Ej. Mañana, Tarde, Rotativo...')
                            ->maxLength(255),
                    ]),
                CheckboxList::make('dias_laborales')
                    ->label('Días Laborales')
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                        'Domingo' => 'Domingo',
                    ])
                    ->columns(7)
                    ->columnSpan('full')
                    ->required(),
                Grid::make(2)
                    ->schema([
                        TimePicker::make('hora_inicio')
                            ->label('Hora de Inicio')
                            ->required(),
                        TimePicker::make('hora_fin')
                            ->label('Hora de Fin')
                            ->required(),
                    ]),
                Textarea::make('horarios')
                    ->label('Detalles del Horario')
                    ->placeholder('Ej. Lunes a Viernes de 9:00 a 18:00...')
                    ->required()
                    ->rows(3)
                    ->columnSpan('full'),
                FileUpload::make('calendario_laboral_path')
                     ->label('Calendario Laboral (PDF/Imagen)')
                     ->directory('empleados/calendarios')
                     ->disk('local')
                     ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo_jornada')
            ->columns([
                TextColumn::make('tipo_jornada')
                    ->label('Tipo de Jornada')
                    ->badge(),
                TextColumn::make('dias_laborales')
                    ->label('Días Laborales')
                    ->badge()
                    ->separator(', '),
                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->time('H:i'),
                TextColumn::make('hora_fin')
                    ->label('Fin')
                    ->time('H:i'),
                TextColumn::make('turnos')
                    ->label('Turnos')
                    ->default('-'),
                TextColumn::make('calendario_laboral_path')
                    ->label('Calendario Adjunto')
                    ->formatStateUsing(fn ($state) => $state ? 'Descargar' : 'Sin archivo')
                    ->url(fn ($record) => $record->calendario_laboral_path ? route('admin.recursos_humanos.descargar_archivo', ['path' => $record->calendario_laboral_path]) : null, true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_horarios_empleados')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_horarios_empleados')),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_horarios_empleados')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
