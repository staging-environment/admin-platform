<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Filament\Pages\FileExplorer;

class FileExplorerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        Role::create(['name' => 'admin']);
    }

    public function test_guest_cannot_access_file_explorer_page(): void
    {
        $response = $this->get('/admin/file-explorer');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_file_explorer_page(): void
    {
        $user = User::factory()->create();

        // Standard user does not have admin role
        $response = $this->actingAs($user)->get('/admin/file-explorer');
        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_file_explorer_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get('/admin/file-explorer');
        $response->assertSuccessful();
    }

    public function test_admin_can_interact_with_file_explorer_livewire_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        Livewire::actingAs($user)
            ->test(FileExplorer::class)
            ->assertSet('selectedDisk', 'public')
            ->assertSet('viewMode', 'grid')
            ->call('selectDisk', 'local')
            ->assertSet('selectedDisk', 'local')
            ->call('goToPath', 'test-folder')
            ->assertSet('currentPath', 'test-folder');
    }
}
