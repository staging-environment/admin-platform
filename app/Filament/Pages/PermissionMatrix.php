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

    /**
     * Asegura el ancho completo en pantallas grandes
     */
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    /**
     * Botón de volver dinámico para evitar errores de ruta
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Volver al Dashboard')
                ->color('gray')
                ->icon('heroicon-m-arrow-left')
                // Apunta directamente a la ruta raíz de tu dashboard
                ->url(route('dashboard')),
        ];
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

        if ($role->hasPermissionTo($permissionId)) {
            $role->revokePermissionTo($permissionId);
        } else {
            $role->givePermissionTo($permissionId);
        }

        // Refrescamos la colección de roles para actualizar los checks en la vista
        $this->roles = Role::with('permissions')->get();
    }
}
