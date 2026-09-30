<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Nombre del rol')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo electrónico de notificación')
                    ->email()
                    ->placeholder('ejemplo@utrecar.com')
                    ->helperText('Los avisos y notificaciones dirigidos a este rol se enviarán a esta dirección.')
                    ->maxLength(255),
            ]);
    }
}
