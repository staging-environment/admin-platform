<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
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

                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(50)
                    ->disabled(fn () => auth()->user()?->email !== 'jarodriguezbonilla@gmail.com'),

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
                    ->columns(2)
                    ->live(),

                Toggle::make('usuario_activo')
                    ->label('Usuario activo')
                    ->live()
                    ->afterStateHydrated(function (Toggle $component, $record) {
                        if ($record) {
                            $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                            $component->state($empleado ? !$empleado->trashed() : true);
                        } else {
                            $component->state(true);
                        }
                    })
                    ->visible(function (Get $get, $record) {
                        $roles = $get('roles') ?? [];
                        $empleadoRole = \Spatie\Permission\Models\Role::where('name', 'Empleado')->first();
                        $hasEmpleadoRoleSelected = $empleadoRole && in_array($empleadoRole->id, $roles);
                        if ($hasEmpleadoRoleSelected) {
                            return true;
                        }
                        if ($record) {
                            return \App\Models\Empleado::withTrashed()->where('email', $record->email)->exists();
                        }
                        return false;
                    }),
            ]);
    }
}
