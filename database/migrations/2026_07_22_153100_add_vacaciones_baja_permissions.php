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

        // Create new permissions
        $p1 = Permission::firstOrCreate(['name' => 'solicitar_ver_vacaciones']);
        $p2 = Permission::firstOrCreate(['name' => 'solicitud_baja_enfermedad']);

        // Sync to Admin and Empleado roles
        foreach (['Admin', 'Empleado'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($p1);
                $role->givePermissionTo($p2);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', ['solicitar_ver_vacaciones', 'solicitud_baja_enfermedad'])->delete();
    }
};
