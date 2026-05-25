<?php

namespace App\Filament\Resources\FtpUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn; // Importamos el componente IconColumn
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

                // Columna de rol reintroducida
                TextColumn::make('role')
                    ->label('Rol')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dir')
                    ->label('Directorio')
                    ->searchable()
                    ->sortable(),

                // Columnas de permisos granulares
                IconColumn::make('can_upload')
                    ->label('Subir')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('can_download')
                    ->label('Descargar')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('can_delete')
                    ->label('Eliminar')
                    ->boolean()
                    ->toggleable(),

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
