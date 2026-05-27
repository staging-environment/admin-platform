<?php

namespace App\Filament\Resources\ContactoMensajes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactoMensajeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('gasolinera_codigo')
                    ->label('Código de Gasolinera')
                    ->disabled(),
                TextInput::make('nombre')
                    ->label('Remitente')
                    ->disabled(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->disabled(),
                Textarea::make('mensaje')
                    ->label('Mensaje')
                    ->disabled()
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->label('Leído')
                    ->disabled(),
            ]);
    }
}
