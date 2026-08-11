<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\EmpleadoVacacion;
use App\Models\EmpleadoAusencia;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class Aprobaciones extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Aprobación de Solicitudes';
    protected static ?string $title = 'Aprobación de Vacaciones y Bajas';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.aprobaciones';

    public $vacacionesPendientes = [];
    public $bajasPendientes = [];
    public $historicoProcesadas = [];

    public $comentariosVacaciones = [];
    public $comentariosBajas = [];

    public $selectedDocUrl = null;
    public $selectedDocType = null;

    public $denyingType = null;
    public $denyingId = null;
    public $motivoDenegacion = '';

    public $viewingRecord = null;
    public $viewingType = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $user->load('roles', 'permissions');
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        
        return $user->hasRole('Admin') || $user->can('aprobacion_vacaciones_bajas');
    }

    public function mount(): void
    {
        $this->loadPendientes();
    }

    public function loadPendientes(): void
    {
        $this->vacacionesPendientes = EmpleadoVacacion::with('empleado')
            ->where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->bajasPendientes = [];

        $this->historicoProcesadas = EmpleadoVacacion::with('empleado')
            ->whereIn('estado', ['Aceptada', 'Rechazada'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->all();

        // Clear comment inputs and modal state
        $this->comentariosVacaciones = [];
        $this->comentariosBajas = [];
        $this->denyingType = null;
        $this->denyingId = null;
        $this->motivoDenegacion = '';
        $this->viewingRecord = null;
        $this->viewingType = null;
    }


    private function checkPermission(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $user->load('roles', 'permissions');
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1 || $user->hasRole('Admin') || $user->can('aprobacion_vacaciones_bajas')) {
            return true;
        }

        Notification::make()
            ->title('Acceso Denegado')
            ->body('No tienes permisos para realizar esta acción.')
            ->danger()
            ->send();

        return false;
    }

    public function aprobarVacacion($id): void
    {
        if (!$this->checkPermission()) return;

        $vac = EmpleadoVacacion::find($id);
        if ($vac) {
            $comentario = $this->comentariosVacaciones[$id] ?? null;

            $vac->update([
                'estado' => 'Aceptada',
                'comentario_aprobador' => $comentario
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
                        $comentario
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
}
