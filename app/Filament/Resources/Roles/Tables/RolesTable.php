<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\DeleteAction::make()
                    ->before(function (\Filament\Actions\DeleteAction $action, $record) {
                        $hasUsers = $record->users()->exists();
                        if ($hasUsers) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('No se puede eliminar el rol')
                                ->body('Existen usuarios asignados a este rol. Debe cambiar el rol o eliminar a esos usuarios antes de poder borrar el rol.')
                                ->send();

                            $action->cancel();
                        }
                    })
            ])
            ->bulkActions([]);
    }
}
