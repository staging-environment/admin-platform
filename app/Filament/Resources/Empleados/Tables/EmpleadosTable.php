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
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([10, 20, 50, 100])
            ->columns([
                \Filament\Tables\Columns\ViewColumn::make('apellidos')
                    ->label('Apellidos')
                    ->view('filament.tables.columns.nombre-con-alerta')
                    ->alignStart()
                    ->disabledClick()
                    ->extraAttributes([
                        'onclick' => 'event.stopPropagation()',
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->formatStateUsing(fn ($state) => mb_strtoupper(trim($state ?? '')))
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('telefono_principal')
                    ->label('Teléfono')
                    ->size('xs')
                    ->color('gray')
                    ->default('—'),

                 TextColumn::make('gasolinera.Nombre')
                    ->label('Ubicación de trabajo')
                    ->size('xs')
                    ->color('gray')
                    ->default('—'),

                TextColumn::make('puesto')
                    ->label('Puesto')
                    ->size('xs')
                    ->color('gray')
                    ->default('—'),
            ])
            ->striped()
            ->defaultSort('apellidos', 'asc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('centro_trabajo')
                    ->label('Ubicación de trabajo')
                    ->options([
                        'Sevilla' => 'Sevilla',
                        'Utrera' => 'Utrera',
                        'El Cuervo' => 'El Cuervo',
                        'Lebrija' => 'Lebrija',
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['value'],
                            function (\Illuminate\Database\Eloquent\Builder $query, $centro) {
                                $map = [
                                    'Sevilla' => 2,
                                    'Utrera' => 1,
                                    'El Cuervo' => 3,
                                    'Lebrija' => 4,
                                ];
                                $codigo = $map[$centro] ?? null;
                                if (!$codigo) return $query;

                                return $query->whereIn('id', function ($subQuery) use ($codigo) {
                                    $subQuery->select('ed1.empleado_id')
                                        ->from('empleado_documentos as ed1')
                                        ->where('ed1.tipo', 'Contratos')
                                        ->where('ed1.gasolinera_codigo', $codigo)
                                        ->whereRaw('ed1.id = (select ed2.id from empleado_documentos as ed2 where ed2.empleado_id = ed1.empleado_id and ed2.tipo = "Contratos" order by ed2.id desc limit 1)');
                                    });
                            }
                        );
                    }),
                \Filament\Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Alta' => 'Alta / Activo',
                        'Baja_Empresa' => 'Baja en la empresa',
                        'Baja_Medica' => 'Baja médica',
                    ])
                    ->default('Alta')
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['value'],
                            function (\Illuminate\Database\Eloquent\Builder $query, $value) {
                                if ($value === 'Alta') {
                                    return $query->where('estado', 'Alta');
                                }

                                if ($value === 'Baja_Empresa') {
                                    return $query->where('estado', 'Baja');
                                }

                                if ($value === 'Baja_Medica') {
                                    return $query->where('estado', 'Alta')
                                        ->whereHas('ausencias', function ($subQuery) {
                                            $subQuery->where('tipo', 'Bajas médicas')
                                                ->whereNull('fecha_fin');
                                        });
                                }

                                return $query;
                            }
                        );
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
            ->filtersFormColumns(3)
            ->actions([
                EditAction::make()->iconButton(),
                \Filament\Actions\DeleteAction::make()->iconButton(),
            ]);
    }
}
