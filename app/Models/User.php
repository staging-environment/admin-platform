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

    public function mustChangePassword(): bool
    {
        if ($this->hasRole('Empleado') || $this->hasRole('empleado')) {
            return \Illuminate\Support\Facades\Hash::check('1234', $this->password);
        }
        return false;
    }

    /**
     * Control de acceso al panel de Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $empleado = \App\Models\Empleado::whereRaw('LOWER(email) = ?', [strtolower($this->email)])->first();
        if ($empleado) {
            if ($empleado->estado === 'Baja' || $empleado->estaSuspendido()) {
                return false;
            }
        }

        return $this->hasRole('Admin') 
            || $this->hasRole('admin') 
            || $this->hasRole('Gestor') 
            || $this->hasRole('gestor') 
            || $this->hasRole('Empleado')
            || $this->hasRole('empleado')
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
            $empleado = \App\Models\Empleado::withTrashed()->where('email', $user->email)->first();
            
            if ($user->hasRole('Empleado') || $user->hasRole('empleado')) {
                if (!$empleado) {
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
                    ]);
                } else if ($empleado->trashed()) {
                    $empleado->restore();
                }
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
