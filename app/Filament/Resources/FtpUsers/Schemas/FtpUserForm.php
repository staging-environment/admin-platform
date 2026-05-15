<?php

namespace App\Filament\Resources\FtpUsers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class FtpUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->password()
                    ->required()
                    ->revealable() // Para que podáis verla con el icono del ojo
                    ->maxLength(255),
            ]);
    }
}
