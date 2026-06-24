<?php

namespace App\Filament\Resources\Empleados\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class EmpleadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (\App\Models\Empleado $record): string => \App\Filament\Resources\Empleados\EmpleadoResource::getUrl('view', ['record' => $record]))
            ->columns([
                Split::make([
                    ImageColumn::make('foto')
                        ->label('Foto')
                        ->circular()
                        ->defaultImageUrl(url('https://ui-avatars.com/api/?background=f59e0b&color=fff&name=E+M'))
                        ->size(80)
                        ->grow(false),

                    Stack::make([
                        TextColumn::make('nombre_completo_tarjeta')
                            ->state(function (\App\Models\Empleado $record) {
                                $nombre = trim($record->nombre);
                                $apellidos = trim($record->apellidos ?? '');

                                if (empty($apellidos)) {
                                    return mb_strtoupper($nombre);
                                }

                                $parts = preg_split('/\s+/', $apellidos);
                                $primerApellido = array_shift($parts);
                                $segundoApellido = count($parts) > 0 ? implode(' ', $parts) : '';

                                if ($segundoApellido !== '') {
                                    return mb_strtoupper($primerApellido) . ', ' . mb_strtoupper($segundoApellido) . ' ' . mb_strtoupper($nombre);
                                }
                                return mb_strtoupper($primerApellido) . ', ' . mb_strtoupper($nombre);
                            })
                            ->searchable(['nombre', 'apellidos'])
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->size('lg'),

                        TextColumn::make('dni')
                            ->icon('heroicon-m-identification')
                            ->color('gray')
                            ->state(fn ($record) => $record->dni ?? 'Sin DNI'),

                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope')
                            ->color('gray')
                            ->state(fn ($record) => $record->email ?? 'Sin Email'),

                        TextColumn::make('telefono_principal')
                            ->icon('heroicon-m-phone')
                            ->color('gray')
                            ->state(fn ($record) => $record->telefono_principal ?? 'Sin Teléfono'),

                        TextColumn::make('localidad_provincia')
                            ->icon('heroicon-m-map-pin')
                            ->color('gray')
                            ->state(function ($record) {
                                $loc = trim($record->localidad ?? '');
                                $prov = trim($record->provincia ?? '');
                                if ($loc !== '' && $prov !== '') {
                                    return "$loc ($prov)";
                                }
                                return $loc !== '' ? $loc : ($prov !== '' ? $prov : 'Sin localización');
                            }),
                    ])->space(1),
                ])
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->defaultSort('apellidos', 'asc')
            ->filters([
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
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
