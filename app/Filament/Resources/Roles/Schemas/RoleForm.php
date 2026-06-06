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

                CheckboxList::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                        'ver_dashboard' => 'Ver Comparador de Precios',
                        'gestion_usuarios' => 'Gestión de Usuarios',
                        'gestion_roles' => 'Gestión de Roles y Matriz de Permisos',
                        'utilizar_explorador' => 'Utilizar Explorador de Archivos',
                        'ver_informes' => 'Ver Informes y Estadísticas',
                        'gestion_gasolineras' => 'Gestión de Gasolineras',
                        'gestion_portada' => 'Configuración de Portada',
                        'gestion_ofertas' => 'Gestión de Ofertas de Trabajo',
                        'gestion_comentarios_empleados' => 'Gestionar comentarios de empleados',
                        default => $record->name,
                    })
                    ->columns(3)
                    ->searchable(),

            ]);
    }
}
