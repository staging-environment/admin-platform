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
                @php
                    $groups = [
                        'Sistema y General' => [
                            'ver_dashboard' => 'Ver Comparador de Precios',
                            'gestion_usuarios' => 'Gestión de Usuarios',
                            'gestion_eliminar_usuarios' => 'Eliminar usuarios',
                            'gestion_roles' => 'Gestión de Roles y Matriz de Permisos',
                            'utilizar_explorador' => 'Utilizar Explorador de Archivos',
                            'ver_informes' => 'Ver Informes y Estadísticas',
                            'ver_analiticas' => 'Ver Analíticas de Visitas',
                            'gestion_portada' => 'Configuración de Portada',
                            'recibir_notificaciones_competencia' => 'Recibir alertas de cambios en los precios de las gasolineras por telegram',
                        ],
                        'Gasolineras' => [
                            'gestion_gasolineras' => 'Gestión de Gasolineras',
                        ],
                        'Ofertas de Empleo' => [
                            'gestion_ofertas' => 'Gestión de Ofertas de Trabajo',
                        ],
                        'Recursos Humanos / Empleados' => [
                            'gestion_recursos_humanos' => 'Acceso General a Recursos Humanos',
                            'ver_ficha_empleado' => 'Ver Ficha de Empleado (Detalle)',
                            'acceder_portal_fichajes' => 'Acceder al listado / portal de Fichajes',
                            'gestion_alta_empleados' => 'Alta de nuevos empleados',
                            'gestion_editar_empleados' => 'Edición de ficha principal',
                            'gestion_eliminar_empleados' => 'Eliminar empleados',
                            'aprobacion_vacaciones_bajas' => 'Aprobación y Gestión de Vacaciones y Bajas (acceso a panel y avisos por correo)',
                            'recibir_notificaciones_recursos_humanos' => 'Copia informativa por correo de eventos de RRHH (solo avisos por email, sin acceso a validación)',
                        ],
                        'SUBSECCIONES' => [
                            'ver_documentacion_empleados' => 'Ver Documentación de Empleados',
                            'editar_documentacion_empleados' => 'Editar Documentación de Empleados',
                        ],
                        'LANDING / PORTAL EMPLEADO' => [
                            'solicitar_ver_vacaciones' => 'Solicitar / Ver vacaciones',
                            'solicitud_baja_enfermedad' => 'Registrar baja o alta médica',
                        ]
                    ];

                    $allGroupedNames = [];
                    foreach($groups as $gPerms) {
                        $allGroupedNames = array_merge($allGroupedNames, array_keys($gPerms));
                    }
                    $otherPermissions = $permissions->filter(fn($p) => !in_array($p->name, $allGroupedNames));
                @endphp

                @foreach($groups as $groupName => $groupPermissions)
                    <tr class="{{ $groupName === 'SUBSECCIONES' ? 'bg-blue-50/40 dark:bg-indigo-950/20 border-l-4 border-blue-400 dark:border-blue-700 ml-4' : 'bg-blue-50/80 dark:bg-indigo-950/40 border-l-4 border-blue-600 dark:border-blue-500' }}">
                        <td colspan="{{ count($roles) + 1 }}" class="p-4 text-sm font-black uppercase tracking-wider text-blue-900 dark:text-blue-200">
                            <div class="flex items-center gap-2 {{ $groupName === 'SUBSECCIONES' ? 'pl-8' : '' }}">
                                <span class="h-2 w-2 rounded-full {{ $groupName === 'SUBSECCIONES' ? 'bg-blue-400 dark:bg-blue-600' : 'bg-blue-600 dark:bg-blue-400' }}"></span>
                                {{ $groupName }}
                            </div>
                        </td>
                    </tr>
                    @foreach($groupPermissions as $permissionName => $label)
                        @php
                            $permission = $permissions->firstWhere('name', $permissionName);
                        @endphp
                        @if($permission)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="p-4 text-sm font-medium text-gray-700 dark:text-gray-200" style="{{ $groupName === 'SUBSECCIONES' ? 'padding-left: 4.5rem !important;' : 'padding-left: 2rem !important;' }}">
                                    {{ $label }}
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
                        @endif
                    @endforeach
                @endforeach

                @if($otherPermissions->count() > 0)
                    <tr class="bg-gray-100/80 dark:bg-gray-800/40 border-l-4 border-gray-500 dark:border-gray-400">
                        <td colspan="{{ count($roles) + 1 }}" class="p-4 text-sm font-black uppercase tracking-wider text-gray-800 dark:text-gray-200">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-gray-500 dark:bg-gray-400"></span>
                                Otros
                            </div>
                        </td>
                    </tr>
                    @foreach($otherPermissions as $permission)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4 pl-8 text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $permission->name }}
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
                @endif
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
