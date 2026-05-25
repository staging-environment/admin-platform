<?php

namespace App\Filament\Resources\FtpUsers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select; // Importamos el componente Select

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

                // Campo de rol reintroducido
                Select::make('role')
                    ->label('Rol')
                    ->options([
                        'editor' => 'Editor (Puede subir, descargar, eliminar)',
                        'viewer' => 'Visor (Solo puede descargar)',
                    ])
                    ->required()
                    ->default('editor'),

                TextInput::make('dir')
                    ->label('Directorio')
                    ->required()
                    ->default(fn (?string $state, $get) => $state ?: '/home/ftpusers/' . $get('user'))
                    ->maxLength(255),

                // UID y GID se gestionan en el modelo, pero los mantenemos aquí para visibilidad si se desea editar
                TextInput::make('uid')
                    ->label('UID')
                    ->numeric()
                    ->default(1000) // Ajustado a 1000 (developer)
                    ->required(),

                TextInput::make('gid')
                    ->label('GID')
                    ->numeric()
                    ->default(33) // Ajustado a 33 (www-data)
                    ->required(),

                // Campos de permisos granulares
                Checkbox::make('can_upload')
                    ->label('Puede Subir Archivos')
                    ->default(true),

                Checkbox::make('can_download')
                    ->label('Puede Descargar Archivos')
                    ->default(true),

                Checkbox::make('can_delete')
                    ->label('Puede Eliminar Archivos')
                    ->default(true),
            ]);
    }
}
