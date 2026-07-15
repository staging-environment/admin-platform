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

    // Validation rules
    protected $rules = [
        'tipo' => 'required|in:DNI,Contratos,Certificados,Titulaciones,Carnets,Resolución Discapacidad,Dictamen Técnico,Certificado Discapacidad,Incapacidad,Otros',
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
        } elseif ($this->family === 'contratos') {
            $this->tipo = 'Contratos';
        } elseif ($this->family === 'formacion') {
            $this->tipo = 'Certificados';
        } elseif ($this->family === 'discapacidad') {
            $this->tipo = 'Resolución Discapacidad';
        } elseif ($this->family === 'incapacidad') {
            $this->tipo = 'Incapacidad';
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
            $query->where('tipo', 'Incapacidad');
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

        $this->validate();

        $path = $this->file->store('empleados/documentos', 'local');

        $this->empleado->documentos()->create([
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'file_path' => $path,
        ]);

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
            $this->tipo = 'Incapacidad';
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

        session()->flash('message', 'Documento eliminado correctamente.');
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
