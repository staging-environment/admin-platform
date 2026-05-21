<?php

namespace App\Filament\Resources\FtpUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FtpUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dir')
                    ->label('Directorio')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('uid')
                    ->label('UID')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('gid')
                    ->label('GID')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
