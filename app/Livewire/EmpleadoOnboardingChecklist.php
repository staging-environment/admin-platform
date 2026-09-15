<?php

namespace App\Livewire;

use App\Models\Empleado;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmpleadoOnboardingChecklist extends Component
{
    public $empleadoId;
    public $checklist = [];

    protected $listeners = ['refreshOnboardingChecklist' => '$refresh'];

    public function mount($empleadoId)
    {
        $this->empleadoId = $empleadoId;
        $this->loadChecklist();
    }

    public function loadChecklist()
    {
        $empleado = Empleado::find($this->empleadoId);
        if (!$empleado) {
            return;
        }

        $defaultChecks = [
            'password_changed' => [
                'label' => 'Contraseña inicial modificada por el empleado',
                'checked' => $empleado->user ? !\Illuminate\Support\Facades\Hash::check('1234', $empleado->user->password) : false,
                'auto' => true,
            ],
            'datos_personales' => [
                'label' => 'Datos personales, NUSS e IBAN revisados y completos',
                'checked' => !empty($empleado->dni) && !str_starts_with($empleado->dni, 'PENDIENTE') && !empty($empleado->nuss) && !empty($empleado->iban),
                'auto' => false,
            ],
            'dni_verificado' => [
                'label' => 'DNI/NIE verificado con fecha de caducidad en regla',
                'checked' => $empleado->documentos()->where('tipo', 'DNI')->exists() && $empleado->fecha_caducidad_dni && $empleado->fecha_caducidad_dni->isFuture(),
                'auto' => false,
            ],
            'prl_documentacion' => [
                'label' => 'Documentación PRL / Formación verificada',
                'checked' => $empleado->documentos()->where('tipo', 'Prevención de riesgos laborales')->exists(),
                'auto' => false,
            ],
            'politicas_aceptadas' => [
                'label' => 'Aceptación de normativas internas y RGPD formalizada',
                'checked' => $empleado->politicas_aceptadas_at !== null,
                'auto' => true,
            ],
            'alta_seguridad_social' => [
                'label' => 'Alta en Seguridad Social tramitada y confirmada',
                'checked' => false,
                'auto' => false,
            ],
            'entrega_uniforme_material' => [
                'label' => 'Entrega de uniforme, EPIs y accesos completada',
                'checked' => false,
                'auto' => false,
            ],
        ];

        $saved = $empleado->onboarding_checklist ?: [];
        foreach ($defaultChecks as $key => $item) {
            if (isset($saved[$key])) {
                $defaultChecks[$key]['checked'] = (bool) $saved[$key];
            }
        }

        $this->checklist = $defaultChecks;
    }

    public function toggleCheck($key)
    {
        if (!Auth::user()->can('gestion_recursos_humanos') && Auth::id() !== 1 && Auth::user()->email !== 'jarodriguezbonilla@gmail.com') {
            Notification::make()->title('No tienes permisos para modificar el checklist')->danger()->send();
            return;
        }

        if (isset($this->checklist[$key])) {
            $this->checklist[$key]['checked'] = !$this->checklist[$key]['checked'];

            $empleado = Empleado::find($this->empleadoId);
            if ($empleado) {
                $saved = $empleado->onboarding_checklist ?: [];
                $saved[$key] = $this->checklist[$key]['checked'];
                $empleado->onboarding_checklist = $saved;
                $empleado->save();
            }
        }
    }

    public function aprobarOnboarding()
    {
        if (!Auth::user()->can('gestion_recursos_humanos') && Auth::id() !== 1 && Auth::user()->email !== 'jarodriguezbonilla@gmail.com') {
            Notification::make()->title('No tienes permisos para aprobar el onboarding')->danger()->send();
            return;
        }

        $empleado = Empleado::find($this->empleadoId);
        if ($empleado) {
            $empleado->update([
                'onboarding_completado' => true,
                'onboarding_verificado_por_admin' => true,
                'onboarding_verificado_at' => now(),
                'onboarding_verificado_user_id' => Auth::id(),
                'onboarding_fecha_completado' => $empleado->onboarding_fecha_completado ?: now(),
            ]);

            $empleado->actualizarAlertas();

            Notification::make()
                ->title('Onboarding Aprobado')
                ->body('El expediente de incorporación ha sido verificado y aprobado oficialmente.')
                ->success()
                ->send();

            $this->dispatch('onboardingAprobado');
        }
    }

    public function reabrirOnboarding()
    {
        if (!Auth::user()->can('gestion_recursos_humanos') && Auth::id() !== 1 && Auth::user()->email !== 'jarodriguezbonilla@gmail.com') {
            return;
        }

        $empleado = Empleado::find($this->empleadoId);
        if ($empleado) {
            $empleado->update([
                'onboarding_completado' => false,
                'onboarding_verificado_por_admin' => false,
                'onboarding_paso_actual' => 1,
            ]);

            Notification::make()
                ->title('Onboarding Reabierto')
                ->body('El empleado deberá volver a completar los pasos de incorporación en su próximo acceso.')
                ->warning()
                ->send();
        }
    }

    public function render()
    {
        $empleado = Empleado::with('onboardingVerificadoPor')->find($this->empleadoId);

        $total = count($this->checklist);
        $checkedCount = count(array_filter($this->checklist, fn($c) => !empty($c['checked'])));
        $porcentaje = $total > 0 ? (int) round(($checkedCount / $total) * 100) : 0;

        return view('livewire.empleado-onboarding-checklist', [
            'empleado' => $empleado,
            'porcentaje' => $porcentaje,
            'checkedCount' => $checkedCount,
            'total' => $total,
        ]);
    }
}
