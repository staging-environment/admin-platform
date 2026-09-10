<x-filament-panels::page>
    <div class="space-y-3.5">

        <!-- Top Bar: Calendario vacaciones -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 p-2.5 sm:px-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-2xl shadow-xs">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div>
                    <h4 class="text-xs sm:text-[13px] font-bold text-gray-900 dark:text-white">Calendario General de Vacaciones</h4>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Consulta anual y mensual de todas las solicitudes aprobadas, pendientes y denegadas.</p>
                </div>
            </div>
            <button type="button" wire:click="openCalendarioAnual" class="inline-flex items-center justify-center gap-2 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl text-xs font-bold transition-all shadow-sm shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Calendario vacaciones</span>
            </button>
        </div>

        <!-- Vacations Section -->
        <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between pb-2.5 border-b border-gray-50 dark:border-white/5 mb-3">
                <h3 class="text-sm sm:text-[15px] font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 bg-sky-500/10 text-sky-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Solicitudes Pendientes
                </h3>
                <span class="px-2.5 py-0.5 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                    {{ count($this->vacacionesPendientes) }} pendientes
                </span>
            </div>

            <!-- Pendientes Filters -->
            <div class="mb-3 p-2.5 bg-gray-50 dark:bg-gray-950/40 border border-gray-100 dark:border-white/5 rounded-xl flex flex-wrap items-center gap-2.5">
                <div class="w-64 max-w-xs">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Empleado</label>
                    <input type="text" list="dl-pendiente-empleado" wire:model.live.debounce.300ms="filter_pendiente_empleado" placeholder="Escribe para buscar empleado..." class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500" />
                    <datalist id="dl-pendiente-empleado">
                        @foreach($this->empleados as $emp)
                            <option value="{{ $emp->nombre }} {{ $emp->apellidos }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="w-40 min-w-[130px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo</label>
                    <select wire:model.live="filter_pendiente_tipo" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos los tipos</option>
                        @foreach($this->tipos as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-36 min-w-[120px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Mes</label>
                    <select wire:model.live="filter_pendiente_mes" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos los meses</option>
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>

                <div class="w-32 min-w-[100px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Año</label>
                    <select wire:model.live="filter_pendiente_anio" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                @if($filter_pendiente_empleado || $filter_pendiente_tipo || $filter_pendiente_mes || $filter_pendiente_anio)
                <div class="self-end">
                    <button type="button" wire:click="resetPendienteFilters" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold hover:bg-gray-300 transition-all">
                        Limpiar Filtros
                    </button>
                </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/5 text-[10.5px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-2 px-3.5">Empleado</th>
                            <th class="py-2 px-3.5">Tipo</th>
                            <th class="py-2 px-3.5">Fechas</th>
                            <th class="py-2 px-3.5">Días</th>
                            <th class="py-2 px-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-xs">
                        @forelse($this->vacacionesPendientes as $vac)
                            <tr>
                                <td class="py-2.5 px-3.5 font-semibold text-gray-900 dark:text-white text-xs">
                                    {{ $vac->empleado ? $vac->empleado->nombre . ' ' . $vac->empleado->apellidos : 'N/A' }}
                                </td>
                                <td class="py-2.5 px-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400">
                                        {{ $vac->tipo }}
                                    </span>
                                    @if($vac->justificante_path)
                                        <div class="mt-0.5">
                                            <a href="{{ route('admin.recursos_humanos.descargar_archivo', ['path' => $vac->justificante_path]) }}" class="text-[10.5px] text-amber-600 dark:text-amber-400 font-bold hover:underline inline-flex items-center gap-1" target="_blank">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Ver Justificante
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3.5 text-gray-500 dark:text-gray-400 text-xs">
                                    Del {{ \Carbon\Carbon::parse($vac->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vac->fecha_fin)->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3.5 font-bold text-gray-700 dark:text-gray-300 text-xs">
                                    {{ $vac->dias_solicitados }}
                                </td>
                                <td class="py-2.5 px-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" wire:click="iniciarAprobacion({{ $vac->id }})" style="background-color: #16a34a; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-green-700">
                                            Aprobar
                                        </button>
                                         <button type="button" wire:click="iniciarDenegacion({{ $vac->id }}, 'vacacion')" style="background-color: #dc2626; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-red-700">
                                             Denegar
                                         </button>
                                     </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                    No hay solicitudes de vacaciones pendientes de aprobación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Processed Requests History Section -->
        <div class="p-4 sm:p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between pb-2.5 border-b border-gray-50 dark:border-white/5 mb-3">
                <h3 class="text-sm sm:text-[15px] font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 bg-indigo-500/10 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </span>
                    Historial de Solicitudes Procesadas
                </h3>
                <span class="px-2.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 rounded-full text-xs font-bold">
                    {{ $this->historicoProcesadas->total() }} procesadas
                </span>
            </div>

            <!-- Histórico Filters -->
            <div class="mb-3 p-2.5 bg-gray-50 dark:bg-gray-950/40 border border-gray-100 dark:border-white/5 rounded-xl flex flex-wrap items-center gap-2.5">
                <div class="w-64 max-w-xs">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Empleado</label>
                    <input type="text" list="dl-historico-empleado" wire:model.live.debounce.300ms="filter_historico_empleado" placeholder="Escribe para buscar empleado..." class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500" />
                    <datalist id="dl-historico-empleado">
                        @foreach($this->empleados as $emp)
                            <option value="{{ $emp->nombre }} {{ $emp->apellidos }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="w-36 min-w-[120px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo</label>
                    <select wire:model.live="filter_historico_tipo" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos los tipos</option>
                        @foreach($this->tipos as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-36 min-w-[120px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Estado</label>
                    <select wire:model.live="filter_historico_estado" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos los estados</option>
                        <option value="Aceptada">Aprobada</option>
                        <option value="Rechazada">Denegada</option>
                    </select>
                </div>

                <div class="w-36 min-w-[120px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Mes</label>
                    <select wire:model.live="filter_historico_mes" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos los meses</option>
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>

                <div class="w-32 min-w-[100px]">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Año</label>
                    <select wire:model.live="filter_historico_anio" class="w-full text-xs rounded-xl border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-sm py-1.5 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                @if($filter_historico_empleado || $filter_historico_tipo || $filter_historico_estado || $filter_historico_mes || $filter_historico_anio)
                <div class="self-end">
                    <button type="button" wire:click="resetHistoricoFilters" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-bold hover:bg-gray-300 transition-all">
                        Limpiar Filtros
                    </button>
                </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/5 text-[10.5px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-2 px-3.5">Empleado</th>
                            <th class="py-2 px-3.5">Tipo</th>
                            <th class="py-2 px-3.5">Fechas</th>
                            <th class="py-2 px-3.5">Estado</th>
                            <th class="py-2 px-3.5">Resolución</th>
                            <th class="py-2 px-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-xs">
                        @forelse($this->historicoProcesadas as $record)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-2.5 px-3.5 font-semibold text-gray-900 dark:text-white text-xs">
                                    {{ $record->empleado ? $record->empleado->nombre . ' ' . $record->empleado->apellidos : 'N/A' }}
                                </td>
                                <td class="py-2.5 px-3.5 text-gray-600 dark:text-gray-400 font-medium text-xs">
                                    @if(isset($record->dias_solicitados))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400 font-bold text-[9.5px] uppercase">
                                            Vacaciones
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 font-bold text-[9.5px] uppercase">
                                            Baja Médica
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3.5 text-gray-700 dark:text-gray-300 font-mono text-xs">
                                    @if(isset($record->dias_solicitados))
                                        {{ \Carbon\Carbon::parse($record->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($record->fecha_fin)->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($record->fecha_inicio)->format('d/m/Y') }} @if($record->fecha_fin) - {{ \Carbon\Carbon::parse($record->fecha_fin)->format('d/m/Y') }} @else (Indefinida) @endif
                                    @endif
                                </td>
                                <td class="py-2.5 px-3.5">
                                    @if($record->estado === 'Aceptada')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 text-[11px] font-bold">
                                            Aprobada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 text-[11px] font-bold">
                                            Denegada
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3.5 text-gray-500 dark:text-gray-400 text-xs font-medium">
                                    Resuelto: {{ \Carbon\Carbon::parse($record->updated_at)->translatedFormat('d \d\e F \d\e Y H:i') }}
                                </td>
                                <td class="py-2.5 px-3.5 text-right">
                                    <button type="button" wire:click="verDetalles({{ $record->id }}, '{{ isset($record->dias_solicitados) ? 'vacacion' : 'baja' }}')" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Ver Detalles
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                    No hay solicitudes resueltas en el historial.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                {{ $this->historicoProcesadas->links() }}
            </div>
        </div>
    </div>

    @if($selectedDocUrl)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="closeDocument">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Justificante Médico</h3>
                <button type="button" wire:click="closeDocument" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6 overflow-y-auto flex-grow flex items-center justify-center bg-gray-50 dark:bg-gray-950/30">
                @if($selectedDocType === 'pdf')
                    <iframe src="{{ $selectedDocUrl }}" class="w-full h-[70vh] rounded-2xl border-0 bg-white shadow-inner"></iframe>
                @else
                    <img src="{{ $selectedDocUrl }}" class="max-w-full max-h-[70vh] object-contain rounded-2xl shadow-md border border-gray-200 dark:border-white/5" />
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Confirm Approval Modal -->
    @if($approvingVacacion)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="cancelarAprobacion">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all border border-gray-100 dark:border-white/5">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 bg-green-500/10 text-green-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    Aprobar Solicitud de Vacaciones
                </h3>
                <button type="button" wire:click="cancelarAprobacion" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6 space-y-3">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    ¿Estás seguro de que deseas aprobar las vacaciones de <strong>{{ $approvingVacacion->empleado ? $approvingVacacion->empleado->nombre . ' ' . $approvingVacacion->empleado->apellidos : 'Empleado' }}</strong>?
                </p>
                <div class="p-4 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/30 rounded-2xl text-xs text-green-900 dark:text-green-300 space-y-1">
                    <div><strong>Período:</strong> Del {{ \Carbon\Carbon::parse($approvingVacacion->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($approvingVacacion->fecha_fin)->format('d/m/Y') }}</div>
                    <div><strong>Días solicitados:</strong> {{ $approvingVacacion->dias_solicitados }} días</div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-950/20 border-t border-gray-100 dark:border-white/5 flex items-center justify-end gap-3">
                <button type="button" wire:click="cancelarAprobacion" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                    Cancelar
                </button>
                <button type="button" wire:click="confirmarAprobacion" style="background-color: #16a34a; color: #ffffff;" class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-green-700">
                    Confirmar Aprobación
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Denial Reason Modal -->
    @if($denyingId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="cancelarDenegacion">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Denegar Solicitud</h3>
                <button type="button" wire:click="cancelarDenegacion" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Por favor, introduce el motivo de la denegación de la solicitud. Este motivo se guardará en el registro y se le enviará por correo electrónico al empleado.
                </p>
                <div>
                    <label for="motivoDenegacion" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Motivo de Denegación</label>
                    <textarea id="motivoDenegacion" wire:model="motivoDenegacion" rows="4" placeholder="Escribe el motivo aquí..." class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm py-2 px-3"></textarea>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-950/20 border-t border-gray-100 dark:border-white/5 flex items-center justify-end gap-3">
                <button type="button" wire:click="cancelarDenegacion" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                    Cancelar
                </button>
                <button type="button" wire:click="confirmarDenegacion" style="background-color: #dc2626; color: #ffffff;" class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-red-700">
                    Confirmar Denegación
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Request Details Modal -->
    @if($viewingRecord)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="cerrarDetalles">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl w-full max-w-xl overflow-hidden transform transition-all border border-gray-100 dark:border-white/5">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    Detalles de la Solicitud
                </h3>
                <button type="button" wire:click="cerrarDetalles" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6 space-y-5">
                <!-- Status Row -->
                <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 dark:bg-gray-950/20 border border-gray-100 dark:border-white/5">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Estado de la Solicitud</span>
                    @if($viewingRecord->estado === 'Aceptada')
                        <span class="px-3 py-1 bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                            Aprobada
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 rounded-full text-xs font-bold">
                            Denegada
                        </span>
                    @endif
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Empleado</span>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $viewingRecord->empleado ? $viewingRecord->empleado->nombre . ' ' . $viewingRecord->empleado->apellidos : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo de Solicitud</span>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $viewingType === 'vacacion' ? 'Vacaciones (' . ($viewingRecord->tipo ?? 'Normal') . ')' : 'Baja Médica / Ausencia' }}
                        </p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha de Inicio</span>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">
                            {{ \Carbon\Carbon::parse($viewingRecord->fecha_inicio)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha Fin / Prevista</span>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">
                            {{ $viewingRecord->fecha_fin ? \Carbon\Carbon::parse($viewingRecord->fecha_fin)->format('d/m/Y') : 'No definida' }}
                        </p>
                    </div>
                    @if($viewingType === 'vacacion')
                        <div>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Días Solicitados</span>
                            <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 font-mono">
                                {{ $viewingRecord->dias_solicitados }}
                            </p>
                        </div>
                    @endif
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha de Resolución</span>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ \Carbon\Carbon::parse($viewingRecord->updated_at)->translatedFormat('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                @if($viewingRecord->comentario_empleado)
                    <div class="p-3 bg-gray-50 dark:bg-gray-950/20 border border-gray-100 dark:border-white/5 rounded-2xl">
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Explicación del Empleado</span>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $viewingRecord->comentario_empleado }}
                        </p>
                    </div>
                @endif

                @if($viewingRecord->comentario_aprobador)
                    <div class="p-3 bg-amber-500/5 border border-amber-500/20 rounded-2xl">
                        <span class="block text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Razón / Comentario del Aprobador</span>
                        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
                            {{ $viewingRecord->comentario_aprobador }}
                        </p>
                    </div>
                @endif

                @if($viewingRecord->justificante_path)
                    <div class="p-3 bg-indigo-500/5 border border-indigo-500/20 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">Documento Justificante</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Hay un archivo justificante adjunto.</span>
                        </div>
                        <button type="button" wire:click="showDocument('{{ $viewingRecord->justificante_path }}')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all">
                            Ver Justificante
                        </button>
                    </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-950/20 border-t border-gray-100 dark:border-white/5 flex items-center justify-end">
                <button type="button" wire:click="cerrarDetalles" style="background-color: #4f46e5; color: #ffffff;" class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-indigo-700">
                    Cerrar Detalles
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Calendario Mensual de Solicitudes (Flotante) -->
    @if($showCalendarioModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" wire:click.self="closeCalendarioAnual">
        <div style="width: min(1300px, 96vw); height: min(820px, 88vh);" class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-100 dark:border-white/10 my-auto">
            
            <!-- Modal Header & Navigation Bar -->
            <div class="shrink-0 px-4 sm:px-6 py-3 border-b border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-gray-900">
                <!-- Left: < > Hoy + Selector [ Mensual | Anual ] -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5 shadow-2xs">
                        @if($calendarioVista === 'mensual')
                            <button type="button" wire:click="mesAnterior" class="p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded-md transition-all text-gray-700 dark:text-gray-200" title="Mes anterior">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="mesSiguiente" class="p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded-md transition-all text-gray-700 dark:text-gray-200" title="Mes siguiente">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <button type="button" wire:click="anioAnterior" class="p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded-md transition-all text-gray-700 dark:text-gray-200" title="Año anterior">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="anioSiguiente" class="p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded-md transition-all text-gray-700 dark:text-gray-200" title="Año siguiente">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    <button type="button" wire:click="irHoy" class="px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                        Hoy
                    </button>

                    <!-- Segmented Control: Mensual / Anual -->
                    <div class="inline-flex items-center bg-gray-100 dark:bg-gray-800 rounded-xl p-1 border border-gray-200/70 dark:border-white/10 text-xs font-bold shadow-2xs">
                        <button
                            type="button"
                            wire:click="setCalendarioVista('mensual')"
                            class="px-3 py-1 rounded-lg transition-all {{ $calendarioVista === 'mensual' ? 'bg-indigo-600 text-white shadow-xs font-black' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Mensual
                        </button>
                        <button
                            type="button"
                            wire:click="setCalendarioVista('anual')"
                            class="px-3 py-1 rounded-lg transition-all {{ $calendarioVista === 'anual' ? 'bg-indigo-600 text-white shadow-xs font-black' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Anual
                        </button>
                    </div>
                </div>

                <!-- Center: Month & Year Title -->
                <div class="text-center">
                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-wide">
                        @if($calendarioVista === 'mensual')
                            <span class="lowercase">{{ $this->calendario['mesNombre'] }}</span> de {{ $this->calendario['anio'] }}
                        @else
                            Año {{ $this->calendarioAnual['anio'] }}
                        @endif
                    </h3>
                </div>

                <!-- Right: Search, Legend & Close -->
                <div class="flex items-center gap-3">
                    <!-- Employee Search Filter -->
                    <div class="relative w-36 sm:w-52">
                        <input
                            type="text"
                            list="dl-cal-empleados"
                            wire:model.live.debounce.300ms="calendarioEmpleado"
                            placeholder="Filtrar empleado..."
                            class="w-full text-xs rounded-lg border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 shadow-2xs py-1 px-2.5 focus:ring-indigo-500 h-7"
                        />
                        <datalist id="dl-cal-empleados">
                            @foreach($this->empleados as $emp)
                                <option value="{{ $emp->nombre }} {{ $emp->apellidos }}"></option>
                            @endforeach
                        </datalist>
                        @if($calendarioEmpleado)
                            <button type="button" wire:click="$set('calendarioEmpleado', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 font-bold" title="Quitar filtro">
                                ✕
                            </button>
                        @endif
                    </div>

                    <!-- Color Legend -->
                    <div class="hidden sm:flex items-center gap-1.5 text-[10px]">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-bold" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: #16a34a;"></span>
                            Aprobada
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-bold" style="background-color: #fffbeb; color: #92400e; border: 1px solid #fde68a;">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: #d97706;"></span>
                            Pendiente
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded font-bold" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: #dc2626;"></span>
                            Denegada
                        </span>
                    </div>

                    <!-- Close Button -->
                    <button type="button" wire:click="closeCalendarioAnual" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            @if($calendarioVista === 'mensual')
                <!-- Vista Mensual: Weekdays Header (7 columns: lun, mar, mié, jue, vie, sáb, dom) -->
                <div style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));" class="shrink-0 border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-gray-900/80 text-center py-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                    <div class="py-0.5">lun</div>
                    <div class="py-0.5">mar</div>
                    <div class="py-0.5">mié</div>
                    <div class="py-0.5">jue</div>
                    <div class="py-0.5">vie</div>
                    <div class="py-0.5 text-gray-400 dark:text-gray-500">sáb</div>
                    <div class="py-0.5 text-gray-400 dark:text-gray-500">dom</div>
                </div>

                <!-- Month Days Grid (7 columns x 5-6 rows) -->
                <div class="flex-1 min-h-0 overflow-y-auto bg-gray-200 dark:bg-white/10 p-px" style="overflow-y: auto !important;">
                    <div style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));" class="gap-px bg-gray-200 dark:bg-white/10">
                        @foreach($this->calendario['days'] as $d)
                            @php
                                $isCurrent = $d['isCurrentMonth'];
                                $isToday = $d['isToday'];
                                $cellBg = $isToday ? 'bg-indigo-50/70 dark:bg-indigo-950/40' : ($isCurrent ? 'bg-white dark:bg-gray-900' : 'bg-gray-50/80 dark:bg-gray-950/60 opacity-50');
                            @endphp

                            <div style="min-height: 95px; max-height: 125px;" class="{{ $cellBg }} p-1 sm:p-1.5 flex flex-col justify-start transition-colors relative overflow-visible">
                                <!-- Day Number Header -->
                                <div class="flex items-center justify-between mb-1 leading-none">
                                    <div></div>
                                    <span class="text-[10px] sm:text-[11px] font-bold {{ $isToday ? 'w-5 h-5 rounded-full flex items-center justify-center bg-indigo-600 text-white font-black shadow-xs' : ($isCurrent ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600') }}">
                                        {{ $d['day'] }}
                                    </span>
                                </div>

                                <!-- Day Requests (Pills) -->
                                @if(!empty($d['solicitudes']))
                                    <div class="space-y-1 overflow-y-auto max-h-[75px] sm:max-h-[92px] pr-0.5">
                                        @foreach($d['solicitudes'] as $sol)
                                            @php
                                                $st = $sol['estado'];
                                                if ($st === 'Aprobada') {
                                                    $pillStyle = 'background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;';
                                                    $badgeStyle = 'background-color: #16a34a; color: #ffffff;';
                                                    $dotColor = '#16a34a';
                                                } elseif ($st === 'Pendiente') {
                                                    $pillStyle = 'background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e;';
                                                    $badgeStyle = 'background-color: #d97706; color: #ffffff;';
                                                    $dotColor = '#d97706';
                                                } else {
                                                    $pillStyle = 'background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b;';
                                                    $badgeStyle = 'background-color: #dc2626; color: #ffffff;';
                                                    $dotColor = '#dc2626';
                                                }
                                                $tooltipText = "Empleado: {$sol['empleado']}\nEstado: {$sol['estado']}\nPeriodo: {$sol['fechas_completas']} ({$sol['dias']} días)";
                                            @endphp

                                            <div class="relative group/tip" title="{{ $tooltipText }}">
                                                <!-- Píldora visible con nombre completo del empleado (fuente fina 8px) -->
                                                <div style="{{ $pillStyle }} font-size: 8px; font-weight: 600; line-height: 1; padding: 2px 5px; border-radius: 4px;" class="flex items-center justify-between gap-1 shadow-2xs cursor-pointer hover:opacity-90 transition-all">
                                                    <span class="truncate" style="font-size: 8px; font-weight: 700; letter-spacing: -0.2px;">{{ $sol['empleado'] }}</span>
                                                    <span style="width: 4px; height: 4px; border-radius: 50%; background-color: {{ $dotColor }}; flex-shrink: 0;"></span>
                                                </div>

                                                <!-- Tooltip flotante enriquecido en hover -->
                                                <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 z-50 hidden group-hover/tip:flex flex-col gap-1 bg-gray-900 text-white text-[10px] rounded-xl p-2.5 shadow-2xl border border-white/10 whitespace-nowrap min-w-[210px]">
                                                    <div class="font-extrabold text-white text-[11px] border-b border-white/10 pb-1 flex items-center justify-between gap-2">
                                                        <span class="truncate">{{ $sol['empleado'] }}</span>
                                                        <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase" style="{{ $badgeStyle }}">
                                                            {{ $sol['estado'] }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center justify-between text-gray-300 text-[10px] pt-0.5">
                                                        <span class="text-gray-400">Periodo:</span>
                                                        <span class="font-semibold text-white font-mono">{{ $sol['fechas_completas'] }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between text-gray-300 text-[10px]">
                                                        <span class="text-gray-400">Duración:</span>
                                                        <span class="font-bold text-emerald-400">{{ $sol['dias'] }} {{ $sol['dias'] == 1 ? 'día' : 'días' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Vista Anual: Cuadrícula de 12 meses (4 columnas x 3 filas) -->
                <div class="flex-1 min-h-0 overflow-y-auto bg-gray-200 dark:bg-white/10 p-px" style="overflow-y: auto !important;">
                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1px;" class="bg-gray-200 dark:bg-white/10">
                        @foreach($this->calendarioAnual['meses'] as $mes)
                            @php
                                $isCurrentMonthNow = ($this->calendarioAnual['anio'] == date('Y') && $mes['numero'] == date('n'));
                            @endphp
                            <div style="min-height: 220px;" class="{{ $isCurrentMonthNow ? 'bg-indigo-50/50 dark:bg-indigo-950/30' : 'bg-white dark:bg-gray-900' }} p-3 flex flex-col justify-start transition-colors relative overflow-hidden">
                                <!-- Header del Mes -->
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">
                                            {{ $mes['nombre'] }}
                                        </span>
                                        @if($isCurrentMonthNow)
                                            <span class="px-1.5 py-0.5 rounded-full bg-indigo-600 text-white font-black text-[9px] uppercase leading-none">
                                                Actual
                                            </span>
                                        @endif
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $mes['total'] > 0 ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                                        {{ $mes['total'] }} {{ $mes['total'] == 1 ? 'solicitud' : 'solicitudes' }}
                                    </span>
                                </div>

                                <!-- Lista de Solicitudes del Mes -->
                                <div class="flex-1 overflow-y-auto space-y-1.5 pr-1 max-h-[175px]">
                                    @forelse($mes['solicitudes'] as $sol)
                                        @php
                                            $st = $sol['estado'];
                                            if ($st === 'Aprobada') {
                                                $pillStyle = 'background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;';
                                                $badgeStyle = 'background-color: #16a34a; color: #ffffff;';
                                                $dotColor = '#16a34a';
                                            } elseif ($st === 'Pendiente') {
                                                $pillStyle = 'background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e;';
                                                $badgeStyle = 'background-color: #d97706; color: #ffffff;';
                                                $dotColor = '#d97706';
                                            } else {
                                                $pillStyle = 'background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b;';
                                                $badgeStyle = 'background-color: #dc2626; color: #ffffff;';
                                                $dotColor = '#dc2626';
                                            }
                                            $tooltipText = "Empleado: {$sol['empleado']}\nEstado: {$sol['estado']}\nPeriodo: {$sol['fechas_completas']} ({$sol['dias']} días)";
                                        @endphp

                                        <div class="relative group/tip" title="{{ $tooltipText }}">
                                            <!-- Píldora visible con nombre completo del empleado (fuente fina 8px) -->
                                            <div style="{{ $pillStyle }} font-size: 8px; font-weight: 600; line-height: 1.1; padding: 2.5px 6px; border-radius: 4px;" class="flex items-center justify-between gap-1.5 shadow-2xs cursor-pointer hover:opacity-90 hover:scale-[1.01] transition-all">
                                                <span class="truncate font-bold tracking-tight" style="font-size: 8px;">{{ $sol['empleado'] }}</span>
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <span class="text-[7.5px] opacity-75 font-mono">{{ $sol['fechas'] }}</span>
                                                    <span style="width: 4px; height: 4px; border-radius: 50%; background-color: {{ $dotColor }}; flex-shrink: 0;"></span>
                                                </div>
                                            </div>

                                            <!-- Tooltip flotante enriquecido en hover -->
                                            <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 z-50 hidden group-hover/tip:flex flex-col gap-1 bg-gray-900 text-white text-[10px] rounded-xl p-2.5 shadow-2xl border border-white/10 whitespace-nowrap min-w-[210px]">
                                                <div class="font-extrabold text-white text-[11px] border-b border-white/10 pb-1 flex items-center justify-between gap-2">
                                                    <span class="truncate">{{ $sol['empleado'] }}</span>
                                                    <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase" style="{{ $badgeStyle }}">
                                                        {{ $sol['estado'] }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between text-gray-300 text-[10px] pt-0.5">
                                                    <span class="text-gray-400">Periodo:</span>
                                                    <span class="font-semibold text-white font-mono">{{ $sol['fechas_completas'] }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-gray-300 text-[10px]">
                                                    <span class="text-gray-400">Duración:</span>
                                                    <span class="font-bold text-emerald-400">{{ $sol['dias'] }} {{ $sol['dias'] == 1 ? 'día' : 'días' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 italic py-6 text-center">
                                            Sin solicitudes
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Modal Footer -->
            <div class="shrink-0 px-4 py-2 bg-gray-50/80 dark:bg-gray-900/80 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                    Pasa el ratón sobre cualquier solicitud para ver los detalles de fechas y estado.
                </div>
                <button type="button" wire:click="closeCalendarioAnual" class="px-3.5 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-bold transition-all">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
