<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    public $password;

    protected $guarded = [];

    protected $attributes = [
        'estado' => 'Alta',
    ];

    protected $casts = [
        'tiene_discapacidad' => 'boolean',
        'no_tiene_discapacidad' => 'boolean',
        'pertenece_andalucia' => 'boolean',
        'tipo_discapacidad' => 'array',
        'tiene_incapacidad' => 'boolean',
        'tipo_incapacidad' => 'array',
        'fecha_resolucion_discapacidad' => 'date',
        'fecha_reconocimiento' => 'date',
        'fecha_vencimiento_contrato' => 'date',
        'gasolinera_codigo' => 'integer',
        'fecha_caducidad_dni' => 'date',
        'fecha_baja' => 'date',
    ];


    protected static function booted()
    {
        static::saving(function ($empleado) {
            if (array_key_exists('password', $empleado->attributes)) {
                $empleado->password = $empleado->attributes['password'];
                unset($empleado->attributes['password']);
            }
        });

        static::saved(function ($empleado) {
            $empleado->actualizarAlertas();

            if ($empleado->wasRecentlyCreated) {
                $user = \App\Models\User::where('email', $empleado->email)->first();
                if (!$user) {
                    $user = new \App\Models\User();
                    $defaultPass = $empleado->password ?: '1234';
                    $user->password = bcrypt($defaultPass);
                }
            } else {
                $originalEmail = $empleado->getOriginal('email');
                $user = \App\Models\User::where('email', $originalEmail)->first();
                if (!$user) {
                    $user = \App\Models\User::where('email', $empleado->email)->first();
                    if (!$user) {
                        $user = new \App\Models\User();
                        $defaultPass = $empleado->password ?: '1234';
                        $user->password = bcrypt($defaultPass);
                    }
                }
                
                if ($user && $empleado->password) {
                    $user->password = bcrypt($empleado->password);
                }
            }

            $isAdmin = $user->exists && ($user->hasRole('Admin') || $user->hasRole('admin'));

            if (!$isAdmin) {
                $user->name = $empleado->nombre . ' ' . $empleado->apellidos;
            }
            $user->email = $empleado->email;
            $user->telefono = $empleado->telefono_principal;
            $user->save();

            if (!$isAdmin && !$user->hasRole('Empleado')) {
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

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
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
            ->orderByRaw('COALESCE(fecha_inicio_contrato, "1970-01-01") DESC')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $isEventual = in_array(strtolower(trim($latest->tipo_contrato ?? '')), ['eventual', 'temporal']);
            $this->update([
                'tipo_contrato' => $latest->tipo_contrato,
                'fecha_vencimiento_contrato' => $isEventual ? $latest->fecha_vencimiento_contrato : null,
                'gasolinera_codigo' => $latest->gasolinera_codigo ?: $this->gasolinera_codigo,
                'puesto' => $latest->puesto ?: $this->puesto,
            ]);
        } else {
            $this->update([
                'tipo_contrato' => null,
                'fecha_vencimiento_contrato' => null,
            ]);
        }
    }

    public function estaSuspendido(): bool
    {
        if ($this->estado === 'Baja') {
            return false;
        }

        $suspension = $this->notificaciones()
            ->where('tipo', 'Cierre expediente disciplinario')
            ->where('resolucion_cierre', 'Suspensión de empleo y sueldo')
            ->whereNotNull('fecha_comunicacion')
            ->where('dias_suspension', '>', 0)
            ->latest('fecha_comunicacion')
            ->first();

        if ($suspension) {
            $fechaInicio = \Carbon\Carbon::parse($suspension->fecha_comunicacion)->startOfDay();
            $fechaFin = (clone $fechaInicio)->addDays((int) $suspension->dias_suspension)->endOfDay();
            $now = \Carbon\Carbon::now();

            if ($now->between($fechaInicio, $fechaFin)) {
                return true;
            }
        }

        return false;
    }

    public function alertas()
    {
        return $this->hasMany(EmpleadoAlerta::class);
    }

    public function actualizarAlertas(): void
    {
        // 1. Limpiar alertas anteriores
        $this->alertas()->delete();

        // Si el empleado está dado de baja en la empresa:
        // No debe tener alertas operativas de personal en alta,
        // pero DEBE alertar si le falta el documento oficial de baja o finiquito.
        if ($this->estado === 'Baja') {
            $hasDocBaja = !empty($this->documento_baja_path) || $this->documentos()->where('tipo', 'Documento de Baja')->exists();
            if (!$hasDocBaja) {
                $this->alertas()->create([
                    'tipo' => 'falta_documento_baja',
                    'titulo' => 'Falta documento de baja',
                    'descripcion' => 'El empleado está dado de baja en la empresa pero no tiene adjuntado el documento oficial de baja o finiquito.',
                ]);
            }
            return;
        }

        // Obtener el contrato más relevante (ordenado por fecha de inicio más reciente o último ID)
        $latestContract = $this->documentos()
            ->where('tipo', 'Contratos')
            ->orderByRaw('COALESCE(fecha_inicio_contrato, "1970-01-01") DESC')
            ->orderBy('id', 'desc')
            ->first();

        $tipoContrato = $latestContract?->tipo_contrato ?: $this->tipo_contrato;
        $fechaVencimiento = $latestContract?->fecha_vencimiento_contrato ?: $this->fecha_vencimiento_contrato;
        $hasContract = ($latestContract !== null) || !empty($tipoContrato);

        // 2. Alerta: Sin Contrato
        if (!$hasContract) {
            $this->alertas()->create([
                'tipo' => 'sin_contrato',
                'titulo' => 'Sin contrato registrado',
                'descripcion' => 'Este empleado no tiene ningún documento de contrato asociado en su ficha.',
            ]);
        } else {
            // 3. Alerta: Contrato Eventual Expirado
            $isEventual = in_array(strtolower(trim($tipoContrato ?? '')), ['eventual', 'temporal']);
            if ($isEventual) {
                if ($fechaVencimiento && $fechaVencimiento->endOfDay()->isPast()) {
                    $this->alertas()->create([
                        'tipo' => 'contrato_vencido',
                        'titulo' => 'Contrato eventual vencido',
                        'descripcion' => 'La fecha de finalización del contrato temporal (' . $fechaVencimiento->format('d/m/Y') . ') ya ha pasado.',
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
        } else {
            // 5. Alerta: DNI Caducado
            if ($this->fecha_caducidad_dni && $this->fecha_caducidad_dni->endOfDay()->isPast()) {
                $this->alertas()->create([
                    'tipo' => 'dni_caducado',
                    'titulo' => 'DNI / NIE caducado',
                    'descripcion' => 'La fecha de caducidad del DNI/NIE del empleado (' . $this->fecha_caducidad_dni->format('d/m/Y') . ') ha expirado.',
                ]);
            }
        }

        // 6. Alerta: De Baja Médica
        $isOnBaja = $this->ausencias()->where('tipo', 'Bajas médicas')->whereNull('fecha_fin')->exists();
        if ($isOnBaja) {
            $this->alertas()->create([
                'tipo' => 'baja_medica',
                'titulo' => 'De baja médica',
                'descripcion' => 'Este empleado se encuentra actualmente en estado de baja médica.',
            ]);
        }

        // 7. Alerta: Sin Discapacidad / Incapacidad configurada
        $hasDiscapacidadConfigured = $this->tiene_discapacidad || $this->tiene_incapacidad || $this->no_tiene_discapacidad;
        if (!$hasDiscapacidadConfigured) {
            $this->alertas()->create([
                'tipo' => 'sin_discapacidad',
                'titulo' => 'Sin discapacidad / incapacidad configurada',
                'descripcion' => 'Este empleado no tiene configurada ninguna opción de discapacidad o incapacidad en su ficha.',
            ]);
        }

        // 8. Alerta: Discapacidad activa con archivos pendientes
        if ($this->tiene_discapacidad) {
            $hasRes = $this->documentos()->where('tipo', 'Resolución Discapacidad')->exists();
            $hasDict = $this->documentos()->where('tipo', 'Dictamen Técnico')->exists();
            $hasCert = $this->documentos()->where('tipo', 'Certificado Discapacidad')->exists();

            if (!$hasRes && !$hasDict && !$hasCert) {
                $this->alertas()->create([
                    'tipo' => 'discapacidad_archivos_pendientes',
                    'titulo' => 'Documentación de discapacidad incompleta',
                    'descripcion' => 'Tiene marcada discapacidad pero debe adjuntar al menos uno de los archivos requeridos (Resolución, Dictamen o Certificado).',
                ]);
            }
        }

        // 9. Alerta: Incapacidad activa sin documentación
        if ($this->tiene_incapacidad) {
            $hasIncap = $this->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists();
            if (!$hasIncap) {
                $this->alertas()->create([
                    'tipo' => 'incapacidad_archivos_pendientes',
                    'titulo' => 'Documentación de incapacidad incompleta',
                    'descripcion' => 'Tiene marcada incapacidad pero no tiene adjunta la documentación médica correspondiente.',
                ]);
            }
        }

        // 10. Alerta: Falta Autorización de Consulta (cuando tiene discapacidad o incapacidad)
        if ($this->tiene_discapacidad || $this->tiene_incapacidad) {
            $hasAuth = $this->documentos()->where('tipo', 'Autorización de Consulta')->exists();
            if (!$hasAuth) {
                $this->alertas()->create([
                    'tipo' => 'falta_autorizacion_consulta',
                    'titulo' => 'Falta Autorización de Consulta',
                    'descripcion' => 'El empleado tiene marcada discapacidad o incapacidad pero falta adjuntar la Autorización de Consulta.',
                ]);
            }
        }
    }
}
