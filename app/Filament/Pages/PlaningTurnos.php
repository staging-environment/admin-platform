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
    public ?int $modalEmpleadoId = null;
    public bool $modalEsSustitucion = false;
    public ?string $modalMotivo = null;
    public ?string $modalNotas = null;
    public ?string $modalTitularNombre = null;

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

        if ($registro) {
            $this->modalTurnoId = $registro->id;
            $this->modalEmpleadoId = $registro->empleado_id;
            $this->modalEsSustitucion = (bool) $registro->es_sustitucion;
            $this->modalMotivo = $registro->motivo_sustitucion;
            $this->modalNotas = $registro->notas;
            $this->modalTitularNombre = $registro->empleadoTitular
                ? $registro->empleadoTitular->nombre . ' ' . $registro->empleadoTitular->apellidos
                : null;
        } else {
            $this->modalTurnoId = null;
            $this->modalEmpleadoId = null;
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

        $service->asignarTurnoManual(
            $cuadrante->id,
            $this->modalFecha,
            $this->modalPuesto,
            $this->modalTurno,
            $this->modalEmpleadoId ?: null,
            $this->modalEsSustitucion,
            $this->modalMotivo,
            $this->modalNotas
        );

        $this->mostrarModalTurno = false;

        Notification::make()
            ->title('Turno actualizado correctamente')
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
            // Ronda Norte (24H): 5 turnos de trabajo
            return [
                [
                    'puesto' => 'Puesto 1',
                    'turno' => 'M',
                    'etiqueta' => 'PUESTO 1 - MAÑANA',
                    'icono' => 'heroicon-m-sun',
                    'color_badge' => 'bg-sky-50 text-sky-700 border-sky-300',
                ],
                [
                    'puesto' => 'Puesto 1',
                    'turno' => 'T',
                    'etiqueta' => 'PUESTO 1 - TARDE',
                    'icono' => 'heroicon-m-clock',
                    'color_badge' => 'bg-amber-50 text-amber-700 border-amber-300',
                ],
                [
                    'puesto' => 'Puesto 2',
                    'turno' => 'M',
                    'etiqueta' => 'PUESTO 2 - MAÑANA',
                    'icono' => 'heroicon-m-sun',
                    'color_badge' => 'bg-sky-50 text-sky-700 border-sky-300',
                ],
                [
                    'puesto' => 'Puesto 2',
                    'turno' => 'T',
                    'etiqueta' => 'PUESTO 2 - TARDE',
                    'icono' => 'heroicon-m-clock',
                    'color_badge' => 'bg-amber-50 text-amber-700 border-amber-300',
                ],
                [
                    'puesto' => 'Noche',
                    'turno' => 'N',
                    'etiqueta' => 'TURNO NOCHE',
                    'icono' => 'heroicon-m-moon',
                    'color_badge' => 'bg-purple-50 text-purple-700 border-purple-300',
                ],
            ];
        }

        // Estaciones Diurnas (1, 3, 4): Solo Mañana y Tarde
        return [
            [
                'puesto' => 'Puesto Único',
                'turno' => 'M',
                'etiqueta' => 'MAÑANA',
                'icono' => 'heroicon-m-sun',
                'color_badge' => 'bg-sky-50 text-sky-700 border-sky-300',
            ],
            [
                'puesto' => 'Puesto Único',
                'turno' => 'T',
                'etiqueta' => 'TARDE',
                'icono' => 'heroicon-m-clock',
                'color_badge' => 'bg-amber-50 text-amber-700 border-amber-300',
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

