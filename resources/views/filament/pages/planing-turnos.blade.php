<x-filament-panels::page>
    <style>
        .fi-main-ctn, .fi-page-content-ctn, .fi-section {
            max-width: none !important;
            width: 100% !important;
        }

        .cuadrante-scroll-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.5rem;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 20;
            background-color: #ffffff;
        }
        .dark .sticky-col {
            background-color: #18181b;
        }

        .cell-turno {
            min-width: 78px;
            max-width: 95px;
            height: 52px;
            text-align: center;
            vertical-align: middle;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }

        .cell-turno:hover {
            filter: brightness(0.95);
        }

        .dark .cell-turno:hover {
            filter: brightness(1.15);
        }
    </style>

    {{-- 1. Barra Superior de Selección de Gasolinera --}}
    <div class="flex flex-wrap items-center gap-2 p-1.5 bg-gray-100 dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10">
        @php
            $estaciones = [
                1 => ['nombre' => 'Vistalegre', 'sub' => 'Utrera (1 Puesto)', 'icon' => 'heroicon-m-building-storefront'],
                2 => ['nombre' => 'Ronda Norte', 'sub' => 'Utrera (24 Horas)', 'icon' => 'heroicon-m-clock'],
                3 => ['nombre' => 'Rodalabota', 'sub' => 'El Cuervo (1 Puesto)', 'icon' => 'heroicon-m-map-pin'],
                4 => ['nombre' => 'Atenas', 'sub' => 'Lebrija (1 Puesto)', 'icon' => 'heroicon-m-sparkles'],
            ];
        @endphp

        @foreach($estaciones as $codigo => $est)
            <button
                type="button"
                wire:click="seleccionarGasolinera({{ $codigo }})"
                class="flex-1 min-w-[200px] flex items-center gap-3 px-4 py-2.5 rounded-lg text-left transition-all duration-150 {{ $gasolineraCodigo === $codigo ? 'bg-white dark:bg-white/15 text-primary-600 dark:text-primary-400 shadow-sm border border-gray-200/80 dark:border-white/20 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50' }}"
            >
                <div class="p-2 rounded-lg {{ $gasolineraCodigo === $codigo ? 'bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400' : 'bg-gray-200/60 dark:bg-white/10 text-gray-500' }}">
                    <x-filament::icon :icon="$est['icon']" class="w-5 h-5" />
                </div>
                <div>
                    <div class="text-sm font-bold leading-tight">{{ $est['nombre'] }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500 font-normal">{{ $est['sub'] }}</div>
                </div>
            </button>
        @endforeach
    </div>

    {{-- 2. Barra de Control Temporal y Acciones --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 bg-white dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm">
        {{-- Navegación de Mes/Año --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center bg-gray-100 dark:bg-white/10 rounded-lg p-1 border border-gray-200 dark:border-white/10">
                <button
                    type="button"
                    wire:click="cambiarMes(-1)"
                    title="Mes Anterior"
                    class="p-1.5 rounded-md hover:bg-white dark:hover:bg-white/10 text-gray-600 dark:text-gray-300 transition"
                >
                    <x-filament::icon icon="heroicon-m-chevron-left" class="w-5 h-5" />
                </button>

                <div class="px-3 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                    {{ \Carbon\Carbon::create($anio, $mes, 1)->locale('es')->isoFormat('MMMM YYYY') }}
                </div>

                <button
                    type="button"
                    wire:click="cambiarMes(1)"
                    title="Mes Siguiente"
                    class="p-1.5 rounded-md hover:bg-white dark:hover:bg-white/10 text-gray-600 dark:text-gray-300 transition"
                >
                    <x-filament::icon icon="heroicon-m-chevron-right" class="w-5 h-5" />
                </button>
            </div>

            <button
                type="button"
                wire:click="irMesActual"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/20 transition border border-gray-200 dark:border-white/10"
            >
                Mes Actual
            </button>

            {{-- Selectores rápidos --}}
            <select
                wire:model.live="mes"
                class="text-xs font-medium bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-2.5 py-1.5 text-gray-700 dark:text-gray-300 focus:ring-primary-500 focus:border-primary-500"
            >
                @foreach([
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ] as $num => $nombre)
                    <option value="{{ $num }}">{{ $nombre }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="anio"
                class="text-xs font-medium bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg px-2.5 py-1.5 text-gray-700 dark:text-gray-300 focus:ring-primary-500 focus:border-primary-500"
            >
                @foreach([2025, 2026, 2027, 2028] as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </div>

        {{-- Acciones principales --}}
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                wire:click="generarCuadrante"
                wire:confirm="¿Deseas calcular o rellenar la rotación teórica 4x2 para este mes? Los turnos manuales y sustituciones existentes se respetarán."
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs transition shadow-sm"
            >
                <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4" />
                <span>Generar Cuadrante 4x2</span>
            </button>

            <button
                type="button"
                wire:click="exportarPdf"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs transition shadow-sm"
            >
                <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                <span>Exportar PDF</span>
            </button>
        </div>
    </div>

    {{-- 3. Cuadrícula Interactiva del Planing (Solo Turnos de Trabajo, Sin Descanso) --}}
    <x-filament::section>
        <div class="cuadrante-scroll-container border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900">
            <table class="w-full border-collapse text-xs">
                <thead>
                    {{-- Fila 1: Días de la semana --}}
                    <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                        <th class="sticky-col p-2 text-left font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-r border-gray-200 dark:border-white/10 min-w-[140px] max-w-[160px]">
                            Turno / Puesto
                        </th>
                        @foreach($this->diasMes as $dia)
                            <th class="p-1 text-center font-bold border-r border-gray-200/60 dark:border-white/10 {{ $dia['esFinDeSemana'] ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400' : 'text-gray-600 dark:text-gray-300' }}">
                                <div class="text-[10px] uppercase font-bold">{{ $dia['diaSemana'] }}</div>
                                <div class="text-xs font-extrabold mt-0.5">{{ $dia['numero'] }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach($this->filasTurnos as $fila)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                            {{-- Cabecera de Fila (Turno/Puesto) --}}
                            <td class="sticky-col p-2.5 border-r border-gray-200 dark:border-white/10 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-1.5">
                                    <x-filament::icon :icon="$fila['icono']" class="w-4 h-4 text-gray-400" />
                                    <span class="text-xs uppercase tracking-tight">{{ $fila['etiqueta'] }}</span>
                                </div>
                            </td>

                            {{-- Celdas de cada día --}}
                            @foreach($this->diasMes as $dia)
                                @php
                                    $key = $dia['fecha'] . '_' . $fila['puesto'] . '_' . $fila['turno'];
                                    $turnoReg = $this->turnosMap[$key] ?? null;
                                    $empleado = $turnoReg?->empleado;
                                    $esSustitucion = $turnoReg?->es_sustitucion;
                                    $color = $empleado?->color ?? '#64748b';
                                @endphp

                                <td
                                    wire:click="abrirModalTurno('{{ $dia['fecha'] }}', '{{ $fila['puesto'] }}', '{{ $fila['turno'] }}')"
                                    class="cell-turno p-1 border-r border-gray-200/50 dark:border-white/5 {{ $dia['esFinDeSemana'] ? 'bg-amber-500/[0.03]' : '' }}"
                                    title="{{ $turnoReg ? ($empleado ? $empleado->nombre . ' ' . $empleado->apellidos : 'Sin asignar') . ($esSustitucion ? ' (Sustitución)' : '') : 'Clic para asignar turno' }}"
                                >
                                    @if($empleado)
                                        <div
                                            class="h-full w-full rounded-md p-1 flex flex-col justify-center items-center text-center transition shadow-xs {{ $esSustitucion ? 'border-2 border-dashed' : 'border' }}"
                                            style="background-color: {{ $color }}18; border-color: {{ $color }}; color: {{ $color }};"
                                        >
                                            <span class="font-extrabold text-[10.5px] leading-tight truncate max-w-full">
                                                {{ mb_strtoupper(explode(' ', trim($empleado->nombre))[0]) }}
                                                @if($esSustitucion)
                                                    <span class="text-red-500 font-black" title="Sustitución: {{ $turnoReg->motivo_sustitucion ?? 'Suplente' }}">*</span>
                                                @endif
                                            </span>

                                            @if($esSustitucion && $turnoReg->motivo_sustitucion)
                                                <span class="text-[8px] font-semibold uppercase opacity-85 truncate">
                                                    {{ $turnoReg->motivo_sustitucion }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="h-full w-full rounded-md border border-dashed border-gray-300 dark:border-white/20 flex items-center justify-center text-gray-300 dark:text-gray-600 hover:border-gray-400 hover:text-gray-500">
                                            <span class="text-xs">+</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Nota visual indicativa --}}
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-2 px-1">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                <span>Cuadrante enfocado en turnos de trabajo. Las libranzas se deducen automáticamente del ciclo rotativo 4x2.</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1 font-semibold text-gray-700 dark:text-gray-300">
                    <span class="text-red-500 font-bold">*</span> Sustitución / Suplencia
                </span>
            </div>
        </div>
    </x-filament::section>

    {{-- 4. Observaciones del Mes y Leyenda de Colores --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Observaciones del Mes --}}
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    Observaciones y Acuerdos de Mes
                </x-slot>
                <x-slot name="description">
                    Espacio para registrar acuerdos de gerencia, adaptaciones horarias, coberturas o refuerzos.
                </x-slot>

                <div class="space-y-3">
                    <textarea
                        wire:model="observaciones"
                        rows="4"
                        placeholder="Escribe aquí las observaciones del cuadrante para este mes (ej. acuerdos de turnos, apoyos en eventos, etc.)..."
                        class="w-full text-xs font-normal bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg p-3 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                    ></textarea>

                    <div class="flex justify-end">
                        <button
                            type="button"
                            wire:click="guardarObservaciones"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 text-white font-semibold text-xs transition shadow-sm"
                        >
                            <x-filament::icon icon="heroicon-m-check" class="w-4 h-4" />
                            <span>Guardar Observaciones</span>
                        </button>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Leyenda de Empleados y Colores --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <x-slot name="heading">
                    Plantilla Asignada
                </x-slot>
                <x-slot name="description">
                    Código de colores para reconocimiento inmediato.
                </x-slot>

                <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                    @forelse($this->empleadosLegenda as $emp)
                        <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200/60 dark:border-white/10">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $emp->color }};"></span>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $emp->nombre }} {{ $emp->apellidos }}
                                </span>
                            </div>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-medium shrink-0">
                                {{ $emp->puesto ?? 'Expendedor' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 italic py-2">
                            Pulsa en "Generar Cuadrante 4x2" para visualizar el equipo.
                        </div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>
    </div>

    {{-- 5. Modal de Asignación / Modificación de Turno --}}
    @if($mostrarModalTurno)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl max-w-md w-full overflow-hidden transition-all">
                {{-- Encabezado Modal --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            Asignación de Turno
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($modalFecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="cerrarModal"
                        class="text-gray-400 hover:text-gray-500 p-1"
                    >
                        <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Cuerpo Modal --}}
                <div class="p-6 space-y-4">
                    {{-- Información del Puesto y Turno --}}
                    <div class="p-3 bg-primary-50/60 dark:bg-primary-950/30 rounded-xl border border-primary-200/60 dark:border-primary-800/40 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-primary-600 dark:text-primary-400">Puesto</div>
                            <div class="text-xs font-extrabold text-gray-900 dark:text-white">{{ $modalPuesto }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] uppercase font-bold text-primary-600 dark:text-primary-400">Turno</div>
                            <div class="text-xs font-extrabold text-gray-900 dark:text-white">
                                {{ $modalTurno === 'M' ? 'Mañana' : ($modalTurno === 'T' ? 'Tarde' : 'Noche') }}
                            </div>
                        </div>
                    </div>

                    {{-- Selector de Empleado --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Empleado Asignado
                        </label>
                        <select
                            wire:model="modalEmpleadoId"
                            class="w-full text-xs bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">-- Sin asignar / Dejar libre --</option>
                            @foreach($this->empleadosDisponibles as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->nombre }} {{ $emp->apellidos }} ({{ $emp->puesto ?? 'Expendedor' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Checkbox Sustitución --}}
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="modalEsSustitucion"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            />
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                Es una sustitución o suplencia
                            </span>
                        </label>
                    </div>

                    {{-- Campos condicionales de sustitución --}}
                    @if($modalEsSustitucion)
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-800/40 space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-amber-900 dark:text-amber-200 mb-1">
                                    Motivo de la sustitución
                                </label>
                                <select
                                    wire:model="modalMotivo"
                                    class="w-full text-xs bg-white dark:bg-gray-800 border border-amber-300 dark:border-amber-700 rounded-lg p-2 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500"
                                >
                                    <option value="">-- Seleccionar motivo --</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                    <option value="Baja médica">Baja médica / IT</option>
                                    <option value="Permiso">Permiso retribuido</option>
                                    <option value="Cambio de turno">Cambio de turno acordado</option>
                                    <option value="Refuerzo">Refuerzo extraordinario</option>
                                    <option value="Otro">Otro motivo</option>
                                </select>
                            </div>

                            @if($modalTitularNombre)
                                <div class="text-[11px] text-amber-800 dark:text-amber-300 font-medium">
                                    <strong>Titular teórico:</strong> {{ $modalTitularNombre }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Notas Adicionales --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Notas o comentarios del turno
                        </label>
                        <input
                            type="text"
                            wire:model="modalNotas"
                            placeholder="Ej. Entra 1 hora antes, apoyo en pista..."
                            class="w-full text-xs bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                        />
                    </div>
                </div>

                {{-- Pie del Modal / Botones --}}
                <div class="px-6 py-3.5 bg-gray-50 dark:bg-white/5 border-t border-gray-200 dark:border-white/10 flex items-center justify-between gap-2">
                    <div>
                        @if($modalTurnoId)
                            <button
                                type="button"
                                wire:click="borrarAsignacionModal"
                                wire:confirm="¿Seguro que deseas eliminar esta asignación del cuadrante?"
                                class="px-3 py-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 text-xs font-semibold transition"
                            >
                                Borrar Asignación
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="cerrarModal"
                            class="px-3.5 py-2 rounded-lg bg-gray-200/80 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-300 font-semibold text-xs transition"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            wire:click="guardarTurnoModal"
                            class="px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs transition shadow-sm"
                        >
                            Guardar Turno
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>

