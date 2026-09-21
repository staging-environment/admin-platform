<div>
@php
    $user = auth()->user();
    $isAdmin = $user && ($user->id === 1 || $user->email === 'jarodriguezbonilla@gmail.com' || $user->hasRole(['Admin', 'admin', 'Administrador', 'Superadmin']) || $user->can('gestion_recursos_humanos'));
    $isEmpleado = $user && $user->hasRole('Empleado');
@endphp

@if(!$isAdmin && $isEmpleado && $empleado && !$empleado->onboarding_completado)
<div class="p-6 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 shadow-sm space-y-6">
    
    {{-- Header con Título y Estado --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-white/5 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        Checklist de Verificación de Onboarding
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Pasos obligatorios para completar tu incorporación
                    </p>
                </div>
            </div>
        </div>

        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40 animate-pulse">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                En Progreso (Paso {{ $empleado?->onboarding_paso_actual ?? 1 }} de 5)
            </span>
        </div>
    </div>

    {{-- Barra de Progreso --}}
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-semibold">
            <span class="text-gray-700 dark:text-gray-300">Progreso de tu Expediente</span>
            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $porcentaje }}% ({{ $checkedCount }} de {{ $total }} verificaciones)</span>
        </div>
        <div class="w-full h-2.5 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
            <div class="h-full transition-all duration-500 rounded-full {{ $porcentaje === 100 ? 'bg-green-500' : 'bg-amber-500' }}" style="width: {{ $porcentaje }}%;"></div>
        </div>
    </div>

    {{-- Lista de Verificaciones --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
        @foreach ($checklist as $key => $item)
            <div class="p-3.5 rounded-xl border transition-all flex items-center justify-between gap-3 {{ !empty($item['checked']) ? 'bg-green-50/50 dark:bg-green-950/10 border-green-200 dark:border-green-800/30' : 'bg-gray-50/50 dark:bg-white/5 border-gray-200 dark:border-white/10' }}">
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-md flex items-center justify-center transition-all {{ !empty($item['checked']) ? 'bg-green-600 text-white' : 'border border-gray-300 dark:border-white/20 bg-white dark:bg-gray-800' }}">
                        @if (!empty($item['checked']))
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </div>
                    <span class="text-xs font-medium {{ !empty($item['checked']) ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                        {{ $item['label'] }}
                    </span>
                </div>
                @if (!empty($item['auto']))
                    <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-white/5 px-2 py-0.5 rounded">
                        Auto
                    </span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Footer con Metadatos y Botón de Acción --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-white/5">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            <span>Debes completar todos los pasos obligatorios para formalizar tu incorporación en la empresa.</span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('empleado.onboarding') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold transition-all shadow-md hover:shadow-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Continuar Onboarding</span>
            </a>
        </div>
    </div>

</div>
@endif

</div>
