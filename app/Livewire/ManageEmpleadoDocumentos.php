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
    
    // Form fields for uploading a new document
    public $tipo = 'Otros';
    public $nombre = '';
    public $file;

    // Validation rules
    protected $rules = [
        'tipo' => 'required|in:Certificados,Titulaciones,Carnets,Otros',
        'nombre' => 'required|string|max:255',
        'file' => 'required|file|max:10240', // 10MB max
    ];

    public function mount($empleadoId)
    {
        $this->empleadoId = $empleadoId;
    }

    public function getEmpleadoProperty()
    {
        return Empleado::findOrFail($this->empleadoId);
    }

    public function getDocumentosProperty()
    {
        return $this->empleado->documentos()->orderBy('created_at', 'desc')->get();
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
        $this->tipo = 'Otros';

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
