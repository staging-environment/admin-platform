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

        // Crear permisos
        $permissions = [
            'view-dashboard',
            'view-reports',
            'create-filter',
            'edit-filter',
            'delete-filter',
            'view-all-data',
            'export-data',
            'manage-users',
            'manage-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Crear roles
        $adminRole = Role::findOrCreate('Admin');
        $managerRole = Role::findOrCreate('Manager');
        $userRole = Role::findOrCreate('User');

        // Asignar permisos a roles
        $adminRole->syncPermissions(Permission::all());

        $managerRole->syncPermissions([
            'view-dashboard',
            'view-reports',
            'create-filter',
            'edit-filter',
            'delete-filter',
            'view-all-data',
            'export-data',
        ]);

        $userRole->syncPermissions([
            'view-dashboard',
            'view-reports',
            'create-filter',
            'edit-filter',
            'delete-filter',
        ]);
    }
}

