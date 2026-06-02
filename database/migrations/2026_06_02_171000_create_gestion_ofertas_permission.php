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
        app()['cache']->forget('spatie.permission.cache');

        // Create the permission if it doesn't exist
        $permissionName = 'gestion_ofertas';
        $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);

        // Sincronizar con el rol Admin si existe
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $permission = Permission::where('name', 'gestion_ofertas')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
