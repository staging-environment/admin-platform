<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\EmpleadoVacacion;
use App\Models\EmpleadoAusencia;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Livewire\WithPagination;

class Aprobaciones extends Page
{
    use WithPagination;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Aprobación de Solicitudes';
    protected static ?string $title = 'Aprobación de Solicitudes';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.aprobaciones';

    // Filters for Pendientes
    public $filter_pendiente_empleado = '';
    public $filter_pendiente_tipo = '';
    public $filter_pendiente_mes = '';
    public $filter_pendiente_anio = '';

    // Filters for Histórico
    public $filter_historico_empleado = '';
    public $filter_historico_tipo = '';
    public $filter_historico_estado = '';
    public $filter_historico_mes = '';
    public $filter_historico_anio = '';

    public $comentariosVacaciones = [];
    public $comentariosBajas = [];

    public $selectedDocUrl = null;
    public $selectedDocType = null;

    public $denyingType = null;
    public $denyingId = null;
    public $motivoDenegacion = '';

    public $approvingId = null;
    public $approvingVacacion = null;

    public $viewingRecord = null;
    public $viewingType = null;

    // Calendario Modal
    public bool $showCalendarioModal = false;
    public int $calendarioAnio = 2026;
    public int $calendarioMes = 9;
    public string $calendarioEmpleado = '';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->can('aprobacion_vacaciones_bajas')
            || $user->can('gestion_recursos_humanos')
            || $user->email === 'jarodriguezbonilla@gmail.com'
            || $user->id === 1;
    }

    public function updatedFilterHistoricoEmpleado() { $this->resetPage('historicoPage'); }
    public function updatedFilterHistoricoTipo() { $this->resetPage('historicoPage'); }
    public function updatedFilterHistoricoEstado() { $this->resetPage('historicoPage'); }
    public function updatedFilterHistoricoMes() { $this->resetPage('historicoPage'); }
    public function updatedFilterHistoricoAnio() { $this->resetPage('historicoPage'); }

    public function resetPendienteFilters(): void
    {
        $this->filter_pendiente_empleado = '';
        $this->filter_pendiente_tipo = '';
        $this->filter_pendiente_mes = '';
        $this->filter_pendiente_anio = '';
    }

    public function resetHistoricoFilters(): void
    {
        $this->filter_historico_empleado = '';
        $this->filter_historico_tipo = '';
        $this->filter_historico_estado = '';
        $this->filter_historico_mes = '';
        $this->filter_historico_anio = '';
        $this->resetPage('historicoPage');
    }

    public function getVacacionesPendientesProperty()
    {
        $query = EmpleadoVacacion::with('empleado')
            ->where('estado', 'Pendiente');

        if ($this->filter_pendiente_empleado) {
            $search = trim($this->filter_pendiente_empleado);
            if (is_numeric($search)) {
                $query->where('empleado_id', $search);
            } else {
                $query->whereHas('empleado', function($q) use ($search) {
                    $q->whereRaw("CONCAT(nombre, ' ', apellidos) LIKE ?", ["%{$search}%"]);
                });
            }
        }
        if ($this->filter_pendiente_tipo) {
            $query->where('tipo', $this->filter_pendiente_tipo);
        }
        if ($this->filter_pendiente_mes) {
            $query->whereMonth('fecha_inicio', $this->filter_pendiente_mes);
        }
        if ($this->filter_pendiente_anio) {
            $query->whereYear('fecha_inicio', $this->filter_pendiente_anio);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getHistoricoProcesadasProperty()
    {
        $query = EmpleadoVacacion::with('empleado')
            ->whereIn('estado', ['Aceptada', 'Rechazada']);

        if ($this->filter_historico_empleado) {
            $search = trim($this->filter_historico_empleado);
            if (is_numeric($search)) {
                $query->where('empleado_id', $search);
            } else {
                $query->whereHas('empleado', function($q) use ($search) {
                    $q->whereRaw("CONCAT(nombre, ' ', apellidos) LIKE ?", ["%{$search}%"]);
                });
            }
        }
        if ($this->filter_historico_tipo) {
            $query->where('tipo', $this->filter_historico_tipo);
        }
        if ($this->filter_historico_estado) {
            $query->where('estado', $this->filter_historico_estado);
        }
        if ($this->filter_historico_mes) {
            $query->whereMonth('fecha_inicio', $this->filter_historico_mes);
        }
        if ($this->filter_historico_anio) {
            $query->whereYear('fecha_inicio', $this->filter_historico_anio);
        }

        return $query->orderBy('updated_at', 'desc')->paginate(25, ['*'], 'historicoPage');
    }

    public function getEmpleadosProperty()
    {
        return \App\Models\Empleado::orderBy('nombre')->get(['id', 'nombre', 'apellidos']);
    }

    public function getTiposProperty()
    {
        return ['Vacaciones', 'Permiso Retribuido'];
    }

    public function mount(): void
    {
        $this->loadPendientes();
    }

    public function loadPendientes(): void
    {
        // Clear comment inputs and modal state
        $this->comentariosVacaciones = [];
        $this->comentariosBajas = [];
        $this->denyingType = null;
        $this->denyingId = null;
        $this->motivoDenegacion = '';
        $this->approvingId = null;
        $this->approvingVacacion = null;
        $this->viewingRecord = null;
        $this->viewingType = null;
    }


    private function checkPermission(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $user->load('roles', 'permissions');
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1 || $user->can('aprobacion_vacaciones_bajas') || $user->can('gestion_recursos_humanos')) {
            return true;
        }

        Notification::make()
            ->title('Acceso Denegado')
            ->body('No tienes permisos para realizar esta acción.')
            ->danger()
            ->send();

        return false;
    }

    public function iniciarAprobacion($id): void
    {
        if (!$this->checkPermission()) return;
        $this->approvingId = $id;
        $this->approvingVacacion = EmpleadoVacacion::with('empleado')->find($id);
    }

    public function cancelarAprobacion(): void
    {
        $this->approvingId = null;
        $this->approvingVacacion = null;
    }

    public function confirmarAprobacion(): void
    {
        if ($this->approvingId) {
            $id = $this->approvingId;
            $this->cancelarAprobacion();
            $this->aprobarVacacion($id);
        }
    }

    public function aprobarVacacion($id): void
    {
        if (!$this->checkPermission()) return;

        $vac = EmpleadoVacacion::find($id);
        if ($vac) {
            $vac->update([
                'estado' => 'Aceptada',
                'comentario_aprobador' => null,
            ]);
            
            // Database notification for employee
            if ($vac->empleado && $vac->empleado->email) {
                $user = User::where('email', $vac->empleado->email)->first();
                if ($user) {
                    Notification::make()
                        ->title("Solicitud de Vacaciones Aceptada")
                        ->body("Tu solicitud de vacaciones del " . Carbon::parse($vac->fecha_inicio)->format('d/m/Y') . " al " . Carbon::parse($vac->fecha_fin)->format('d/m/Y') . " ha sido aceptada.")
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->sendToDatabase($user);
                }
            }

            // Email for the approver (actor)
            $actor = auth()->user();
            if ($actor && $actor->email && $vac->empleado) {
                try {
                    \Illuminate\Support\Facades\Mail::to($actor->email)->send(new \App\Mail\SolicitudEstadoMail(
                        $vac->empleado->nombre,
                        $vac->tipo,
                        Carbon::parse($vac->fecha_inicio)->format('d/m/Y'),
                        Carbon::parse($vac->fecha_fin)->format('d/m/Y'),
                        'Aceptada',
                        null
                    ));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending vacation approval confirmation email to actor: " . $e->getMessage());
                }
            }

            Notification::make()
                ->title('Vacaciones Aprobadas')
                ->success()
                ->send();
            $this->loadPendientes();
        }
    }

    public function iniciarDenegacion($id, $type): void
    {
        if (!$this->checkPermission()) return;
        $this->denyingId = $id;
        $this->denyingType = $type;
        $this->motivoDenegacion = '';
    }

    public function cancelarDenegacion(): void
    {
        $this->denyingId = null;
        $this->denyingType = null;
        $this->motivoDenegacion = '';
    }

    public function confirmarDenegacion(): void
    {
        if (!$this->checkPermission()) return;
        if (!$this->denyingId || !$this->denyingType) return;

        if ($this->denyingType === 'vacacion') {
            $vac = EmpleadoVacacion::find($this->denyingId);
            if ($vac) {
                $vac->update([
                    'estado' => 'Rechazada',
                    'comentario_aprobador' => $this->motivoDenegacion
                ]);

                if ($vac->empleado && $vac->empleado->email) {
                    $user = User::where('email', $vac->empleado->email)->first();
                    if ($user) {
                        Notification::make()
                            ->title("Solicitud de Vacaciones Rechazada")
                            ->body("Tu solicitud de vacaciones del " . Carbon::parse($vac->fecha_inicio)->format('d/m/Y') . " al " . Carbon::parse($vac->fecha_fin)->format('d/m/Y') . " ha sido rechazada. Motivo: {$this->motivoDenegacion}")
                            ->icon('heroicon-o-x-circle')
                            ->iconColor('danger')
                            ->sendToDatabase($user);
                    }
                }

                $actor = auth()->user();
                if ($actor && $actor->email && $vac->empleado) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($actor->email)->send(new \App\Mail\SolicitudEstadoMail(
                            $vac->empleado->nombre,
                            $vac->tipo,
                            Carbon::parse($vac->fecha_inicio)->format('d/m/Y'),
                            Carbon::parse($vac->fecha_fin)->format('d/m/Y'),
                            'Rechazada',
                            $this->motivoDenegacion
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Error sending vacation rejection email: " . $e->getMessage());
                    }
                }

                Notification::make()->title('Vacaciones Denegadas')->success()->send();
            }
        } else {
            $baja = EmpleadoAusencia::find($this->denyingId);
            if ($baja) {
                $baja->update([
                    'estado' => 'Rechazada',
                    'comentario_aprobador' => $this->motivoDenegacion
                ]);

                if ($baja->empleado && $baja->empleado->email) {
                    $user = User::where('email', $baja->empleado->email)->first();
                    if ($user) {
                        Notification::make()
                            ->title("Solicitud de Baja Médica Rechazada")
                            ->body("Tu solicitud de baja médica iniciada el " . Carbon::parse($baja->fecha_inicio)->format('d/m/Y') . " ha sido rechazada. Motivo: {$this->motivoDenegacion}")
                            ->icon('heroicon-o-x-circle')
                            ->iconColor('danger')
                            ->sendToDatabase($user);
                    }
                }

                $actor = auth()->user();
                if ($actor && $actor->email && $baja->empleado) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($actor->email)->send(new \App\Mail\SolicitudEstadoMail(
                            $baja->empleado->nombre,
                            'Bajas médicas',
                            Carbon::parse($baja->fecha_inicio)->format('d/m/Y'),
                            $baja->fecha_fin ? Carbon::parse($baja->fecha_fin)->format('d/m/Y') : null,
                            'Rechazada',
                            $this->motivoDenegacion
                        ));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Error sending absence rejection email: " . $e->getMessage());
                    }
                }

                Notification::make()->title('Baja Médica Denegada')->success()->send();
            }
        }

        $this->cancelarDenegacion();
        $this->loadPendientes();
    }

    public function aprobarBaja($id): void
    {
        if (!$this->checkPermission()) return;

        $baja = EmpleadoAusencia::find($id);
        if ($baja) {
            $comentario = $this->comentariosBajas[$id] ?? null;

            $baja->update([
                'estado' => 'Aceptada',
                'comentario_aprobador' => $comentario
            ]);

            if ($baja->empleado && $baja->empleado->email) {
                $user = User::where('email', $baja->empleado->email)->first();
                if ($user) {
                    Notification::make()
                        ->title("Solicitud de Baja Médica Aceptada")
                        ->body("Tu solicitud de baja médica iniciada el " . Carbon::parse($baja->fecha_inicio)->format('d/m/Y') . " ha sido aceptada.")
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->sendToDatabase($user);
                }
            }

            // Email for the approver (actor)
            $actor = auth()->user();
            if ($actor && $actor->email && $baja->empleado) {
                try {
                    \Illuminate\Support\Facades\Mail::to($actor->email)->send(new \App\Mail\SolicitudEstadoMail(
                        $baja->empleado->nombre,
                        'Bajas médicas',
                        Carbon::parse($baja->fecha_inicio)->format('d/m/Y'),
                        $baja->fecha_fin ? Carbon::parse($baja->fecha_fin)->format('d/m/Y') : null,
                        'Aceptada',
                        $comentario
                    ));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Error sending absence approval confirmation email to actor: " . $e->getMessage());
                }
            }

            Notification::make()
                ->title('Baja Médica Aprobada')
                ->success()
                ->send();
            $this->loadPendientes();
        }
    }


    public function showDocument($path): void
    {
        $this->selectedDocUrl = route('admin.recursos_humanos.ver_archivo', ['path' => $path]);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $this->selectedDocType = 'pdf';
        } else {
            $this->selectedDocType = 'image';
        }
    }

    public function closeDocument(): void
    {
        $this->selectedDocUrl = null;
        $this->selectedDocType = null;
    }

    public function verDetalles($id, $type): void
    {
        if (!$this->checkPermission()) return;
        $this->viewingType = $type;
        if ($type === 'vacacion') {
            $this->viewingRecord = EmpleadoVacacion::with('empleado')->find($id);
        } else {
            $this->viewingRecord = EmpleadoAusencia::with('empleado')->find($id);
        }
    }

    public function cerrarDetalles(): void
    {
        $this->viewingRecord = null;
        $this->viewingType = null;
    }

    public function openCalendarioAnual(): void
    {
        if (empty($this->calendarioAnio)) {
            $this->calendarioAnio = (int) date('Y');
        }
        if (empty($this->calendarioMes)) {
            $this->calendarioMes = (int) date('n');
        }
        $this->showCalendarioModal = true;
    }

    public function closeCalendarioAnual(): void
    {
        $this->showCalendarioModal = false;
    }

    public function mesAnterior(): void
    {
        if ($this->calendarioMes === 1) {
            $this->calendarioMes = 12;
            $this->calendarioAnio--;
        } else {
            $this->calendarioMes--;
        }
    }

    public function mesSiguiente(): void
    {
        if ($this->calendarioMes === 12) {
            $this->calendarioMes = 1;
            $this->calendarioAnio++;
        } else {
            $this->calendarioMes++;
        }
    }

    public function irHoy(): void
    {
        $this->calendarioMes = (int) date('n');
        $this->calendarioAnio = (int) date('Y');
    }

    public function getCalendarioProperty(): array
    {
        $year = (int) ($this->calendarioAnio ?: date('Y'));
        $month = (int) ($this->calendarioMes ?: date('n'));

        $inicioMes = Carbon::create($year, $month, 1);
        $diasEnMes = $inicioMes->daysInMonth;
        $primerDiaSemana = $inicioMes->dayOfWeekIso; // 1 (Lun) a 7 (Dom)
        $diasPrevios = $primerDiaSemana - 1;

        $startDate = $inicioMes->copy()->subDays($diasPrevios);
        
        // Verificamos si caben en 35 días (5 semanas) o 42 días (6 semanas)
        $day35Date = $startDate->copy()->addDays(34);
        $totalDays = ($day35Date->month == $month && $day35Date->day < $diasEnMes) ? 42 : 35;
        
        $endDate = $startDate->copy()->addDays($totalDays - 1);

        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');

        $query = EmpleadoVacacion::with('empleado')
            ->where(function ($q) use ($startDateStr, $endDateStr) {
                $q->whereBetween('fecha_inicio', [$startDateStr, $endDateStr])
                  ->orWhereBetween('fecha_fin', [$startDateStr, $endDateStr])
                  ->orWhere(function ($q2) use ($startDateStr, $endDateStr) {
                      $q2->where('fecha_inicio', '<=', $startDateStr)
                         ->where('fecha_fin', '>=', $endDateStr);
                  });
            });

        if (!empty($this->calendarioEmpleado)) {
            $search = trim($this->calendarioEmpleado);
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }

        $vacaciones = $query->orderBy('fecha_inicio', 'asc')->get();

        $mesesNombres = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];

        $todayStr = date('Y-m-d');
        $days = [];

        for ($i = 0; $i < $totalDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dateStr = $currentDate->format('Y-m-d');
            $isCurrentMonth = ($currentDate->month === $month);
            $isToday = ($dateStr === $todayStr);
            $isWeekend = ($currentDate->dayOfWeekIso >= 6);

            $dayVacations = [];
            foreach ($vacaciones as $v) {
                $fin = $v->fecha_fin ?: $v->fecha_inicio;
                if ($v->fecha_inicio <= $dateStr && $fin >= $dateStr) {
                    $rawEstado = strtolower(trim($v->estado ?? ''));
                    if (in_array($rawEstado, ['aceptada', 'aprobada', 'aprobado', 'aceptado'])) {
                        $estado = 'Aprobada';
                    } elseif (in_array($rawEstado, ['rechazada', 'denegada', 'rechazado', 'denegado', 'cancelada'])) {
                        $estado = 'Denegada';
                    } else {
                        $estado = 'Pendiente';
                    }

                    $nombre = $v->empleado ? $v->empleado->nombre : '';
                    $apellidos = $v->empleado ? $v->empleado->apellidos : '';
                    $empName = trim($nombre . ' ' . $apellidos) ?: 'Empleado';
                    $iniciales = self::extractIniciales($nombre, $apellidos);

                    $inicioCarbon = Carbon::parse($v->fecha_inicio);
                    $finCarbon = Carbon::parse($v->fecha_fin ?: $v->fecha_inicio);
                    $fechasStr = $inicioCarbon->format('d/m') . ' - ' . $finCarbon->format('d/m');
                    $fechasCompletas = $inicioCarbon->format('d/m/Y') . ' al ' . $finCarbon->format('d/m/Y');
                    $diasCalculados = $v->dias ?: ($inicioCarbon->diffInDays($finCarbon) + 1);

                    $dayVacations[] = [
                        'id' => $v->id,
                        'empleado' => $empName,
                        'iniciales' => $iniciales,
                        'estado' => $estado,
                        'fechas' => $fechasStr,
                        'fechas_completas' => $fechasCompletas,
                        'dias' => $diasCalculados,
                    ];
                }
            }

            $days[] = [
                'date' => $dateStr,
                'day' => $currentDate->day,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $isToday,
                'isWeekend' => $isWeekend,
                'solicitudes' => $dayVacations,
            ];
        }

        return [
            'mesNombre' => $mesesNombres[$month],
            'mesNumero' => $month,
            'anio' => $year,
            'totalDias' => $totalDays,
            'days' => $days,
        ];
    }

    public static function extractIniciales(?string $nombre, ?string $apellidos = null): string
    {
        $fullName = trim(($nombre ?? '') . ' ' . ($apellidos ?? ''));
        if (empty($fullName)) {
            return 'EMP';
        }
        $words = preg_split('/\s+/', $fullName);
        $initials = '';
        foreach ($words as $w) {
            if ($w !== '') {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1));
            }
        }
        return mb_substr($initials, 0, 4) ?: 'EMP';
    }
}
