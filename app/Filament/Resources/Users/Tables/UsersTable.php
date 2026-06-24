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
                    ->sortable(),

                TextColumn::make('name')
                    ->sortable(),

                TextColumn::make('email'),

                TextColumn::make('telefono')
                    ->label('Teléfono'),

                TextColumn::make('roles.name') // 🔥 magia aquí
                ->label('Roles')
                    ->badge()
                    ->separator(','),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
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
