<?php

namespace App\Filament\Resources\Gasolineras\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class GasolinerasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('Nombre')
                    ->label('Nombre Comercial')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('Direccion')
                    ->label('Dirección')
                    ->limit(30)
                    ->searchable(),
                
                TextColumn::make('Poblacion')
                    ->label('Población')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('Provincia')
                    ->label('Provincia')
                    ->sortable(),
                
                TextColumn::make('marca')
                    ->label('Marca / Bandera')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'REPSOL' => 'warning',
                        'BP' => 'info',
                        'CEPSA' => 'danger',
                        'GALP' => 'orange',
                        'DISA/SHELL' => 'amber',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                // Deshabilitamos el borrado masivo por seguridad
            ]);
    }
}
