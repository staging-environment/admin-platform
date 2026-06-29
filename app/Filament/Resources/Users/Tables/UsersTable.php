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
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->sortable(),

                TextColumn::make('email'),

                TextColumn::make('telefono')
                    ->label('Teléfono'),

                TextColumn::make('localidad')
                    ->label('Localidad')
                    ->getStateUsing(fn ($record) => $record->empleado?->localidad ?? '-'),

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
                                $q->where('localidad', $value);
                            })
                        );
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('gestion_eliminar_usuarios')),
                \Filament\Tables\Actions\Action::make('restoreEmpleado')
                    ->label('Restaurar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
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
                        ->visible(fn () => auth()->user()->can('gestion_eliminar_usuarios')),
                ]),
            ]);
    }
}
