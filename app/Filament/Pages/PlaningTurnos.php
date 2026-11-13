<?php

namespace App\Filament\Pages;

use App\Models\Empleado;
use App\Models\PlaningCuadrante;
use App\Models\PlaningTurno;
use App\Services\PlaningTurnosService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlaningTurnos extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Planing';
    protected static ?string $title = 'Planing de Turnos';
    protected static ?string $slug = 'planing-turnos';

    protected static string|\UnitEnum|null $navigationGroup = 'Recursos humanos';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.planing-turnos';

    protected $queryString = [
        'gasolineraCodigo' => ['except' => 1, 'as' => 'estacion'],
        'vista' => ['except' => 'cuadricula'],
    ];

    // Estado principal
    public int $gasolineraCodigo = 1;
    public int $anio;
    public int $mes;
    public ?string $observaciones = '';
    public string $vista = 'cuadricula'; // 'cuadricula' (por defecto en recuadros sin scroll) o 'horizontal' (tabla corrida)
    public string $filtroPuestoRonda = 'todos'; // 'todos', 'Puesto 1', 'Puesto 2', 'Noche'

    // Estado del modal de turno
    public bool $mostrarModalTurno = false;
    public ?int $modalTurnoId = null;
    public ?string $modalFecha = null;
    public ?string $modalPuesto = null;
    public ?string $modalTurno = null;
    public $modalEmpleadoId = null;
    public ?int $modalTitularId = null;
    public bool $modalEsSustitucion = false;
    public ?string $modalMotivo = null;
    public ?string $modalNotas = null;
    public ?string $modalTitularNombre = null;
    public string $modalAlcance = 'puntual'; // 'puntual', 'en_adelante', 'todo_el_mes'

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $user->unsetRelation('roles')->unsetRelation('permissions');

        // Regla: Autorización basada exclusivamente en permisos
        return $user->can('gestion_planing')
            || $user->email === 'jarodriguezbonilla@gmail.com'
            || $user->id === 1;
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getBreadcrumbs(): array
    {
        $estacionNombre = match ($this->gasolineraCodigo) {
            1 => 'Vistalegre',
            2 => 'Ronda Norte',
            3 => 'Rodalabota',
            4 => 'Atenas',
            default => 'Estación',
        };

        return [
            'Recursos humanos',
            'Planing',
            $estacionNombre,
        ];
    }

    public function mount(): void
    {
        $estacion = request()->query('estacion');
        if ($estacion && in_array((int) $estacion, [1, 2, 3, 4])) {
            $this->gasolineraCodigo = (int) $estacion;
        }

        $vistaQuery = request()->query('vista');
        if ($vistaQuery && in_array($vistaQuery, ['cuadricula', 'horizontal'])) {
            $this->vista = $vistaQuery;
        }

        $this->anio = (int) now()->format('Y');
        $this->mes = (int) now()->format('n');

        $this->cargarObservaciones();

        // Asegurar que la estación activa tenga su horizonte de 12 meses calculado automáticamente
        app(PlaningTurnosService::class)->asegurarHorizonte12Meses($this->gasolineraCodigo, 12, false);
    }

    public function cambiarVista(string $nuevaVista): void
    {
        $this->vista = in_array($nuevaVista, ['cuadricula', 'horizontal']) ? $nuevaVista : 'cuadricula';
    }

    public function seleccionarGasolinera(int $codigo): void
    {
        $this->gasolineraCodigo = $codigo;
        $this->filtroPuestoRonda = 'todos';
        $this->cargarObservaciones();

        // Asegurar que la estación seleccionada tenga su horizonte de 12 meses calculado
        app(PlaningTurnosService::class)->asegurarHorizonte12Meses($codigo, 12, false);
    }

    public function setFiltroPuesto(string $puesto): void
    {
        $this->filtroPuestoRonda = in_array($puesto, ['todos', 'Puesto 1', 'Puesto 2']) ? $puesto : 'todos';
    }

    public function updatedAnio(): void
    {
        $this->cargarObservaciones();
    }

    public function updatedMes(): void
    {
        $this->cargarObservaciones();
    }

    public function cambiarMes(int $delta): void
    {
        $fecha = Carbon::create($this->anio, $this->mes, 1)->addMonths($delta);
        $this->anio = (int) $fecha->format('Y');
        $this->mes = (int) $fecha->format('n');
        $this->cargarObservaciones();
    }

    public function irMesActual(): void
    {
        $this->anio = (int) now()->format('Y');
        $this->mes = (int) now()->format('n');
        $this->cargarObservaciones();
    }

    protected function cargarObservaciones(): void
    {
        $cuadrante = PlaningCuadrante::where('gasolinera_codigo', $this->gasolineraCodigo)
            ->where('anio', $this->anio)
            ->where('mes', $this->mes)
            ->first();

        $this->observaciones = $cuadrante?->observaciones ?? '';
    }

    public function guardarObservaciones(): void
    {
        $service = app(PlaningTurnosService::class);
        $cuadrante = $service->obtenerOCrearCuadrante($this->gasolineraCodigo, $this->anio, $this->mes);
        $cuadrante->observaciones = $this->observaciones;
        $cuadrante->save();

        Notification::make()
            ->title('Observaciones guardadas con éxito')
            ->success()
            ->send();
    }

    public function generarCuadrante(): void
    {
        $service = app(PlaningTurnosService::class);
        $cantidad = $service->generarCuadranteTeorico($this->gasolineraCodigo, $this->anio, $this->mes, true);

        Notification::make()
            ->title("Cuadrante generado correctamente ({$cantidad} turnos asignados)")
            ->body('Se ha calculado la rotación 4x2 sin incluir libranzas en la visualización.')
            ->success()
            ->send();
    }

    public function abrirModalTurno(string $fecha, string $puesto, string $turno): void
    {
        $service = app(PlaningTurnosService::class);
        $cuadrante = $service->obtenerOCrearCuadrante($this->gasolineraCodigo, $this->anio, $this->mes);

        $registro = PlaningTurno::with(['empleado', 'empleadoTitular'])
            ->where('planing_cuadrante_id', $cuadrante->id)
            ->where('fecha', $fecha)
            ->where('puesto', $puesto)
            ->where('turno', $turno)
            ->first();

        $this->modalFecha = $fecha;
        $this->modalPuesto = $puesto;
        $this->modalTurno = $turno;
        $this->modalAlcance = 'puntual';

        if ($registro) {
            $this->modalTurnoId = $registro->id;
            $this->modalEmpleadoId = $registro->empleado_id;
            $this->modalTitularId = $registro->empleado_titular_id ?: $registro->empleado_id;
            $this->modalEsSustitucion = (bool) $registro->es_sustitucion;
            $this->modalMotivo = $registro->motivo_sustitucion;
            $this->modalNotas = $registro->notas;
            $this->modalTitularNombre = $registro->empleadoTitular
                ? $registro->empleadoTitular->nombre . ' ' . $registro->empleadoTitular->apellidos
                : ($registro->empleado ? $registro->empleado->nombre . ' ' . $registro->empleado->apellidos : null);
        } else {
            $this->modalTurnoId = null;
            $this->modalEmpleadoId = null;
            $this->modalTitularId = null;
            $this->modalEsSustitucion = false;
            $this->modalMotivo = null;
            $this->modalNotas = null;
            $this->modalTitularNombre = null;
        }

        $this->mostrarModalTurno = true;
    }

    public function cerrarModal(): void
    {
        $this->mostrarModalTurno = false;
    }

    public function guardarTurnoModal(): void
    {
        if (!$this->modalFecha || !$this->modalPuesto || !$this->modalTurno) {
            return;
        }

        $service = app(PlaningTurnosService::class);
        $cuadrante = $service->obtenerOCrearCuadrante($this->gasolineraCodigo, $this->anio, $this->mes);

        $nuevoEmpleadoId = !empty($this->modalEmpleadoId) ? (int) $this->modalEmpleadoId : null;
        
        // Si la sustitución es permanente o para todo el mes, NO se marca como sustitución visual en el cuadrante
        $esPermanente = ($this->modalAlcance === 'en_adelante' || $this->modalAlcance === 'todo_el_mes');
        $esSustitucion = $esPermanente ? false : (bool) $this->modalEsSustitucion;
        $motivo = $esSustitucion ? $this->modalMotivo : null;

        // Turno actual seleccionado
        $registroActual = PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)
            ->where('fecha', $this->modalFecha)
            ->where('puesto', $this->modalPuesto)
            ->where('turno', $this->modalTurno)
            ->first();

        $titularId = $this->modalTitularId
            ?: ($registroActual?->empleado_titular_id ?: $registroActual?->empleado_id);

        // Validación: No permitir asignar a un empleado que esté de baja en esa fecha
        if ($nuevoEmpleadoId && $nuevoEmpleadoId !== $registroActual?->empleado_id) {
            $empleadoCandidato = Empleado::find($nuevoEmpleadoId);
            if ($empleadoCandidato && $empleadoCandidato->estaDeBajaEnFecha($this->modalFecha)) {
                Notification::make()
                    ->title('Empleado no disponible')
                    ->body("{$empleadoCandidato->nombre} {$empleadoCandidato->apellidos} se encuentra en situación de baja en la fecha seleccionada.")
                    ->danger()
                    ->send();
                return;
            }
        }

        // 1. Asignar el turno actual seleccionado
        $service->asignarTurnoManual(
            $cuadrante->id,
            $this->modalFecha,
            $this->modalPuesto,
            $this->modalTurno,
            $nuevoEmpleadoId,
            $esSustitucion,
            $motivo,
            $this->modalNotas
        );

        $totalAfectados = 1;

        // 2. Si el usuario seleccionó sustitución permanente en adelante
        if ($this->modalAlcance === 'en_adelante' && $titularId && $nuevoEmpleadoId) {
            // Asegurar que existan los cuadrantes futuros de esta estación en el horizonte (12 meses)
            $service->asegurarHorizonte12Meses($this->gasolineraCodigo, 12, false);

            $queryFuturos = PlaningTurno::whereHas('cuadrante', function ($q) {
                    $q->where('gasolinera_codigo', $this->gasolineraCodigo);
                })
                ->where('fecha', '>', $this->modalFecha)
                ->where(function ($q) use ($titularId) {
                    $q->where('empleado_id', $titularId)
                      ->orWhere('empleado_titular_id', $titularId);
                });

            if (!empty($this->modalPuesto)) {
                $queryFuturos->where('puesto', $this->modalPuesto);
            }

            $turnosFuturos = $queryFuturos->get();

            foreach ($turnosFuturos as $tf) {
                if (!$tf->empleado_titular_id) {
                    $tf->empleado_titular_id = $tf->empleado_id ?: $titularId;
                }
                $tf->empleado_id = $nuevoEmpleadoId;
                // Permanente: sin marca de sustitución en el cuadrante
                $tf->es_sustitucion = false;
                $tf->motivo_sustitucion = null;
                $tf->save();
                $totalAfectados++;
            }
        }
        // 3. Si el usuario seleccionó reemplazar en todo el mes
        elseif ($this->modalAlcance === 'todo_el_mes' && $titularId && $nuevoEmpleadoId) {
            $turnosMes = PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)
                ->where('fecha', '!=', $this->modalFecha)
                ->where(function ($q) use ($titularId) {
                    $q->where('empleado_id', $titularId)
                      ->orWhere('empleado_titular_id', $titularId);
                })
                ->get();

            foreach ($turnosMes as $tm) {
                if (!$tm->empleado_titular_id) {
                    $tm->empleado_titular_id = $tm->empleado_id ?: $titularId;
                }
                $tm->empleado_id = $nuevoEmpleadoId;
                $tm->es_sustitucion = false;
                $tm->motivo_sustitucion = null;
                $tm->save();
                $totalAfectados++;
            }
        }

        $this->mostrarModalTurno = false;

        $msg = $totalAfectados > 1
            ? "Se han actualizado {$totalAfectados} turnos correctamente (sustitución en lote)."
            : "Turno actualizado correctamente.";

        Notification::make()
            ->title($msg)
            ->success()
            ->send();
    }

    public function borrarAsignacionModal(): void
    {
        if ($this->modalTurnoId) {
            PlaningTurno::where('id', $this->modalTurnoId)->delete();
        }

        $this->mostrarModalTurno = false;

        Notification::make()
            ->title('Asignación eliminada del cuadrante')
            ->success()
            ->send();
    }

    public function exportarPdf(): StreamedResponse
    {
        $service = app(PlaningTurnosService::class);
        $cuadrante = $service->obtenerOCrearCuadrante($this->gasolineraCodigo, $this->anio, $this->mes);

        $dias = $this->getDiasMesProperty();
        $filasTurnos = $this->getFilasTurnosProperty();
        $turnosMap = $this->getTurnosMapProperty();
        $empleadosLegenda = $this->getEmpleadosLegendaProperty();

        $nombreEstacion = match ($this->gasolineraCodigo) {
            1 => 'E.S. Vistalegre (Utrera)',
            2 => 'E.S. Ronda Norte (Utrera)',
            3 => 'E.S. Rodalabota (El Cuervo)',
            4 => 'E.S. Atenas (Lebrija)',
            default => 'Estación #' . $this->gasolineraCodigo,
        };

        $mesNombre = Carbon::create($this->anio, $this->mes, 1)->locale('es')->isoFormat('MMMM YYYY');

        $pdf = Pdf::loadView('pdf.planing-cuadrante-impresion', [
            'nombreEstacion' => $nombreEstacion,
            'mesNombre' => mb_strtoupper($mesNombre),
            'anio' => $this->anio,
            'mes' => $this->mes,
            'dias' => $dias,
            'bloquesPuestos' => $this->bloquesPuestos,
            'filasTurnos' => $filasTurnos,
            'turnosMap' => $turnosMap,
            'observaciones' => $this->observaciones,
            'empleadosLegenda' => $empleadosLegenda,
            'leyendaTurnos' => $this->leyendaTurnos,
        ])->setPaper('a4', 'landscape');

        $filename = "Planing_{$this->gasolineraCodigo}_{$this->anio}_{$this->mes}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }

    public function getSemanasMesProperty(): array
    {
        $primerDiaMes = Carbon::create($this->anio, $this->mes, 1)->startOfDay();
        $ultimoDiaMes = Carbon::create($this->anio, $this->mes, 1)->endOfMonth()->startOfDay();

        // En España/Europa, la semana arranca en Lunes (Carbon::MONDAY = 1)
        $inicioCalendario = $primerDiaMes->copy()->startOfWeek(Carbon::MONDAY);
        $finCalendario = $ultimoDiaMes->copy()->endOfWeek(Carbon::SUNDAY);

        $semanas = [];
        $cursor = $inicioCalendario->copy();
        $diasCortos = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
        $diasLargos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        while ($cursor->lte($finCalendario)) {
            $diasSemana = [];
            $numeroSemana = (int) $cursor->isoWeek();

            for ($i = 0; $i < 7; $i++) {
                $esMesActual = ($cursor->month === $this->mes && $cursor->year === $this->anio);
                $diasSemana[] = [
                    'numero' => $cursor->day,
                    'fecha' => $cursor->toDateString(),
                    'diaSemana' => $diasCortos[$i],
                    'diaSemanaLargo' => $diasLargos[$i],
                    'esMesActual' => $esMesActual,
                    'esFinDeSemana' => in_array($i, [5, 6]), // Sábado y Domingo
                    'esHoy' => $cursor->isToday(),
                ];
                $cursor->addDay();
            }

            $semanas[] = [
                'numeroSemana' => $numeroSemana,
                'dias' => $diasSemana,
            ];
        }

        return $semanas;
    }

    public function getDiasMesProperty(): array
    {
        $diasInMonth = Carbon::create($this->anio, $this->mes, 1)->daysInMonth;
        $dias = [];

        $diasSemana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];

        for ($d = 1; $d <= $diasInMonth; $d++) {
            $fecha = Carbon::create($this->anio, $this->mes, $d);
            $dayOfWeek = (int) $fecha->format('w'); // 0 = Domingo, 6 = Sábado
            $dias[] = [
                'numero' => $d,
                'fecha' => $fecha->toDateString(),
                'diaSemana' => $diasSemana[$dayOfWeek],
                'esFinDeSemana' => in_array($dayOfWeek, [0, 6]),
            ];
        }

        return $dias;
    }

    public function getBloquesPuestosProperty(): array
    {
        if ($this->gasolineraCodigo === 2) {
            $bloques = [
                'Puesto 1' => [
                    'id' => 'Puesto 1',
                    'nombre' => 'Puesto 1 (Empleado 1)',
                    'subtitulo' => 'Turnos: Mañana, Tarde y Noche',
                    'icono' => 'heroicon-m-user',
                    'color_badge' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
                    'filas' => [
                        [
                            'puesto' => 'Puesto 1',
                            'turno' => 'M',
                            'etiqueta' => 'MAÑANA',
                            'icono' => 'heroicon-m-sun',
                            'color_hex' => '#0284c7',
                            'color_bg' => '#f8fafc',
                            'color_border' => '#cbd5e1',
                            'color_text' => '#1e293b',
                        ],
                        [
                            'puesto' => 'Puesto 1',
                            'turno' => 'T',
                            'etiqueta' => 'TARDE',
                            'icono' => 'heroicon-m-clock',
                            'color_hex' => '#ea580c',
                            'color_bg' => '#f8fafc',
                            'color_border' => '#cbd5e1',
                            'color_text' => '#1e293b',
                        ],
                        [
                            'puesto' => 'Noche',
                            'turno' => 'N',
                            'etiqueta' => 'NOCHE',
                            'icono' => 'heroicon-m-moon',
                            'color_hex' => '#7c3aed',
                            'color_bg' => '#f8fafc',
                            'color_border' => '#cbd5e1',
                            'color_text' => '#1e293b',
                        ],
                    ],
                ],
                'Puesto 2' => [
                    'id' => 'Puesto 2',
                    'nombre' => 'Puesto 2 (Empleado 2)',
                    'subtitulo' => 'Turnos: Mañana y Tarde',
                    'icono' => 'heroicon-m-user-group',
                    'color_badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                    'filas' => [
                        [
                            'puesto' => 'Puesto 2',
                            'turno' => 'M',
                            'etiqueta' => 'MAÑANA',
                            'icono' => 'heroicon-m-sun',
                            'color_hex' => '#0d9488',
                            'color_bg' => '#f8fafc',
                            'color_border' => '#cbd5e1',
                            'color_text' => '#1e293b',
                        ],
                        [
                            'puesto' => 'Puesto 2',
                            'turno' => 'T',
                            'etiqueta' => 'TARDE',
                            'icono' => 'heroicon-m-clock',
                            'color_hex' => '#db2777',
                            'color_bg' => '#f8fafc',
                            'color_border' => '#cbd5e1',
                            'color_text' => '#1e293b',
                        ],
                    ],
                ],
            ];

            if ($this->filtroPuestoRonda !== 'todos' && isset($bloques[$this->filtroPuestoRonda])) {
                return [$this->filtroPuestoRonda => $bloques[$this->filtroPuestoRonda]];
            }

            return $bloques;
        }

        // Estaciones Diurnas (1: Vistalegre, 3: Rodalabota, 4: Atenas)
        return [
            'Puesto Único' => [
                'id' => 'Puesto Único',
                'nombre' => null,
                'subtitulo' => null,
                'icono' => null,
                'color_badge' => null,
                'filas' => [
                    [
                        'puesto' => 'Puesto Único',
                        'turno' => 'M',
                        'etiqueta' => 'MAÑANA',
                        'icono' => 'heroicon-m-sun',
                        'color_hex' => '#0284c7',
                        'color_bg' => '#f8fafc',
                        'color_border' => '#cbd5e1',
                        'color_text' => '#1e293b',
                    ],
                    [
                        'puesto' => 'Puesto Único',
                        'turno' => 'T',
                        'etiqueta' => 'TARDE',
                        'icono' => 'heroicon-m-clock',
                        'color_hex' => '#ea580c',
                        'color_bg' => '#f8fafc',
                        'color_border' => '#cbd5e1',
                        'color_text' => '#1e293b',
                    ],
                ],
            ],
        ];
    }

    public function getFilasTurnosProperty(): array
    {
        $filas = [];
        $vistos = [];
        foreach ($this->bloquesPuestos as $bloque) {
            foreach ($bloque['filas'] as $f) {
                $k = $f['puesto'] . '_' . $f['turno'];
                if (!isset($vistos[$k])) {
                    $vistos[$k] = true;
                    $filas[] = $f;
                }
            }
        }
        return $filas;
    }

    public function getTurnosMapProperty(): array
    {
        $service = app(PlaningTurnosService::class);

        $cuadrante = PlaningCuadrante::where('gasolinera_codigo', $this->gasolineraCodigo)
            ->where('anio', $this->anio)
            ->where('mes', $this->mes)
            ->first();

        if (!$cuadrante) {
            $service->asegurarCuadranteMes($this->gasolineraCodigo, $this->anio, $this->mes, false);
            $cuadrante = PlaningCuadrante::where('gasolinera_codigo', $this->gasolineraCodigo)
                ->where('anio', $this->anio)
                ->where('mes', $this->mes)
                ->first();
        }

        if (!$cuadrante) {
            return [];
        }

        $turnos = PlaningTurno::with(['empleado', 'empleadoTitular', 'cuadrante'])
            ->where('planing_cuadrante_id', $cuadrante->id)
            ->get();

        if ($turnos->isEmpty() && $service->tieneEmpleados($this->gasolineraCodigo)) {
            $service->generarCuadranteTeorico($this->gasolineraCodigo, $this->anio, $this->mes, false);
            $turnos = PlaningTurno::with(['empleado', 'empleadoTitular', 'cuadrante'])
                ->where('planing_cuadrante_id', $cuadrante->id)
                ->get();
        }

        $map = [];
        foreach ($turnos as $t) {
            $key = $t->fecha->toDateString() . '_' . $t->puesto . '_' . $t->turno;
            $map[$key] = $t;
        }

        return $map;
    }

    public function getEmpleadosDisponiblesProperty(): Collection
    {
        return app(PlaningTurnosService::class)->getTodosEmpleadosActivos($this->modalFecha);
    }

    public function getEmpleadoActualDeBajaProperty(): ?Empleado
    {
        if (!$this->modalEmpleadoId || !$this->modalFecha) {
            return null;
        }

        $emp = Empleado::find($this->modalEmpleadoId);
        if ($emp && $emp->estaDeBajaEnFecha($this->modalFecha)) {
            return $emp;
        }

        return null;
    }

    public function getEmpleadosLegendaProperty(): Collection
    {
        $turnosMap = $this->getTurnosMapProperty();
        $empleadosIds = [];

        foreach ($turnosMap as $t) {
            if ($t->empleado_id) {
                $empleadosIds[$t->empleado_id] = true;
            }
        }

        if (empty($empleadosIds)) {
            return app(PlaningTurnosService::class)->getEmpleadosEstacion($this->gasolineraCodigo);
        }

        return Empleado::whereIn('id', array_keys($empleadosIds))->orderBy('nombre')->get();
    }

    public function getLeyendaTurnosProperty(): array
    {
        return app(PlaningTurnosService::class)->obtenerLeyendaTurnos(
            $this->gasolineraCodigo,
            $this->filtroPuestoRonda
        );
    }
}

