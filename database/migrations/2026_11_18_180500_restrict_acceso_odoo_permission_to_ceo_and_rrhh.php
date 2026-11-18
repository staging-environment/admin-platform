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

        $perm = Permission::firstOrCreate([
            'name' => 'acceso_odoo',
            'guard_name' => 'web',
        ]);

        $allowedRoleNames = [
            'CEO',
            'ceo',
            'Admin',
            'admin',
            'ADMINISTRACION Y RRHH',
            'ADMINISTRACIÓN Y RRHH',
            'ADMINISTRACION RR HH',
            'ADMINISTRACIÓN RR HH',
            'ADMINISTRACION RRHH',
        ];

        // Revoke from any role not in allowed list, ensure allowed roles have it
        $allRoles = Role::all();
        foreach ($allRoles as $role) {
            $isAllowed = false;
            foreach ($allowedRoleNames as $allowed) {
                if (strcasecmp(trim($role->name), trim($allowed)) === 0) {
                    $isAllowed = true;
                    break;
                }
            }

            if ($isAllowed) {
                if (!$role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                }
            } else {
                if ($role->hasPermissionTo($perm)) {
                    $role->revokePermissionTo($perm);
                }
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
