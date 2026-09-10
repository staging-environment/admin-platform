<style>
    @keyframes alert-pulse-animation {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.97); }
    }
</style>

<div x-data="{ open: false }" class="inline-flex items-center">
    @php
        $totalAlerts = count($alerts);
        $totalChanged = 0;
        foreach ($alerts as $a) {
            $totalChanged += ($a['changed_stations_count'] ?? 1);
        }
        $countDisplay = $totalChanged > 0 ? $totalChanged : $totalAlerts;
    @endphp

    @if ($totalAlerts > 0)
        <!-- Boton pulsante de alerta identico al de empleados -->
        <button type="button" 
                @click.stop="open = true" 
                style="position: relative !important; z-index: 20 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; padding: 0 10px !important; height: 24px !important; border-radius: 9999px !important; background-color: #dc2626 !important; color: white !important; font-family: inherit !important; font-size: 11px !important; font-weight: bold !important; border: none !important; cursor: pointer !important; line-height: 1 !important; transition: all 0.2s !important; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3) !important; flex-shrink: 0 !important; outline: none !important; animation: alert-pulse-animation 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;"
                onmouseover="this.style.backgroundColor='#b91c1c'"
                onmouseout="this.style.backgroundColor='#dc2626'"
                title="Cambios de precio detectados en la competencia (?ltimas 2 horas)">
            <svg style="width: 14px !important; height: 14px !important; fill: none !important; stroke: currentColor !important; stroke-width: 2.5 !important; flex-shrink: 0 !important;" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span style="font-family: inherit !important; font-size: 11px !important; font-weight: 800 !important; line-height: 1 !important; white-space: nowrap !important;">
                {{ $countDisplay }} {{ $countDisplay === 1 ? 'cambio detectado' : 'cambios detectados' }}
            </span>
        </button>

        <!-- Modal Flotante de Cambios en Competencia -->
        <template x-teleport="body">
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[999999] flex items-center justify-center p-3 sm:p-4 bg-gray-950/60 backdrop-blur-sm"
                 style="display: none; z-index: 999999 !important;"
                 @click="open = false"
                 @keydown.escape.window="open = false">
                 
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden flex flex-col text-left"
                     style="max-width: 680px !important; width: 95% !important; margin: 0 auto !important;"
                     @click.stop>
                     
                    <!-- Cabecera del Modal -->
                    <div class="flex justify-between items-center px-5 py-3.5 bg-gray-900 text-white border-b border-gray-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-red-500/20 border border-red-500/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white leading-tight">
                                    Variaciones de Precios en la Competencia
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Aviso sincronizado con Telegram ? Visible durante las ?ltimas 2 horas
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-white transition-colors p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Cuerpo con listado de alertas -->
                    <div class="p-4 sm:p-5 space-y-4 max-h-[70vh] overflow-y-auto bg-gray-50/50 dark:bg-gray-900/50">
                        @foreach ($alerts as $alert)
                            @php
                                $isDiesel = ($alert['fuel_type'] ?? '') === 'diesel';
                                $timeAgo = isset($alert['created_at']) ? \Carbon\Carbon::createFromTimestamp($alert['created_at'])->diffForHumans() : '';
                            @endphp

                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                                <!-- Cabecera de la alerta concreta -->
                                <div class="px-4 py-2.5 bg-gray-100/80 dark:bg-gray-700/50 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-1">
                                            ?? {{ $alert['locality_name'] ?? 'Localidad' }}
                                        </span>
                                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded {{ $isDiesel ? 'bg-gray-900 text-white' : 'bg-green-600 text-white' }}">
                                            {{ $alert['fuel_label'] ?? ($isDiesel ? 'DI?SEL' : 'GASOLINA 95') }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 tabular-nums">
                                        ?? {{ $alert['formatted_time'] ?? '' }} ({{ $timeAgo }})
                                    </span>
                                </div>

                                <!-- Listado de estaciones TOP 5 de la localidad -->
                                <div class="p-3 divide-y divide-gray-100 dark:divide-gray-700/50 space-y-2">
                                    @foreach ($alert['stations'] ?? [] as $station)
                                        @php
                                            $changed = $station['is_changed'] ?? false;
                                            $direction = $station['direction'] ?? null;
                                        @endphp

                                        <div class="pt-2 first:pt-0 flex items-center justify-between gap-3 text-xs {{ $changed ? 'p-2 rounded-lg bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/30' : '' }}">
                                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0 {{ $isDiesel ? 'bg-gray-900 text-white' : 'bg-green-600 text-white' }}">
                                                    {{ $station['rank'] ?? '?' }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-bold truncate text-gray-900 dark:text-white text-xs">
                                                        {{ $station['name'] ?? 'Estaci?n' }}
                                                    </p>
                                                    @if(!empty($station['address']))
                                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                            {{ $station['address'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-right flex-shrink-0">
                                                @if ($changed)
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        <span class="text-[10px] line-through text-gray-400 tabular-nums">
                                                            {{ number_format($station['old_price'], 3, ',', '.') }}&nbsp;?
                                                        </span>
                                                        <span class="text-xs font-black text-gray-900 dark:text-white tabular-nums">
                                                            {{ number_format($station['price'], 3, ',', '.') }}&nbsp;?
                                                        </span>
                                                    </div>
                                                    <div class="mt-0.5">
                                                        @if ($direction === 'sube')
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-red-600 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 px-1.5 py-0.2 rounded">
                                                                ?? Sube {{ $station['diff_text'] ?? '' }}&nbsp;?
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-green-600 bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800/50 px-1.5 py-0.2 rounded">
                                                                ?? Baja {{ $station['diff_text'] ?? '' }}&nbsp;?
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                                        {{ number_format($station['price'], 3, ',', '.') }}&nbsp;?
                                                    </span>
                                                    <p class="text-[9px] text-gray-400">Sin cambios</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pie del modal -->
                    <div class="px-5 py-3 bg-gray-100 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                            ?? Esta alarma desaparecer? autom?ticamente tras 2 horas.
                        </span>
                        <button type="button" @click="open = false" class="px-4 py-1.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
