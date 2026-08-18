<div class="space-y-6">
    @if (session()->has('notificacion_success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400 border border-emerald-200" role="alert">
            {{ session('notificacion_success') }}
        </div>
    @endif

    <!-- Formulario para Cerrar Expediente Disciplinario Seleccionado -->
    @if ($selectedNotificacionIdParaCierre && $selectedNotificacionParaCierre)
        <div class="p-5 bg-purple-50 dark:bg-purple-950/30 rounded-xl border-2 border-purple-300 dark:border-purple-800 space-y-4 shadow-sm">
            <div class="flex items-center justify-between border-b border-purple-200 dark:border-purple-800/60 pb-3">
                <h3 class="text-base font-bold text-purple-900 dark:text-purple-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Cierre de Expediente Disciplinario (Apertura: {{ $selectedNotificacionParaCierre->fecha_comunicacion ? $selectedNotificacionParaCierre->fecha_comunicacion->format('d/m/Y') : '-' }} @if($selectedNotificacionParaCierre->gravedad) &bull; Gravedad: {{ $selectedNotificacionParaCierre->gravedad }} @endif)
                </h3>
                <button type="button" wire:click="cancelarCierreExpediente" class="text-xs font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    ✕ Cancelar
                </button>
            </div>

            <form wire:submit.prevent="guardarCierreExpediente" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Fecha de Resolución / Cierre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha de resolución / cierre <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                        </label>
                        <input type="date" wire:model="cierre_fecha_comunicacion" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-purple-500 focus:border-purple-500">
                        @error('cierre_fecha_comunicacion') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <!-- Resolución de Cierre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Resolución de cierre <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                        </label>
                        <select wire:model.live="cierre_resolucion" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="Amonestación">Amonestación</option>
                            <option value="Suspensión de empleo y sueldo">Suspensión de empleo y sueldo</option>
                            <option value="Despido disciplinario">Despido disciplinario</option>
                        </select>
                        @error('cierre_resolucion') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <!-- Días de suspensión (condicional) -->
                    @if ($cierre_resolucion === 'Suspensión de empleo y sueldo')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Días de suspensión <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                            </label>
                            <input type="number" min="1" wire:model="cierre_dias_suspension" placeholder="Ej: 5" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-purple-500 focus:border-purple-500">
                            @error('cierre_dias_suspension') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Archivo Justificativo de Cierre -->
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Adjuntar Archivo Justificativo de Resolución (PDF o Imagen) <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                        </label>
                        <input type="file" wire:model="cierre_archivo" accept=".pdf,image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200 dark:file:bg-gray-700 dark:file:text-purple-400">
                        <div wire:loading wire:target="cierre_archivo" class="text-xs text-purple-600 mt-1">Cargando archivo...</div>
                        @error('cierre_archivo') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="cancelarCierreExpediente" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirmar y Cerrar Expediente
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Formulario para Añadir Notificación -->
    <div class="p-5 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Registrar Nueva Notificación
        </h3>

        <form wire:submit.prevent="guardarNotificacion" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tipo de Notificación -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tipo de Notificación <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                    </label>
                    <select wire:model.live="tipo" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-amber-500 focus:border-amber-500">
                        <option value="Modificación sustancial del contrato">Modificación sustancial del contrato</option>
                        <option value="Apertura Expediente disciplinario">Apertura Expediente disciplinario</option>
                    </select>
                    @error('tipo') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <!-- Fecha de Comunicación -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Fecha de comunicación <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                    </label>
                    <input type="date" wire:model="fecha_comunicacion" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-amber-500 focus:border-amber-500">
                    @error('fecha_comunicacion') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <!-- Condicional 1: Modificación sustancial del contrato -->
                @if ($tipo === 'Modificación sustancial del contrato')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Fecha de efecto <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                        </label>
                        <input type="date" wire:model="fecha_efecto" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-amber-500 focus:border-amber-500">
                        @error('fecha_efecto') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Condicional 2: Apertura Expediente disciplinario -->
                @if ($tipo === 'Apertura Expediente disciplinario')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Gravedad <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                        </label>
                        <select wire:model="gravedad" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="Leve">Leve</option>
                            <option value="Grave">Grave</option>
                            <option value="Muy Grave">Muy Grave</option>
                        </select>
                        @error('gravedad') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Archivo Adjunto Obligatorio -->
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Adjuntar Archivo Justificativo (PDF o Imagen) <span class="text-red-600 dark:text-red-500 font-bold" style="color: #dc2626 !important;">*</span>
                    </label>
                    <input type="file" wire:model="archivo" accept=".pdf,image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-gray-700 dark:file:text-amber-400">
                    <div wire:loading wire:target="archivo" class="text-xs text-amber-600 mt-1">Cargando archivo...</div>
                    @error('archivo') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Registrar Notificación
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Notificaciones Registradas -->
    <div class="space-y-3">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            Historial de Notificaciones ({{ $notificaciones->count() }})
        </h4>

        @if ($notificaciones->isEmpty())
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                No hay notificaciones registradas para este empleado.
            </div>
        @else
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="py-2.5 px-3 w-[26%]">Tipo</th>
                            <th class="py-2.5 px-2 w-[16%] text-center">F. Comunicación</th>
                            <th class="py-2.5 px-2 w-[22%] text-center">Estado / Detalles</th>
                            <th class="py-2.5 px-2 w-[16%] text-center">Documentos</th>
                            <th class="py-2.5 px-3 w-[20%] text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($notificaciones as $notif)
                            @php
                                $isDisciplinario = str_contains($notif->tipo, 'disciplinario') || str_contains($notif->tipo, 'Expediente');
                                $isOpen = empty($notif->resolucion_cierre);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="py-2.5 px-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $notif->tipo }}
                                </td>
                                <td class="py-2.5 px-2 text-center whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ $notif->fecha_comunicacion ? $notif->fecha_comunicacion->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2.5 px-2 text-center">
                                    @if ($notif->tipo === 'Modificación sustancial del contrato')
                                        <span class="inline-flex items-center text-[11px] bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 px-2 py-0.5 rounded font-medium">
                                            Efecto: {{ $notif->fecha_efecto ? $notif->fecha_efecto->format('d/m/Y') : '-' }}
                                        </span>
                                    @elseif ($isDisciplinario)
                                        @if ($isOpen)
                                            <span class="inline-flex items-center gap-1 text-[11px] bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-2 py-0.5 rounded-full font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Abierto @if($notif->gravedad) ({{ $notif->gravedad }}) @endif
                                            </span>
                                        @else
                                            <div class="inline-flex flex-col items-center">
                                                <span class="inline-flex items-center gap-1 text-[11px] bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 px-2 py-0.5 rounded-full font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    {{ $notif->resolucion_cierre }}
                                                </span>
                                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                    @if ($notif->fecha_cierre)
                                                        <span>{{ $notif->fecha_cierre->format('d/m/Y') }}</span>
                                                    @endif
                                                    @if ($notif->resolucion_cierre === 'Suspensión de empleo y sueldo' && $notif->dias_suspension)
                                                        <span> &bull; {{ $notif->dias_suspension }}d</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1">
                                        @if ($notif->file_path)
                                            <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $notif->file_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-amber-600 dark:text-amber-400 hover:underline font-semibold" title="Documento de Notificación / Apertura">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                {{ ($isDisciplinario && $notif->cierre_file_path) ? 'Doc. Apertura' : 'Ver Archivo' }}
                                            </a>
                                        @endif

                                        @if ($notif->cierre_file_path)
                                            <a href="{{ route('admin.recursos_humanos.ver_archivo', ['path' => $notif->cierre_file_path]) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-purple-600 dark:text-purple-400 hover:underline font-semibold" title="Documento de Resolución de Cierre">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Doc. Cierre
                                            </a>
                                        @endif

                                        @if (!$notif->file_path && !$notif->cierre_file_path)
                                            <span class="text-[11px] text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @if ($isDisciplinario && $isOpen)
                                            <button type="button" wire:click="iniciarCierreExpediente({{ $notif->id }})" class="inline-flex items-center px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white text-[11px] font-semibold rounded shadow-sm transition-colors whitespace-nowrap">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Cerrar
                                            </button>
                                        @endif

                                        <button type="button" wire:click="eliminarNotificacion({{ $notif->id }})" wire:confirm="¿Seguro que deseas eliminar esta notificación?" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-semibold py-1 px-1">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

