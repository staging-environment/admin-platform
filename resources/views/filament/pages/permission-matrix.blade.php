<x-filament-panels::page>
    <style>
        /* 1. Mantenemos el ancho completo que te funcionó */
        .fi-main-ctn, .fi-page-content-ctn, .fi-section {
            max-width: none !important;
            width: 100% !important;
        }

        /* 2. Control de columnas: Quitamos el ancho fijo de roles para que se peguen a la izquierda */
        .col-permiso { width: 25%; min-width: 200px; }

        /* Forzamos que las celdas de roles solo ocupen lo necesario */
        .col-role { width: auto; text-align: left !important; }

        /* 3. Ajuste de tabla */
        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Forzamos que el texto de los encabezados NO se centre por herencia */
        th {
            text-align: left !important;
        }
    </style>

    <x-filament::section>
        <div class="table-container">
            <table class="w-full border-collapse" style="table-layout: auto;">
                <thead>
                <tr class="bg-gray-50 dark:bg-white/5">
                    <th class="col-permiso p-4 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-white/10">
                        Permiso
                    </th>
                    @foreach($roles as $role)
                        <th class="col-role p-4 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-white/10">
                            {{ $role->name }}
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach($permissions as $permission)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                        <td class="p-4 text-sm font-medium text-gray-700 dark:text-gray-200">
                            @php
                                $n = $permission->name;
                                $f = [
                                    'ver_dashboard' => 'Ver Comparador de Precios',
                                    'gestion_usuarios_roles' => 'Gestión de Usuarios y Roles',
                                    'utilizar_explorador' => 'Utilizar Explorador de Archivos',
                                    'ver_informes' => 'Ver Informes y Estadísticas',
                                    'gestion_gasolineras' => 'Gestión de Gasolineras',
                                    'gestion_portada' => 'Configuración de Portada',
                                    'gestion_ofertas' => 'Gestión de Ofertas de Trabajo',
                                    'gestion_recursos_humanos' => 'Recursos humanos',
                                    'gestion_documentacion_empleados' => 'Documentación de Empleados',
                                    'gestion_cursos_empleados' => 'Formación/Cursos de Empleados',
                                    'gestion_notificaciones_empleados' => 'Notificaciones de Empleados',
                                    'gestion_horarios_empleados' => 'Horario Laboral de Empleados',
                                    'gestion_ausencias_empleados' => 'Ausencias y Bajas de Empleados',
                                    'gestion_vacaciones_empleados' => 'Vacaciones y Permisos de Empleados',
                                    'gestion_contratos_empleados' => 'Contratos de Empleados',
                                    'gestion_comentarios_empleados' => 'Gestionar comentarios de empleados',
                                ];
                            @endphp
                            {{ $f[$n] ?? $n }}
                        </td>
                        @foreach($roles as $role)
                            <td class="p-4">
                                <div class="flex items-center justify-start ml-0.5">
                                    <x-filament::input.checkbox
                                        wire:click="togglePermission({{ $role->id }}, {{ $permission->id }})"
                                        :checked="$this->hasPermission($role->id, $permission->id)"
                                    />
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
