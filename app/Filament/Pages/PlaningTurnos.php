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

        // Regla: Autorización basada exclusivamente en permisos
        return $user->can('gestion_planing')
            || $user->can('gestion_recursos_humanos')
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
    }

    public function cambiarVista(string $nuevaVista): void
    {
        $this->vista = in_array($nuevaVista, ['cuadricula', 'horizontal']) ? $nuevaVista : 'cuadricula';
    }

    public function seleccionarGasolinera(int $codigo): void
    {
        $this->gasolineraCodigo = $codigo;
        $this->cargarObservaciones();
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
        $esSustitucion = $this->modalEsSustitucion || ($this->modalAlcance === 'en_adelante');
        $motivo = $this->modalMotivo ?: ($this->modalAlcance === 'en_adelante' ? 'Sustitución permanente' : null);

        // Turno actual seleccionado
        $registroActual = PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)
            ->where('fecha', $this->modalFecha)
            ->where('puesto', $this->modalPuesto)
            ->where('turno', $this->modalTurno)
            ->first();

        $titularId = $this->modalTitularId
            ?: ($registroActual?->empleado_titular_id ?: $registroActual?->empleado_id);

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
            $turnosFuturos = PlaningTurno::where('planing_cuadrante_id', $cuadrante->id)
                ->where('fecha', '>', $this->modalFecha)
                ->where(function ($q) use ($titularId) {
                    $q->where('empleado_id', $titularId)
                      ->orWhere('empleado_titular_id', $titularId);
                })
                ->get();

            foreach ($turnosFuturos as $tf) {
                if (!$tf->empleado_titular_id) {
                    $tf->empleado_titular_id = $tf->empleado_id ?: $titularId;
                }
                $tf->empleado_id = $nuevoEmpleadoId;
                $tf->es_sustitucion = true;
                if ($motivo) {
                    $tf->motivo_sustitucion = $motivo;
                }
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
                $tm->es_sustitucion = $esSustitucion;
                if ($motivo) {
                    $tm->motivo_sustitucion = $motivo;
                }
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
            'filasTurnos' => $filasTurnos,
            'turnosMap' => $turnosMap,
            'observaciones' => $this->observaciones,
            'empleadosLegenda' => $empleadosLegenda,
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

    public function getFilasTurnosProperty(): array
    {
        if ($this->gasolineraCodigo === 2) {
            // Ronda Norte (24H): 5 turnos de trabajo con colores propios de turno
            return [
                [
                    'puesto' => 'Puesto 1',
                    'turno' => 'M',
                    'etiqueta' => 'PUESTO 1 - MAÑANA',
                    'icono' => 'heroicon-m-sun',
                    'color_hex' => '#0284c7',
                    'color_bg' => '#e0f2fe',
                    'color_border' => '#7dd3fc',
                    'color_text' => '#0369a1',
                ],
                [
                    'puesto' => 'Puesto 1',
                    'turno' => 'T',
                    'etiqueta' => 'PUESTO 1 - TARDE',
                    'icono' => 'heroicon-m-clock',
                    'color_hex' => '#ea580c',
                    'color_bg' => '#ffedd5',
                    'color_border' => '#fdba74',
                    'color_text' => '#c2410c',
                ],
                [
                    'puesto' => 'Puesto 2',
                    'turno' => 'M',
                    'etiqueta' => 'PUESTO 2 - MAÑANA',
                    'icono' => 'heroicon-m-sun',
                    'color_hex' => '#0d9488',
                    'color_bg' => '#ccfbf1',
                    'color_border' => '#5eead4',
                    'color_text' => '#0f766e',
                ],
                [
                    'puesto' => 'Puesto 2',
                    'turno' => 'T',
                    'etiqueta' => 'PUESTO 2 - TARDE',
                    'icono' => 'heroicon-m-clock',
                    'color_hex' => '#db2777',
                    'color_bg' => '#fce7f3',
                    'color_border' => '#f472b6',
                    'color_text' => '#be185d',
                ],
                [
                    'puesto' => 'Noche',
                    'turno' => 'N',
                    'etiqueta' => 'TURNO NOCHE',
                    'icono' => 'heroicon-m-moon',
                    'color_hex' => '#7c3aed',
                    'color_bg' => '#f3e8ff',
                    'color_border' => '#c084fc',
                    'color_text' => '#6d28d9',
                ],
            ];
        }

        // Estaciones Diurnas (1: Vistalegre, 3: Rodalabota, 4: Atenas)
        return [
            [
                'puesto' => 'Puesto Único',
                'turno' => 'M',
                'etiqueta' => 'MAÑANA',
                'icono' => 'heroicon-m-sun',
                'color_hex' => '#0284c7',
                'color_bg' => '#e0f2fe',
                'color_border' => '#7dd3fc',
                'color_text' => '#0369a1',
            ],
            [
                'puesto' => 'Puesto Único',
                'turno' => 'T',
                'etiqueta' => 'TARDE',
                'icono' => 'heroicon-m-clock',
                'color_hex' => '#ea580c',
                'color_bg' => '#ffedd5',
                'color_border' => '#fdba74',
                'color_text' => '#c2410c',
            ],
        ];
    }

    public function getTurnosMapProperty(): array
    {
        $cuadrante = PlaningCuadrante::where('gasolinera_codigo', $this->gasolineraCodigo)
            ->where('anio', $this->anio)
            ->where('mes', $this->mes)
            ->first();

        if (!$cuadrante) {
            return [];
        }

        $turnos = PlaningTurno::with(['empleado', 'empleadoTitular'])
            ->where('planing_cuadrante_id', $cuadrante->id)
            ->get();

        $map = [];
        foreach ($turnos as $t) {
            $key = $t->fecha->toDateString() . '_' . $t->puesto . '_' . $t->turno;
            $map[$key] = $t;
        }

        return $map;
    }

    public function getEmpleadosDisponiblesProperty(): Collection
    {
        return app(PlaningTurnosService::class)->getTodosEmpleadosActivos();
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
}

