<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = 'admin@example.com';
        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin User',
                'password' => bcrypt('secret'),
            ]
        );
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $admin->assignRole($adminRole);
    }
}
