@php
    $user = auth()->user();
    $isAdmin = $user && ($user->id === 1 || $user->email === 'jarodriguezbonilla@gmail.com' || $user->hasRole(['Admin', 'admin', 'Administrador', 'Superadmin']) || $user->can('gestion_recursos_humanos'));
    $isEmpleado = $user && $user->hasRole('Empleado');
    $record = $getRecord();
@endphp

@if(!$isAdmin && $isEmpleado && $record && !$record->onboarding_completado)
    @livewire('empleado-onboarding-checklist', ['empleadoId' => $record->id], key('onboarding-checklist-'.$record->id))
@endif
