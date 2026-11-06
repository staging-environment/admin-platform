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
        'gasolineras_codigos' => 'array',
        'fecha_caducidad_dni' => 'date',
        'fecha_baja' => 'date',
        'onboarding_completado' => 'boolean',
        'onboarding_paso_actual' => 'integer',
        'onboarding_fecha_completado' => 'datetime',
        'politicas_aceptadas_at' => 'datetime',
        'onboarding_verificado_por_admin' => 'boolean',
        'onboarding_verificado_at' => 'datetime',
        'onboarding_checklist' => 'array',
        'color_hex' => 'string',
    ];

    public function getEstiloColorAttribute(): array
    {
        $id = (int) ($this->id ?? 0);
        $nombreCompleto = mb_strtolower(trim($this->nombre . ' ' . ($this->apellidos ?? '')));

        // 1. Asignaciones 100% individuales y únicas (cada trabajador tiene su color exclusivo)
        // RONDA NORTE
        if ($id === 93 || (str_contains($nombreCompleto, 'romero') && !str_contains($nombreCompleto, 'rodriguez') && !str_contains($nombreCompleto, 'zambrano'))) {
            // Romero (Antonio Romero Martín - Ronda Norte Noche): Amarillo puro brillante según Excel oficial
            return [
                'bg' => '#ffff00',
                'text' => '#1f2937',
                'border' => '#ca8a04',
                'color' => '#eab308',
            ];
        }

        if ($id === 71 || str_contains($nombreCompleto, 'carvajal')) {
            // Antonio Carvajal (Ronda Norte P1): Verde hoja pastel según Excel oficial
            return [
                'bg' => '#a9d08e',
                'text' => '#1f2937',
                'border' => '#689f38',
                'color' => '#70ad47',
            ];
        }

        if ($id === 83 || str_contains($nombreCompleto, 'prieto')) {
            // Mari Carmen Prieto (Ronda Norte P1): Azul cielo lavanda suave según Excel oficial
            return [
                'bg' => '#8ea9db',
                'text' => '#1f2937',
                'border' => '#3f6bb5',
                'color' => '#4472c4',
            ];
        }

        if ($id === 64 || (str_contains($nombreCompleto, 'arcos') && str_contains($nombreCompleto, 'jesus'))) {
            // Jesús Arcos (Ronda Norte P1): Naranja cálido según Excel oficial
            return [
                'bg' => '#ed7d31',
                'text' => '#1f2937',
                'border' => '#c65911',
                'color' => '#ed7d31',
            ];
        }

        if ($id === 60 || str_contains($nombreCompleto, 'acaiña') || (str_contains($nombreCompleto, 'francisco') && str_contains($nombreCompleto, 'alvarez'))) {
            // Fran (Ronda Norte Noche): Púrpura intenso con texto blanco según Excel oficial
            return [
                'bg' => '#7030a0',
                'text' => '#ffffff',
                'border' => '#522177',
                'color' => '#7030a0',
            ];
        }

        if ($id === 82 || (str_contains($nombreCompleto, 'sergio') && str_contains($nombreCompleto, 'povea'))) {
            // Sergio Povea (Ronda Norte P2): Rojo intenso con texto blanco según Excel oficial
            return [
                'bg' => '#ef4444',
                'text' => '#ffffff',
                'border' => '#b91c1c',
                'color' => '#ef4444',
            ];
        }

        if ($id === 103 || str_contains($nombreCompleto, 'laura')) {
            // Laura Linares (Ronda Norte P2): Rosa salmón pastel según Excel oficial
            return [
                'bg' => '#f8cbad',
                'text' => '#1f2937',
                'border' => '#d98d60',
                'color' => '#f8cbad',
            ];
        }

        if ($id === 86 || str_contains($nombreCompleto, 'israel')) {
            // Israel Vega (Ronda Norte P2): Turquesa aguamarina vivo (diferenciado de Carvajal)
            return [
                'bg' => '#2dd4bf',
                'text' => '#042f2e',
                'border' => '#0d9488',
                'color' => '#14b8a6',
            ];
        }

        if ($id === 69 || str_contains($nombreCompleto, 'campos')) {
            // Antonio Campos (Ronda Norte): Ámbar dorado (diferenciado del amarillo de Romero)
            return [
                'bg' => '#fbbf24',
                'text' => '#78350f',
                'border' => '#d97706',
                'color' => '#f59e0b',
            ];
        }

        // VISTALEGRE
        if ($id === 84 || str_contains($nombreCompleto, 'antonio jesus') || str_contains($nombreCompleto, 'ant. jesus')) {
            // Ant. Jesús (Vistalegre): Verde menta pastel distintivo (diferenciado de Carvajal)
            return [
                'bg' => '#86efac',
                'text' => '#14532d',
                'border' => '#22c55e',
                'color' => '#16a34a',
            ];
        }

        if ($id === 95 || str_contains($nombreCompleto, 'felisa')) {
            // Felisa Díaz (Vistalegre): Rosa salmón pastel según Excel oficial
            return [
                'bg' => '#f8cbad',
                'text' => '#1f2937',
                'border' => '#e29e74',
                'color' => '#f8cbad',
            ];
        }

        if ($id === 67 || str_contains($nombreCompleto, 'blanquero')) {
            // Miguel Ángel Blanquero (Vistalegre): Naranja según Excel oficial
            return [
                'bg' => '#ed7d31',
                'text' => '#ffffff',
                'border' => '#c65911',
                'color' => '#ed7d31',
            ];
        }

        if ($id === 88 || str_contains($nombreCompleto, 'consolacion')) {
            // Consolación Zapata (Vistalegre): Naranja cálido (suplente de Miguel Ángel según Excel oficial)
            return [
                'bg' => '#f97316',
                'text' => '#ffffff',
                'border' => '#ea580c',
                'color' => '#ea580c',
            ];
        }

        // RODALABOTA
        if ($id === 79 || str_contains($nombreCompleto, 'marrufo')) {
            // Antonio Marrufo (Rodalabota): Verde claro según Excel oficial
            return [
                'bg' => '#92d050',
                'text' => '#1f2937',
                'border' => '#65a30d',
                'color' => '#84cc16',
            ];
        }

        if ($id === 68 || str_contains($nombreCompleto, 'borja')) {
            // Borja Jesús (Rodalabota): Azul suave según Excel oficial
            return [
                'bg' => '#5b9bd5',
                'text' => '#ffffff',
                'border' => '#2c5b94',
                'color' => '#4472c4',
            ];
        }

        if ($id === 76 || str_contains($nombreCompleto, 'emilio')) {
            // Emilio González (Rodalabota): Naranja cálido según Excel oficial
            return [
                'bg' => '#ed7d31',
                'text' => '#ffffff',
                'border' => '#c65911',
                'color' => '#ed7d31',
            ];
        }

        // ATENAS
        if ($id === 77 || str_contains($nombreCompleto, 'llompart')) {
            // Toni Llompart (Atenas): Azul cielo suave según Excel oficial
            return [
                'bg' => '#9bc2e6',
                'text' => '#1f2937',
                'border' => '#5b9bd5',
                'color' => '#41719c',
            ];
        }

        if ($id === 87 || str_contains($nombreCompleto, 'zambrano')) {
            // Juan Antonio Zambrano (Atenas): Amarillo puro brillante según Excel oficial
            return [
                'bg' => '#ffff00',
                'text' => '#1f2937',
                'border' => '#ca8a04',
                'color' => '#eab308',
            ];
        }

        if ($id === 70 || str_contains($nombreCompleto, 'joaquin')) {
            // Joaquín Caro (Atenas): Naranja cálido según Excel oficial
            return [
                'bg' => '#ed7d31',
                'text' => '#ffffff',
                'border' => '#c65911',
                'color' => '#ed7d31',
            ];
        }

        // 2. Si tiene un color personalizado explícito en base de datos
        if (!empty($this->color_hex)) {
            return [
                'bg' => $this->color_hex,
                'text' => '#ffffff',
                'border' => $this->color_hex,
                'color' => $this->color_hex,
            ];
        }

        // 3. Paleta armónica distintiva por defecto para otros trabajadores (sin repetir)
        $palette = [
            ['bg' => '#bae6fd', 'text' => '#0369a1', 'border' => '#7dd3fc', 'color' => '#0284c7'],
            ['bg' => '#fef08a', 'text' => '#854d0e', 'border' => '#fde047', 'color' => '#d97706'],
            ['bg' => '#e9d5ff', 'text' => '#6b21a8', 'border' => '#d8b4fe', 'color' => '#9333ea'],
            ['bg' => '#99f6e4', 'text' => '#115e59', 'border' => '#5eead4', 'color' => '#0d9488'],
            ['bg' => '#fed7aa', 'text' => '#9a3412', 'border' => '#fdba74', 'color' => '#ea580c'],
            ['bg' => '#ddd6fe', 'text' => '#5b21b6', 'border' => '#c4b5fd', 'color' => '#7c3aed'],
        ];

        return $palette[($this->id ?? 1) % count($palette)];
    }

    public function getColorBgAttribute(): string
    {
        return $this->estilo_color['bg'];
    }

    public function getColorTextAttribute(): string
    {
        return $this->estilo_color['text'];
    }

    public function getColorBorderAttribute(): string
    {
        return $this->estilo_color['border'];
    }

    public function getColorAttribute(): string
    {
        return $this->estilo_color['color'] ?? ($this->color_hex ?: '#0284c7');
    }

    public function getNombreCortoAttribute(): string
    {
        $id = (int) ($this->id ?? 0);
        $nombre = trim($this->nombre);
        $apellidos = trim($this->apellidos ?? '');
        $nombreCompleto = mb_strtoupper($nombre . ' ' . $apellidos);

        // Identificación inequívoca por ID o nombre específico
        if ($id === 84 || str_contains($nombreCompleto, 'ANTONIO JESUS') || str_contains($nombreCompleto, 'ANT. JESUS')) {
            return 'ANT. JESUS';
        }
        if ($id === 87 || str_contains($nombreCompleto, 'ZAMBRANO')) {
            return 'ZAMBRANO';
        }
        if ($id === 93 || str_contains($nombreCompleto, 'ROMERO')) {
            return 'ROMERO';
        }
        if ($id === 71 || str_contains($nombreCompleto, 'CARVAJAL')) {
            return 'CARVAJAL';
        }
        if ($id === 69 || str_contains($nombreCompleto, 'CAMPOS')) {
            return 'CAMPOS';
        }
        if ($id === 60 || str_contains($nombreCompleto, 'ACAIÑA') || (str_contains($nombreCompleto, 'FRANCISCO') && str_contains($nombreCompleto, 'ALVAREZ'))) {
            return 'FRAN';
        }
        if ($id === 83 || str_contains($nombreCompleto, 'PRIETO') || str_contains($nombreCompleto, 'MARI CARMEN') || str_contains($nombreCompleto, 'MARIA DEL CARMEN')) {
            return 'MARI CRMEN';
        }
        if ($id === 64 || (str_contains($nombreCompleto, 'ARCOS') && str_contains($nombreCompleto, 'JESUS'))) {
            return 'JESUS';
        }
        if ($id === 82 || (str_contains($nombreCompleto, 'SERGIO') && str_contains($nombreCompleto, 'POVEA'))) {
            return 'SERGIO';
        }
        if ($id === 86 || str_contains($nombreCompleto, 'ISRAEL')) {
            return 'ISRAEL';
        }
        if ($id === 103 || str_contains($nombreCompleto, 'LAURA')) {
            return 'LAURA';
        }
        if ($id === 95 || str_contains($nombreCompleto, 'FELISA')) {
            return 'FELISA';
        }
        if ($id === 67 || str_contains($nombreCompleto, 'BLANQUERO') || str_contains($nombreCompleto, 'MIGUEL ANGEL')) {
            return 'MIGUEL ANGEL';
        }
        if ($id === 88 || str_contains($nombreCompleto, 'CONSOLACION')) {
            return 'CONSOLACION';
        }
        if ($id === 79 || str_contains($nombreCompleto, 'MARRUFO')) {
            return 'MARRUFO';
        }
        if ($id === 68 || str_contains($nombreCompleto, 'BORJA')) {
            return 'BORJA';
        }
        if ($id === 76 || str_contains($nombreCompleto, 'EMILIO')) {
            return 'EMILIO';
        }
        if ($id === 77 || str_contains($nombreCompleto, 'LLOMPART')) {
            return 'TONI';
        }
        if ($id === 70 || str_contains($nombreCompleto, 'JOAQUIN')) {
            return 'JOAQUIN';
        }

        $partes = preg_split('/\s+/', $nombre);

        if (count($partes) >= 2) {
            $p0 = mb_strtoupper($partes[0]);
            $p1 = mb_strtoupper($partes[1]);

            if (in_array($p0, ['ANTONIO', 'ANT.', 'ANT'])) {
                return 'ANT. ' . $p1;
            }
            if (in_array($p0, ['MIGUEL', 'JOSE', 'JUAN', 'MARI', 'MARIA'])) {
                return $p0 . ' ' . $p1;
            }
            return $p0;
        }

        return mb_strtoupper($nombre);
    }


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

    public function getEstacionesAttribute()
    {
        $codigos = $this->gasolineras_codigos;
        if (empty($codigos) && $this->gasolinera_codigo) {
            $codigos = [$this->gasolinera_codigo];
        }
        if (empty($codigos)) {
            return collect();
        }
        try {
            return Gasolinera::whereIn('Codigo', $codigos)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function getEstacionesNombresAttribute(): string
    {
        $codigos = $this->gasolineras_codigos;
        if (empty($codigos) && $this->gasolinera_codigo) {
            $codigos = [$this->gasolinera_codigo];
        }
        if (empty($codigos)) {
            return '—';
        }

        $map = [
            1 => 'E.S. VISTALEGRE',
            2 => 'RONDA NORTE',
            3 => 'E.S RODALABOTA',
            4 => 'E.S. ATENAS',
            5 => 'Trieste',
        ];

        try {
            $nombres = Gasolinera::whereIn('Codigo', $codigos)->pluck('Nombre')->filter()->toArray();
            if (!empty($nombres)) {
                return implode(', ', $nombres);
            }
        } catch (\Throwable $e) {
            // Fallback to static map
        }

        $nombres = array_filter(array_map(fn($c) => $map[(int)$c] ?? null, $codigos));
        return !empty($nombres) ? implode(', ', $nombres) : '—';
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
            
            $codigos = $latest->gasolineras_codigos;
            if (empty($codigos) && $latest->gasolinera_codigo) {
                $codigos = [$latest->gasolinera_codigo];
            }
            if (empty($codigos) && !empty($this->gasolineras_codigos)) {
                $codigos = $this->gasolineras_codigos;
            } elseif (empty($codigos) && $this->gasolinera_codigo) {
                $codigos = [$this->gasolinera_codigo];
            }

            $codigos = !empty($codigos) ? array_values(array_unique(array_filter(array_map('intval', (array) $codigos)))) : null;
            $primaryCodigo = (!empty($codigos) && isset($codigos[0])) ? (int)$codigos[0] : $this->gasolinera_codigo;

            $this->update([
                'tipo_contrato' => $latest->tipo_contrato,
                'fecha_vencimiento_contrato' => $isEventual ? $latest->fecha_vencimiento_contrato : null,
                'gasolineras_codigos' => $codigos,
                'gasolinera_codigo' => $primaryCodigo,
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

    public function isOnBajaMedica(): bool
    {
        if ($this->relationLoaded('ausencias')) {
            return $this->ausencias->where('tipo', 'Bajas médicas')->whereNull('fecha_fin')->isNotEmpty();
        }

        return $this->ausencias()->where('tipo', 'Bajas médicas')->whereNull('fecha_fin')->exists();
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
        $hasContract = ($latestContract !== null);

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

    public function onboardingVerificadoPor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'onboarding_verificado_user_id');
    }

    public function isOnboardingPending(): bool
    {
        return !$this->onboarding_completado;
    }

    public function getOnboardingProgressPercentage(): int
    {
        if ($this->onboarding_completado) {
            return 100;
        }

        $completedSteps = 0;
        $totalSteps = 4;

        // Paso 1: Password changed (if user doesn't have default 1234)
        $user = $this->user;
        if ($user && !\Illuminate\Support\Facades\Hash::check('1234', $user->password)) {
            $completedSteps++;
        }

        // Paso 2: Personal data & IBAN & NUSS
        if (!empty($this->dni) && !str_starts_with($this->dni, 'PENDIENTE') && !empty($this->iban)) {
            $completedSteps++;
        }

        // Paso 3: Documents uploaded (DNI uploaded)
        if ($this->documentos()->where('tipo', 'DNI')->exists()) {
            $completedSteps++;
        }

        // Paso 4: Policies accepted
        if ($this->politicas_aceptadas_at !== null) {
            $completedSteps++;
        }

        return (int) round(($completedSteps / $totalSteps) * 100);
    }
}
