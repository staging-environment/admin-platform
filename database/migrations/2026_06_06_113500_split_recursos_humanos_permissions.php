<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Permissions to delete
        $oldPermissions = [
            'gestion_documentacion_empleados',
            'gestion_cursos_empleados',
            'gestion_notificaciones_empleados',
            'gestion_horarios_empleados',
            'gestion_ausencias_empleados',
            'gestion_vacaciones_empleados',
            'gestion_contratos_empleados',
            'gestion_comentarios_empleados',
        ];

        foreach ($oldPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }

        // 2. New split permissions
        $newPermissions = [
            'ver_documentacion_empleados',
            'editar_documentacion_empleados',
            'ver_cursos_empleados',
            'editar_cursos_empleados',
            'ver_notificaciones_empleados',
            'editar_notificaciones_empleados',
            'ver_horarios_empleados',
            'editar_horarios_empleados',
            'ver_ausencias_empleados',
            'editar_ausencias_empleados',
            'ver_vacaciones_empleados',
            'editar_vacaciones_empleados',
            'ver_contratos_empleados',
            'editar_contratos_empleados',
            'ver_comentarios_empleados',
            'editar_comentarios_empleados',
        ];

        foreach ($newPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Sync to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $newPermissions = [
            'ver_documentacion_empleados',
            'editar_documentacion_empleados',
            'ver_cursos_empleados',
            'editar_cursos_empleados',
            'ver_notificaciones_empleados',
            'editar_notificaciones_empleados',
            'ver_horarios_empleados',
            'editar_horarios_empleados',
            'ver_ausencias_empleados',
            'editar_ausencias_empleados',
            'ver_vacaciones_empleados',
            'editar_vacaciones_empleados',
            'ver_contratos_empleados',
            'editar_contratos_empleados',
            'ver_comentarios_empleados',
            'editar_comentarios_empleados',
        ];

        foreach ($newPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }

        $oldPermissions = [
            'gestion_documentacion_empleados',
            'gestion_cursos_empleados',
            'gestion_notificaciones_empleados',
            'gestion_horarios_empleados',
            'gestion_ausencias_empleados',
            'gestion_vacaciones_empleados',
            'gestion_contratos_empleados',
            'gestion_comentarios_empleados',
        ];

        foreach ($oldPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($oldPermissions);
        }
    }
};
