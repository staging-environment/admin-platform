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
    public $fecha_inicio_contrato;
    public $fecha_vencimiento_contrato;
    public $tipo_jornada = 'Jornada completa';
    public $tipo_jornada_otro;
    public $fecha_realizacion;
    public $gasolinera_codigo;
    public $puesto;

    // Properties for inline editing
    public $editingDocumentId = null;
    public $edit_tipo_contrato;
    public $edit_fecha_inicio_contrato;
    public $edit_fecha_vencimiento_contrato;
    public $edit_fecha_caducidad_dni;
    public $edit_tipo_jornada;
    public $edit_tipo_jornada_otro;
    public $edit_file;
    public $edit_fecha_realizacion;
    public $edit_tipo;
    public $edit_nombre;
    public $edit_gasolinera_codigo;
    public $edit_puesto;

    // Validation rules
    protected $rules = [
        'tipo' => 'required|in:DNI,Contratos,Certificados,Titulaciones,Carnets,Resolución Discapacidad,Dictamen Técnico,Certificado Discapacidad,Incapacidad,Incapacidad Física,Incapacidad Psíquica,Otros,Prevención de riesgos laborales,Manipulación de alimentos',
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
            $this->tipo_jornada = 'Jornada completa';
            $this->gasolinera_codigo = $this->empleado->gasolinera_codigo;
            $this->puesto = $this->empleado->puesto;
        } elseif ($this->family === 'formacion') {
            $this->tipo = 'Prevención de riesgos laborales';
            $this->fecha_realizacion = null;
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
            $query->whereIn('tipo', ['Certificados', 'Titulaciones', 'Carnets', 'Otros', 'Prevención de riesgos laborales', 'Manipulación de alimentos']);
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
        if ($this->family === 'contratos') {
            $rules['fecha_inicio_contrato'] = 'required|date';
            $rules['fecha_vencimiento_contrato'] = 'required_if:tipo_contrato,Eventual|nullable|date';
            $rules['tipo_jornada'] = 'required|string|in:Jornada completa,Media Jornada,Otros';
            $rules['tipo_jornada_otro'] = 'required_if:tipo_jornada,Otros|nullable|string|max:255';
            $rules['gasolinera_codigo'] = 'required|integer|exists:gasolineras,Codigo';
            $rules['puesto'] = 'required|string|max:255';
        }
        if ($this->family === 'formacion') {
            $rules['fecha_realizacion'] = 'required|date';
            if ($this->tipo === 'Otros') {
                $rules['nombre'] = 'required|string|max:255';
            } else {
                $this->nombre = $this->tipo;
                unset($rules['nombre']);
            }
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
            'fecha_inicio_contrato' => ($this->family === 'contratos' && $this->fecha_inicio_contrato) ? $this->fecha_inicio_contrato : null,
            'fecha_vencimiento_contrato' => ($this->family === 'contratos' && $this->tipo_contrato === 'Eventual' && $this->fecha_vencimiento_contrato) ? $this->fecha_vencimiento_contrato : null,
            'tipo_jornada' => $this->family === 'contratos' ? ($this->tipo_jornada ?: null) : null,
            'tipo_jornada_otro' => ($this->family === 'contratos' && $this->tipo_jornada === 'Otros') ? $this->tipo_jornada_otro : null,
            'gasolinera_codigo' => $this->family === 'contratos' ? ($this->gasolinera_codigo ?: null) : null,
            'puesto' => $this->family === 'contratos' ? ($this->puesto ?: null) : null,
            'fecha_realizacion' => $this->family === 'formacion' ? $this->fecha_realizacion : null,
        ]);

        if ($this->family === 'contratos') {
            $this->empleado->syncLatestContract();
            $this->refreshContratoFields();
        } elseif ($this->family === 'discapacidad') {
            $this->empleado->update(['tiene_discapacidad' => true]);
        } elseif ($this->family === 'incapacidad') {
            $this->empleado->update(['tiene_incapacidad' => true]);
        }

        $this->empleado->actualizarAlertas();

        // Reset form fields
        $this->reset(['nombre', 'file', 'tipo_jornada', 'tipo_jornada_otro', 'fecha_realizacion']);
        if ($this->family === 'dni') {
            $this->tipo = 'DNI';
        } elseif ($this->family === 'contratos') {
            $this->tipo = 'Contratos';
            $this->tipo_jornada = 'Jornada completa';
        } elseif ($this->family === 'formacion') {
            $this->tipo = 'Prevención de riesgos laborales';
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
        $this->gasolinera_codigo = $this->empleado->gasolinera_codigo;
        $this->puesto = $this->empleado->puesto;
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

        $this->empleado->actualizarAlertas();

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
        $this->edit_file = null;
        
        if ($this->family === 'contratos') {
            $this->edit_tipo_contrato = $doc->tipo_contrato ?: 'Indefinido';
            $this->edit_fecha_inicio_contrato = $doc->fecha_inicio_contrato ? $doc->fecha_inicio_contrato->format('Y-m-d') : null;
            $this->edit_fecha_vencimiento_contrato = $doc->fecha_vencimiento_contrato ? $doc->fecha_vencimiento_contrato->format('Y-m-d') : null;
            $this->edit_tipo_jornada = $doc->tipo_jornada ?: 'Jornada completa';
            $this->edit_tipo_jornada_otro = $doc->tipo_jornada_otro;
            $this->edit_gasolinera_codigo = $doc->gasolinera_codigo ?: $this->empleado->gasolinera_codigo;
            $this->edit_puesto = $doc->puesto ?: $this->empleado->puesto;
        } elseif ($this->family === 'dni') {
            $this->edit_fecha_caducidad_dni = $this->empleado->fecha_caducidad_dni ? $this->empleado->fecha_caducidad_dni->format('Y-m-d') : null;
        } elseif ($this->family === 'formacion') {
            $this->edit_tipo = $doc->tipo;
            $this->edit_nombre = $doc->nombre;
            $this->edit_fecha_realizacion = $doc->fecha_realizacion ? $doc->fecha_realizacion->format('Y-m-d') : null;
        }
    }

    public function cancelEdit()
    {
        $this->editingDocumentId = null;
        $this->edit_file = null;
    }

    public function saveDocumentEdit()
    {
        if (!auth()->user()->can('editar_documentacion_empleados')) {
            session()->flash('error', 'No tienes permisos para editar documentos.');
            return;
        }

        $doc = EmpleadoDocumento::findOrFail($this->editingDocumentId);
        
        if ($this->family === 'contratos') {
            $rules = [
                'edit_fecha_inicio_contrato' => 'required|date',
                'edit_fecha_vencimiento_contrato' => 'required_if:edit_tipo_contrato,Eventual|nullable|date',
                'edit_tipo_jornada' => 'required|string|in:Jornada completa,Media Jornada,Otros',
                'edit_tipo_jornada_otro' => 'required_if:edit_tipo_jornada,Otros|nullable|string|max:255',
                'edit_gasolinera_codigo' => 'required|integer|exists:gasolineras,Codigo',
                'edit_puesto' => 'required|string|max:255',
            ];
            if ($this->edit_file) {
                $rules['edit_file'] = 'file|max:10240';
            }
            $this->validate($rules);

            $path = $doc->file_path;
            if ($this->edit_file) {
                if ($doc->file_path && Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                }
                $path = $this->edit_file->store('empleados/documentos', 'local');
            }

            $doc->update([
                'file_path' => $path,
                'tipo_contrato' => $this->edit_tipo_contrato ?: null,
                'fecha_inicio_contrato' => $this->edit_fecha_inicio_contrato ?: null,
                'fecha_vencimiento_contrato' => ($this->edit_tipo_contrato === 'Eventual' && $this->edit_fecha_vencimiento_contrato) ? $this->edit_fecha_vencimiento_contrato : null,
                'tipo_jornada' => $this->edit_tipo_jornada ?: null,
                'tipo_jornada_otro' => ($this->edit_tipo_jornada === 'Otros') ? $this->edit_tipo_jornada_otro : null,
                'gasolinera_codigo' => $this->edit_gasolinera_codigo ?: null,
                'puesto' => $this->edit_puesto ?: null,
            ]);
            $this->empleado->syncLatestContract();
            $this->refreshContratoFields();
        } elseif ($this->family === 'dni') {
            if ($this->edit_file) {
                $this->validate(['edit_file' => 'file|max:10240']);
                if ($doc->file_path && Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                }
                $path = $this->edit_file->store('empleados/documentos', 'local');
                $doc->update(['file_path' => $path]);
            }
            $this->empleado->update([
                'fecha_caducidad_dni' => $this->edit_fecha_caducidad_dni ?: null,
            ]);
            $this->fecha_caducidad_dni = $this->edit_fecha_caducidad_dni;
        } elseif ($this->family === 'formacion') {
            $rules = [
                'edit_tipo' => 'required|in:DNI,Contratos,Certificados,Titulaciones,Carnets,Resolución Discapacidad,Dictamen Técnico,Certificado Discapacidad,Incapacidad,Incapacidad Física,Incapacidad Psíquica,Otros,Prevención de riesgos laborales,Manipulación de alimentos',
                'edit_fecha_realizacion' => 'required|date',
            ];
            if ($this->edit_tipo === 'Otros') {
                $rules['edit_nombre'] = 'required|string|max:255';
            } else {
                $this->edit_nombre = $this->edit_tipo;
            }
            if ($this->edit_file) {
                $rules['edit_file'] = 'file|max:10240';
            }
            $this->validate($rules);

            $path = $doc->file_path;
            if ($this->edit_file) {
                if ($doc->file_path && Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                }
                $path = $this->edit_file->store('empleados/documentos', 'local');
            }

            $doc->update([
                'tipo' => $this->edit_tipo,
                'nombre' => $this->edit_nombre,
                'file_path' => $path,
                'fecha_realizacion' => $this->edit_fecha_realizacion,
            ]);
        }

        $this->empleado->actualizarAlertas();
        $this->editingDocumentId = null;
        $this->edit_file = null;
        
        session()->flash('message', 'Documento actualizado correctamente.');
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
