<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Hash; // Importante por si acaso

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de usuario (Acceso)')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nombre_empleado')
                    ->label('Nombre (Ficha Empleado)')
                    ->visible(fn ($record) => $record && \App\Models\Empleado::withTrashed()->where('email', $record->email)->exists())
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                            $component->state($empleado?->nombre);
                        }
                    })
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                        if ($empleado) {
                            $empleado->update(['nombre' => $state]);
                        }
                    })
                    ->dehydrated(false),

                TextInput::make('apellidos_empleado')
                    ->label('Apellidos (Ficha Empleado)')
                    ->visible(fn ($record) => $record && \App\Models\Empleado::withTrashed()->where('email', $record->email)->exists())
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                            $component->state($empleado?->apellidos);
                        }
                    })
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                        if ($empleado) {
                            $empleado->update(['apellidos' => $state]);
                        }
                    })
                    ->dehydrated(false),

                TextInput::make('localidad_empleado')
                    ->label('Localidad (Ficha Empleado)')
                    ->visible(fn ($record) => $record && \App\Models\Empleado::withTrashed()->where('email', $record->email)->exists())
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                            $component->state($empleado?->localidad);
                        }
                    })
                    ->saveRelationshipsUsing(function ($record, $state) {
                        $empleado = \App\Models\Empleado::withTrashed()->where('email', $record->email)->first();
                        if ($empleado) {
                            $empleado->update(['localidad' => $state]);
                        }
                    })
                    ->dehydrated(false),

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

                TextInput::make('telegram_chat_id')
                    ->label('ID de Telegram')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('No asociado')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && $record->can('recibir_notificaciones_competencia')),

                Placeholder::make('telegram_instructions')
                    ->label('')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && $record->can('recibir_notificaciones_competencia'))
                    ->content(new HtmlString('
                        <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-md text-sm text-blue-900 space-y-2">
                            <p class="font-semibold flex items-center gap-1">
                                📢 Tienes activo el permiso para recibir alertas de competencia. Sigue estos pasos para configurar las alertas en tu móvil:
                            </p>
                            <ol class="list-decimal list-inside space-y-2 text-blue-800">
                                <li><b>Instala la aplicación de Telegram</b> en tu móvil desde Google Play Store (Android) o App Store (iPhone) si aún no la tienes instalada.</li>
                                <li>Escribe tu número de teléfono móvil en el campo superior y pulsa el botón <b>Save</b> (Guardar).</li>
                                <li>Abre Telegram y busca el bot <b>@utrecar_alertas_bot</b> o pulsa directamente este enlace: <a href="https://t.me/utrecar_alertas_bot" target="_blank" class="underline font-semibold hover:text-blue-950">t.me/utrecar_alertas_bot</a>.</li>
                                <li>Pulsa el botón <b>Iniciar</b> (Start) dentro del bot.</li>
                                <li>Pulsa el botón <b>📱 Compartir Teléfono</b> que aparecerá abajo para verificar tu número.</li>
                            </ol>
                            <p class="text-xs text-blue-700 font-medium">
                                El sistema validará tu contacto y asociará tu cuenta automáticamente para enviarte las alertas al instante.
                            </p>
                        </div>
                    ')),

                Placeholder::make('telegram_no_permission')
                    ->label('')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && !$record->can('recibir_notificaciones_competencia'))
                    ->content(new HtmlString('
                        <div class="mt-4 p-4 bg-gray-50 border-l-4 border-gray-400 rounded-r-md text-sm text-gray-700">
                            ℹ️ No tienes activo el permiso para recibir alertas de competencia. Si lo necesitas, solicita su activación en la Matriz de Permisos.
                        </div>
                    ')),

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
