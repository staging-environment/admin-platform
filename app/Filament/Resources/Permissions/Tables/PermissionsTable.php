<?php

namespace App\Filament\Resources\Permissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),

                TextColumn::make('name')
                    ->label('Permiso')
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('-', ' ', $state)))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Acción')
                    ->getStateUsing(fn ($record) => explode('-', $record->name)[0])
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'view' => 'gray',
                        'create' => 'success',
                        'edit' => 'warning',
                        'delete' => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('resource')
                    ->label('Recurso')
                    ->getStateUsing(fn ($record) => ucfirst(explode('-', $record->name)[1] ?? '')),

                TextColumn::make('guard_name')
                    ->label('Guard'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
