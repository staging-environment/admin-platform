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
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class NotificacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'notificaciones';

    protected static ?string $title = 'Notificaciones';
    protected static ?string $modelLabel = 'Aviso';
    protected static ?string $pluralModelLabel = 'Avisos y Notificaciones';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('ver_notificaciones_empleados');
    }

    protected function canCreate(): bool
    {
        return auth()->user()->can('editar_notificaciones_empleados');
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->can('editar_notificaciones_empleados');
    }

    protected function canDelete(Model $record): bool
    {
        return auth()->user()->can('editar_notificaciones_empleados');
    }

    public function isReadOnly(): bool
    {
        return !auth()->user()->can('editar_notificaciones_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
                    ->label('Tipo de Aviso')
                    ->options([
                        'Comunicados' => 'Comunicados',
                        'Avisos internos' => 'Avisos internos',
                        'Vencimientos' => 'Vencimientos',
                        'Recordatorios' => 'Recordatorios',
                    ])
                    ->required(),
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Textarea::make('contenido')
                    ->label('Contenido')
                    ->required()
                    ->rows(3),
                DatePicker::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->date(),
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
