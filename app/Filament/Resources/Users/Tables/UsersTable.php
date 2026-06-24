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

                TextColumn::make('centro_trabajo')
                    ->label('Centro de Trabajo')
                    ->getStateUsing(fn ($record) => $record->empleado?->contratos?->sortByDesc('fecha_inicio')?->first()?->centro_trabajo ?? '-'),

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
                \Filament\Tables\Filters\SelectFilter::make('centro_trabajo')
                    ->label('Centro de Trabajo')
                    ->options([
                        'Sevilla' => 'Sevilla',
                        'Utrera' => 'Utrera',
                        'El Cuervo' => 'El Cuervo',
                        'Lebrija' => 'Lebrija',
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            $data['value'],
                            fn (\Illuminate\Database\Eloquent\Builder $query, $value) => $query->whereHas('empleado.contratos', function ($q) use ($value) {
                                $q->where('centro_trabajo', $value);
                            })
                        );
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
