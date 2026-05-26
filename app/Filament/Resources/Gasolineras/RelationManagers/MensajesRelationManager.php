<?php

namespace App\Filament\Resources\Gasolineras\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class MensajesRelationManager extends RelationManager
{
    protected static string $relationship = 'mensajes';

    protected static ?string $title = 'Mensajes de Contacto Recibidos';

    protected static ?string $modelLabel = 'Mensaje';

    protected static ?string $pluralModelLabel = 'Mensajes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->disabled(),
                
                TextInput::make('email')
                    ->label('Email')
                    ->disabled(),
                
                Textarea::make('mensaje')
                    ->label('Mensaje')
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),
                
                TextInput::make('created_at')
                    ->label('Fecha de Envío')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('nombre')
                    ->label('Remitente')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('mensaje')
                    ->label('Mensaje')
                    ->limit(50)
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // No permitimos crear mensajes de contacto desde el panel
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver Mensaje'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
