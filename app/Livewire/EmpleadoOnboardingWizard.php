<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Empleado;
use App\Models\EmpleadoDocumento;
use Carbon\Carbon;

class EmpleadoOnboardingWizard extends Component
{
    use WithFileUploads;

    public int $paso = 1;
    public ?Empleado $empleado = null;

    // Paso 2: Contraseña
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Paso 3: Datos Personales
    public string $nombre = '';
    public string $apellidos = '';
    public string $dni = '';
    public string $fecha_nacimiento = '';
    public string $telefono_principal = '';
    public string $direccion = '';
    public string $codigo_postal = '';
    public string $localidad = '';
    public string $provincia = '';
    public string $nuss = '';
    public string $iban = '';
    public string $contacto_emergencia_nombre = '';
    public string $contacto_emergencia_telefono = '';

    // Paso 4: Documentos
    public ?string $fecha_caducidad_dni = '';
    public bool $tiene_discapacidad = false;
    public $file_dni = null;
    public $file_banco = null;
    public $file_prl = null;
    public $file_discapacidad = null;

    // Paso 5: Políticas
    public bool $acepta_rgpd = false;
    public bool $acepta_normativa = false;
    public bool $acepta_prl = false;

    public function mount()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->to('/login');
        }

        $this->empleado = Empleado::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();

        if (!$this->empleado) {
            $parts = explode(' ', trim($user->name ?: 'Empleado'));
            $nombre = $parts[0];
            $apellidos = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'Apellidos';

            $this->empleado = Empleado::create([
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'dni' => 'PENDIENTE-' . strtoupper(substr(md5($user->email), 0, 5)),
                'fecha_nacimiento' => '1990-01-01',
                'direccion' => 'Dirección pendiente',
                'localidad' => 'Utrera',
                'codigo_postal' => '41710',
                'provincia' => 'Sevilla',
                'telefono_principal' => $user->telefono ?: '600000000',
                'email' => $user->email,
                'onboarding_completado' => false,
                'onboarding_paso_actual' => 1,
            ]);
        }

        // Si ya completó onboarding, redirigir al portal
        if ($this->empleado->onboarding_completado) {
            return redirect()->to('/admin/portal-empleado');
        }

        // Cargar datos existentes
        $this->nombre = (string) ($this->empleado->nombre ?? '');
        $this->apellidos = (string) ($this->empleado->apellidos ?? '');
        $this->dni = (string) (str_starts_with((string)$this->empleado->dni, 'PENDIENTE-') ? '' : $this->empleado->dni);
        $this->fecha_nacimiento = $this->empleado->fecha_nacimiento ? Carbon::parse($this->empleado->fecha_nacimiento)->format('Y-m-d') : '';
        $this->telefono_principal = (string) ($this->empleado->telefono_principal ?? '');
        $this->direccion = (string) ($this->empleado->direccion === 'Dirección pendiente' ? '' : $this->empleado->direccion);
        $this->codigo_postal = (string) ($this->empleado->codigo_postal ?? '');
        $this->localidad = (string) ($this->empleado->localidad ?? '');
        $this->provincia = (string) ($this->empleado->provincia ?? '');
        $this->nuss = (string) ($this->empleado->nuss ?? '');
        $this->iban = (string) ($this->empleado->iban ?? '');
        $this->contacto_emergencia_nombre = (string) ($this->empleado->contacto_emergencia_nombre ?? '');
        $this->contacto_emergencia_telefono = (string) ($this->empleado->contacto_emergencia_telefono ?? '');
        $this->fecha_caducidad_dni = $this->empleado->fecha_caducidad_dni ? Carbon::parse($this->empleado->fecha_caducidad_dni)->format('Y-m-d') : '';
        $this->tiene_discapacidad = (bool) $this->empleado->tiene_discapacidad;

        $pasoActual = (int) ($this->empleado->onboarding_paso_actual ?: 1);
        $this->paso = min(max(1, $pasoActual), 5);
    }

    public function irPaso(int $nuevoPaso)
    {
        $maxPaso = max((int) $this->empleado->onboarding_paso_actual, $this->paso);
        if ($nuevoPaso <= $maxPaso && $nuevoPaso >= 1 && $nuevoPaso <= 5) {
            $this->paso = $nuevoPaso;
        }
    }

    public function completarPaso1Bienvenida()
    {
        $this->paso = 2;
        $this->empleado->update([
            'onboarding_paso_actual' => max(2, (int) $this->empleado->onboarding_paso_actual),
        ]);
    }

    public function guardarPaso2Password()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', Password::min(8), 'confirmed'],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria (1234 si es primer acceso).',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.required' => 'Debes indicar una nueva contraseña.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = auth()->user();
        $user->forceFill([
            'password' => Hash::make($this->new_password),
        ])->save();

        // Preservar estado de suplantación si existe antes de re-autenticar
        $impersonatorId = session('impersonated_by');
        $impersonatorGuard = session('impersonator_guard');
        $impersonatorGuardUsing = session('impersonator_guard_using');
        $impersonateBackTo = session('impersonate.back_to');
        $impersonateGuard = session('impersonate.guard');

        // Mantener sesión autenticada
        \Illuminate\Support\Facades\Auth::guard('web')->login($user);
        session()->put('password_hash_web', $user->getAuthPassword());
        session()->put('password_hash_' . \Illuminate\Support\Facades\Auth::getDefaultDriver(), $user->getAuthPassword());

        // Restaurar estado de suplantación tras login()
        if ($impersonatorId) {
            session()->put('impersonated_by', $impersonatorId);
            session()->put('impersonator_guard', $impersonatorGuard ?: 'web');
            session()->put('impersonator_guard_using', $impersonatorGuardUsing ?: 'web');
            session()->put('impersonate.back_to', $impersonateBackTo ?: '/admin/recursos-humanos');
            if ($impersonateGuard) {
                session()->put('impersonate.guard', $impersonateGuard);
            }
        }

        $this->paso = 3;
        $this->empleado->update([
            'onboarding_paso_actual' => max(3, (int) $this->empleado->onboarding_paso_actual),
        ]);

        session()->flash('success_step', '¡Contraseña actualizada con éxito! Ahora verifica y completa tus datos personales.');
    }

    public function guardarPaso3Datos()
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
            'iban' => 'required|string|max:40',
            'contacto_emergencia_nombre' => 'nullable|string|max:255',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
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
            'iban.required' => 'El código IBAN bancario es obligatorio para el pago de nóminas.',
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
            'iban' => strtoupper(str_replace(' ', '', $this->iban)),
            'contacto_emergencia_nombre' => $this->contacto_emergencia_nombre ?: null,
            'contacto_emergencia_telefono' => $this->contacto_emergencia_telefono ?: null,
            'onboarding_paso_actual' => max(4, (int) $this->empleado->onboarding_paso_actual),
        ]);

        $this->paso = 4;
        session()->flash('success_step', 'Datos personales guardados correctamente. Ahora adjunta tu documentación digital.');
    }

    public function guardarPaso4Documentos()
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
            'file_dni.required' => 'Debes adjuntar el documento escaneado o foto legible de tu DNI/NIE.',
            'file_dni.mimes' => 'El DNI debe ser un archivo PDF o imagen (JPG, PNG).',
            'file_dni.max' => 'El tamaño máximo permitido es 10 MB.',
        ]);

        if ($this->file_dni) {
            $path = $this->file_dni->store('empleados_documentos', 'local');
            EmpleadoDocumento::create([
                'empleado_id' => $this->empleado->id,
                'nombre' => 'DNI - ' . $this->empleado->nombre . ' ' . $this->empleado->apellidos,
                'tipo' => 'DNI',
                'file_path' => $path,
            ]);
        }

        if ($this->file_banco) {
            $pathBanco = $this->file_banco->store('empleados_documentos', 'local');
            EmpleadoDocumento::create([
                'empleado_id' => $this->empleado->id,
                'nombre' => 'Certificado Titularidad Bancaria / IBAN',
                'tipo' => 'Certificados',
                'file_path' => $pathBanco,
            ]);
        }

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
            'onboarding_paso_actual' => max(5, (int) $this->empleado->onboarding_paso_actual),
        ]);

        $this->empleado->actualizarAlertas();

        $this->paso = 5;
        session()->flash('success_step', 'Documentación adjuntada con éxito. Por favor, revisa y confirma las normativas de empresa.');
    }

    public function finalizarOnboarding()
    {
        $this->validate([
            'acepta_rgpd' => 'accepted',
            'acepta_normativa' => 'accepted',
            'acepta_prl' => 'accepted',
        ], [
            'acepta_rgpd.accepted' => 'Debes aceptar la política de Protección de Datos (RGPD).',
            'acepta_normativa.accepted' => 'Debes aceptar la Normativa Interna y Código de Conducta.',
            'acepta_prl.accepted' => 'Debes confirmar la recepción y aceptación de las directrices de Prevención (PRL).',
        ]);

        $this->empleado->update([
            'politicas_aceptadas_at' => now(),
            'onboarding_completado' => true,
            'onboarding_fecha_completado' => now(),
            'onboarding_paso_actual' => 5,
        ]);

        $this->empleado->actualizarAlertas();

        // Notificar por correo a los administradores y gestores
        try {
            $destinatarios = \App\Models\User::all()->filter(function ($user) {
                return $user->hasRole(['Administrador', 'admin', 'Admin', 'Gestor', 'gestor'])
                    || $user->can('gestion_recursos_humanos')
                    || $user->can('aprobacion_vacaciones_bajas')
                    || $user->email === 'jarodriguezbonilla@gmail.com';
            });

            foreach ($destinatarios as $admin) {
                if (!empty($admin->email)) {
                    \Illuminate\Support\Facades\Mail::to($admin->email)
                        ->send(new \App\Mail\OnboardingCompletadoAdminMail($this->empleado));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error al enviar email de onboarding completado: " . $e->getMessage());
        }

        session()->flash('message', '¡Enhorabuena! Has completado con éxito tu incorporación oficial. ¡Bienvenido/a a Utrecar - Active Network!');

        return redirect()->to('/admin/portal-empleado');
    }

    public function render()
    {
        return view('livewire.empleado-onboarding-wizard')
            ->layout('layouts.app', ['title' => 'Bienvenida e Incorporación de Empleado - Utrecar']);
    }
}
