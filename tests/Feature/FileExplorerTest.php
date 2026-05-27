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
}

