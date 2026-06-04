<?php

namespace App\Filament\Resources\Empleados\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContratosRelationManager extends RelationManager
{
    protected static string $relationship = 'contratos';

    protected static ?string $title = 'Contratos Laborales';
    protected static ?string $modelLabel = 'Contrato';
    protected static ?string $pluralModelLabel = 'Contratos Laborales';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('gestion_contratos_empleados');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tipo_contrato')
                    ->label('Tipo de Contrato')
                    ->placeholder('Indefinido, Temporal, Prácticas...')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin (Opcional)'),
                Select::make('jornada')
                    ->label('Jornada')
                    ->options([
                        'Completa' => 'Jornada Completa',
                        'Parcial' => 'Jornada Parcial',
                    ])
                    ->required(),
                TextInput::make('salario')
                    ->label('Salario Bruto Anual / Mensual')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                TextInput::make('centro_trabajo')
                    ->label('Centro de Trabajo')
                    ->placeholder('Oficina Principal, Delegación Sevilla...')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo_contrato')
            ->columns([
                TextColumn::make('tipo_contrato')
                    ->label('Contrato')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date()
                    ->default('Indefinido'),
                TextColumn::make('jornada')
                    ->label('Jornada')
                    ->badge(),
                TextColumn::make('salario')
                    ->label('Salario')
                    ->money('EUR'),
                TextColumn::make('centro_trabajo')
                    ->label('Centro de Trabajo'),
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
