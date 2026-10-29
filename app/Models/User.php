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
    use HasFactory, Notifiable;
    use HasRoles {
        hasRole as protected hasRoleFromTrait;
        getRoleNames as protected getRoleNamesFromTrait;
    }

    
    /**
     * Obtiene el rol activo del usuario en la sesión actual.
     * Si sólo tiene un rol asignado, retorna ese rol.
     * Si tiene varios roles, retorna el seleccionado en sesión ('active_role').
     * Si no hay rol en sesión, prioriza 'Gestor' si lo tiene, o el primero.
     */
    public function getActiveRole(): ?string
    {
        $dbRoles = $this->roles->pluck('name')->toArray();
        if (count($dbRoles) <= 1) {
            return $dbRoles[0] ?? null;
        }

        $sessionRole = session('active_role');
        if ($sessionRole && in_array($sessionRole, $dbRoles, true)) {
            return $sessionRole;
        }

        // Si tiene rol Gestor y Empleado, por defecto al entrar inicia como Empleado
        if (in_array('Gestor', $dbRoles, true) && in_array('Empleado', $dbRoles, true)) {
            return 'Empleado';
        }

        if (in_array('Gestor', $dbRoles, true)) {
            return 'Gestor';
        }

        return $dbRoles[0] ?? null;
    }

    /**
     * Comprueba si el usuario tiene un rol determinado.
     * Si tiene múltiples roles asignados, la comprobación se evalúa contra el rol activo.
     */
    public function hasRole($roles, string $guard = null): bool
    {
        $dbRoles = $this->roles->pluck('name')->toArray();
        if (count($dbRoles) > 1) {
            $activeRole = $this->getActiveRole();
            if ($activeRole) {
                if (is_string($roles)) {
                    return $activeRole === $roles;
                }
                if (is_array($roles)) {
                    return in_array($activeRole, $roles, true);
                }
                if ($roles instanceof \Illuminate\Support\Collection) {
                    $names = $roles->map(fn($r) => is_string($r) ? $r : ($r->name ?? null))->filter();
                    return $names->contains($activeRole);
                }
                if ($roles instanceof \Spatie\Permission\Contracts\Role) {
                    return $activeRole === $roles->name;
                }
            }
        }

        return $this->hasRoleFromTrait($roles, $guard);
    }

    /**
     * Retorna los nombres de roles efectivos del usuario.
     */
    public function getRoleNames(): \Illuminate\Support\Collection
    {
        $dbRoles = $this->roles->pluck('name')->toArray();
        if (count($dbRoles) > 1) {
            $activeRole = $this->getActiveRole();
            if ($activeRole) {
                return collect([$activeRole]);
            }
        }

        return $this->getRoleNamesFromTrait();
    }

    /**
     * Retorna todos los nombres de roles asignados en base de datos.
     */
    public function getAssignedRoles(): \Illuminate\Support\Collection
    {
        return $this->roles->pluck('name');
    }

    /**
     * Indica si el usuario tiene más de un rol asignado.
     */
    public function hasMultipleRoles(): bool
    {
        return $this->roles->count() > 1;
    }

    public function mustChangePassword(): bool
    {
        return \Illuminate\Support\Facades\Hash::check('1234', $this->password);
    }

    /**
     * Control de acceso al panel de Filament
     */

    /**
     * Determina si el usuario puede suplantar / enmascararse como otro usuario.
     */
    public function canImpersonate(): bool
    {
        return $this->can('suplantar_usuarios')
            || $this->email === 'jarodriguezbonilla@gmail.com'
            || $this->id === 1;
    }

    /**
     * Determina si este usuario puede ser suplantado.
     */
    public function canBeImpersonated(): bool
    {
        return $this->id !== auth()->id();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $empleado = \App\Models\Empleado::withTrashed()->whereRaw('LOWER(email) = ?', [strtolower($this->email)])->first();
        if ($empleado) {
            if ($empleado->estado === 'Baja' || $empleado->trashed() || $empleado->estaSuspendido()) {
                return false;
            }
        }

        return $this->can('acceder_portal_fichajes')
            || $this->can('ver_listado_fichajes')
            || $this->can('ver_dashboard')
            || $this->can('gestion_recursos_humanos')
            || $this->permissions()->exists()
            || $this->email === 'jarodriguezbonilla@gmail.com'
            || $this->id === 1;
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

    public $usuario_activo;

    protected static function booted()
    {
        static::deleted(function ($user) {
            $empleado = \App\Models\Empleado::withTrashed()->where('email', $user->email)->first();
            if ($empleado) {
                $empleado->forceDelete();
            }
        });

        static::saved(function ($user) {
            $user->load('roles');
            $oldEmail = $user->getOriginal('email');

            // Buscar empleado existente por email actual o email anterior si cambió
            $empleado = \App\Models\Empleado::withTrashed()
                ->where('email', $user->email)
                ->when($oldEmail && strtolower($oldEmail) !== strtolower($user->email), function ($q) use ($oldEmail) {
                    $q->orWhere('email', $oldEmail);
                })
                ->first();

            // Si aún no se encuentra, comprobar si la empresa ya registró un empleado con este mismo nombre y apellidos
            if (!$empleado && $user->name) {
                $empleado = \App\Models\Empleado::withTrashed()
                    ->whereRaw('LOWER(TRIM(CONCAT(nombre, " ", apellidos))) = ?', [strtolower(trim($user->name))])
                    ->where('dni', 'not like', 'PENDIENTE-%')
                    ->first();
            }

            if ($empleado) {
                if ($empleado->email !== $user->email) {
                    $empleado->email = $user->email;
                    $empleado->save();
                }

                if ($user->can('acceder_portal_fichajes') || $user->hasRole('Empleado')) {
                    if ($empleado->trashed()) {
                        $empleado->restore();
                    }
                }
            } else if ($user->can('acceder_portal_fichajes') || $user->hasRole('Empleado')) {
                $parts = explode(' ', trim($user->name ?: 'Empleado'));
                $nombre = $parts[0];
                $apellidos = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'Apellidos';
                
                \App\Models\Empleado::create([
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'dni' => 'PENDIENTE-' . strtoupper(substr(md5($user->email), 0, 5)),
                    'fecha_nacimiento' => '1990-01-01',
                    'direccion' => 'Dirección pendiente',
                    'localidad' => 'Utrera',
                    'codigo_postal' => '41710',
                    'provincia' => 'Sevilla',
                    'telefono_principal' => $user->telefono ?: '600000000',
                    'email' => $user->email,
                    'onboarding_completado' => false,
                    'onboarding_verificado_por_admin' => false,
                    'onboarding_paso_actual' => 1,
                ]);
            }
            if ($empleado && isset($user->usuario_activo)) {
                $isActive = filter_var($user->usuario_activo, FILTER_VALIDATE_BOOLEAN);
                if ($isActive) {
                    if ($empleado->trashed()) {
                        $empleado->restore();
                    }
                    if (!$user->hasRole('Empleado')) {
                        $user->assignRole('Empleado');
                    }
                } else {
                    if (!$empleado->trashed()) {
                        $empleado->delete();
                    }
                }
            }
        });
    }
}
