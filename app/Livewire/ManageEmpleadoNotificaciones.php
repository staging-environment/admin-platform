<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\EmpleadoNotificacion;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManageEmpleadoNotificaciones extends Component
{
    use WithFileUploads;

    public $empleadoId;

    // Form fields for new notification
    public $tipo = 'Modificación sustancial del contrato';
    public $fecha_comunicacion;
    public $fecha_efecto;
    public $gravedad = 'Leve';
    public $archivo;

    // Form fields for closing an existing disciplinary file
    public $selectedNotificacionIdParaCierre = null;
    public $selectedNotificacionParaCierre = null;
    public $cierre_fecha_comunicacion;
    public $cierre_resolucion = 'Amonestación';
    public $cierre_dias_suspension = null;
    public $cierre_archivo;

    public function mount($empleadoId)
    {
        $this->empleadoId = $empleadoId;
        $this->fecha_comunicacion = now()->format('Y-m-d');
        $this->fecha_efecto = now()->format('Y-m-d');
    }

    public function updatedTipo($value)
    {
        if ($value === 'Modificación sustancial del contrato') {
            $this->fecha_efecto = now()->format('Y-m-d');
        } elseif ($value === 'Apertura Expediente disciplinario') {
            $this->gravedad = 'Leve';
        }
    }

    public function updatedCierreResolucion($value)
    {
        if ($value !== 'Suspensión de empleo y sueldo') {
            $this->cierre_dias_suspension = null;
        }
    }

    public function guardarNotificacion()
    {
        $rules = [
            'tipo' => 'required|in:Modificación sustancial del contrato,Apertura Expediente disciplinario',
            'fecha_comunicacion' => 'required|date',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ];

        if ($this->tipo === 'Modificación sustancial del contrato') {
            $rules['fecha_efecto'] = 'required|date';
        } elseif ($this->tipo === 'Apertura Expediente disciplinario') {
            $rules['gravedad'] = 'required|in:Leve,Grave,Muy Grave';
        }

        $this->validate($rules, [
            'tipo.required' => 'El tipo de notificación es obligatorio.',
            'fecha_comunicacion.required' => 'La fecha de comunicación es obligatoria.',
            'fecha_efecto.required' => 'La fecha de efecto es obligatoria.',
            'gravedad.required' => 'La gravedad es obligatoria.',
            'archivo.required' => 'Es obligatorio adjuntar un archivo justificativo.',
        ]);

        $empleado = Empleado::findOrFail($this->empleadoId);

        // Guardar archivo
        $path = $this->archivo->store('empleados/notificaciones', 'local');

        // Crear notificación
        EmpleadoNotificacion::create([
            'empleado_id' => $empleado->id,
            'tipo' => $this->tipo,
            'titulo' => $this->tipo,
            'contenido' => $this->tipo,
            'fecha_comunicacion' => $this->fecha_comunicacion,
            'fecha_efecto' => $this->tipo === 'Modificación sustancial del contrato' ? $this->fecha_efecto : null,
            'gravedad' => $this->tipo === 'Apertura Expediente disciplinario' ? $this->gravedad : null,
            'resolucion_cierre' => null,
            'dias_suspension' => null,
            'file_path' => $path,
        ]);

        // Registrar en documentos de la ficha
        $empleado->documentos()->create([
            'tipo' => 'Notificaciones',
            'nombre' => "Notificación: {$this->tipo} - {$empleado->nombre} {$empleado->apellidos}",
            'file_path' => $path,
        ]);

        // Reset form
        $this->reset(['archivo']);
        $this->fecha_comunicacion = now()->format('Y-m-d');
        $this->fecha_efecto = now()->format('Y-m-d');

        session()->flash('notificacion_success', 'Notificación registrada correctamente.');
    }

    public function iniciarCierreExpediente($notificacionId)
    {
        $notif = EmpleadoNotificacion::where('empleado_id', $this->empleadoId)->findOrFail($notificacionId);
        $this->selectedNotificacionIdParaCierre = $notif->id;
        $this->selectedNotificacionParaCierre = $notif;
        $this->cierre_fecha_comunicacion = now()->format('Y-m-d');
        $this->cierre_resolucion = 'Amonestación';
        $this->cierre_dias_suspension = null;
        $this->cierre_archivo = null;
    }

    public function cancelarCierreExpediente()
    {
        $this->selectedNotificacionIdParaCierre = null;
        $this->selectedNotificacionParaCierre = null;
        $this->cierre_dias_suspension = null;
        $this->cierre_archivo = null;
    }

    public function guardarCierreExpediente()
    {
        $rules = [
            'cierre_fecha_comunicacion' => 'required|date',
            'cierre_resolucion' => 'required|in:Amonestación,Suspensión de empleo y sueldo,Despido disciplinario',
            'cierre_archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ];

        if ($this->cierre_resolucion === 'Suspensión de empleo y sueldo') {
            $rules['cierre_dias_suspension'] = 'required|integer|min:1';
        }

        $this->validate($rules, [
            'cierre_fecha_comunicacion.required' => 'La fecha de resolución/cierre es obligatoria.',
            'cierre_resolucion.required' => 'La resolución de cierre es obligatoria.',
            'cierre_dias_suspension.required' => 'El número de días de suspensión es obligatorio.',
            'cierre_dias_suspension.min' => 'Los días de suspensión deben ser al menos 1.',
            'cierre_archivo.required' => 'Es obligatorio adjuntar un archivo justificativo de la resolución.',
        ]);

        $notif = EmpleadoNotificacion::where('empleado_id', $this->empleadoId)->findOrFail($this->selectedNotificacionIdParaCierre);
        $empleado = Empleado::findOrFail($this->empleadoId);

        // Guardar archivo de cierre
        $path = $this->cierre_archivo->store('empleados/notificaciones', 'local');

        // Actualizar el expediente con los datos de cierre
        $notif->update([
            'resolucion_cierre' => $this->cierre_resolucion,
            'fecha_cierre' => $this->cierre_fecha_comunicacion,
            'dias_suspension' => ($this->cierre_resolucion === 'Suspensión de empleo y sueldo') ? (int)$this->cierre_dias_suspension : null,
            'cierre_file_path' => $path,
        ]);

        // Registrar en documentos de la ficha
        $empleado->documentos()->create([
            'tipo' => 'Notificaciones',
            'nombre' => "Notificación: Cierre Expediente disciplinario ({$this->cierre_resolucion}) - {$empleado->nombre} {$empleado->apellidos}",
            'file_path' => $path,
        ]);

        // Si es Despido disciplinario -> Dar de baja definitiva al empleado
        if ($this->cierre_resolucion === 'Despido disciplinario') {
            $empleado->update([
                'estado' => 'Baja',
                'motivo_baja' => 'Despido disciplinario',
                'observaciones_baja' => 'Cierre de expediente disciplinario con resolución de despido.',
                'fecha_baja' => $this->cierre_fecha_comunicacion,
                'documento_baja_path' => $path,
            ]);
        }

        $this->cancelarCierreExpediente();
        session()->flash('notificacion_success', 'Cierre de expediente disciplinario registrado con éxito.');
    }

    public function eliminarNotificacion($id)
    {
        $notif = EmpleadoNotificacion::where('empleado_id', $this->empleadoId)->findOrFail($id);
        $notif->delete();
        session()->flash('notificacion_success', 'Notificación eliminada.');
    }

    public function render()
    {
        $notificaciones = EmpleadoNotificacion::where('empleado_id', $this->empleadoId)
            ->latest('id')
            ->get();

        return view('livewire.manage-empleado-notificaciones', [
            'notificaciones' => $notificaciones,
        ]);
    }
}

