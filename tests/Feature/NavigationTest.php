<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear and re-register permissions in memory
        $registrar = app(\Spatie\Permission\PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();
        $registrar->registerPermissions(app(\Illuminate\Contracts\Auth\Access\Gate::class));

        // Create standard permissions and roles
        Permission::findOrCreate('manage-users');
        Permission::findOrCreate('gestion_roles');
        Permission::findOrCreate('ver_dashboard');
        $adminRole = Role::findOrCreate('Admin');
        $adminRole->givePermissionTo('manage-users');
        $adminRole->givePermissionTo('gestion_roles');
    }

    public function test_guest_cannot_see_administration_menu(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_see_administration_menu(): void
    {
        // Create dummy user to occupy ID 1 (which acts as Super Admin bypass)
        User::factory()->create();

        $user = User::factory()->create();
        $user->givePermissionTo('ver_dashboard');

        $response = $this->actingAs($user)->get('/admin');
        $response->assertSuccessful();
        $response->assertDontSee(__('Administración'));
        $response->assertDontSee('/admin/gasolineras');
        $response->assertDontSee('/admin/users');
        $response->assertDontSee('/admin/file-explorer');
    }

    public function test_admin_user_can_see_administration_menu_with_all_links(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)->get('/admin');
        $response->assertSuccessful();
        $response->assertSee(__('Administración'));
        $response->assertSee('/admin/gasolineras');
        $response->assertSee('/admin/users');
        $response->assertSee('/admin/file-explorer');
        $response->assertDontSee('/admin/ftp-management');
        $response->assertSee('/admin/manage-home');
        $response->assertSee('/admin/permission-matrix');
        $response->assertSee('/admin/roles');
        $response->assertSee('/admin/permissions');
    }
}
