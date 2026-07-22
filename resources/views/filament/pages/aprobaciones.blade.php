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
                            <th class="py-3 px-4" style="min-width: 250px;">Comentario / Razón</th>
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
                                <td class="py-4 px-4">
                                    <input type="text" wire:model="comentariosVacaciones.{{ $vac->id }}" placeholder="Escribe la razón aquí..." class="w-full text-xs rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-1" />
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" wire:click="aprobarVacacion({{ $vac->id }})" style="background-color: #16a34a; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-green-700">
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
                                <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
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
                            <th class="py-3 px-4" style="min-width: 250px;">Comentario / Razón</th>
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
                                        <button type="button" wire:click="showDocument('{{ $baja->justificante_path }}')" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Ver Documento
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No adjuntado</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <input type="text" wire:model="comentariosBajas.{{ $baja->id }}" placeholder="Escribe la razón aquí..." class="w-full text-xs rounded-lg border-gray-300 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm py-1" />
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" wire:click="aprobarBaja({{ $baja->id }})" style="background-color: #16a34a; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-green-700">
                                            Aprobar
                                        </button>
                                         <button type="button" wire:click="iniciarDenegacion({{ $baja->id }}, 'baja')" style="background-color: #dc2626; color: #ffffff;" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all hover:bg-red-700">
                                             Denegar
                                         </button>
                                     </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                    No hay solicitudes de baja médica pendientes de aprobación.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
</x-filament-panels::page>
