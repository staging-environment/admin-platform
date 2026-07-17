<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Empleado;
use App\Models\EmpleadoDocumento;
use Illuminate\Support\Facades\Storage;

class ManageEmpleadoDocumentos extends Component
{
    use WithFileUploads;

    public $empleadoId;
    public $family;
    
    // Form fields for uploading a new document
    public $tipo = 'Otros';
    public $nombre = '';
    public $file;
    public $selectedIncapacidad = [];
    public $fecha_caducidad_dni;

    // Validation rules
    protected $rules = [
        'tipo' => 'required|in:DNI,Contratos,Certificados,Titulaciones,Carnets,Resolución Discapacidad,Dictamen Técnico,Certificado Discapacidad,Incapacidad,Incapacidad Física,Incapacidad Psíquica,Otros',
        'nombre' => 'required|string|max:255',
        'file' => 'required|file|max:10240', // 10MB max
    ];

    public function mount($empleadoId, $family = null)
    {
        $this->empleadoId = $empleadoId;
        $this->family = $family;

        // Set default tipo depending on family
        if ($this->family === 'dni') {
            $this->tipo = 'DNI';
            $this->fecha_caducidad_dni = $this->empleado->fecha_caducidad_dni?->format('Y-m-d');
        } elseif ($this->family === 'contratos') {
            $this->tipo = 'Contratos';
        } elseif ($this->family === 'formacion') {
            $this->tipo = 'Certificados';
        } elseif ($this->family === 'discapacidad') {
            $this->tipo = 'Resolución Discapacidad';
        } elseif ($this->family === 'incapacidad') {
            $this->tipo = 'Incapacidad Física';
            $this->selectedIncapacidad = $this->empleado->tipo_incapacidad ?? [];
        }
    }

    public function getEmpleadoProperty()
    {
        return Empleado::findOrFail($this->empleadoId);
    }

    public function getDocumentosProperty()
    {
        $query = $this->empleado->documentos();
        if ($this->family === 'dni') {
            $query->where('tipo', 'DNI');
        } elseif ($this->family === 'contratos') {
            $query->where('tipo', 'Contratos');
        } elseif ($this->family === 'formacion') {
            $query->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros']);
        } elseif ($this->family === 'discapacidad') {
            $query->whereIn('tipo', ['Resolución Discapacidad', 'Dictamen Técnico', 'Certificado Discapacidad']);
        } elseif ($this->family === 'incapacidad') {
            $query->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad']);
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function uploadDocument()
    {
        // Check permission
        if (!auth()->user()->can('editar_documentacion_empleados')) {
            session()->flash('error', 'No tienes permisos para añadir documentos.');
            return;
        }

        if ($this->family === 'dni') {
            $this->tipo = 'DNI';
            $this->nombre = 'DNI ' . $this->empleado->nombre . ' ' . $this->empleado->apellidos;
            $this->empleado->update([
                'fecha_caducidad_dni' => $this->fecha_caducidad_dni ?: null
            ]);
        }

        $this->validate();

        $path = $this->file->store('empleados/documentos', 'local');

        $this->empleado->documentos()->create([
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'file_path' => $path,
        ]);

        if ($this->family === 'discapacidad') {
            $this->empleado->update(['tiene_discapacidad' => true]);
        } elseif ($this->family === 'incapacidad') {
            $this->empleado->update(['tiene_incapacidad' => true]);
        }

        // Reset form fields
        $this->reset(['nombre', 'file']);
        if ($this->family === 'dni') {
            $this->tipo = 'DNI';
        } elseif ($this->family === 'contratos') {
            $this->tipo = 'Contratos';
        } elseif ($this->family === 'formacion') {
            $this->tipo = 'Certificados';
        } elseif ($this->family === 'discapacidad') {
            $this->tipo = 'Resolución Discapacidad';
        } elseif ($this->family === 'incapacidad') {
            $this->tipo = 'Incapacidad Física';
        } else {
            $this->tipo = 'Otros';
        }

        session()->flash('message', 'Documento añadido correctamente.');
    }

    public function deleteDocument($id)
    {
        // Check permission
        if (!auth()->user()->can('editar_documentacion_empleados')) {
            session()->flash('error', 'No tienes permisos para borrar documentos.');
            return;
        }

        $documento = EmpleadoDocumento::where('empleado_id', $this->empleadoId)->findOrFail($id);
        
        // Delete file from disk
        if ($documento->file_path && Storage::disk('local')->exists($documento->file_path)) {
            Storage::disk('local')->delete($documento->file_path);
        }

        $documento->delete();

        if ($this->family === 'discapacidad') {
            $hasRemaining = $this->empleado->documentos()->whereIn('tipo', ['Resolución Discapacidad', 'Dictamen Técnico', 'Certificado Discapacidad'])->exists();
            if (!$hasRemaining) {
                $this->empleado->update(['tiene_discapacidad' => false]);
            }
        } elseif ($this->family === 'incapacidad') {
            $hasRemaining = $this->empleado->documentos()->whereIn('tipo', ['Incapacidad Física', 'Incapacidad Psíquica', 'Incapacidad'])->exists();
            if (!$hasRemaining) {
                $this->empleado->update(['tiene_incapacidad' => false]);
            }
        }

        session()->flash('message', 'Documento eliminado correctamente.');
    }

    public function saveIncapacidad()
    {
        if (!auth()->user()->can('editar_documentacion_empleados')) {
            session()->flash('error', 'No tienes permisos para editar la incapacidad.');
            return;
        }

        $empleado = $this->empleado;
        $empleado->tipo_incapacidad = $this->selectedIncapacidad;
        $empleado->tiene_incapacidad = !empty($this->selectedIncapacidad);
        $empleado->save();

        session()->flash('message', 'Tipo de incapacidad guardado correctamente.');
    }

    public function saveFechaCaducidad()
    {
        if (!auth()->user()->can('editar_documentacion_empleados')) {
            session()->flash('error', 'No tienes permisos para editar la fecha de caducidad.');
            return;
        }

        $this->empleado->update([
            'fecha_caducidad_dni' => $this->fecha_caducidad_dni ?: null
        ]);

        session()->flash('message', 'Fecha de caducidad del DNI guardada correctamente.');
    }

    public function render()
    {
        // Check view permission
        if (!auth()->user()->can('ver_documentacion_empleados')) {
            return <<<'HTML'
                <div class="p-4 text-center text-red-500 font-medium">
                    No tienes permisos para ver la documentación de este empleado.
                </div>
            HTML;
        }

        return view('livewire.manage-empleado-documentos');
    }
}
