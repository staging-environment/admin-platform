@props([
    'alerts' => [],
    'gasoilData' => null,
    'rbobData' => null,
])

@if(!empty($alerts))
    @php
        $totalChangedStations = 0;
        // Group by locality with changed station names
        $byLocality = [];
        foreach ($alerts as $a) {
            $loc = $a['locality_name'] ?? 'Localidad';
            $fuel = $a['fuel_label'] ?? (($a['fuel_type'] ?? '') === 'diesel' ? 'Diésel' : 'Gasolina 95');
            $count = $a['changed_stations_count'] ?? 1;
            $totalChangedStations += $count;

            $changedNames = [];
            foreach ($a['stations'] ?? [] as $st) {
                if (!empty($st['is_changed'])) {
                    $changedNames[] = $st['name'] ?? 'Estación';
                }
            }

            if (!isset($byLocality[$loc])) {
                $byLocality[$loc] = [];
            }
            $byLocality[$loc][] = [
                'fuel' => $fuel,
                'fuel_type' => $a['fuel_type'] ?? 'diesel',
                'count' => $count,
                'station_names' => $changedNames,
            ];
        }
    @endphp

    <div x-data="{ open: false }" class="mb-3">
        <!-- Banner Global de Alerta por encima de Mercados Energéticos -->
        <div class="rounded-xl p-3 sm:p-3.5 shadow-md flex flex-col md:flex-row md:items-center justify-between text-white transition-all"
             style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); border: 1px solid rgba(239, 68, 68, 0.6); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.28);">
            
            <div class="flex items-start sm:items-center gap-3 mb-2.5 md:mb-0 flex-1 min-w-0">
                <div class="bg-white/20 p-2 rounded-xl flex-shrink-0 flex items-center justify-center animate-pulse shadow-sm">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs font-black uppercase tracking-wider text-white leading-tight">
                            MITECO &bull; Variaci&oacute;n de Precios en Competencia
                        </h3>
                        <span class="bg-black/35 text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white/20">
                            {{ $totalChangedStations }} {{ $totalChangedStations === 1 ? 'cambio detectado' : 'cambios detectados' }}
                        </span>
                    </div>

                    {{-- Gasolineras y localidades con cambios detectados --}}
                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                        <span class="text-[11px] text-red-100 font-semibold flex items-center gap-1">
                            <span>Gasolineras con cambios:</span>
                        </span>
                        @foreach($byLocality as $locName => $items)
                            @php
                                $detailParts = [];
                                foreach ($items as $it) {
                                    $stList = !empty($it['station_names']) ? implode(', ', $it['station_names']) : '';
                                    if ($stList) {
                                        $detailParts[] = "{$stList} ({$it['fuel']})";
                                    } else {
                                        $detailParts[] = ($it['count'] > 1 ? "{$it['count']} en " : '') . $it['fuel'];
                                    }
                                }
                                $desc = implode(' · ', $detailParts);
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/25 text-white text-[11px] font-bold border border-white/20 shadow-sm flex-wrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-300 flex-shrink-0"></span>
                                <span class="font-extrabold text-amber-200">{{ $locName }}:</span>
                                <span class="font-semibold text-white">{{ $desc }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0 self-end md:self-center mt-2 md:mt-0">
                <button type="button" 
                        @click="open = true" 
                        class="inline-flex items-center justify-center px-3.5 py-1.5 bg-white hover:bg-gray-100 text-red-600 rounded-lg font-black shadow hover:shadow-md transition-all text-xs flex-shrink-0 cursor-pointer" 
                        style="color: #dc2626;">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    VER DETALLES
                </button>
                <button type="button" 
                        @click="if (confirm('¿Deseas desactivar y ocultar todos los avisos de alerta actuales?')) { window.dismissCompetitorAlerts ? window.dismissCompetitorAlerts() : null; open = false; }"
                        title="Desactivar avisos de alerta manualmente"
                        class="inline-flex items-center justify-center px-3 py-1.5 bg-red-950/40 hover:bg-red-950/70 text-red-100 hover:text-white border border-red-300/40 rounded-lg font-bold shadow-sm transition-all text-xs flex-shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5 mr-1 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    DESACTIVAR
                </button>
            </div>
        </div>

        <!-- Modal Flotante de Cambios en Competencia (Todas las localidades) -->
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
                                <h3 class="text-sm font-bold text-white leading-tight flex items-center gap-2">
                                    <span>Variaciones de Precios Detectadas por MITECO</span>
                                    <span class="text-xs font-normal text-gray-300">&bull; Todas las localidades</span>
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Aviso sincronizado con Telegram &bull; Detecci&oacute;n autom&aacute;tica de MITECO &bull; Visible durante 3 horas
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

                            <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm" style="border: 1.5px solid {{ $isDiesel ? '#374151' : '#16a34a' }} !important;">
                                <!-- Cabecera de la alerta concreta -->
                                <div class="px-4 py-2.5 bg-gray-100/90 dark:bg-gray-700/60 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1 font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wider">
                                            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                            </svg>
                                            {{ $alert['locality_name'] ?? 'Localidad' }}
                                        </div>

                                        @if($isDiesel)
                                            <span style="background-color: #111827 !important; color: #ffffff !important; padding: 3px 10px !important; border-radius: 9999px !important; font-size: 10px !important; font-weight: 900 !important; display: inline-flex !important; align-items: center !important; gap: 6px !important; letter-spacing: 0.04em !important; box-shadow: 0 1px 3px rgba(0,0,0,0.35) !important;">
                                                <span style="width: 7px !important; height: 7px !important; border-radius: 9999px !important; background-color: #f59e0b !important; display: inline-block !important; flex-shrink: 0 !important;"></span>
                                                CAMBIO EN DI&Eacute;SEL
                                            </span>
                                        @else
                                            <span style="background-color: #15803d !important; color: #ffffff !important; padding: 3px 10px !important; border-radius: 9999px !important; font-size: 10px !important; font-weight: 900 !important; display: inline-flex !important; align-items: center !important; gap: 6px !important; letter-spacing: 0.04em !important; box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;">
                                                <span style="width: 7px !important; height: 7px !important; border-radius: 9999px !important; background-color: #ffffff !important; display: inline-block !important; flex-shrink: 0 !important;"></span>
                                                CAMBIO EN GASOLINA 95
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 text-[11px] font-medium text-gray-500 dark:text-gray-400 tabular-nums">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $alert['formatted_time'] ?? '' }} ({{ $timeAgo }})
                                    </div>
                                </div>

                                {{-- Sugerencia Estratégica de Precio --}}
                                @include('filament.components.competitor-price-suggestion', [
                                    'alert' => $alert,
                                    'gasoilData' => $gasoilData,
                                    'rbobData' => $rbobData,
                                ])

                                <!-- Listado de estaciones TOP 5 de la localidad -->
                                <div class="p-3 divide-y divide-gray-100 dark:divide-gray-700/50 space-y-2">
                                    @foreach ($alert['stations'] ?? [] as $station)
                                        @php
                                            $changed = $station['is_changed'] ?? false;
                                            $direction = $station['direction'] ?? null;
                                        @endphp

                                        <div class="pt-2 first:pt-0 flex items-center justify-between gap-3 text-xs {{ $changed ? 'p-2 rounded-lg bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/30' : '' }}">
                                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                                <!-- Chip numerico con color explicito de combustible -->
                                                <span style="background-color: {{ $isDiesel ? '#1f2937' : '#15803d' }} !important; color: #ffffff !important; width: 22px !important; height: 22px !important; min-width: 22px !important; border-radius: 9999px !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 11px !important; font-weight: 900 !important; flex-shrink: 0 !important; line-height: 1 !important;">
                                                    {{ $station['rank'] ?? '&bull;' }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-bold truncate text-gray-900 dark:text-white text-xs">
                                                        {{ $station['name'] ?? 'Estaci&oacute;n' }}
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        @if(!empty($station['address']))
                                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                                {{ $station['address'] }}
                                                            </p>
                                                        @endif
                                                        <span style="font-size: 9px !important; font-weight: 700 !important; color: {{ $isDiesel ? '#4b5563' : '#15803d' }} !important; text-transform: uppercase !important;">
                                                            {!! $isDiesel ? 'Di&eacute;sel' : 'Gasolina 95' !!}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right flex-shrink-0">
                                                @if ($changed)
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        <span class="text-[10px] line-through text-gray-400 tabular-nums">
                                                            {{ number_format($station['old_price'], 3, ',', '.') }}&nbsp;&euro;
                                                        </span>
                                                        <span class="text-xs font-black text-gray-900 dark:text-white tabular-nums">
                                                            {{ number_format($station['price'], 3, ',', '.') }}&nbsp;&euro;
                                                        </span>
                                                    </div>
                                                    <div class="mt-0.5">
                                                        @if ($direction === 'sube')
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 px-1.5 py-0.5 rounded">
                                                                <svg class="w-2.5 h-2.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                                                Sube {{ $station['diff_text'] ?? '' }}&nbsp;&euro;
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600 bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800/50 px-1.5 py-0.5 rounded">
                                                                <svg class="w-2.5 h-2.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                                                Baja {{ $station['diff_text'] ?? '' }}&nbsp;&euro;
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                                        {{ number_format($station['price'], 3, ',', '.') }}&nbsp;&euro;
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
                    <div class="px-5 py-3 bg-gray-100 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                            Esta alerta desaparecer&aacute; autom&aacute;ticamente tras 3 horas de la detecci&oacute;n de MITECO.
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="if (confirm('¿Deseas desactivar y ocultar todos los avisos de alerta actuales?')) { window.dismissCompetitorAlerts ? window.dismissCompetitorAlerts() : null; open = false; }" 
                                    class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Desactivar alertas
                            </button>
                            <button type="button" @click="open = false" class="px-4 py-1.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-all shadow-sm cursor-pointer">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endif