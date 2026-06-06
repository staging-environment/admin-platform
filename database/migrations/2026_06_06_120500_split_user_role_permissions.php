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

        // 1. Delete old permission
        $oldPermission = Permission::where('name', 'gestion_usuarios_roles')->first();
        if ($oldPermission) {
            $oldPermission->delete();
        }

        // 2. Create new split permissions
        $newPermissions = [
            'gestion_usuarios',
            'gestion_roles',
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
            'gestion_usuarios',
            'gestion_roles',
        ];

        foreach ($newPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }

        Permission::firstOrCreate(['name' => 'gestion_usuarios_roles']);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(['gestion_usuarios_roles']);
        }
    }
};
