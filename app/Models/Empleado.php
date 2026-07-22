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
            $empleado->actualizarAlertas();

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

    public function fichajes()
    {
        return $this->hasMany(EmpleadoFichaje::class);
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

    public function syncLatestContract(): void
    {
        $latest = $this->documentos()
            ->where('tipo', 'Contratos')
            ->latest('id')
            ->first();

        if ($latest) {
            $this->update([
                'tipo_contrato' => $latest->tipo_contrato,
                'fecha_vencimiento_contrato' => ($latest->tipo_contrato === 'Eventual') ? $latest->fecha_vencimiento_contrato : null,
                'gasolinera_codigo' => $latest->gasolinera_codigo ?: $this->gasolinera_codigo,
                'puesto' => $latest->puesto ?: $this->puesto,
            ]);
        }
    }

    public function alertas()
    {
        return $this->hasMany(EmpleadoAlerta::class);
    }

    public function actualizarAlertas(): void
    {
        // 1. Limpiar alertas anteriores
        $this->alertas()->delete();

        // 2. Alerta: Sin Contrato
        $hasContract = $this->documentos()->where('tipo', 'Contratos')->exists();
        if (!$hasContract) {
            $this->alertas()->create([
                'tipo' => 'sin_contrato',
                'titulo' => 'Sin contrato registrado',
                'descripcion' => 'Este empleado no tiene ningún documento de contrato asociado en su ficha.',
            ]);
        } else {
            // 3. Alerta: Contrato Eventual Expirado
            $latestContract = $this->documentos()
                ->where('tipo', 'Contratos')
                ->latest('id')
                ->first();

            if ($latestContract && $latestContract->tipo_contrato === 'Eventual') {
                if ($latestContract->fecha_vencimiento_contrato && $latestContract->fecha_vencimiento_contrato->isPast()) {
                    $this->alertas()->create([
                        'tipo' => 'contrato_vencido',
                        'titulo' => 'Contrato eventual vencido',
                        'descripcion' => 'La fecha de vencimiento del último contrato temporal (' . $latestContract->fecha_vencimiento_contrato->format('d/m/Y') . ') ya ha pasado.',
                    ]);
                }
            }
        }

        // 4. Alerta: Sin DNI
        $hasDNI = $this->documentos()->where('tipo', 'DNI')->exists();
        if (!$hasDNI) {
            $this->alertas()->create([
                'tipo' => 'sin_dni',
                'titulo' => 'Sin DNI / NIE registrado',
                'descripcion' => 'Este empleado no tiene ningún documento de DNI/NIE asociado en su ficha.',
            ]);
        }

        // 5. Alerta: DNI Caducado
        if ($this->fecha_caducidad_dni && $this->fecha_caducidad_dni->isPast()) {
            $this->alertas()->create([
                'tipo' => 'dni_caducado',
                'titulo' => 'DNI / NIE caducado',
                'descripcion' => 'La fecha de caducidad del DNI/NIE del empleado (' . $this->fecha_caducidad_dni->format('d/m/Y') . ') ha expirado.',
            ]);
        }
    }
}
