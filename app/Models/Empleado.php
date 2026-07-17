<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tiene_discapacidad' => 'boolean',
        'pertenece_andalucia' => 'boolean',
        'tipo_discapacidad' => 'array',
        'tiene_incapacidad' => 'boolean',
        'tipo_incapacidad' => 'array',
        'fecha_resolucion_discapacidad' => 'date',
        'fecha_reconocimiento' => 'date',
        'fecha_vencimiento_contrato' => 'date',
        'gasolinera_codigo' => 'integer',
        'fecha_caducidad_dni' => 'date',
    ];


    protected static function booted()
    {
        static::saved(function ($empleado) {
            if ($empleado->wasRecentlyCreated) {
                $user = \App\Models\User::where('email', $empleado->email)->first();
                if (!$user) {
                    $user = new \App\Models\User();
                    $user->password = bcrypt('12345678');
                }
            } else {
                $originalEmail = $empleado->getOriginal('email');
                $user = \App\Models\User::where('email', $originalEmail)->first();
                if (!$user) {
                    $user = \App\Models\User::where('email', $empleado->email)->first();
                    if (!$user) {
                        $user = new \App\Models\User();
                        $user->password = bcrypt('12345678');
                    }
                }
            }

            $user->name = $empleado->nombre . ' ' . $empleado->apellidos;
            $user->email = $empleado->email;
            $user->telefono = $empleado->telefono_principal;
            $user->save();

            if (!$user->hasRole('Empleado')) {
                $user->assignRole('Empleado');
            }
        });

        static::deleted(function ($empleado) {
            if (method_exists($empleado, 'isForceDeleting') && !$empleado->isForceDeleting()) {
                return;
            }

            $user = \App\Models\User::where('email', $empleado->email)->first();
            if ($user) {
                $user->delete();
            }
        });

        static::restored(function ($empleado) {
            $user = \App\Models\User::where('email', $empleado->email)->first();
            if ($user) {
                if (!$user->hasRole('Empleado')) {
                    $user->assignRole('Empleado');
                }
            }
        });
    }

    public function gasolinera()
    {
        return $this->belongsTo(Gasolinera::class, 'gasolinera_codigo', 'Codigo');
    }

    public function documentos()
    {
        return $this->hasMany(EmpleadoDocumento::class);
    }

    public function cursos()
    {
        return $this->hasMany(EmpleadoCurso::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(EmpleadoNotificacion::class);
    }

    public function horarios()
    {
        return $this->hasMany(EmpleadoHorario::class);
    }

    public function ausencias()
    {
        return $this->hasMany(EmpleadoAusencia::class);
    }

    public function vacaciones()
    {
        return $this->hasMany(EmpleadoVacacion::class);
    }

    public function contratos()
    {
        return $this->hasMany(EmpleadoContrato::class);
    }

    public function comentarios()
    {
        return $this->hasMany(EmpleadoComentario::class);
    }
}
