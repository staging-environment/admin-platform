<?php

namespace App\Filament\Resources\FtpUsers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox; // Importamos el componente Checkbox

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
                    ->live(onBlur: true)
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
                    ->live(onBlur: true)
                    ->maxLength(255),

                TextInput::make('uid')
                    ->label('UID')
                    ->numeric()
                    ->default(1000)
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                TextInput::make('gid')
                    ->label('GID')
                    ->numeric()
                    ->default(33)
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                // Nuevos campos de permisos
                Checkbox::make('can_upload')
                    ->label('Puede Subir Archivos')
                    ->inline(false)
                    ->dehydrated()
                    ->default(true), // Por defecto, permitir subir

                Checkbox::make('can_download')
                    ->label('Puede Descargar Archivos')
                    ->inline(false)
                    ->dehydrated()
                    ->default(true), // Por defecto, permitir descargar

                Checkbox::make('can_delete')
                    ->label('Puede Eliminar Archivos')
                    ->inline(false)
                    ->dehydrated()
                    ->default(true), // Por defecto, permitir eliminar
            ]);
    }
}
