<?php

namespace Tests\Unit;

use App\Filament\Pages\PlaningTurnos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PlaningPermissionAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::firstOrCreate(['name' => 'gestion_planing', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'gestion_recursos_humanos', 'guard_name' => 'web']);

        // Crear roles
        $roleCeo = Role::firstOrCreate(['name' => 'CEO', 'guard_name' => 'web']);
        $roleGestor = Role::firstOrCreate(['name' => 'Gestor', 'guard_name' => 'web']);
        $roleEmpleado = Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);

        // Asignar gestion_planing a CEO y Gestor por defecto
        $roleCeo->givePermissionTo('gestion_planing');
        $roleGestor->givePermissionTo('gestion_planing');
    }

    public function test_usuario_con_permiso_gestion_planing_puede_acceder(): void
    {
        $user = User::factory()->create([
            'email' => 'operador@test.com',
        ]);
        $user->givePermissionTo('gestion_planing');

        $this->actingAs($user);
        $this->assertTrue(PlaningTurnos::canAccess());
    }

    public function test_usuario_rol_ceo_tiene_acceso_por_defecto_a_planing(): void
    {
        $user = User::factory()->create([
            'email' => 'ceo@test.com',
        ]);
        $user->assignRole('CEO');

        $this->actingAs($user);
        $this->assertTrue(PlaningTurnos::canAccess());
    }

    public function test_usuario_rol_gestor_tiene_acceso_por_defecto_a_planing(): void
    {
        $user = User::factory()->create([
            'email' => 'gestor@test.com',
        ]);
        $user->assignRole('Gestor');

        $this->actingAs($user);
        $this->assertTrue(PlaningTurnos::canAccess());
    }

    public function test_usuario_sin_permiso_planing_no_puede_acceder_aunque_tenga_rrhh(): void
    {
        $user = User::factory()->create([
            'id' => 999,
            'email' => 'rrhh_sin_planing@test.com',
        ]);
        $user->givePermissionTo('gestion_recursos_humanos');

        $this->actingAs($user);
        $this->assertFalse(PlaningTurnos::canAccess());
    }

    public function test_superadmin_siempre_tiene_acceso(): void
    {
        $superadmin = User::factory()->create([
            'id' => 1,
            'email' => 'jarodriguezbonilla@gmail.com',
        ]);

        $this->actingAs($superadmin);
        $this->assertTrue(PlaningTurnos::canAccess());
    }
}
