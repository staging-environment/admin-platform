<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionMatrix extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected string $view = 'filament.pages.permission-matrix';

    protected static ?string $navigationLabel = 'Permisos';
    protected static ?string $title = 'Matriz de Permisos';

    public static function canAccess(): bool
    {
        app()['cache']->forget('spatie.permission.cache');
        $user = auth()->user();
        if (!$user) return false;
        
        // Reload relationships to bypass session/memory stale data
        $user->load('roles', 'permissions');
        
        return $user->can('gestion_roles');
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    public function getBreadcrumbs(): array
    {
        return [
            '#' => 'Administración',
            static::getNavigationLabel(),
        ];
    }

    /**
     * Asegura el ancho completo en pantallas grandes
     */
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }


    public $roles;
    public $permissions;

    public function mount(): void
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();
    }

    public function hasPermission($roleId, $permissionId): bool
    {
        $role = $this->roles->firstWhere('id', $roleId);
        return $role?->permissions->contains('id', $permissionId) ?? false;
    }

    public function togglePermission($roleId, $permissionId): void
    {
        $role = Role::find($roleId);
        $permission = Permission::find($permissionId);

        if ($role && $permission) {
            if ($role->hasPermissionTo($permission)) {
                $role->revokePermissionTo($permission);
            } else {
                $role->givePermissionTo($permission);
            }
        }

        // Limpiar la caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Refrescamos la colección de roles para actualizar los checks en la vista
        $this->roles = Role::with('permissions')->get();
    }
}
