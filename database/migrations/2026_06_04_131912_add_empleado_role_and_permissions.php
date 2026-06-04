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

        // 1. Crear Rol Empleado si no existe
        $empleadoRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Empleado']);

        // 2. Crear nuevos permisos si no existen
        $permissions = [
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

        // 3. Asignar los nuevos permisos al rol Admin para que no se queden sin acceso
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permissionName) {
                if (!$adminRole->hasPermissionTo($permissionName)) {
                    $adminRole->givePermissionTo($permissionName);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En migraciones de producción, solemos dejar el down vacío o revertir asignaciones.
        // Opcional: eliminar los permisos creados
        $permissions = [
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
