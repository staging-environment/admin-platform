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
            'gestion_roles',
            'gestion_usuarios',
            'gestion_eliminar_usuarios',
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
            'recibir_notificaciones_competencia',
            'ver_analiticas',
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
        
        // Re-assign 'Admin' role to the first user and specific email if exists to not lock us out
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            $firstUser->assignRole('Admin');
        }
        $targetUser = \App\Models\User::where('email', 'jarodriguezbonilla@gmail.com')->first();
        if ($targetUser) {
            $targetUser->assignRole('Admin');
        }
    }
}

