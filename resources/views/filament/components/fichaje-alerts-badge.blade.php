<div x-data="{ open: false }" class="inline-flex items-center shrink-0">
    @php
        $alertas = $fichaje->alertas_descuadre ?? [];
        $count = count($alertas);
    @endphp

    @if ($count > 0)
        <button type="button" 
                @click.stop="open = true" 
                onclick="event.stopPropagation()"
                title="Descuadre horario detectado: {{ $count }} {{ $count === 1 ? 'alerta' : 'alertas' }} (diferencia > 5 min)"
                style="position: relative !important; z-index: 30 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 4px !important; padding: 0 8px !important; height: 22px !important; min-width: 22px !important; border-radius: 9999px !important; background-color: #dc2626 !important; color: white !important; font-family: inherit !important; font-size: 11px !important; font-weight: bold !important; border: none !important; cursor: pointer !important; line-height: 1 !important; transition: background-color 0.2s !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; flex-shrink: 0 !important; outline: none !important; animation: badge-pulse-animation 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;"
                onmouseover="this.style.backgroundColor='#b91c1c'"
                onmouseout="this.style.backgroundColor='#dc2626'">
            <svg style="width: 13px !important; height: 13px !important; fill: none !important; stroke: currentColor !important; stroke-width: 2.5 !important; flex-shrink: 0 !important;" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span style="font-family: inherit !important; font-size: 11px !important; font-weight: 800 !important; line-height: 1 !important;">{{ $count }}</span>
        </button>

        <!-- Modal Overlay -->
        <template x-teleport="body">
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm"
                 style="display: none; z-index: 999999 !important;"
                 @click="open = false"
                 @keydown.escape.window="open = false">
                 
                <!-- Modal Box -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-lg p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl space-y-4 text-left"
                     style="max-width: 480px !important; width: 90% !important; margin: 0 auto !important;"
                     @click.stop>
                     
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-white/10">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Alarma: Descuadre de Horario
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Empleado: <strong class="text-gray-700 dark:text-gray-300">{{ $fichaje->empleado ? ($fichaje->empleado->apellidos . ', ' . $fichaje->empleado->nombre) : 'N/A' }}</strong> &bull; Fecha: <strong class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($fichaje->fecha)->format('d/m/Y') }}</strong>
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none p-1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                        @foreach ($alertas as $alerta)
                            <div class="p-3.5 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/30 rounded-xl text-sm">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                            {{ $alerta->titulo }}
                                        </span>
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300 bg-white/80 dark:bg-gray-800 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">
                                            📅 {{ \Carbon\Carbon::parse($alerta->fecha ?? $fichaje->fecha)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-extrabold text-red-600 dark:text-red-400">
                                        +{{ $alerta->diferencia_minutos }} min diferencia
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-2 bg-white/70 dark:bg-gray-800/60 p-2.5 rounded-lg text-xs border border-red-100 dark:border-red-900/20">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400 block font-medium">Hora indicada:</span>
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $alerta->hora_usuario }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400 block font-medium">Hora real sistema:</span>
                                        <span class="font-mono font-bold text-red-600 dark:text-red-400 text-sm">{{ $alerta->hora_servidor }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-end pt-2 border-t border-gray-100 dark:border-white/10">
                        <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-800 dark:text-gray-200 text-xs font-bold rounded-lg transition-all shadow-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @else
        <div style="position: relative !important; z-index: 30 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 22px !important; height: 22px !important; border-radius: 9999px !important; background-color: #16a34a !important; color: white !important; font-family: inherit !important; font-size: 11px !important; font-weight: bold !important; line-height: 1 !important; select-none: none !important; flex-shrink: 0 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;" title="Horario sincronizado con el sistema">
            ✓
        </div>
    @endif
</div>
