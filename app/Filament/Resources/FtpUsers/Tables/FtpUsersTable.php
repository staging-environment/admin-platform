<?php

namespace App\Filament\Resources\FtpUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn; // Importamos el componente IconColumn

class FtpUsersTable
{
    public static function configure(\Filament\Tables\Table $table): \Filament\Tables\Table // Usando FQCN
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
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
