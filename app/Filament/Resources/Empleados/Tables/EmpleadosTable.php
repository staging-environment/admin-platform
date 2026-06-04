<?php

namespace App\Filament\Resources\Empleados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class EmpleadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('apellidos')
                    ->label('Apellidos')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('telefono_principal')
                    ->label('Teléfono'),

                TextColumn::make('provincia')
                    ->label('Provincia')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('contratos.centro_trabajo')
                    ->label('Centro de Trabajo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('apellidos', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('provincia')
                    ->label('Provincia')
                    ->options(function () {
                        return \App\Models\Empleado::query()
                            ->select('provincia')
                            ->whereNotNull('provincia')
                            ->where('provincia', '!=', '')
                            ->distinct()
                            ->pluck('provincia', 'provincia')
                            ->toArray();
                    }),
                \Filament\Tables\Filters\SelectFilter::make('localidad')
                    ->label('Localidad')
                    ->options(function () {
                        return \App\Models\Empleado::query()
                            ->select('localidad')
                            ->whereNotNull('localidad')
                            ->where('localidad', '!=', '')
                            ->distinct()
                            ->pluck('localidad', 'localidad')
                            ->toArray();
                    }),
                \Filament\Tables\Filters\SelectFilter::make('contratos.centro_trabajo')
                    ->label('Centro de Trabajo (Empresa)')
                    ->options(function () {
                        return \App\Models\EmpleadoContrato::query()
                            ->select('centro_trabajo')
                            ->whereNotNull('centro_trabajo')
                            ->where('centro_trabajo', '!=', '')
                            ->distinct()
                            ->pluck('centro_trabajo', 'centro_trabajo')
                            ->toArray();
                    }),
                \Filament\Tables\Filters\TernaryFilter::make('origen')
                    ->label('Origen del Empleado')
                    ->placeholder('Todos')
                    ->trueLabel('Importados de Virtus')
                    ->falseLabel('Creados Manualmente')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('virtus_codigo'),
                        false: fn ($query) => $query->whereNull('virtus_codigo'),
                        blank: fn ($query) => $query,
                    ),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                \Filament\Actions\ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
