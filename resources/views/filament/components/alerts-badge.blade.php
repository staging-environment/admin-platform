<div x-data="{ open: false }" class="inline-block">
    @php
        $count = $record ? $record->alertas()->count() : 0;
        $alertas = $record ? $record->alertas : collect();
    @endphp

    @if ($count > 0)
        <button type="button" 
                @click.stop="open = true" 
                onclick="event.stopPropagation()"
                style="position: relative !important; z-index: 30 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; padding: 0 10px !important; height: 24px !important; min-width: 24px !important; border-radius: 9999px !important; background-color: #dc2626 !important; color: white !important; font-family: inherit !important; font-size: 12px !important; font-weight: bold !important; border: none !important; cursor: pointer !important; line-height: 1 !important; transition: background-color 0.2s !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; flex-shrink: 0 !important; outline: none !important;"
                onmouseover="this.style.backgroundColor='#b91c1c'"
                onmouseout="this.style.backgroundColor='#dc2626'">
            <svg style="width: 14px !important; height: 14px !important; fill: none !important; stroke: currentColor !important; stroke-width: 2.5 !important; flex-shrink: 0 !important;" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span style="font-family: inherit !important; font-size: 12px !important; font-weight: 800 !important; line-height: 1 !important;">{{ $count }}</span>
        </button>
    @else
        <div style="position: relative !important; z-index: 30 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 24px !important; height: 24px !important; border-radius: 9999px !important; background-color: #16a34a !important; color: white !important; font-family: inherit !important; font-size: 12px !important; font-weight: bold !important; line-height: 1 !important; select-none: none !important; flex-shrink: 0 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;">
            ✓
        </div>
    @endif

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
