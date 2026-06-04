<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear los Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $gestorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Gestor']);
        $empleadoRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Empleado']);

        // 2. Definir y crear TODOS los permisos del sistema si no existen
        $permissions = [
            // Permisos Base del sistema
            'ver_dashboard',
            'gestion_usuarios_roles',
            'utilizar_explorador',
            'ver_informes',
            'gestion_gasolineras',
            'gestion_portada',
            'gestion_ofertas',
            
            // Permisos del Módulo de Recursos Humanos
            'gestion_recursos_humanos',
            'gestion_documentacion_empleados',
            'gestion_cursos_empleados',
            'gestion_notificaciones_empleados',
            'gestion_horarios_empleados',
            'gestion_ausencias_empleados',
            'gestion_vacaciones_empleados',
            'gestion_contratos_empleados',
        ];

        foreach ($permissions as $permissionName) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);
        }

        // 3. Sincronizar todos los permisos al rol Admin para evitar bloqueos
        $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());

        // 4. Asignar por defecto el rol Admin al usuario principal en producción
        $firstUser = \App\Models\User::where('email', 'jarodriguezbonilla@gmail.com')->first() ?: \App\Models\User::first();
        if ($firstUser) {
            if (!$firstUser->hasRole('Admin')) {
                $firstUser->assignRole('Admin');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En reversiones, eliminamos únicamente los permisos específicos introducidos en esta fase
        $permissionsToDelete = [
            'gestion_recursos_humanos',
            'gestion_documentacion_empleados',
            'gestion_cursos_empleados',
            'gestion_notificaciones_empleados',
            'gestion_horarios_empleados',
            'gestion_ausencias_empleados',
            'gestion_vacaciones_empleados',
            'gestion_contratos_empleados',
        ];

        foreach ($permissionsToDelete as $permissionName) {
            $permission = \Spatie\Permission\Models\Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }

        $empleadoRole = \Spatie\Permission\Models\Role::where('name', 'Empleado')->first();
        if ($empleadoRole) {
            $empleadoRole->delete();
        }
    }
};
