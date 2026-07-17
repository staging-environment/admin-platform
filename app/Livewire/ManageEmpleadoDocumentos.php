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
    public $tipo_contrato;
    public $fecha_vencimiento_contrato;

    // Properties for inline editing
    public $editingDocumentId = null;
    public $edit_tipo_contrato;
    public $edit_fecha_vencimiento_contrato;

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
            $this->fecha_caducidad_dni = null;
        } elseif ($this->family === 'contratos') {
            $this->tipo = 'Contratos';
            $this->tipo_contrato = $this->empleado->tipo_contrato;
            $this->fecha_vencimiento_contrato = $this->empleado->fecha_vencimiento_contrato?->format('Y-m-d');
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
        } elseif ($this->family === 'contratos') {
            $this->tipo = 'Contratos';
            if (empty($this->nombre)) {
                $this->nombre = 'Contrato ' . ($this->tipo_contrato ?: '') . ' ' . $this->empleado->nombre . ' ' . $this->empleado->apellidos;
            }
        }

        $rules = $this->rules;
        if ($this->family === 'dni') {
            $rules['fecha_caducidad_dni'] = 'required|date';
        }
        $this->validate($rules);

        if ($this->family === 'dni') {
            $this->empleado->update([
                'fecha_caducidad_dni' => $this->fecha_caducidad_dni ?: null
            ]);
        }

        $path = $this->file->store('empleados/documentos', 'local');

        $this->empleado->documentos()->create([
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'file_path' => $path,
            'tipo_contrato' => $this->family === 'contratos' ? ($this->tipo_contrato ?: null) : null,
            'fecha_vencimiento_contrato' => ($this->family === 'contratos' && $this->tipo_contrato === 'Eventual' && $this->fecha_vencimiento_contrato) ? $this->fecha_vencimiento_contrato : null,
        ]);

        if ($this->family === 'contratos') {
            $this->empleado->syncLatestContract();
            $this->refreshContratoFields();
        } elseif ($this->family === 'discapacidad') {
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

    protected function refreshContratoFields()
    {
        $this->tipo_contrato = $this->empleado->tipo_contrato;
        $this->fecha_vencimiento_contrato = $this->empleado->fecha_vencimiento_contrato?->format('Y-m-d');
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

        if ($this->family === 'contratos') {
            $this->empleado->syncLatestContract();
            $this->refreshContratoFields();
        } elseif ($this->family === 'discapacidad') {
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

    public function editDocument($id)
    {
        $doc = EmpleadoDocumento::findOrFail($id);
        $this->editingDocumentId = $id;
        $this->edit_tipo_contrato = $doc->tipo_contrato ?: 'Indefinido';
        $this->edit_fecha_vencimiento_contrato = $doc->fecha_vencimiento_contrato ? $doc->fecha_vencimiento_contrato->format('Y-m-d') : null;
    }

    public function cancelEdit()
    {
        $this->editingDocumentId = null;
    }

    public function saveDocumentEdit()
    {
        if (!auth()->user()->can('editar_documentacion_empleados')) {
            session()->flash('error', 'No tienes permisos para editar documentos.');
            return;
        }

        $doc = EmpleadoDocumento::findOrFail($this->editingDocumentId);
        $doc->update([
            'tipo_contrato' => $this->edit_tipo_contrato ?: null,
            'fecha_vencimiento_contrato' => ($this->edit_tipo_contrato === 'Eventual' && $this->edit_fecha_vencimiento_contrato) ? $this->edit_fecha_vencimiento_contrato : null,
        ]);

        $this->empleado->syncLatestContract();
        $this->refreshContratoFields();
        $this->editingDocumentId = null;
        
        session()->flash('message', 'Contrato actualizado correctamente.');
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
