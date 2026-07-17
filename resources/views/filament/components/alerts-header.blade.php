<div x-data="{ open: false }" class="inline-block">
    <div class="flex items-center gap-3">
        @php
            $count = $record ? $record->alertas()->count() : 0;
            $alertas = $record ? $record->alertas : collect();
        @endphp

        @if ($count > 0)
            <button type="button" @click.stop="open = true" class="flex items-center justify-center w-6 h-6 rounded-full bg-red-600 hover:bg-red-700 text-white text-xs font-bold focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all shadow-sm shrink-0">
                {{ $count }}
            </button>
        @else
            <div class="flex items-center justify-center w-6 h-6 rounded-full bg-green-600 text-white text-[10px] font-bold shadow-sm shrink-0">
                ✓
            </div>
        @endif
        
        <span class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">Datos Personales del Trabajador</span>
    </div>

    <!-- Modal Overlay -->
    <template x-teleport="body">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm"
             style="display: none;"
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
                 @click.stop>
                 
                <div class="flex justify-between items-center pb-2 border-b border-gray-100 dark:border-white/10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Alertas Activas del Trabajador
                    </h3>
                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                    @foreach ($alertas as $alerta)
                        <div class="flex gap-3 p-3 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800/30 rounded-xl text-sm">
                            <div class="text-red-600 dark:text-red-400 mt-0.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-red-800 dark:text-red-400">{{ $alerta->titulo }}</h4>
                                <p class="text-red-700 dark:text-red-300 text-xs mt-1">{{ $alerta->descripcion }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-800 dark:text-gray-200 text-xs font-bold rounded-lg transition-all shadow-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
