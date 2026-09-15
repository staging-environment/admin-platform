<?php

namespace App\Livewire;

use App\Models\Empleado;
use App\Models\EmpleadoDocumento;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmpleadoOnboardingWizard extends Component
{
    use WithFileUploads;

    public $paso = 1;
    public $empleado;

    // Paso 1: Password
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Paso 2: Datos Personales, NUSS e IBAN
    public $nombre;
    public $apellidos;
    public $dni;
    public $fecha_nacimiento;
    public $telefono_principal;
    public $direccion;
    public $codigo_postal;
    public $localidad;
    public $provincia;
    public $nuss;
    public $iban;
    public $contacto_emergencia_nombre;
    public $contacto_emergencia_telefono;

    // Paso 3: Documentación
    public $file_dni;
    public $fecha_caducidad_dni;
    public $file_banco;
    public $file_prl;
    public $tiene_discapacidad = false;
    public $file_discapacidad;

    // Paso 4: Políticas
    public $acepta_rgpd = false;
    public $acepta_normativa = false;
    public $acepta_prl = false;

    public function mount()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->empleado = Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();

        if (!$this->empleado) {
            // Crear registro provisional de empleado si aún no existe
            $parts = explode(' ', trim($user->name ?: 'Empleado'));
            $nombre = $parts[0];
            $apellidos = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

            $this->empleado = Empleado::create([
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $user->email,
                'telefono_principal' => $user->telefono ?: '',
                'onboarding_completado' => false,
                'onboarding_paso_actual' => 1,
            ]);
        }

        // Si ya completó el onboarding, redirigir al portal
        if ($this->empleado->onboarding_completado) {
            return redirect()->to('/admin');
        }

        $this->paso = $this->empleado->onboarding_paso_actual ?: 1;

        // Cargar datos previos
        $this->nombre = $this->empleado->nombre;
        $this->apellidos = $this->empleado->apellidos;
        $this->dni = str_starts_with($this->empleado->dni ?? '', 'PENDIENTE') ? '' : $this->empleado->dni;
        $this->fecha_nacimiento = $this->empleado->fecha_nacimiento ? $this->empleado->fecha_nacimiento->format('Y-m-d') : null;
        $this->telefono_principal = $this->empleado->telefono_principal ?: $user->telefono;
        $this->direccion = $this->empleado->direccion === 'Dirección pendiente' ? '' : $this->empleado->direccion;
        $this->codigo_postal = $this->empleado->codigo_postal;
        $this->localidad = $this->empleado->localidad ?: 'Utrera';
        $this->provincia = $this->empleado->provincia ?: 'Sevilla';
        $this->nuss = $this->empleado->nuss;
        $this->iban = $this->empleado->iban;
        $this->contacto_emergencia_nombre = $this->empleado->contacto_emergencia_nombre;
        $this->contacto_emergencia_telefono = $this->empleado->contacto_emergencia_telefono;
        $this->fecha_caducidad_dni = $this->empleado->fecha_caducidad_dni ? $this->empleado->fecha_caducidad_dni->format('Y-m-d') : null;
        $this->tiene_discapacidad = (bool) $this->empleado->tiene_discapacidad;
    }

    public function irPaso($numero)
    {
        if ($numero <= $this->empleado->onboarding_paso_actual) {
            $this->paso = $numero;
        }
    }

    public function guardarPaso1()
    {
        $user = Auth::user();
        $isDefaultPassword = Hash::check('1234', $user->password);

        $rules = [
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ];

        if (!$isDefaultPassword) {
            $rules['current_password'] = 'required|current_password';
        }

        $this->validate($rules, [
            'new_password.required' => 'Debes introducir una nueva contraseña.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'new_password.different' => 'La nueva contraseña debe ser diferente a la anterior.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
        ]);

        // Actualizar contraseña del usuario
        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->paso = 2;
        $this->empleado->update([
            'onboarding_paso_actual' => max(2, (int) $this->empleado->onboarding_paso_actual),
        ]);

        session()->flash('success_step', '¡Contraseña actualizada correctamente! Por favor, verifica tus datos personales.');
    }

    public function guardarPaso2()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono_principal' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'localidad' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'nuss' => 'required|string|max:30',
            'iban' => 'required|string|max:40',
            'contacto_emergencia_nombre' => 'required|string|max:255',
            'contacto_emergencia_telefono' => 'required|string|max:20',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'dni.required' => 'El DNI/NIE es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'telefono_principal.required' => 'El teléfono de contacto es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
            'localidad.required' => 'La localidad es obligatoria.',
            'provincia.required' => 'La provincia es obligatoria.',
            'nuss.required' => 'El número de la Seguridad Social (NUSS) es obligatorio.',
            'iban.required' => 'El código IBAN bancario es obligatorio.',
            'contacto_emergencia_nombre.required' => 'El contacto de emergencia es obligatorio.',
            'contacto_emergencia_telefono.required' => 'El teléfono de emergencia es obligatorio.',
        ]);

        $this->empleado->update([
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'dni' => strtoupper(trim($this->dni)),
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'telefono_principal' => $this->telefono_principal,
            'direccion' => $this->direccion,
            'codigo_postal' => $this->codigo_postal,
            'localidad' => $this->localidad,
            'provincia' => $this->provincia,
            'nuss' => strtoupper(trim($this->nuss)),
            'iban' => strtoupper(str_replace(' ', '', $this->iban)),
            'contacto_emergencia_nombre' => $this->contacto_emergencia_nombre,
            'contacto_emergencia_telefono' => $this->contacto_emergencia_telefono,
            'onboarding_paso_actual' => max(3, (int) $this->empleado->onboarding_paso_actual),
        ]);

        $this->paso = 3;
        session()->flash('success_step', 'Datos personales guardados. Ahora sube tu documentación.');
    }

    public function guardarPaso3()
    {
        $hasDniDoc = $this->empleado->documentos()->where('tipo', 'DNI')->exists();

        $rules = [
            'fecha_caducidad_dni' => 'required|date|after:today',
        ];

        if (!$hasDniDoc || $this->file_dni) {
            $rules['file_dni'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:10240';
        }

        if ($this->file_banco) {
            $rules['file_banco'] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
        }

        if ($this->file_prl) {
            $rules['file_prl'] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
        }

        if ($this->file_discapacidad) {
            $rules['file_discapacidad'] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
        }

        $this->validate($rules, [
            'fecha_caducidad_dni.required' => 'La fecha de caducidad del DNI es obligatoria.',
            'fecha_caducidad_dni.after' => 'La fecha de caducidad del DNI debe ser posterior a la de hoy.',
            'file_dni.required' => 'Debes adjuntar el documento de tu DNI/NIE.',
            'file_dni.mimes' => 'El DNI debe ser un archivo PDF o imagen (JPG, PNG).',
            'file_dni.max' => 'El tamaño máximo permitido es 10 MB.',
        ]);

        // Guardar DNI
        if ($this->file_dni) {
            $path = $this->file_dni->store('empleados_documentos', 'local');
            EmpleadoDocumento::create([
                'empleado_id' => $this->empleado->id,
                'nombre' => 'DNI - ' . $this->empleado->nombre . ' ' . $this->empleado->apellidos,
                'tipo' => 'DNI',
                'file_path' => $path,
            ]);
        }

        // Guardar Justificante Bancario
        if ($this->file_banco) {
            $pathBanco = $this->file_banco->store('empleados_documentos', 'local');
            EmpleadoDocumento::create([
                'empleado_id' => $this->empleado->id,
                'nombre' => 'Certificado Titularidad Bancaria / IBAN',
                'tipo' => 'Certificados',
                'file_path' => $pathBanco,
            ]);
        }

        // Guardar Formación PRL
        if ($this->file_prl) {
            $pathPrl = $this->file_prl->store('empleados_documentos', 'local');
            EmpleadoDocumento::create([
                'empleado_id' => $this->empleado->id,
                'nombre' => 'Certificado de Formación / PRL',
                'tipo' => 'Prevención de riesgos laborales',
                'file_path' => $pathPrl,
                'fecha_inicio' => Carbon::today(),
                'fecha_fin' => Carbon::today()->addYears(5),
            ]);
        }

        // Guardar Discapacidad
        if ($this->file_discapacidad) {
            $pathDisc = $this->file_discapacidad->store('empleados_documentos', 'local');
            EmpleadoDocumento::create([
                'empleado_id' => $this->empleado->id,
                'nombre' => 'Certificado Discapacidad',
                'tipo' => 'Certificado Discapacidad',
                'file_path' => $pathDisc,
            ]);
        }

        $this->empleado->update([
            'fecha_caducidad_dni' => $this->fecha_caducidad_dni,
            'tiene_discapacidad' => $this->tiene_discapacidad,
            'no_tiene_discapacidad' => !$this->tiene_discapacidad,
            'onboarding_paso_actual' => max(4, (int) $this->empleado->onboarding_paso_actual),
        ]);

        $this->empleado->actualizarAlertas();

        $this->paso = 4;
        session()->flash('success_step', 'Documentación subida con éxito. Por favor, lee y acepta las políticas finales.');
    }

    public function finalizarOnboarding()
    {
        $this->validate([
            'acepta_rgpd' => 'accepted',
            'acepta_normativa' => 'accepted',
            'acepta_prl' => 'accepted',
        ], [
            'acepta_rgpd.accepted' => 'Debes aceptar la política de protección de datos.',
            'acepta_normativa.accepted' => 'Debes aceptar las normativas internas de la empresa.',
            'acepta_prl.accepted' => 'Debes confirmar la recepción y aceptación de las directrices de prevención.',
        ]);

        $this->empleado->update([
            'politicas_aceptadas_at' => now(),
            'onboarding_completado' => true,
            'onboarding_fecha_completado' => now(),
            'onboarding_paso_actual' => 4,
        ]);

        $this->empleado->actualizarAlertas();

        session()->flash('message', '¡Enhorabuena! Has completado con éxito tu proceso de Onboarding. ¡Bienvenido/a al equipo!');

        return redirect()->to('/admin');
    }

    public function render()
    {
        return view('livewire.empleado-onboarding-wizard')
            ->layout('layouts.app', ['title' => 'Onboarding de Empleado - Utrecar']);
    }
}
