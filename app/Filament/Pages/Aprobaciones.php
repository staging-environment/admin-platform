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

    // Calendario Anual Modal
    public bool $showCalendarioModal = false;
    public int $calendarioAnio = 2026;
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
        $this->showCalendarioModal = true;
    }

    public function closeCalendarioAnual(): void
    {
        $this->showCalendarioModal = false;
    }

    public function cambiarAnioCalendario(int $delta): void
    {
        $this->calendarioAnio += $delta;
    }

    public function getCalendarioAnualProperty(): array
    {
        $year = (int) ($this->calendarioAnio ?: date('Y'));
        $yearStart = "$year-01-01";
        $yearEnd = "$year-12-31";

        $query = EmpleadoVacacion::with('empleado')
            ->where(function ($q) use ($yearStart, $yearEnd) {
                $q->whereBetween('fecha_inicio', [$yearStart, $yearEnd])
                  ->orWhereBetween('fecha_fin', [$yearStart, $yearEnd])
                  ->orWhere(function ($q2) use ($yearStart, $yearEnd) {
                      $q2->where('fecha_inicio', '<=', $yearStart)
                         ->where('fecha_fin', '>=', $yearEnd);
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
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $meses = [];

        for ($m = 1; $m <= 12; $m++) {
            $startOfMonth = Carbon::create($year, $m, 1);
            $daysInMonth = $startOfMonth->daysInMonth;

            $monthStartStr = sprintf('%04d-%02d-01', $year, $m);
            $monthEndStr = sprintf('%04d-%02d-%02d', $year, $m, $daysInMonth);

            $monthVacations = $vacaciones->filter(function ($v) use ($monthStartStr, $monthEndStr) {
                $fin = $v->fecha_fin ?: $v->fecha_inicio;
                return $v->fecha_inicio <= $monthEndStr && $fin >= $monthStartStr;
            });

            $aprobadasCount = 0;
            $pendientesCount = 0;
            $denegadasCount = 0;
            $solicitudes = [];

            foreach ($monthVacations as $v) {
                $estado = in_array($v->estado, ['Aceptada', 'Aprobada']) ? 'Aprobada' : (in_array($v->estado, ['Rechazada', 'Denegada']) ? 'Denegada' : 'Pendiente');
                if ($estado === 'Aprobada') $aprobadasCount++;
                elseif ($estado === 'Pendiente') $pendientesCount++;
                else $denegadasCount++;

                $empName = $v->empleado ? ($v->empleado->nombre . ' ' . $v->empleado->apellidos) : 'Empleado';
                
                $inicioCarbon = Carbon::parse($v->fecha_inicio);
                $finCarbon = Carbon::parse($v->fecha_fin ?: $v->fecha_inicio);
                $fechasStr = $inicioCarbon->format('d/m/Y') . ' - ' . $finCarbon->format('d/m/Y');
                $diasCalculados = $v->dias ?: ($inicioCarbon->diffInDays($finCarbon) + 1);

                $solicitudes[] = [
                    'id' => $v->id,
                    'empleado' => $empName,
                    'fechas' => $fechasStr,
                    'dias' => $diasCalculados,
                    'estado' => $estado,
                    'tipo' => $v->tipo ?: 'Vacaciones',
                ];
            }

            $meses[$m] = [
                'numero' => $m,
                'nombre' => $mesesNombres[$m],
                'aprobadas' => $aprobadasCount,
                'pendientes' => $pendientesCount,
                'denegadas' => $denegadasCount,
                'total' => count($monthVacations),
                'solicitudes' => $solicitudes,
            ];
        }

        return $meses;
    }
}
