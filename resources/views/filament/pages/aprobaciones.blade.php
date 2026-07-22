<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Banner -->
        <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl text-white shadow-md">
            <h2 class="text-2xl font-black">Centro de Aprobaciones</h2>
            <p class="text-sm opacity-90 mt-1">Gestiona y aprueba de forma centralizada las solicitudes de vacaciones y justificaciones de bajas médicas de la plantilla.</p>
        </div>

        <!-- Vacations Section -->
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-sky-500/10 text-sky-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Solicitudes de Vacaciones Pendientes
                </h3>
                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                    {{ count($vacacionesPendientes) }} pendientes
                </span>
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
                        @forelse($vacacionesPendientes as $vac)
                            <tr>
                                <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $vac->empleado ? $vac->empleado->nombre . ' ' . $vac->empleado->apellidos : 'N/A' }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400">
                                        {{ $vac->tipo }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-gray-500 dark:text-gray-400 text-xs">
                                    Del {{ \Carbon\Carbon::parse($vac->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vac->fecha_fin)->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-4 font-bold text-gray-700 dark:text-gray-300">
                                    {{ $vac->dias_solicitados }}
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" wire:click="aprobarVacacion({{ $vac->id }})" style="background-color: #16a34a; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-green-700">
                                            Aprobar
                                        </button>
                                        <button type="button" wire:click="denegarVacacion({{ $vac->id }})" style="background-color: #dc2626; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-red-700">
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

        <!-- Sick Leaves Section -->
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-gray-50 dark:border-white/5 mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-rose-500/10 text-rose-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    Bajas Médicas por Aprobar
                </h3>
                <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                    {{ count($bajasPendientes) }} pendientes
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/5 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Empleado</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Inicio</th>
                            <th class="py-3 px-4">Fin Previsto</th>
                            <th class="py-3 px-4">Justificante</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-white/5 text-sm">
                        @forelse($bajasPendientes as $baja)
                            <tr>
                                <td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $baja->empleado ? $baja->empleado->nombre . ' ' . $baja->empleado->apellidos : 'N/A' }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400">
                                        {{ $baja->tipo }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($baja->fecha_inicio)->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-4 text-gray-500 dark:text-gray-400">
                                    {{ $baja->fecha_fin ? \Carbon\Carbon::parse($baja->fecha_fin)->format('d/m/Y') : 'Sin especificar' }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($baja->justificante_path)
                                        <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $baja->justificante_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Ver Documento
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No adjuntado</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" wire:click="aprobarBaja({{ $baja->id }})" style="background-color: #16a34a; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-green-700">
                                            Aprobar
                                        </button>
                                        <button type="button" wire:click="denegarBaja({{ $baja->id }})" style="background-color: #dc2626; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-red-700">
                                            Denegar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                    No hay solicitudes de baja médica pendientes de aprobación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
