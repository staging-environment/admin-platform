<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Clean out old permissions and roles completely
        \Illuminate\Support\Facades\DB::table('model_has_permissions')->delete();
        \Illuminate\Support\Facades\DB::table('model_has_roles')->delete();
        \Illuminate\Support\Facades\DB::table('role_has_permissions')->delete();
        \Illuminate\Support\Facades\DB::table('permissions')->delete();
        \Illuminate\Support\Facades\DB::table('roles')->delete();

        $permissions = [
            'ver_dashboard',
            'gestion_usuarios_roles',
            'utilizar_explorador',
            'ver_informes',
            'gestion_gasolineras',
            'gestion_portada',
            'gestion_ofertas',
            'gestion_recursos_humanos',
            'gestion_alta_empleados',
            'gestion_editar_empleados',
            'gestion_eliminar_empleados',
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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::create(['name' => 'Admin']);
        $gestorRole = Role::create(['name' => 'Gestor']);
        $empleadoRole = Role::create(['name' => 'Empleado']);

        // Asignar permisos a roles
        // Admin gets everything
        $adminRole->syncPermissions(Permission::all());

        // Gestor gets a subset by default (can be customized in the UI later)
        $gestorRole->syncPermissions([
            'ver_dashboard',
            'ver_informes',
        ]);
        
        // Re-assign 'Admin' role to the first user if exists to not lock us out
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            $firstUser->assignRole('Admin');
        }
    }
}

