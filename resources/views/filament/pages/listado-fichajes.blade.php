<x-filament-panels::page>
    <div class="space-y-6">
            <!-- Admin Dashboard: List of all employee check-ins / Vacations / Absences -->
            <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-amber-500/10 text-amber-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </span>
                        Control General de Fichajes de Empleados
                    </h3>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="exportPdf" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 active:bg-red-800 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 cursor-pointer" title="Exportar todos los resultados coincidentes a PDF">
                            <svg wire:loading.remove wire:target="exportPdf" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <svg wire:loading wire:target="exportPdf" class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Exportar PDF</span>
                        </button>
                        <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold whitespace-nowrap">
                            {{ $todosLosFichajes instanceof \Illuminate\Pagination\LengthAwarePaginator ? $todosLosFichajes->total() : count($todosLosFichajes) }} registros totales
                        </span>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 dark:bg-gray-950/20 p-4 rounded-2xl border border-gray-100 dark:border-white/5">
                    <!-- Date Filter "Desde" -->
                    <div>
                        <label for="filterDateFrom" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Desde</label>
                        <div class="relative">
                            <input type="date" id="filterDateFrom" wire:model.live="filterDateFrom" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3" />
                            @if($filterDateFrom)
                                <button type="button" wire:click="$set('filterDateFrom', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Date Filter "Hasta" -->
                    <div>
                        <label for="filterDateTo" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Hasta</label>
                        <div class="relative">
                            <input type="date" id="filterDateTo" wire:model.live="filterDateTo" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3" />
                            @if($filterDateTo)
                                <button type="button" wire:click="$set('filterDateTo', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Search Filter -->
                    <div>
                        <label for="filterSearch" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Buscar Empleado</label>
                        <div class="relative">
                            <input type="text" id="filterSearch" wire:model.live.debounce.300ms="filterSearch" placeholder="Ej. José, Bonilla, empleado@utrecar.com..." class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3 pr-10" />
                            @if($filterSearch)
                                <button type="button" wire:click="$set('filterSearch', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="py-2 px-4 cursor-pointer select-none hover:text-amber-600 transition-colors" wire:click="sortBy('apellidos')">
                                        <div class="flex items-center gap-1">
                                            <span>Apellidos</span>
                                            @if($sortField === 'apellidos')
                                                <span class="text-amber-600 font-bold">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="py-2 px-4 cursor-pointer select-none hover:text-amber-600 transition-colors" wire:click="sortBy('nombre')">
                                        <div class="flex items-center gap-1">
                                            <span>Nombre</span>
                                            @if($sortField === 'nombre')
                                                <span class="text-amber-600 font-bold">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="py-2 px-4">Ubicación de trabajo</th>
                                    <th class="py-2 px-4 cursor-pointer select-none hover:text-amber-600 transition-colors" wire:click="sortBy('fecha')">
                                        <div class="flex items-center gap-1">
                                            <span>Fecha</span>
                                            @if($sortField === 'fecha')
                                                <span class="text-amber-600 font-bold">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="py-2 px-4">Hora Entrada</th>
                                    <th class="py-2 px-4">Hora Salida</th>
                                    <th class="py-2 px-4">Tiempo Total</th>
                                    <th class="py-2 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                                @forelse($todosLosFichajes as $fichaje)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-2 px-4 font-bold text-gray-900 dark:text-white uppercase text-xs">
                                            {{ $fichaje->empleado ? mb_strtoupper($fichaje->empleado->apellidos ?? '') : 'N/A' }}
                                        </td>
                                        <td class="py-2 px-4 font-bold text-gray-900 dark:text-white uppercase text-xs">
                                            {{ $fichaje->empleado ? mb_strtoupper($fichaje->empleado->nombre ?? '') : '—' }}
                                        </td>
                                        <td class="py-2 px-4 text-xs text-gray-600 dark:text-gray-400 font-medium uppercase">
                                            {{ $fichaje->empleado?->gasolinera?->Nombre ?? '—' }}
                                        </td>
                                        <td class="py-2 px-4 text-gray-700 dark:text-gray-300 text-xs">
                                            <div class="flex flex-col">
                                                <span>{{ \Carbon\Carbon::parse($fichaje->fecha)->format('d/m/Y') }}</span>
                                                <div class="flex flex-wrap gap-1 mt-1 font-normal text-[10px]">
                                                    @if($fichaje->is_retroactive)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                            Retroactivo
                                                        </span>
                                                    @endif
                                                    @if($fichaje->is_edited)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded font-semibold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400" title="Modificado por {{ $fichaje->edited_by_email }}. Entrada original: {{ $fichaje->original_hora_entrada ? \Carbon\Carbon::parse($fichaje->original_hora_entrada)->format('H:i') : '-' }}, Salida original: {{ $fichaje->original_hora_salida ? \Carbon\Carbon::parse($fichaje->original_hora_salida)->format('H:i') : '-' }}">
                                                            Modificado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
