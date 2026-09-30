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
                    ->label('Nombre del rol')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Correo de notificación')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->placeholder('Sin correo asignado')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->iconButton(),
                \Filament\Actions\DeleteAction::make()
                    ->iconButton()
                    ->before(function (\Filament\Actions\DeleteAction $action, $record) {
                        $hasUsers = $record->users()->exists();
                        if ($hasUsers) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('No se puede eliminar el rol')
                                ->body('Existen usuarios asignados a este rol. Debe cambiar el rol o eliminar a esos usuarios antes de poder borrar el rol.')
                                ->send();

                            $ction->cancel();
                        }
                    })
            ])
            ->bulkActions([]);
    }
}
