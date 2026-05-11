<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash; // Importante por si acaso

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                // --- EL CAMPO QUE FALTABA ---
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    // Solo obligatorio cuando estamos CREANDO un usuario
                    ->required(fn ($context) => $context === 'create')
                    // Evita que se sobrescriba con vacío si editamos y no ponemos nada
                    ->dehydrated(fn ($state) => filled($state))
                    ->revealable()
                    ->maxLength(255),
                // ----------------------------

                CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->columns(2),
            ]);
    }
}
