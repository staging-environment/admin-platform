<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser; // <-- Añade esta línea
use Filament\Panel;                          // <-- Añade esta línea
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'telefono', 'telegram_chat_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser // <-- Añade "implements FilamentUser"
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Control de acceso al panel de Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Da acceso si es Admin, Gestor, si tiene el permiso ver_dashboard, o si es el email del creador
        return $this->hasRole('Admin') 
            || $this->hasRole('admin') 
            || $this->hasRole('Gestor') 
            || $this->hasRole('gestor') 
            || $this->email === 'jarodriguezbonilla@gmail.com'
            || $this->can('ver_dashboard');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con filtros guardados
     */
    public function filters(): HasMany
    {
        return $this->hasMany(Filter::class);
    }

    /**
     * Relación con el empleado correspondiente por email
     */
    public function empleado(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Empleado::class, 'email', 'email');
    }
}
