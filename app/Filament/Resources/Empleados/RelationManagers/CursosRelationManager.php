<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CursosRelationManager extends RelationManager
{
    protected static string $relationship = 'cursos';

    protected static ?string $title = 'Cursos';
    protected static ?string $modelLabel = 'Curso';
    protected static ?string $pluralModelLabel = 'Cursos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('ver_cursos_empleados');
    }

    protected function canCreate(): bool
    {
        return auth()->user()->can('editar_cursos_empleados');
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar_cursos_empleados');
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()->can('editar_cursos_empleados');
    }

    public function isReadOnly(): bool
    {
        return !auth()->user()->can('editar_cursos_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre_curso')
                    ->label('Nombre del Curso')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio'),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin'),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'Asignado' => 'Asignado',
                        'En curso' => 'En curso',
                        'Realizado' => 'Realizado',
                    ])
                    ->required(),
                FileUpload::make('certificado_path')
                    ->label('Certificado (PDF/Imagen)')
                    ->directory('empleados/certificados')
                    ->disk('local'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre_curso')
            ->columns([
                TextColumn::make('nombre_curso')
                    ->label('Nombre del Curso')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Realizado' => 'success',
                        'En curso' => 'warning',
                        'Asignado' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('certificado_path')
                    ->label('Certificado')
                    ->formatStateUsing(fn ($state) => $state ? 'Descargar' : 'No aportado')
                    ->url(fn ($record) => $record->certificado_path ? route('admin.recursos_humanos.descargar_archivo', ['path' => $record->certificado_path]) : null, true),
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
