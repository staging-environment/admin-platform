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
                        'ver_dashboard' => 'Ver Comparador de Precios (Dashboard)',
                        'gestion_usuarios' => 'Ver y Editar Usuarios',
                        'gestion_eliminar_usuarios' => 'Eliminar Usuarios',
                        'gestion_roles' => 'Gestión de Roles y Matriz de Permisos',
                        'utilizar_explorador' => 'Utilizar Explorador de Archivos',
                        'ver_informes' => 'Ver Informes y Estadísticas',
                        'gestion_gasolineras' => 'Gestión de Gasolineras',
                        'gestion_portada' => 'Configuración de Portada',
                        'gestion_ofertas' => 'Gestión de Ofertas de Trabajo',
                        'gestion_recursos_humanos' => 'Gestión de Recursos Humanos (Fichas)',
                        'gestion_alta_empleados' => 'Alta de Empleados',
                        'gestion_editar_empleados' => 'Editar Empleados',
                        'gestion_eliminar_empleados' => 'Eliminar Empleados',
                        'ver_documentacion_empleados' => 'Ver Documentación de Empleados',
                        'editar_documentacion_empleados' => 'Añadir/Editar/Borrar Documentación de Empleados',
                        default => $record->name,
                    })
                    ->columns(3)
                    ->searchable(),

            ]);
    }
}
