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
    public $filterDateFrom = '';
    public $filterDateTo = '';
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

    // Retroactive check-in properties
    public $selectedRetroactiveDate = null;
    public $retroactive_hora_entrada = null;
    public $retroactive_hora_salida = null;
    public $isCreatingNewRetroactive = false;
    public $retroactive_fecha = null;

    // Form fields for vacations
    public $vacacion_fecha_inicio;
    public $vacacion_fecha_fin;
    public $vacacion_tipo = 'Vacaciones';
    public $vacacion_mes;
    public $vacacion_ano;
    public $vacacion_quincena = '1';
    public $permiso_justificante;
    public $permiso_motivo;

    // Form fields for sick leaves
    public $baja_fecha_inicio;
    public $baja_fecha_fin;
    public $baja_justificante;
    public $alta_fecha_fin;
    public $alta_justificante;
    public $activeBajaId;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        return $user->hasRole('Admin') 
            || $user->hasRole('admin') 
            || $user->hasRole('Empleado') 
            || $user->hasRole('empleado') 
            || $user->can('acceder_portal_fichajes')
            || $user->email === 'jarodriguezbonilla@gmail.com' 
            || $user->id === 1;
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

        // Initialize default vacation month, year and fortnight
        $this->vacacion_mes = (int) Carbon::now()->format('m');
        $this->vacacion_ano = (int) Carbon::now()->year;
        $this->vacacion_quincena = '1';

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

                if ($this->filterDateFrom) {
                    $query->where('fecha', '>=', $this->filterDateFrom);
                }
                if ($this->filterDateTo) {
                    $query->where('fecha', '<=', $this->filterDateTo);
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

                if ($this->filterDateFrom) {
                    $query->where('fecha_fin', '>=', $this->filterDateFrom);
                }
                if ($this->filterDateTo) {
                    $query->where('fecha_inicio', '<=', $this->filterDateTo);
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

                if ($this->filterDateFrom) {
                    $query->where(function($q) {
                        $q->where('fecha_fin', '>=', $this->filterDateFrom)
                          ->orWhereNull('fecha_fin');
                    });
                }
                if ($this->filterDateTo) {
                    $query->where('fecha_inicio', '<=', $this->filterDateTo);
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

        // Busca la sesión activa de fichaje sin salida para el día de hoy
        $this->fichajeDelDia = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', Carbon::today()->format('Y-m-d'))
            ->whereNull('hora_salida')
            ->latest('id')
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

        $activeFichaje = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', $today)
            ->whereNull('hora_salida')
            ->latest('id')
            ->first();

        if ($activeFichaje) {
            $activeFichaje->update([
                'hora_entrada' => $this->hora_entrada,
                'server_checkin_at' => Carbon::now(),
                'checkin_latitude' => $latitude,
                'checkin_longitude' => $longitude,
            ]);
        } else {
            EmpleadoFichaje::create([
                'empleado_id' => $this->empleado->id,
                'fecha' => $today,
                'hora_entrada' => $this->hora_entrada,
                'server_checkin_at' => Carbon::now(),
                'checkin_latitude' => $latitude,
                'checkin_longitude' => $longitude,
            ]);
        }

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
            ->whereNull('hora_salida')
            ->latest('id')
            ->first();

        if (!$fichaje) {
            $fichaje = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
                ->where('fecha', $today)
                ->latest('id')
                ->first();
        }

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

        // Store original values if not set yet (first edit)
        $originalHoraEntrada = $fichaje->original_hora_entrada ?: $fichaje->hora_entrada;
        $originalHoraSalida = $fichaje->original_hora_salida ?: $fichaje->hora_salida;

        $fichaje->update([
            'hora_entrada' => $this->editingHoraEntrada ?: null,
            'hora_salida' => $this->editingHoraSalida ?: null,
            'is_edited' => true,
            'original_hora_entrada' => $originalHoraEntrada,
            'original_hora_salida' => $originalHoraSalida,
            'edited_by_email' => auth()->user()->email,
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
        $user = auth()->user();

        if ($fichaje) {
            $fechaFormatted = Carbon::parse($fichaje->fecha)->format('d/m/Y');

            // Registrar trazabilidad de auditoría (quién realizó el borrado)
            $fichaje->update([
                'deleted_by_email' => $user?->email,
                'deleted_by_id' => $user?->id,
            ]);

            // Borrado lógico (Soft Delete)
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
        if ($this->vacacion_tipo === 'Vacaciones') {
            $rules = [
                'vacacion_mes' => 'required|integer|between:1,12',
                'vacacion_ano' => 'required|integer|min:2024',
                'vacacion_quincena' => 'required|in:1,2',
                'vacacion_tipo' => 'required|in:Vacaciones,Permisos',
            ];

            $messages = [
                'vacacion_mes.required' => 'El mes es requerido.',
                'vacacion_ano.required' => 'El año es requerido.',
                'vacacion_quincena.required' => 'La quincena es requerida.',
            ];

            $this->validate($rules, $messages);

            $mesFormatted = sprintf('%02d', $this->vacacion_mes);

            if ($this->vacacion_quincena == '1') {
                $fechaInicioStr = "{$this->vacacion_ano}-{$mesFormatted}-01";
                $fechaFinStr = "{$this->vacacion_ano}-{$mesFormatted}-15";
                $quincenaTexto = "1ª Quincena";
            } else {
                $fechaInicioStr = "{$this->vacacion_ano}-{$mesFormatted}-16";
                $lastDay = Carbon::createFromDate($this->vacacion_ano, (int)$this->vacacion_mes, 1)->endOfMonth()->day;
                $lastDayFormatted = sprintf('%02d', $lastDay);
                $fechaFinStr = "{$this->vacacion_ano}-{$mesFormatted}-{$lastDayFormatted}";
                $quincenaTexto = "2ª Quincena";
            }

            // Validar que no exista ya una solicitud de vacaciones activa o pendiente para esta misma quincena/mes/año
            $existingVacation = \App\Models\EmpleadoVacacion::where('empleado_id', $this->empleado->id)
                ->where('tipo', 'Vacaciones')
                ->whereIn('estado', ['Pendiente', 'Aceptada', 'Aprobada'])
                ->where('fecha_inicio', $fechaInicioStr)
                ->where('fecha_fin', $fechaFinStr)
                ->exists();

            if ($existingVacation) {
                Notification::make()
                    ->title('Solicitud Duplicada')
                    ->body('Ya dispones de una solicitud de vacaciones registrada o pendiente para este mismo período (mes, año y quincena).')
                    ->danger()
                    ->send();
                return;
            }

            $this->vacacion_fecha_inicio = $fechaInicioStr;
            $this->vacacion_fecha_fin = $fechaFinStr;
            $justificantePath = null;
            $comentario = "Solicitud de Vacaciones ({$quincenaTexto})";
        } else {
            $rules = [
                'vacacion_fecha_inicio' => 'required|date|after_or_equal:today',
                'vacacion_fecha_fin' => 'required|date|after_or_equal:vacacion_fecha_inicio',
                'vacacion_tipo' => 'required|in:Vacaciones,Permisos',
                'permiso_motivo' => 'required|string|max:255',
                'permiso_justificante' => 'required|file|max:10240',
            ];

            $messages = [
                'vacacion_fecha_inicio.required' => 'La fecha de inicio es requerida.',
                'vacacion_fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
                'vacacion_fecha_fin.required' => 'La fecha de fin es requerida.',
                'vacacion_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                'permiso_motivo.required' => 'Es obligatorio especificar el motivo del permiso retribuido.',
                'permiso_motivo.max' => 'El motivo no debe superar los 255 caracteres.',
                'permiso_justificante.required' => 'Es obligatorio adjuntar un documento justificante para el permiso retribuido.',
                'permiso_justificante.file' => 'El justificante debe ser un archivo válido.',
                'permiso_justificante.max' => 'El tamaño máximo del archivo es 10MB.',
            ];

            $this->validate($rules, $messages);

            $justificantePath = null;
            if ($this->permiso_justificante) {
                $justificantePath = $this->permiso_justificante->store('empleados/justificantes_permisos', 'local');
            }
            $comentario = $this->permiso_motivo;
        }

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
            'justificante_path' => $justificantePath,
            'comentario_empleado' => $comentario,
        ]);

        $this->vacacion_fecha_inicio = null;
        $this->vacacion_fecha_fin = null;
        $this->vacacion_tipo = 'Vacaciones';
        $this->permiso_justificante = null;
        $this->permiso_motivo = null;

        Notification::make()
            ->title('Solicitud Enviada')
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
            'estado' => 'Aceptada',
        ]);

        $this->baja_fecha_inicio = null;
        $this->baja_fecha_fin = null;
        $this->baja_justificante = null;

        Notification::make()
            ->title('Baja Médica Registrada')
            ->body('Tu baja por enfermedad ha sido registrada correctamente con éxito.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'solicitar-baja-modal');
        $this->loadAusencias();
    }

    public function abrirRegistrarAlta($bajaId): void
    {
        $this->activeBajaId = $bajaId;
        $this->alta_fecha_fin = Carbon::today()->format('Y-m-d');
        $this->dispatch('open-modal', id: 'registrar-alta-modal');
    }

    public function registrarAlta(): void
    {
        $this->validate([
            'alta_fecha_fin' => 'required|date',
            'alta_justificante' => 'required|file|max:10240',
        ], [
            'alta_fecha_fin.required' => 'La fecha de alta es requerida.',
            'alta_justificante.required' => 'Es obligatorio subir el justificante médico de alta.',
            'alta_justificante.file' => 'El justificante de alta debe ser un archivo.',
            'alta_justificante.max' => 'El tamaño máximo del justificante de alta es 10MB.',
        ]);

        $baja = \App\Models\EmpleadoAusencia::find($this->activeBajaId);
        if (!$baja || $baja->empleado_id !== $this->empleado->id) {
            return;
        }

        if (Carbon::parse($this->alta_fecha_fin)->lt(Carbon::parse($baja->fecha_inicio))) {
            Notification::make()
                ->title('Error')
                ->body('La fecha de alta no puede ser anterior a la fecha de inicio de la baja (' . Carbon::parse($baja->fecha_inicio)->format('d/m/Y') . ').')
                ->danger()
                ->send();
            return;
        }

        $path = $this->alta_justificante->store('empleados/altas', 'local');

        $baja->update([
            'fecha_fin' => $this->alta_fecha_fin,
            'justificante_alta_path' => $path,
        ]);

        try {
            $admins = \App\Models\User::all()->filter(function($user) {
                return $user->can('recibir_notificaciones_recursos_humanos') || $user->email === 'jarodriguezbonilla@gmail.com';
            });

            foreach ($admins as $admin) {
                \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\NuevaSolicitudAdminMail(
                    "{$this->empleado->nombre} {$this->empleado->apellidos}",
                    $this->empleado->email,
                    'Bajas médicas (Alta)',
                    Carbon::parse($baja->fecha_inicio)->format('d/m/Y'),
                    Carbon::parse($this->alta_fecha_fin)->format('d/m/Y'),
                    'El empleado ha registrado su alta médica.'
                ));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error sending absence alta email to admins: " . $e->getMessage());
        }

        $this->activeBajaId = null;
        $this->alta_fecha_fin = null;
        $this->alta_justificante = null;

        Notification::make()
            ->title('Alta Médica Registrada')
            ->body('Tu fecha y justificante de alta han sido guardados con éxito.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'registrar-alta-modal');
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

    public function abrirFichajeRetroactivo($date): void
    {
        $this->isCreatingNewRetroactive = false;
        $this->selectedRetroactiveDate = $date;
        $this->retroactive_fecha = $date;
        $existing = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
            ->where('fecha', $date)
            ->first();

        if ($existing) {
            $this->retroactive_hora_entrada = $existing->hora_entrada ? Carbon::parse($existing->hora_entrada)->format('H:i') : null;
            $this->retroactive_hora_salida = $existing->hora_salida ? Carbon::parse($existing->hora_salida)->format('H:i') : null;
        } else {
            $this->retroactive_hora_entrada = null;
            $this->retroactive_hora_salida = null;
        }
    }

    public function abrirFichajeRetroactivoNuevaFecha(): void
    {
        $this->isCreatingNewRetroactive = true;
        $this->selectedRetroactiveDate = Carbon::yesterday()->format('Y-m-d');
        $this->retroactive_fecha = Carbon::yesterday()->format('Y-m-d');
        $this->retroactive_hora_entrada = null;
        $this->retroactive_hora_salida = null;
    }

    public function updatedRetroactiveFecha($value): void
    {
        if ($value) {
            $existing = EmpleadoFichaje::where('empleado_id', $this->empleado->id)
                ->where('fecha', $value)
                ->first();

            if ($existing) {
                $this->retroactive_hora_entrada = $existing->hora_entrada ? Carbon::parse($existing->hora_entrada)->format('H:i') : null;
                $this->retroactive_hora_salida = $existing->hora_salida ? Carbon::parse($existing->hora_salida)->format('H:i') : null;
            } else {
                $this->retroactive_hora_entrada = null;
                $this->retroactive_hora_salida = null;
            }
        }
    }

    public function cerrarFichajeRetroactivo(): void
    {
        $this->selectedRetroactiveDate = null;
        $this->retroactive_fecha = null;
        $this->retroactive_hora_entrada = null;
        $this->retroactive_hora_salida = null;
        $this->isCreatingNewRetroactive = false;
    }

    public function guardarFichajeRetroactivo(): void
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
            'retroactive_fecha' => 'required|date|before_or_equal:today',
            'retroactive_hora_entrada' => 'required|date_format:H:i',
            'retroactive_hora_salida' => 'nullable|date_format:H:i',
        ]);

        $fecha = $this->retroactive_fecha;

        EmpleadoFichaje::updateOrCreate(
            [
                'empleado_id' => $this->empleado->id,
                'fecha' => $fecha,
            ],
            [
                'hora_entrada' => $this->retroactive_hora_entrada,
                'hora_salida' => $this->retroactive_hora_salida,
                'server_checkin_at' => Carbon::parse($fecha . ' ' . $this->retroactive_hora_entrada),
                'server_checkout_at' => $this->retroactive_hora_salida ? Carbon::parse($fecha . ' ' . $this->retroactive_hora_salida) : null,
                'is_retroactive' => true,
            ]
        );

        Notification::make()
            ->title('Fichaje Retroactivo Guardado')
            ->body('Has registrado tu fichaje para el día ' . Carbon::parse($fecha)->format('d/m/Y') . '.')
            ->success()
            ->send();

        $this->cerrarFichajeRetroactivo();
        $this->loadFichajes();
    }
}
