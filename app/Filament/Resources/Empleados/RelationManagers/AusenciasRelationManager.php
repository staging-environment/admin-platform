<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AusenciasRelationManager extends RelationManager
{
    protected static string $relationship = 'ausencias';

    protected static ?string $title = 'Ausencias y Bajas';
    protected static ?string $modelLabel = 'Ausencia';
    protected static ?string $pluralModelLabel = 'Ausencias y Bajas';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('gestion_ausencias_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Tipo de Ausencia')
                    ->options([
                        'Incapacidades' => 'Incapacidad',
                        'Bajas médicas' => 'Baja médica',
                        'Permisos' => 'Permiso',
                        'Faltas' => 'Falta injustificada',
                        'Justificantes' => 'Falta justificada',
                    ])
                    ->required(),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin'),
                FileUpload::make('justificante_path')
                    ->label('Justificante / Documento médico')
                    ->directory('empleados/justificantes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo')
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo de Ausencia')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Bajas médicas' => 'danger',
                        'Incapacidades' => 'danger',
                        'Faltas' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date()
                    ->default('-'),
                TextColumn::make('justificante_path')
                    ->label('Justificante')
                    ->formatStateUsing(fn ($state) => $state ? 'Descargar' : 'Sin justificante')
                    ->url(fn ($record) => $record->justificante_path ? route('admin.recursos_humanos.descargar_archivo', ['path' => $record->justificante_path]) : null, true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
