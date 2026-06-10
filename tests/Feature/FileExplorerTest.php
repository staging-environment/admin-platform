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
            ->assertSet('selectedDisk', 'local')
            ->assertSet('viewMode', 'grid')
            ->call('selectDisk', 'public')
            ->assertSet('selectedDisk', 'public')
            ->call('goToPath', 'test-folder')
            ->assertSet('currentPath', 'test-folder');
    }

    public function test_admin_can_bulk_select_and_delete_files(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $disk = \Illuminate\Support\Facades\Storage::fake('public');
        $disk->put('test-file-1.txt', 'content 1');
        $disk->put('test-file-2.txt', 'content 2');
        $disk->put('test-file-3.txt', 'content 3');

        $this->assertTrue($disk->exists('test-file-1.txt'));
        $this->assertTrue($disk->exists('test-file-2.txt'));
        $this->assertTrue($disk->exists('test-file-3.txt'));

        Livewire::actingAs($user)
            ->test(FileExplorer::class)
            ->set('selectedDisk', 'public')
            ->call('toggleSelectAll')
            // Assert all files are added to selectedItems
            ->assertSet('selectedItems', [
                'test-file-1.txt|file',
                'test-file-2.txt|file',
                'test-file-3.txt|file',
            ])
            // Remove one file from selectedItems manually
            ->set('selectedItems', [
                'test-file-1.txt|file',
                'test-file-3.txt|file',
            ])
            // Call bulk delete action
            ->callAction('deleteSelected')
            // Assert selection is reset
            ->assertSet('selectedItems', []);

        // Assert only selected files were deleted
        $this->assertFalse($disk->exists('test-file-1.txt'));
        $this->assertTrue($disk->exists('test-file-2.txt'));
        $this->assertFalse($disk->exists('test-file-3.txt'));
    }

    public function test_admin_can_bulk_select_and_delete_folders_and_files(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $disk = \Illuminate\Support\Facades\Storage::fake('public');
        $disk->makeDirectory('folder-to-delete');
        $disk->put('folder-to-delete/subfile.txt', 'sub content');
        $disk->put('test-file.txt', 'file content');
        $disk->makeDirectory('folder-to-keep');

        $this->assertTrue($disk->exists('folder-to-delete'));
        $this->assertTrue($disk->exists('test-file.txt'));
        $this->assertTrue($disk->exists('folder-to-keep'));

        Livewire::actingAs($user)
            ->test(FileExplorer::class)
            ->set('selectedDisk', 'public')
            // Manually set selectedItems to delete one folder and one file
            ->set('selectedItems', [
                'folder-to-delete|folder',
                'test-file.txt|file',
            ])
            ->callAction('deleteSelected')
            ->assertSet('selectedItems', []);

        // Assert selected items are deleted, and others are kept
        $this->assertFalse($disk->exists('folder-to-delete'));
        $this->assertFalse($disk->exists('test-file.txt'));
        $this->assertTrue($disk->exists('folder-to-keep'));
    }

    public function test_admin_can_toggle_hidden_files_visibility(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $disk = \Illuminate\Support\Facades\Storage::fake('public');
        $disk->put('.gitignore', 'git content');
        $disk->makeDirectory('livewire-tmp');
        $disk->put('test-file.txt', 'visible content');

        Livewire::actingAs($user)
            ->test(FileExplorer::class)
            ->set('selectedDisk', 'public')
            ->assertSet('showHiddenFiles', false)
            // Toggle showing hidden files
            ->set('showHiddenFiles', true)
            // Now it should have them in selection list when we select all
            ->call('toggleSelectAll')
            ->assertSet('selectedItems', [
                'livewire-tmp|folder',
                '.gitignore|file',
                'test-file.txt|file',
            ]);
    }

    public function test_user_a_personal_storage_isolation(): void
    {
        $userA = User::factory()->create();
        $userA->assignRole('admin');

        $disk = \Illuminate\Support\Facades\Storage::fake('local');
        $disk->put("users/{$userA->id}/file-a.txt", 'content A');

        Livewire::actingAs($userA)
            ->test(FileExplorer::class)
            ->set('selectedDisk', 'personal')
            ->call('toggleSelectAll')
            ->assertSet('selectedItems', [
                'file-a.txt|file',
            ]);
    }

    public function test_user_b_personal_storage_isolation(): void
    {
        $userB = User::factory()->create();
        $userB->assignRole('admin');

        $disk = \Illuminate\Support\Facades\Storage::fake('local');
        $disk->put("users/{$userB->id}/file-b.txt", 'content B');

        Livewire::actingAs($userB)
            ->test(FileExplorer::class)
            ->set('selectedDisk', 'personal')
            ->call('toggleSelectAll')
            ->assertSet('selectedItems', [
                'file-b.txt|file',
            ]);
    }
}

