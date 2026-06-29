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
                        ->size(36)
                        ->grow(false),

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
                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                        ->grow(false),

                    TextColumn::make('detalles_empleado')
                        ->color('gray')
                        ->size('sm')
                        ->state(function ($record) {
                            $parts = [];
                            if ($record->dni) $parts[] = $record->dni;
                            if ($record->telefono_principal) $parts[] = $record->telefono_principal;
                            if ($record->email) $parts[] = $record->email;
                            $loc = trim($record->localidad ?? '');
                            $prov = trim($record->provincia ?? '');
                            if ($loc !== '' && $prov !== '') {
                                $parts[] = "$loc ($prov)";
                            } elseif ($loc !== '') {
                                $parts[] = $loc;
                            } elseif ($prov !== '') {
                                $parts[] = $prov;
                            }
                            return implode('  •  ', $parts);
                        }),
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
                \Filament\Tables\Filters\Filter::make('search')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('query')
                            ->label('Buscar')
                            ->placeholder('Nombre, DNI, email o teléfono...'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['query'],
                            fn (\Illuminate\Database\Eloquent\Builder $query, $search) => $query->where(function ($q) use ($search) {
                                $q->where('nombre', 'like', "%{$search}%")
                                  ->orWhere('apellidos', 'like', "%{$search}%")
                                  ->orWhere('dni', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%")
                                  ->orWhere('telefono_principal', 'like', "%{$search}%");
                            })
                        );
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
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
