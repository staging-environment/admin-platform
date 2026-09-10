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
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permListado = Permission::firstOrCreate([
            'name' => 'ver_listado_fichajes',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole && !$adminRole->hasPermissionTo($permListado)) {
            $adminRole->givePermissionTo($permListado);
        }

        $gestorRole = Role::where('name', 'Gestor')->first();
        if ($gestorRole && !$gestorRole->hasPermissionTo($permListado)) {
            $gestorRole->givePermissionTo($permListado);
        }

        $permPortal = Permission::firstOrCreate([
            'name' => 'acceder_portal_fichajes',
            'guard_name' => 'web',
        ]);

        $empleadoRole = Role::where('name', 'Empleado')->first();
        if ($empleadoRole && !$empleadoRole->hasPermissionTo($permPortal)) {
            $empleadoRole->givePermissionTo($permPortal);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'ver_listado_fichajes')->delete();
    }
};