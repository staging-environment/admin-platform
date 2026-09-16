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
                        Control de incorporación y validación de expediente de RRHH
                    </p>
                </div>
            </div>
        </div>

        <div>
            @if ($empleado?->onboarding_verificado_por_admin)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Onboarding Oficialmente Aprobado
                </span>
            @elseif ($empleado?->onboarding_completado)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40 animate-pulse">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pendiente de Validación por Gestor
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    En Progreso por el Empleado (Paso {{ $empleado?->onboarding_paso_actual ?? 1 }} de 5)
                </span>
            @endif
        </div>
    </div>

    {{-- Barra de Progreso --}}
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-semibold">
            <span class="text-gray-700 dark:text-gray-300">Progreso del Expediente</span>
            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $porcentaje }}% ({{ $checkedCount }} de {{ $total }} verificaciones)</span>
        </div>
        <div class="w-full h-2.5 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
            <div class="h-full transition-all duration-500 rounded-full {{ $porcentaje === 100 ? 'bg-green-500' : 'bg-amber-500' }}" style="width: {{ $porcentaje }}%;"></div>
        </div>
    </div>

    {{-- Lista de Verificaciones --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
        @foreach ($checklist as $key => $item)
            <div wire:click="toggleCheck('{{ $key }}')" class="p-3.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between gap-3 {{ !empty($item['checked']) ? 'bg-green-50/50 dark:bg-green-950/10 border-green-200 dark:border-green-800/30' : 'bg-gray-50/50 dark:bg-white/5 border-gray-200 dark:border-white/10 hover:border-amber-300 dark:hover:border-amber-600/40' }}">
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

    {{-- Footer con Metadatos y Botones de Acción --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-white/5">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            @if ($empleado?->onboarding_verificado_por_admin)
                <span>✓ Validado por <strong class="text-gray-800 dark:text-gray-200">{{ $empleado->onboardingVerificadoPor?->name ?? 'Gestor de RRHH' }}</strong> el {{ $empleado->onboarding_verificado_at?->format('d/m/Y H:i') }}</span>
            @elseif ($empleado?->onboarding_completado)
                <span>✓ Completado por el empleado el {{ $empleado->onboarding_fecha_completado?->format('d/m/Y H:i') }}</span>
            @else
                <span>El empleado está en proceso de completar sus datos iniciales.</span>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if ($empleado?->onboarding_verificado_por_admin)
                <button type="button" wire:click="reabrirOnboarding" wire:confirm="¿Seguro que deseas reabrir el proceso de onboarding para este empleado?" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold transition-all">
                    Reabrir Onboarding
                </button>
            @else
                <button type="button" wire:click="aprobarOnboarding" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold transition-all shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Aprobar y Finalizar Onboarding</span>
                </button>
            @endif
        </div>
    </div>

</div>
