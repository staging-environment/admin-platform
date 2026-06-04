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
            ->recordUrl(fn (\App\Models\Empleado $record): string => \App\Filament\Resources\Empleados\EmpleadoResource::getUrl('view', ['record' => $record]))
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?background=f59e0b&color=fff&name=E+M'))
                    ->size(40),

                TextColumn::make('nombre_completo')
                    ->label('Empleado')
                    ->state(fn (\App\Models\Empleado $record) => trim($record->nombre . ' ' . $record->apellidos))
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable(['nombre', 'apellidos'])
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->description(fn (\App\Models\Empleado $record) => $record->dni ?? 'Sin DNI'),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->copyable()
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('telefono_principal')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone')
                    ->searchable(),

                TextColumn::make('provincia')
                    ->label('Provincia')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('contratos.centro_trabajo')
                    ->label('Centro de Trabajo')
                    ->icon('heroicon-m-building-office-2')
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
