<?php

namespace App\Filament\Resources\ContactoMensajes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactoMensajesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gasolinera.Nombre')
                    ->label('Origen')
                    ->default('Página Principal')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre')
                    ->label('Remitente')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                IconColumn::make('is_read')
                    ->label('Leído')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->before(function (\App\Models\ContactoMensaje $record) {
                        if (!$record->is_read) {
                            $record->update(['is_read' => true]);
                        }
                    }),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