﻿                                        <td class="py-2 px-4 text-gray-600 dark:text-gray-400">
                                            @php
                                                $realCheckin = $fichaje->server_checkin_at
                                                    ? $fichaje->server_checkin_at->timezone("Europe/Madrid")->format("d/m/Y H:i:s")
                                                    : ($fichaje->fecha && $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->fecha . " " . $fichaje->hora_entrada)->format("d/m/Y H:i:s") : null);
                                                $tooltipEntrada = $realCheckin ? "Real: " . $realCheckin : null;
                                            @endphp
                                            <div class="flex items-center gap-1.5">
                                                <span 
                                                    class="inline-flex items-center px-2 py-0.5 rounded bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 font-mono text-xs font-bold w-fit cursor-help transition-all hover:bg-green-100 dark:hover:bg-green-900/50"
                                                    @if($tooltipEntrada)
                                                        x-tooltip="{ content: @js($tooltipEntrada), theme: $store.theme }"
                                                        title="{{ $tooltipEntrada }}"
                                                    @endif
                                                >
                                                    {{ $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->hora_entrada)->format("H:i") : "-" }}
                                                </span>
                                                @if($fichaje->checkin_latitude && $fichaje->checkin_longitude)
                                                    <a href="https://www.google.com/maps?q={{ $fichaje->checkin_latitude }},{{ $fichaje->checkin_longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-semibold text-[10px] transition-colors" title="Ver ubicación en Google Maps ({{ $fichaje->checkin_latitude }}, {{ $fichaje->checkin_longitude }})">
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                        Mapa
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-gray-600 dark:text-gray-400">
                                            @if($fichaje->hora_salida)
                                                @php
                                                    $realCheckout = $fichaje->server_checkout_at
                                                        ? $fichaje->server_checkout_at->timezone("Europe/Madrid")->format("d/m/Y H:i:s")
                                                        : ($fichaje->fecha && $fichaje->hora_salida ? \Carbon\Carbon::parse($fichaje->fecha . " " . $fichaje->hora_salida)->format("d/m/Y H:i:s") : null);
                                                    $tooltipSalida = $realCheckout ? "Real: " . $realCheckout : null;
                                                @endphp
                                                <div class="flex items-center gap-1.5">
                                                    <span 
                                                        class="inline-flex items-center px-2 py-0.5 rounded bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 font-mono text-xs font-bold w-fit cursor-help transition-all hover:bg-orange-100 dark:hover:bg-orange-900/50"
                                                        @if($tooltipSalida)
                                                            x-tooltip="{ content: @js($tooltipSalida), theme: $store.theme }"
                                                            title="{{ $tooltipSalida }}"
                                                        @endif
                                                    >
                                                        {{ \Carbon\Carbon::parse($fichaje->hora_salida)->format("H:i") }}
                                                    </span>
                                                    @if($fichaje->checkout_latitude && $fichaje->checkout_longitude)
                                                        <a href="https://www.google.com/maps?q={{ $fichaje->checkout_latitude }},{{ $fichaje->checkout_longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-semibold text-[10px] transition-colors" title="Ver ubicación en Google Maps ({{ $fichaje->checkout_latitude }}, {{ $fichaje->checkout_longitude }})">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            Mapa
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 font-medium text-xs font-bold">
                                                    En curso
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 font-bold text-gray-700 dark:text-gray-300 text-xs">
                                            @php
                                                if ($fichaje->hora_salida) {
                                                    $t1 = \Carbon\Carbon::parse($fichaje->hora_entrada);
                                                    $t2 = \Carbon\Carbon::parse($fichaje->hora_salida);
                                                    $diff = $t1->diff($t2);
                                                    echo $diff->format('%h h %i m');
                                                } else {
                                                    echo '-';
                                                }
                                            @endphp
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <a href="/admin/portal-empleado?empleado_id={{ $fichaje->empleado_id }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Fichajes
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                            No hay registros de fichajes en el sistema.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($todosLosFichajes instanceof \Illuminate\Pagination\LengthAwarePaginator && $todosLosFichajes->hasPages())
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                            {{ $todosLosFichajes->links() }}
                        </div>
                    @endif
            </div>

    </div>
</x-filament-panels::page>
