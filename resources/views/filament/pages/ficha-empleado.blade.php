<x-filament-panels::page>
    <div class="space-y-6">
        @if($isViewingAdminList)
            <!-- Admin Dashboard: List of all employee check-ins / Vacations / Absences -->
            <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-amber-500/10 text-amber-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </span>
                        @if($filterType === 'fichajes')
                            Control General de Fichajes de Empleados
                        @elseif($filterType === 'vacaciones')
                            Control General de Vacaciones de Empleados
                        @else
                            Control General de Bajas Médicas de Empleados
                        @endif
                    </h3>
                    <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                        @if($filterType === 'fichajes')
                            {{ count($todosLosFichajes) }} registros totales
                        @elseif($filterType === 'vacaciones')
                            {{ count($todasLasVacaciones) }} registros totales
                        @else
                            {{ count($todasLasBajas) }} registros totales
                        @endif
                    </span>
                </div>

                <!-- Filters Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-gray-50 dark:bg-gray-950/20 p-4 rounded-2xl border border-gray-100 dark:border-white/5">
                    <!-- Date Filter (First position as requested: "Sobre todo la fecha que aparezca en primer lugar") -->
                    <div>
                        <label for="filterDate" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filtrar por Fecha</label>
                        <div class="relative">
                            <input type="date" id="filterDate" wire:model.live="filterDate" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3" />
                            @if($filterDate)
                                <button type="button" wire:click="$set('filterDate', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Record Type Filter (Second position) -->
                    <div>
                        <label for="filterType" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ver tipo de registro</label>
                        <select id="filterType" wire:model.live="filterType" class="w-full text-sm rounded-xl border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3">
                            <option value="fichajes">Fichajes (Entradas/Salidas)</option>
                            <option value="vacaciones">Vacaciones Aprobadas</option>
                            <option value="bajas">Bajas Médicas Aprobadas</option>
                        </select>
                    </div>

                    <!-- Search Filter (Third position) -->
                    <div>
                        <label for="filterSearch" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Buscar Empleado (Nombre, Apellidos o Email)</label>
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

                @if($filterType === 'fichajes')
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="py-3 px-4">Empleado</th>
                                    <th class="py-3 px-4">Fecha</th>
                                    <th class="py-3 px-4">Hora Entrada</th>
                                    <th class="py-3 px-4">Hora Salida</th>
                                    <th class="py-3 px-4">Tiempo Total</th>
                                    <th class="py-3 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                                @forelse($todosLosFichajes as $fichaje)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                            {{ $fichaje->empleado ? $fichaje->empleado->nombre . ' ' . $fichaje->empleado->apellidos : 'N/A' }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-700 dark:text-gray-300 text-xs">
                                            {{ \Carbon\Carbon::parse($fichaje->fecha)->translatedFormat('l, d \d\e F \d\e Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 font-mono text-xs font-bold">
                                                {{ $fichaje->hora_entrada }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                            @if($fichaje->hora_salida)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 font-mono text-xs font-bold">
                                                    {{ $fichaje->hora_salida }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 font-medium text-xs font-bold">
                                                    En curso
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-bold text-gray-700 dark:text-gray-300 text-xs">
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
                                        <td class="py-4 px-4 text-right">
                                            <a href="/admin/ficha-empleado?empleado_id={{ $fichaje->empleado_id }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Ver Ficha / Fichajes
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                            No hay registros de fichajes en el sistema.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif($filterType === 'vacaciones')
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="py-3 px-4">Empleado</th>
                                    <th class="py-3 px-4">Fecha Inicio</th>
                                    <th class="py-3 px-4">Fecha Fin</th>
                                    <th class="py-3 px-4">Días</th>
                                    <th class="py-3 px-4">Tipo</th>
                                    <th class="py-3 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                                @forelse($todasLasVacaciones as $vac)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                            {{ $vac->empleado ? $vac->empleado->nombre . ' ' . $vac->empleado->apellidos : 'N/A' }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                            {{ \Carbon\Carbon::parse($vac->fecha_inicio)->translatedFormat('d/m/Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                            {{ \Carbon\Carbon::parse($vac->fecha_fin)->translatedFormat('d/m/Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-600 dark:text-gray-400 font-bold font-mono text-xs">
                                            {{ $vac->dias_solicitados }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-xs font-bold">
                                                {{ $vac->tipo }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <a href="/admin/ficha-empleado?empleado_id={{ $vac->empleado_id }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Ver Ficha / Fichajes
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                            No hay registros de vacaciones aprobadas para el filtro seleccionado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="py-3 px-4">Empleado</th>
                                    <th class="py-3 px-4">Fecha Inicio</th>
                                    <th class="py-3 px-4">Fecha Fin Prevista</th>
                                    <th class="py-3 px-4">Justificante</th>
                                    <th class="py-3 px-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                                @forelse($todasLasBajas as $baja)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                        <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                            {{ $baja->empleado ? $baja->empleado->nombre . ' ' . $baja->empleado->apellidos : 'N/A' }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                            {{ \Carbon\Carbon::parse($baja->fecha_inicio)->translatedFormat('d/m/Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                            {{ $baja->fecha_fin ? \Carbon\Carbon::parse($baja->fecha_fin)->translatedFormat('d/m/Y') : 'No definida' }}
                                        </td>
                                        <td class="py-4 px-4">
                                            @if($baja->justificante_path)
                                                <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $baja->justificante_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline font-bold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Ver Justificante
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No adjuntado</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <a href="/admin/ficha-empleado?empleado_id={{ $baja->empleado_id }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Ver Ficha / Fichajes
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                            No hay registros de bajas médicas aprobadas para el filtro seleccionado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @elseif(!$empleado)
            <div class="p-6 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900 rounded-2xl flex items-start gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-900 dark:text-red-200">Asociación de empleado no encontrada</h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">Tu usuario actual ({{ auth()->user()->email }}) no está asociado a ningún registro de empleado en la base de datos de Recursos Humanos. Contacta con tu administrador para que vincule tu correo electrónico.</p>
                </div>
            </div>
        @else
            @if(request()->query('empleado_id'))
                <!-- Info Banner for Admin viewing specific employee -->
                <div class="p-4 bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-200 dark:border-indigo-900/40 rounded-2xl flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </span>
                        <div>
                            <span class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">Modo de Visualización de Administrador</span>
                            <p class="text-xs text-indigo-700 dark:text-indigo-400">Estás viendo y gestionando el portal del empleado <strong>{{ $empleado->nombre }} {{ $empleado->apellidos }}</strong>.</p>
                        </div>
                    </div>
                    <a href="/admin/ficha-empleado" style="background-color: #4f46e5; color: #ffffff;" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-indigo-700">
                        Volver al Listado General
                    </a>
                </div>
            @endif
            <!-- Header Profile Info -->
            <div class="relative overflow-hidden p-6 rounded-3xl bg-gradient-to-r from-amber-500/10 to-orange-500/10 dark:from-amber-950/20 dark:to-orange-950/20 border border-amber-500/20 dark:border-amber-500/10 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-amber-500/20 text-amber-700 dark:text-amber-300 rounded-2xl">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white">{{ $empleado->nombre }} {{ $empleado->apellidos }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span class="font-medium text-amber-600 dark:text-amber-400">{{ $empleado->puesto ?? 'Empleado' }}</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                            <span>{{ $empleado->gasolinera?->Nombre ?? 'Sin gasolinera asignada' }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Fecha de hoy</span>
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ \Carbon\Carbon::today()->translatedFormat('l, d \d\e F \d\e Y') }}</h3>
                </div>
            </div>

            <!-- Fichaje Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Check-in Card -->
                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="p-2 bg-green-500/10 text-green-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Entrada / Check-In
                            </h3>
                            <span class="text-xs text-gray-400">Registro de entrada</span>
                        </div>

                        <div class="py-6 flex flex-col items-center justify-center min-h-[160px]">
                            @if($fichajeDelDia && $fichajeDelDia->hora_entrada)
                                <div class="text-center space-y-2">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-300 font-bold rounded-full text-sm border border-green-200 dark:border-green-900">
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                        Entrada Registrada
                                    </div>
                                    <h2 class="text-4xl font-black text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($fichajeDelDia->hora_entrada)->format('H:i') }}</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Hora real de registro: {{ $fichajeDelDia->server_checkin_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @else
                                 <div class="w-full max-w-xs space-y-4" x-data="{
                                     loading: false,
                                     doCheckIn() {
                                         this.loading = true;
                                         if (navigator.geolocation) {
                                             navigator.geolocation.getCurrentPosition(
                                                 (position) => {
                                                     $wire.checkIn(position.coords.latitude, position.coords.longitude)
                                                         .then(() => { this.loading = false; })
                                                         .catch(() => { this.loading = false; });
                                                 },
                                                 (error) => {
                                                     console.warn('Geolocation error:', error);
                                                     $wire.checkIn(null, null)
                                                         .then(() => { this.loading = false; })
                                                         .catch(() => { this.loading = false; });
                                                 },
                                                 { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                                             );
                                         } else {
                                             $wire.checkIn(null, null)
                                                 .then(() => { this.loading = false; })
                                                 .catch(() => { this.loading = false; });
                                         }
                                     }
                                 }">
                                     <div>
                                         <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Selecciona Hora de Entrada</label>
                                         <input type="time" wire:model="hora_entrada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                     </div>
                                     <button @click="doCheckIn" x-bind:disabled="loading" style="background-color: #16a34a; color: #ffffff;" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-600/10 hover:shadow-green-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                         <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" style="display: none;"></span>
                                         <span x-show="!loading">Registrar Entrada</span>
                                         <span x-show="loading" style="display: none;">Obteniendo ubicación...</span>
                                     </button>
                                 </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Check-out Card -->
                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="p-2 bg-orange-500/10 text-orange-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                Salida / Check-Out
                            </h3>
                            <span class="text-xs text-gray-400">Registro de salida</span>
                        </div>

                        <div class="py-6 flex flex-col items-center justify-center min-h-[160px]">
                            @if($fichajeDelDia && $fichajeDelDia->hora_salida)
                                <div class="text-center space-y-2">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 font-bold rounded-full text-sm border border-amber-200 dark:border-amber-900">
                                        <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                        Salida Registrada
                                    </div>
                                    <h2 class="text-4xl font-black text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($fichajeDelDia->hora_salida)->format('H:i') }}</h2>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Hora real de registro: {{ $fichajeDelDia->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</p>
                                </div>
                            @elseif(!$fichajeDelDia || !$fichajeDelDia->hora_entrada)
                                <div class="text-center space-y-2 max-w-xs text-gray-400">
                                    <svg class="w-12 h-12 mx-auto stroke-current" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p class="text-sm font-medium">Debes registrar tu entrada primero para poder registrar la salida.</p>
                                </div>
                            @else
                                 <div class="w-full max-w-xs space-y-4" x-data="{
                                     loading: false,
                                     doCheckOut() {
                                         this.loading = true;
                                         if (navigator.geolocation) {
                                             navigator.geolocation.getCurrentPosition(
                                                 (position) => {
                                                     $wire.checkOut(position.coords.latitude, position.coords.longitude)
                                                         .then(() => { this.loading = false; })
                                                         .catch(() => { this.loading = false; });
                                                 },
                                                 (error) => {
                                                     console.warn('Geolocation error:', error);
                                                     $wire.checkOut(null, null)
                                                         .then(() => { this.loading = false; })
                                                         .catch(() => { this.loading = false; });
                                                 },
                                                 { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                                             );
                                         } else {
                                             $wire.checkOut(null, null)
                                                 .then(() => { this.loading = false; })
                                                 .catch(() => { this.loading = false; });
                                         }
                                     }
                                 }">
                                     <div>
                                         <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Selecciona Hora de Salida</label>
                                         <input type="time" wire:model="hora_salida" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                                     </div>
                                     <button @click="doCheckOut" x-bind:disabled="loading" style="background-color: #ea580c; color: #ffffff;" class="w-full py-3 px-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-600/10 hover:shadow-orange-700/20 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                         <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full" style="display: none;"></span>
                                         <span x-show="!loading">Registrar Salida</span>
                                         <span x-show="loading" style="display: none;">Obteniendo ubicación...</span>
                                     </button>
                                 </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fichajes History -->
            <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-2 bg-amber-500/10 text-amber-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        Historial de Fichajes (Últimos 30 días)
                    </h3>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-white/5">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5">
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora Entrada</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Hora Salida</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Horas Trabajadas</th>
                                <th class="p-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($recentFichajes as $fichaje)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                    <td class="p-4 text-sm font-bold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($fichaje->fecha)->translatedFormat('l, d \d\e F') }}
                                    </td>
                                    <td class="p-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $fichaje->hora_entrada ? \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') : '-' }}
                                    </td>
                                    <td class="p-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $fichaje->hora_salida ? \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') : '-' }}
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-amber-600 dark:text-amber-400">
                                        @if($fichaje->hora_entrada && $fichaje->hora_salida)
                                            @php
                                                $in = \Carbon\Carbon::parse($fichaje->hora_entrada);
                                                $out = \Carbon\Carbon::parse($fichaje->hora_salida);
                                                $mins = $in->diffInMinutes($out);
                                                $hours = floor($mins / 60);
                                                $remMins = $mins % 60;
                                                echo "{$hours}h {$remMins}m";
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-4 text-sm text-right space-x-2">
                                        <button wire:click="editFichaje({{ $fichaje->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Editar
                                        </button>
                                         <button type="button" @click="$wire.set('deletingFichajeId', {{ $fichaje->id }}); $dispatch('open-modal', { id: 'delete-fichaje-modal' })" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                             </svg>
                                             Eliminar
                                         </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Aún no has registrado ningún fichaje.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(auth()->user()->can('solicitar_ver_vacaciones') || auth()->user()->can('solicitud_baja_enfermedad'))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Vacations card -->
                    @if(auth()->user()->can('solicitar_ver_vacaciones'))
                        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        <span class="p-2 bg-sky-500/10 text-sky-600 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </span>
                                        Vacaciones / Permisos
                                    </h3>
                                    <button type="button" @click="$dispatch('open-modal', { id: 'solicitar-vacacion-modal' })" style="background-color: #0284c7; color: #ffffff;" class="inline-flex items-center gap-1.5 px-3 py-1.5 hover:bg-sky-700 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                        Solicitar
                                    </button>
                                </div>

                                <div class="py-4 max-h-[250px] overflow-y-auto space-y-3">
                                    @forelse($vacaciones as $v)
                                        <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $v->tipo }}</span>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                                        {{ $v->estado === 'Aceptada' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : '' }}
                                                        {{ $v->estado === 'Rechazada' ? 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' : '' }}
                                                        {{ $v->estado === 'Pendiente' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400' : '' }}
                                                    ">
                                                        {{ $v->estado }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Del {{ \Carbon\Carbon::parse($v->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($v->fecha_fin)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="text-xs font-black text-sky-600 dark:text-sky-400">{{ $v->dias_solicitados }} {{ $v->dias_solicitados == 1 ? 'día' : 'días' }}</span>
                                                @if($v->estado === 'Pendiente')
                                                    <button type="button" wire:click="deleteVacacion({{ $v->id }})" wire:confirm="¿Estás seguro de que deseas cancelar esta solicitud de vacaciones?" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Cancelar Solicitud">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                            No tienes solicitudes de vacaciones registradas.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Absences/Sick leave card -->
                    @if(auth()->user()->can('solicitud_baja_enfermedad'))
                        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        <span class="p-2 bg-rose-500/10 text-rose-600 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </span>
                                        Bajas Médicas
                                    </h3>
                                    <button type="button" @click="$dispatch('open-modal', { id: 'solicitar-baja-modal' })" style="background-color: #e11d48; color: #ffffff;" class="inline-flex items-center gap-1.5 px-3 py-1.5 hover:bg-rose-700 text-white font-bold rounded-lg text-xs transition-all shadow-sm">
                                        Solicitar Baja
                                    </button>
                                </div>

                                <div class="py-4 max-h-[250px] overflow-y-auto space-y-3">
                                    @forelse($ausencias as $a)
                                        <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/5">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $a->tipo }}</span>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                                        {{ $a->estado === 'Aceptada' ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : '' }}
                                                        {{ $a->estado === 'Rechazada' ? 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' : '' }}
                                                        {{ $a->estado === 'Pendiente' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400' : '' }}
                                                    ">
                                                        {{ $a->estado ?? 'Pendiente' }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Inicio: {{ \Carbon\Carbon::parse($a->fecha_inicio)->format('d/m/Y') }}
                                                    @if($a->fecha_fin)
                                                        | Fin: {{ \Carbon\Carbon::parse($a->fecha_fin)->format('d/m/Y') }}
                                                    @else
                                                        | <span class="text-rose-600 dark:text-rose-400 font-bold">Activa</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                @if($a->justificante_path)
                                                    <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $a->justificante_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Ver Justificante
                                                    </a>
                                                @endif
                                                @if(($a->estado ?? 'Pendiente') === 'Pendiente')
                                                    <button type="button" wire:click="deleteAusencia({{ $a->id }})" wire:confirm="¿Estás seguro de que deseas cancelar esta solicitud de baja médica?" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Cancelar Baja">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="py-8 text-center text-xs text-gray-500 dark:text-gray-400">
                                            No tienes solicitudes de baja médica registradas.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <x-filament::modal id="edit-fichaje-modal" width="md">
                <x-slot name="heading">
                    Editar Fichaje del {{ $editingFecha ? \Carbon\Carbon::parse($editingFecha)->format('d/m/Y') : '' }}
                </x-slot>

                <div class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Entrada</label>
                        <input type="time" wire:model="editingHoraEntrada" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Hora de Salida</label>
                        <input type="time" wire:model="editingHoraSalida" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-lg font-bold focus:border-amber-500 focus:ring-amber-500 shadow-sm" />
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'edit-fichaje-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="warning" wire:click="updateFichaje">
                            Guardar Cambios
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::modal>

            <x-filament::modal id="delete-fichaje-modal" width="md">
                <x-slot name="heading">
                    ¿Eliminar registro de fichaje?
                </x-slot>

                <div class="py-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        ¿Estás seguro de que deseas eliminar este registro de fichaje? Esta acción no se puede deshacer y borrará el historial de entrada y salida del día correspondiente.
                    </p>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'delete-fichaje-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="danger" wire:click="confirmDeleteFichaje">
                            Eliminar Registro
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::modal>

            <!-- solicitar-vacacion-modal -->
            <x-filament::modal id="solicitar-vacacion-modal" width="md">
                <x-slot name="heading">
                    Solicitar Vacaciones / Permiso
                </x-slot>

                <form wire:submit.prevent="solicitarVacacion" class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Tipo de Solicitud</label>
                        <select wire:model="vacacion_tipo" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm">
                            <option value="Vacaciones">Vacaciones</option>
                            <option value="Permisos">Permiso Retribuido</option>
                        </select>
                        @error('vacacion_tipo') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Inicio</label>
                        <input type="date" wire:model="vacacion_fecha_inicio" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" />
                        @error('vacacion_fecha_inicio') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Fin</label>
                        <input type="date" wire:model="vacacion_fecha_fin" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" />
                        @error('vacacion_fecha_fin') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'solicitar-vacacion-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="info" type="submit">
                            Enviar Solicitud
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>

            <!-- solicitar-baja-modal -->
            <x-filament::modal id="solicitar-baja-modal" width="md">
                <x-slot name="heading">
                    Solicitud de Baja Médica
                </x-slot>

                <form wire:submit.prevent="solicitarBaja" class="space-y-4 py-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Inicio de la Baja</label>
                        <input type="date" wire:model="baja_fecha_inicio" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm" />
                        @error('baja_fecha_inicio') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Fecha de Finalización Estimada (Opcional)</label>
                        <input type="date" wire:model="baja_fecha_fin" class="w-full rounded-xl border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 text-sm focus:border-rose-500 focus:ring-rose-500 shadow-sm" />
                        @error('baja_fecha_fin') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Justificante Médico (PDF, Imagen)</label>
                        <input type="file" wire:model="baja_justificante" class="w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100" />
                        @error('baja_justificante') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'solicitar-baja-modal' })">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button color="danger" type="submit">
                            Registrar Baja
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::modal>
        @endif
    </div>
</x-filament-panels::page>
