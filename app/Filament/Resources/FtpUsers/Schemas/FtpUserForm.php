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
                TextInput::make('user')
                    ->label('Usuario')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->revealable()
                    ->maxLength(255),

                TextInput::make('dir')
                    ->label('Directorio')
                    ->required()
                    ->default(fn (?string $state, $get) => $state ?: '/home/ftpusers/' . $get('user'))
                    ->maxLength(255),

                TextInput::make('uid')
                    ->label('UID')
                    ->numeric()
                    ->default(1000)
                    ->required(),

                TextInput::make('gid')
                    ->label('GID')
                    ->numeric()
                    ->default(1000)
                    ->required(),
            ]);
    }
}
