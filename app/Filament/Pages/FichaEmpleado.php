<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Empleado;
use App\Models\EmpleadoFichaje;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;

class FichaEmpleado extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Ficha de Empleado';
    protected static ?string $title = 'Portal de Empleado';

    protected string $view = 'filament.pages.ficha-empleado';

    public $empleado;
    public $fichajeDelDia;
    public $recentFichajes;

    public $isViewingAdminList = false;
    public $todosLosFichajes = [];
    public $todasLasVacaciones = [];
    public $todasLasBajas = [];
    public $filterDate = '';
    public $filterSearch = '';
    public $filterType = 'fichajes'; // 'fichajes', 'vacaciones', 'bajas'

    public $hora_entrada;
    public $hora_salida;

    public $editingFichajeId = null;
    public $editingFecha = null;
    public $editingHoraEntrada = null;
    public $editingHoraSalida = null;
    public $deletingFichajeId = null;

    public $vacaciones = [];
    public $ausencias = [];
    public $selectedSolicitud = null;
    public $selectedSolicitudType = null;
    public $missingCheckInDays = [];

    // Form fields for vacations
    public $vacacion_fecha_inicio;
    public $vacacion_fecha_fin;
    public $vacacion_tipo = 'Vacaciones';

    // Form fields for sick leaves
    public $baja_fecha_inicio;
    public $baja_fecha_fin;
    public $baja_justificante;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $user->load('roles', 'permissions');
        if ($user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1) return true;
        
        return $user->hasRole('Admin') || $user->can('acceder_ficha_empleado');
    }

    public function mount(): void
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin') || $user->hasRole('Gestor') || $user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1;

        $empleadoId = request()->query('empleado_id');

        if ($isAdmin && !$empleadoId) {
            $this->isViewingAdminList = true;
            $this->empleado = null;
        } else {
            $this->isViewingAdminList = false;
            if ($isAdmin && $empleadoId) {
                $this->empleado = Empleado::find($empleadoId);
            } else {
                $this->empleado = Empleado::where('email', $user->email)->first();
            }
        }

        // Auto-create mock employee for admin users who also have the Empleado role (or are testing)
        if (!$this->empleado && !$this->isViewingAdminList && ($user->hasRole('Admin') || $user->hasRole('admin'))) {
            $this->empleado = Empleado::create([
                'nombre' => $user->name ?: 'jarodriguezbonilla',
                'apellidos' => '(Admin)',
                'dni' => 'ADMIN-' . substr(md5($user->email), 0, 8),
                'fecha_nacimiento' => '1990-01-01',
                'direccion' => 'Calle Administrador',
                'localidad' => 'Sevilla',
                'codigo_postal' => '41000',
                'provincia' => 'Sevilla',
                'telefono_principal' => $user->telefono ?: '600000000',
                'email' => $user->email,
            ]);
        }
        
        // Initialize default input times to current server time
        $this->hora_entrada = Carbon::now()->format('H:i');
        $this->hora_salida = Carbon::now()->format('H:i');

        $this->loadFichajes();
        if ($this->empleado) {
            $this->loadVacaciones();
            $this->loadAusencias();
        }
    }

    public function loadFichajes(): void
    {
        if ($this->isViewingAdminList) {
            $search = $this->filterSearch ? '%' . $this->filterSearch . '%' : null;

            if ($this->filterType === 'fichajes') {
                $query = EmpleadoFichaje::with('empleado');

                if ($this->filterDate) {
                    $query->where('fecha', $this->filterDate);
                }

                if ($search) {
                    $query->whereHas('empleado', function($q) use ($search) {
                        $q->where('nombre', 'like', $search)
                          ->orWhere('apellidos', 'like', $search)
                          ->orWhere('email', 'like', $search);
                    });
                }

                $this->todosLosFichajes = $query
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora_entrada', 'desc')
                    ->get();
                $this->todasLasVacaciones = [];
                $this->todasLasBajas = [];
            } elseif ($this->filterType === 'vacaciones' || $this->filterType === 'vacaciones_pendientes') {
                $estado = $this->filterType === 'vacaciones' ? 'Aceptada' : 'Pendiente';
                $query = \App\Models\EmpleadoVacacion::with('empleado')->where('estado', $estado);

                if ($this->filterDate) {
                    $query->where('fecha_inicio', '<=', $this->filterDate)
                          ->where('fecha_fin', '>=', $this->filterDate);
                }

                if ($search) {
                    $query->whereHas('empleado', function($q) use ($search) {
                        $q->where('nombre', 'like', $search)
                          ->orWhere('apellidos', 'like', $search)
                          ->orWhere('email', 'like', $search);
                    });
                }

                $this->todasLasVacaciones = $query
                    ->orderBy('fecha_inicio', 'desc')
                    ->get();
                $this->todosLosFichajes = [];
                $this->todasLasBajas = [];
            } elseif ($this->filterType === 'bajas' || $this->filterType === 'bajas_pendientes') {
                $estado = $this->filterType === 'bajas' ? 'Aceptada' : 'Pendiente';
                $query = \App\Models\EmpleadoAusencia::with('empleado')->where('estado', $estado);

                if ($this->filterDate) {
                    $query->where('fecha_inicio', '<=', $this->filterDate)
                          ->where(function($q) {
                              $q->where('fecha_fin', '>=', $this->filterDate)
                                ->orWhereNull('fecha_fin');
                          });
                }

                if ($search) {
                    $query->whereHas('empleado', function($q) use ($search) {
                        $q->where('nombre', 'like', $search)
                          ->orWhere('apellidos', 'like', $search)
                          ->orWhere('email', 'like', $search);
                    });
                }

                $this->todasLasBajas = $query
                    ->orderBy('fecha_inicio', 'desc')
                    ->get();
                $this->todosLosFichajes = [];
                $this->todasLasVacaciones = [];
            }
            $this->fichajeDelDia = null;
            $this->recentFichajes = collect();
            return;
        }

        if (!$this->empleado) {
            $this->fichajeDelDia = null;
            $this->recentFichajes = collect();
            return;
        }

        $this->fichajeDelDia = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', Carbon::today()->format('Y-m-d'))
            ->first();

        $this->recentFichajes = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_entrada', 'desc')
            ->limit(30)
            ->get();

        $this->checkMissingCheckIns();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->loadFichajes();
        return parent::render();
    }

    public function checkIn($latitude = null, $longitude = null): void
    {
        if (!$this->empleado) {
            Notification::make()
                ->title('Error')
                ->body('Tu usuario no está asociado a ningún registro de empleado.')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'hora_entrada' => 'required|date_format:H:i',
        ]);

        $today = Carbon::today()->format('Y-m-d');

        EmpleadoFichaje::updateOrCreate(
            [
                'empleado_id' => $this->empleado->id,
                'fecha' => $today,
            ],
            [
                'hora_entrada' => $this->hora_entrada,
                'server_checkin_at' => Carbon::now(),
                'checkin_latitude' => $latitude,
                'checkin_longitude' => $longitude,
            ]
        );

        Notification::make()
            ->title('Check-in Registrado')
            ->body('Has registrado tu entrada a las ' . $this->hora_entrada . ' (Hora del servidor: ' . Carbon::now()->format('H:i:s') . ').')
            ->success()
            ->send();

        $this->loadFichajes();
    }

    public function checkOut($latitude = null, $longitude = null): void
    {
        if (!$this->empleado) {
            Notification::make()
                ->title('Error')
                ->body('Tu usuario no está asociado a ningún registro de empleado.')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'hora_salida' => 'required|date_format:H:i',
        ]);

        $today = Carbon::today()->format('Y-m-d');

        $fichaje = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', $today)
            ->first();

        if (!$fichaje) {
            Notification::make()
                ->title('Error')
                ->body('No puedes registrar salida sin haber registrado entrada primero.')
                ->danger()
                ->send();
            return;
        }

        $fichaje->update([
            'hora_salida' => $this->hora_salida,
            'server_checkout_at' => Carbon::now(),
            'checkout_latitude' => $latitude,
            'checkout_longitude' => $longitude,
        ]);

        Notification::make()
            ->title('Check-out Registrado')
            ->body('Has registrado tu salida a las ' . $this->hora_salida . ' (Hora del servidor: ' . Carbon::now()->format('H:i:s') . ').')
            ->success()
            ->send();

        $this->loadFichajes();
    }

    public function editFichaje($id): void
    {
        $fichaje = EmpleadoFichaje::find($id);
        if (!$fichaje || $fichaje->empleado_id !== $this->empleado->id) {
            return;
        }

        $this->editingFichajeId = $id;
        $this->editingFecha = $fichaje->fecha;
        $this->editingHoraEntrada = $fichaje->hora_entrada ? Carbon::parse($fichaje->hora_entrada)->format('H:i') : null;
        $this->editingHoraSalida = $fichaje->hora_salida ? Carbon::parse($fichaje->hora_salida)->format('H:i') : null;

        $this->dispatch('open-modal', id: 'edit-fichaje-modal');
    }

    public function updateFichaje(): void
    {
        $this->validate([
            'editingHoraEntrada' => 'nullable',
            'editingHoraSalida' => 'nullable',
        ]);

        $fichaje = EmpleadoFichaje::find($this->editingFichajeId);
        if (!$fichaje || $fichaje->empleado_id !== $this->empleado->id) {
            return;
        }

        $fichaje->update([
            'hora_entrada' => $this->editingHoraEntrada ?: null,
            'hora_salida' => $this->editingHoraSalida ?: null,
        ]);

        Notification::make()
            ->title('Fichaje Actualizado')
            ->body('El registro del día ' . Carbon::parse($fichaje->fecha)->format('d/m/Y') . ' ha sido modificado con éxito.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'edit-fichaje-modal');
        $this->loadFichajes();
    }

    public function confirmDeleteFichaje(): void
    {
        if (!$this->deletingFichajeId) {
            return;
        }

        $fichaje = EmpleadoFichaje::find($this->deletingFichajeId);
        if ($fichaje && $fichaje->empleado_id === $this->empleado->id) {
            $fechaFormatted = Carbon::parse($fichaje->fecha)->format('d/m/Y');
            $fichaje->delete();

            Notification::make()
                ->title('Fichaje Eliminado')
                ->body('El registro del día ' . $fechaFormatted . ' ha sido eliminado con éxito.')
                ->success()
                ->send();
        }

        $this->deletingFichajeId = null;
        $this->dispatch('close-modal', id: 'delete-fichaje-modal');
        $this->loadFichajes();
    }

    public function loadVacaciones(): void
    {
        if ($this->empleado) {
            $this->vacaciones = \App\Models\EmpleadoVacacion::where('empleado_id', $this->empleado->id)
                ->orderBy('fecha_inicio', 'desc')
                ->get();
        }
    }

    public function loadAusencias(): void
    {
        if ($this->empleado) {
            $this->ausencias = \App\Models\EmpleadoAusencia::where('empleado_id', $this->empleado->id)
                ->orderBy('fecha_inicio', 'desc')
                ->get();
        }
    }

    public function solicitarVacacion(): void
    {
        $this->validate([
            'vacacion_fecha_inicio' => 'required|date|after_or_equal:today',
            'vacacion_fecha_fin' => 'required|date|after_or_equal:vacacion_fecha_inicio',
            'vacacion_tipo' => 'required|in:Vacaciones,Permisos',
        ], [
            'vacacion_fecha_inicio.required' => 'La fecha de inicio es requerida.',
            'vacacion_fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'vacacion_fecha_fin.required' => 'La fecha de fin es requerida.',
            'vacacion_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
        ]);

        $inicio = Carbon::parse($this->vacacion_fecha_inicio);
        $fin = Carbon::parse($this->vacacion_fecha_fin);
        $dias = $inicio->diffInDays($fin) + 1;

        \App\Models\EmpleadoVacacion::create([
            'empleado_id' => $this->empleado->id,
            'tipo' => $this->vacacion_tipo,
            'fecha_inicio' => $this->vacacion_fecha_inicio,
            'fecha_fin' => $this->vacacion_fecha_fin,
            'dias_solicitados' => $dias,
            'estado' => 'Pendiente',
        ]);

        $this->vacacion_fecha_inicio = null;
        $this->vacacion_fecha_fin = null;
        $this->vacacion_tipo = 'Vacaciones';

        Notification::make()
            ->title('Solicitud de Vacaciones Enviada')
            ->body('Tu solicitud ha sido registrada correctamente en estado Pendiente.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'solicitar-vacacion-modal');
        $this->loadVacaciones();
    }

    public function solicitarBaja(): void
    {
        $this->validate([
            'baja_fecha_inicio' => 'required|date',
            'baja_fecha_fin' => 'nullable|date|after_or_equal:baja_fecha_inicio',
            'baja_justificante' => 'required|file|max:10240',
        ], [
            'baja_fecha_inicio.required' => 'La fecha de inicio de la baja es requerida.',
            'baja_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
            'baja_justificante.required' => 'Es obligatorio subir un justificante médico.',
            'baja_justificante.file' => 'El justificante debe ser un archivo.',
            'baja_justificante.max' => 'El tamaño máximo del archivo es 10MB.',
        ]);

        $path = $this->baja_justificante->store('empleados/bajas', 'local');

        \App\Models\EmpleadoAusencia::create([
            'empleado_id' => $this->empleado->id,
            'tipo' => 'Bajas médicas',
            'fecha_inicio' => $this->baja_fecha_inicio,
            'fecha_fin' => $this->baja_fecha_fin ?: null,
            'justificante_path' => $path,
        ]);

        $this->baja_fecha_inicio = null;
        $this->baja_fecha_fin = null;
        $this->baja_justificante = null;

        Notification::make()
            ->title('Solicitud de Baja Médica Registrada')
            ->body('Tu baja por enfermedad ha sido registrada correctamente.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'solicitar-baja-modal');
        $this->loadAusencias();
    }

    public function deleteVacacion($id): void
    {
        $vac = \App\Models\EmpleadoVacacion::find($id);
        if ($vac && $vac->empleado_id === $this->empleado->id && $vac->estado === 'Pendiente') {
            $vac->delete();
            Notification::make()
                ->title('Solicitud de Vacaciones Eliminada')
                ->success()
                ->send();
            $this->loadVacaciones();
        }
    }

    public function deleteAusencia($id): void
    {
        $aus = \App\Models\EmpleadoAusencia::find($id);
        if ($aus && $aus->empleado_id === $this->empleado->id && $aus->estado === 'Pendiente') {
            if ($aus->justificante_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($aus->justificante_path)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($aus->justificante_path);
            }
            $aus->delete();
            Notification::make()
                ->title('Solicitud de Baja Médica Eliminada')
                ->success()
                ->send();
            $this->loadAusencias();
        }
    }

    public function verDetallesSolicitud($id, $type): void
    {
        $this->selectedSolicitudType = $type;
        if ($type === 'vacacion') {
            $sol = \App\Models\EmpleadoVacacion::find($id);
        } else {
            $sol = \App\Models\EmpleadoAusencia::find($id);
        }
        
        // Security check: ensure the employee has access to this request or is authorized
        if ($sol && ($sol->empleado_id === $this->empleado->id || auth()->user()->hasRole('Admin') || auth()->user()->can('aprobacion_vacaciones_bajas'))) {
            $this->selectedSolicitud = $sol;
        } else {
            $this->selectedSolicitud = null;
        }
    }

    public function cerrarDetallesSolicitud(): void
    {
        $this->selectedSolicitud = null;
        $this->selectedSolicitudType = null;
    }

    public function checkMissingCheckIns(): void
    {
        if (!$this->empleado) {
            $this->missingCheckInDays = [];
            return;
        }

        $missing = [];
        $start = Carbon::today()->subDays(14);
        $end = Carbon::yesterday();

        for ($date = clone $start; $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $dateStr = $date->format('Y-m-d');

            // 1. Check if check-in exists
            $hasFichaje = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
                ->where('fecha', $dateStr)
                ->exists();

            if ($hasFichaje) {
                continue;
            }

            // 2. Check if approved vacation covers this date
            $hasVacacion = \App\Models\EmpleadoVacacion::where('empleado_id', $this->empleado->id)
                ->where('estado', 'Aceptada')
                ->where('fecha_inicio', '<=', $dateStr)
                ->where('fecha_fin', '>=', $dateStr)
                ->exists();

            if ($hasVacacion) {
                continue;
            }

            // 3. Check if approved absence covers this date
            $hasAusencia = \App\Models\EmpleadoAusencia::where('empleado_id', $this->empleado->id)
                ->where('estado', 'Aceptada')
                ->where('fecha_inicio', '<=', $dateStr)
                ->where(function($q) use ($dateStr) {
                    $q->where('fecha_fin', '>=', $dateStr)
                      ->orWhereNull('fecha_fin');
                })
                ->exists();

            if ($hasAusencia) {
                continue;
            }

            $missing[] = [
                'date' => $dateStr,
                'formatted' => $date->translatedFormat('l, d \d\e F')
            ];
        }

        $this->missingCheckInDays = $missing;
    }
}
