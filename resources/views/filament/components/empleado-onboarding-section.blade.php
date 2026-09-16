@if(!$getRecord()?->onboarding_verificado_por_admin)
<div>
    @livewire('empleado-onboarding-checklist', ['empleadoId' => $getRecord()->id], key('onboarding-checklist-'.$getRecord()->id))
</div>
@endif
