<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add estado column to empleado_ausencias
        if (!Schema::hasColumn('empleado_ausencias', 'estado')) {
            Schema::table('empleado_ausencias', function (Blueprint $table) {
                $table->string('estado')->default('Pendiente')->after('justificante_path');
            });
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create the new permission
        $permission = Permission::firstOrCreate(['name' => 'aprobacion_vacaciones_bajas']);

        // Sync to Admin and Gestor roles
        foreach (['Admin', 'Gestor'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('empleado_ausencias', 'estado')) {
            Schema::table('empleado_ausencias', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }

        Permission::where('name', 'aprobacion_vacaciones_bajas')->delete();
    }
};
