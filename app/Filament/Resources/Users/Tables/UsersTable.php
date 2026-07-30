<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([10, 20, 50, 100])
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->getStateUsing(function ($record) {
                        $empleado = $record->empleado ?: \App\Models\Empleado::onlyTrashed()->where('email', $record->email)->first();
                        $nombre = $empleado ? trim($empleado->nombre) : trim($record->name);
                        $apellidos = $empleado ? trim($empleado->apellidos ?? '') : '';

                        if (empty($apellidos)) {
                            $parts = preg_split('/\s+/', $nombre);
                            if (count($parts) > 1) {
                                $firstName = array_shift($parts);
                                $lastName = implode(' ', $parts);
                                return mb_strtoupper($lastName) . ', ' . mb_strtoupper($firstName);
                            }
                            return mb_strtoupper($nombre);
                        }

                        $parts = preg_split('/\s+/', $apellidos);
                        $primerApellido = array_shift($parts);
                        $segundoApellido = count($parts) > 0 ? implode(' ', $parts) : '';

                        if ($segundoApellido !== '') {
                            return mb_strtoupper($primerApellido) . ' ' . mb_strtoupper($segundoApellido) . ', ' . mb_strtoupper($nombre);
                        } else {
                            return mb_strtoupper($primerApellido) . ', ' . mb_strtoupper($nombre);
                        }
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhereHas('empleado', function ($eq) use ($search) {
                                  $eq->where('nombre', 'like', "%{$search}%")
                                     ->orWhere('apellidos', 'like', "%{$search}%");
                              });
                        });
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy(
                            \Illuminate\Support\Facades\DB::raw("COALESCE((
                                SELECT apellidos 
                                FROM empleados 
                                WHERE empleados.email = users.email 
                                LIMIT 1
                            ), users.name)"),
                            $direction
                        )->orderBy(
                            \Illuminate\Support\Facades\DB::raw("COALESCE((
                                SELECT nombre 
                                FROM empleados 
                                WHERE empleados.email = users.email 
                                LIMIT 1
                            ), '')"),
                            $direction
                        );
                    }),

                TextColumn::make('email'),

                TextColumn::make('localidad')
                    ->label('Localidad')
                    ->getStateUsing(function ($record) {
                        $empleado = $record->empleado ?: \App\Models\Empleado::onlyTrashed()->where('email', $record->email)->first();
                        return $empleado?->localidad ?? '-';
                    }),

                TextColumn::make('roles.name') // 🔥 magia aquí
                ->label('Roles')
                    ->badge()
                    ->separator(','),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->label('Rol'),
                \Filament\Tables\Filters\Filter::make('search')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('query')
                            ->label('Buscar')
                            ->placeholder('Nombre, email o teléfono...'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['query'],
                            fn (\Illuminate\Database\Eloquent\Builder $query, $search) => $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%")
                                  ->orWhere('telefono', 'like', "%{$search}%");
                            })
                        );
                    }),
                \Filament\Tables\Filters\SelectFilter::make('localidad')
                    ->label('Localidad')
                    ->options([
                        'Sevilla' => 'Sevilla',
                        'Utrera' => 'Utrera',
                        'El Cuervo' => 'El Cuervo',
                        'Lebrija' => 'Lebrija',
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['value'],
                            fn (\Illuminate\Database\Eloquent\Builder $query, $value) => $query->whereHas('empleado', function ($q) use ($value) {
                                $q->withTrashed()->where('localidad', $value);
                            })
                        );
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Editar'),
                \Filament\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Borrar')
                    ->visible(fn () => auth()->user()->can('gestion_eliminar_usuarios'))
                    ->before(function ($record) {
                        $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                        if ($empleado) {
                            foreach ($empleado->documentos as $doc) {
                                if ($doc->file_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($doc->file_path)) {
                                    \Illuminate\Support\Facades\Storage::disk('local')->delete($doc->file_path);
                                }
                                $doc->forceDelete();
                            }
                            $empleado->horarios()->delete();
                            $empleado->ausencias()->delete();
                            $empleado->vacaciones()->delete();
                            $empleado->notificaciones()->delete();
                            $empleado->fichajes()->delete();
                            $empleado->cursos()->delete();
                            $empleado->comentarios()->delete();
                            $empleado->alertas()->delete();
                            $empleado->forceDelete();
                        }
                    }),
                \Filament\Actions\Action::make('restoreEmpleado')
                    ->label('Restaurar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Restaurar')
                    ->requiresConfirmation()
                    ->modalHeading('¿Restaurar empleado?')
                    ->modalDescription('Esta acción volverá a activar al empleado y te redirigirá a su edición.')
                    ->visible(function ($record) {
                        if (!$record->hasRole('Empleado')) {
                            return false;
                        }
                        $empleado = \App\Models\Empleado::onlyTrashed()->where('email', $record->email)->first();
                        return $empleado !== null;
                    })
                    ->action(function ($record) {
                        $empleado = \App\Models\Empleado::onlyTrashed()->where('email', $record->email)->first();
                        if ($empleado) {
                            $empleado->restore();
                            if (!$record->hasRole('Empleado')) {
                                $record->assignRole('Empleado');
                            }
                            return redirect()->to(route('filament.admin.resources.recursos-humanos.edit', ['record' => $empleado->id]));
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->can('gestion_eliminar_usuarios'))
                        ->before(function (\Illuminate\Support\Collection $records) {
                            foreach ($records as $record) {
                                $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                                if ($empleado) {
                                    foreach ($empleado->documentos as $doc) {
                                        if ($doc->file_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($doc->file_path)) {
                                            \Illuminate\Support\Facades\Storage::disk('local')->delete($doc->file_path);
                                        }
                                        $doc->forceDelete();
                                    }
                                    $empleado->horarios()->delete();
                                    $empleado->ausencias()->delete();
                                    $empleado->vacaciones()->delete();
                                    $empleado->notificaciones()->delete();
                                    $empleado->fichajes()->delete();
                                    $empleado->cursos()->delete();
                                    $empleado->comentarios()->delete();
                                    $empleado->alertas()->delete();
                                    $empleado->forceDelete();
                                }
                            }
                        }),
                ]),
            ]);
    }
}
