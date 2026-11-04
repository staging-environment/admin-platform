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

        // Create the new permission
        $perm = Permission::firstOrCreate([
            'name' => 'gestion_planing',
            'guard_name' => 'web',
        ]);

        // Assign to Admin, CEO, ADMINISTRACION Y RRHH and Gestor by default
        foreach (['Admin', 'admin', 'CEO', 'ceo', 'ADMINISTRACION Y RRHH', 'Gestor', 'gestor'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($perm);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('name', 'gestion_planing')->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};

