<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Vacations Section -->
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-sky-500/10 text-sky-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Solicitudes Pendientes
                </h3>
                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                    {{ count($this->vacacionesPendientes) }} pendientes
                </span>
            </div>

            <!-- Pendientes Filters -->
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-950/40 border border-gray-100 dark:border-white/5 rounded-2xl flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
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
                        <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Empleado</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Fechas</th>
                            <th class="py-3 px-4">Días</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                        @forelse($this->vacacionesPendientes as $vac)
                            <tr>
                                <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $vac->empleado ? $vac->empleado->nombre . ' ' . $vac->empleado->apellidos : 'N/A' }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400">
                                        {{ $vac->tipo }}
                                    </span>
                                    @if($vac->justificante_path)
                                        <div class="mt-1">
                                            <a href="{{ route('admin.recursos_humanos.descargar_archivo', ['path' => $vac->justificante_path]) }}" class="text-[11px] text-amber-600 dark:text-amber-400 font-bold hover:underline inline-flex items-center gap-1" target="_blank">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Ver Justificante
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-gray-500 dark:text-gray-400 text-xs">
                                    Del {{ \Carbon\Carbon::parse($vac->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vac->fecha_fin)->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-4 font-bold text-gray-700 dark:text-gray-300">
                                    {{ $vac->dias_solicitados }}
                                </td>
                                <td class="py-4 px-4 text-right">
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
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 text-indigo-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </span>
                    Historial de Solicitudes Procesadas
                </h3>
                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 rounded-full text-xs font-bold">
                    {{ $this->historicoProcesadas->total() }} procesadas
                </span>
            </div>

            <!-- Histórico Filters -->
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-950/40 border border-gray-100 dark:border-white/5 rounded-2xl flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
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
                        <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Empleado</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Fechas</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Resolución</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                        @forelse($this->historicoProcesadas as $record)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $record->empleado ? $record->empleado->nombre . ' ' . $record->empleado->apellidos : 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-gray-600 dark:text-gray-400 font-medium text-xs">
                                    @if(isset($record->dias_solicitados))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400 font-bold text-[10px] uppercase">
                                            Vacaciones
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 font-bold text-[10px] uppercase">
                                            Baja Médica
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-gray-700 dark:text-gray-300 font-mono text-xs">
                                    @if(isset($record->dias_solicitados))
                                        {{ \Carbon\Carbon::parse($record->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($record->fecha_fin)->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($record->fecha_inicio)->format('d/m/Y') }} @if($record->fecha_fin) - {{ \Carbon\Carbon::parse($record->fecha_fin)->format('d/m/Y') }} @else (Indefinida) @endif
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($record->estado === 'Aceptada')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 text-xs font-bold">
                                            Aprobada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 text-xs font-bold">
                                            Denegada
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-gray-500 dark:text-gray-400 text-xs font-medium">
                                    Resuelto: {{ \Carbon\Carbon::parse($record->updated_at)->translatedFormat('d \d\e F \d\e Y H:i') }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button type="button" wire:click="verDetalles({{ $record->id }}, '{{ isset($record->dias_solicitados) ? 'vacacion' : 'baja' }}')" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Ver Detalles
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
</x-filament-panels::page>
