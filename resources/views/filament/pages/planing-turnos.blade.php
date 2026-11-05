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

        .card-dia-cuadricula {
            transition: all 0.15s ease-in-out;
        }

        .card-dia-cuadricula:hover {
            border-color: rgba(99, 102, 241, 0.45);
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.06);
        }

        .badge-turno-cuadricula {
            transition: transform 0.12s ease, filter 0.12s ease;
        }

        .badge-turno-cuadricula:hover {
            transform: translateY(-1px);
            filter: brightness(0.96);
        }

        .dark .badge-turno-cuadricula:hover {
            filter: brightness(1.12);
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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 shadow-sm">
        {{-- Navegación de Mes/Año (Selector Principal < MES AÑO >) --}}
        <div class="flex items-center">
            <div class="flex items-center bg-gray-100 dark:bg-white/10 rounded-lg p-1 border border-gray-200 dark:border-white/10">
                <button
                    type="button"
                    wire:click="cambiarMes(-1)"
                    title="Mes Anterior"
                    class="p-1.5 rounded-md hover:bg-white dark:hover:bg-white/10 text-gray-600 dark:text-gray-300 transition"
                >
                    <x-filament::icon icon="heroicon-m-chevron-left" class="w-5 h-5" />
                </button>

                <div class="px-4 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
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
        </div>

        {{-- Acciones principales --}}
        <div class="flex items-center gap-2.5">
            <button
                type="button"
                wire:click="generarCuadrante"
                wire:confirm="¿Deseas calcular o rellenar la rotación teórica 4x2 para este mes? Los turnos manuales y sustituciones existentes se respetarán."
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs transition shadow-sm"
            >
                <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4" />
                <span>Generar Cuadrante 4x2</span>
            </button>

            <button
                type="button"
                wire:click="exportarPdf"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs transition shadow-sm"
            >
                <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                <span>Exportar PDF</span>
            </button>
        </div>
    </div>

    {{-- 3. Visualización del Planing en Cuadrícula Semanal --}}
    <x-filament::section>
        {{-- VISTA CUADRÍCULA EN BLOQUES SEMANALES (Formato Cuadrante en Recuadros) --}}
        <div class="space-y-4">
            {{-- Encabezado General del Cuadrante --}}
            <div class="flex items-center justify-between px-2 py-2 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-300 dark:border-white/10">
                <div class="text-sm md:text-base font-black uppercase tracking-wider text-gray-900 dark:text-white">
                    {{ $estaciones[$gasolineraCodigo]['nombre'] ?? 'Estación' }}
                </div>
                <div class="text-xs md:text-sm font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ \Carbon\Carbon::create($anio, $mes, 1)->locale('es')->isoFormat('MMMM [\'\']YY') }}
                </div>
            </div>

                {{-- Bloques Semanales (Semana a Semana) --}}
                @foreach($this->semanasMes as $semana)
                    <div class="bg-white dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-400 dark:border-white/20 shadow-xs">
                        <table class="w-full border-collapse text-xs table-fixed">
                            <colgroup>
                                <col class="w-[120px] md:w-[140px]">
                                @for($i = 0; $i < 7; $i++)
                                    <col class="w-[calc((100%-140px)/7)]">
                                @endfor
                            </colgroup>
                            <thead>
                                <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-400 dark:border-white/20">
                                    {{-- Columna izquierda: Identificador de Semana --}}
                                    <th class="border-r border-gray-400 dark:border-white/20 p-2 text-center font-extrabold text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Semana {{ $semana['numeroSemana'] }}
                                    </th>

                                    {{-- 7 Columnas: Días de la semana --}}
                                    @foreach($semana['dias'] as $dia)
                                        @if($dia['esMesActual'])
                                            <th class="border-r border-gray-400 dark:border-white/20 p-1.5 text-center font-black {{ $dia['esFinDeSemana'] ? 'bg-amber-500/10 text-amber-900 dark:text-amber-300' : 'text-gray-800 dark:text-gray-200' }}">
                                                <div class="text-[9.5px] uppercase font-bold text-gray-500 dark:text-gray-400">
                                                    {{ $dia['diaSemanaLargo'] }}
                                                </div>
                                                <div class="text-sm font-black mt-0.5 flex items-center justify-center gap-1">
                                                    <span>{{ $dia['numero'] }}</span>
                                                    @if($dia['esHoy'])
                                                        <span class="px-1 py-0.2 text-[8px] font-black uppercase rounded bg-primary-600 text-white tracking-tight">Hoy</span>
                                                    @endif
                                                </div>
                                            </th>
                                        @else
                                            {{-- Celda vacía fuera de mes --}}
                                            <th class="border-r border-gray-300 dark:border-white/10 p-1.5 bg-gray-50/50 dark:bg-white/[0.01]"></th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-400 dark:divide-white/20">
                                @foreach($this->filasTurnos as $fila)
                                    <tr>
                                        {{-- Etiqueta del Turno (MAÑANA, TARDE, etc.) con el color del turno --}}
                                        <td
                                            class="border-r border-gray-400 dark:border-white/20 p-2.5 font-black text-center uppercase text-xs tracking-wider"
                                            style="background-color: {{ $fila['color_bg'] }}; color: {{ $fila['color_text'] }}; border-color: {{ $fila['color_border'] }};"
                                        >
                                            <div class="flex items-center justify-center gap-1.5">
                                                <x-filament::icon :icon="$fila['icono']" class="w-3.5 h-3.5" style="color: {{ $fila['color_text'] }};" />
                                                <span>{{ $fila['etiqueta'] }}</span>
                                            </div>
                                        </td>

                                        {{-- Celdas de los 7 Días con el color dependiente del turno --}}
                                        @foreach($semana['dias'] as $dia)
                                            @if($dia['esMesActual'])
                                                @php
                                                    $key = $dia['fecha'] . '_' . $fila['puesto'] . '_' . $fila['turno'];
                                                    $turnoReg = $this->turnosMap[$key] ?? null;
                                                    $empleado = $turnoReg?->empleado;
                                                    $esSustitucion = $turnoReg?->es_sustitucion;
                                                @endphp

                                                <td
                                                    wire:click="abrirModalTurno('{{ $dia['fecha'] }}', '{{ $fila['puesto'] }}', '{{ $fila['turno'] }}')"
                                                    class="border-r border-gray-400 dark:border-white/20 p-2 text-center select-none cursor-pointer transition-all duration-150 hover:brightness-90 dark:hover:brightness-110 font-black text-xs uppercase"
                                                    style="{{ $empleado ? ('background-color: ' . $fila['color_bg'] . '; color: ' . $fila['color_text'] . '; border-color: ' . $fila['color_border'] . ';') : '' }}"
                                                    title="{{ $turnoReg ? ($empleado ? $empleado->nombre . ' ' . $empleado->apellidos : 'Sin asignar') . ($esSustitucion ? ' (Sustitución: ' . ($turnoReg->motivo_sustitucion ?? '') . ')' : '') : 'Clic para asignar ' . $fila['etiqueta'] }}"
                                                >
                                                    @if($empleado)
                                                        <div class="flex items-center justify-center gap-1 truncate">
                                                            <span class="truncate font-black tracking-wide text-xs" style="color: {{ $fila['color_text'] }};">
                                                                {{ $empleado->nombre_corto }}
                                                            </span>
                                                            @if($esSustitucion && $turnoReg->motivo_sustitucion && !str_contains(mb_strtolower($turnoReg->motivo_sustitucion), 'permanente'))
                                                                <span class="text-red-600 font-black text-sm shrink-0" title="Sustitución puntual: {{ $turnoReg->motivo_sustitucion }}">*</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-primary-600 transition">
                                                            <span class="text-xs font-bold">+</span>
                                                        </div>
                                                    @endif
                                                </td>
                                            @else
                                                {{-- Celda vacía fuera de mes --}}
                                                <td class="border-r border-gray-300 dark:border-white/10 p-2 bg-gray-50/40 dark:bg-white/[0.01]"></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
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

        {{-- Leyenda de Turnos y Colores --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <x-slot name="heading">
                    Leyenda de Turnos
                </x-slot>
                <x-slot name="description">
                    Código de colores asignado a cada turno de trabajo.
                </x-slot>

                <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                    @foreach($this->filasTurnos as $fila)
                        <div
                            class="flex items-center justify-between p-2 rounded-lg border shadow-2xs font-bold text-xs"
                            style="background-color: {{ $fila['color_bg'] }}; border-color: {{ $fila['color_border'] }}; color: {{ $fila['color_text'] }};"
                        >
                            <div class="flex items-center gap-2">
                                <x-filament::icon :icon="$fila['icono']" class="w-4 h-4" />
                                <span class="font-black uppercase tracking-tight">{{ $fila['etiqueta'] }}</span>
                            </div>
                            <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded bg-white/70 dark:bg-black/30">
                                {{ $fila['turno'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>
    </div>

    {{-- 5. Modal de Asignación / Modificación de Turno --}}
    @if($mostrarModalTurno)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl max-w-lg w-full overflow-hidden transition-all">
                {{-- Encabezado Modal --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            Asignación y Sustitución de Turno
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
                    @php
                        $filaActual = collect($this->filasTurnos)->first(fn($f) => $f['puesto'] === $modalPuesto && $f['turno'] === $modalTurno);
                        $bgTurno = $filaActual['color_bg'] ?? '#f0fdf4';
                        $borderTurno = $filaActual['color_border'] ?? '#86efac';
                        $textTurno = $filaActual['color_text'] ?? '#166534';
                    @endphp

                    <div
                        class="p-3 rounded-xl border flex items-center justify-between shadow-xs font-bold"
                        style="background-color: {{ $bgTurno }}; border-color: {{ $borderTurno }}; color: {{ $textTurno }};"
                    >
                        <div>
                            <div class="text-[10px] uppercase font-bold opacity-80">Puesto</div>
                            <div class="text-xs font-black">{{ $modalPuesto }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] uppercase font-bold opacity-80">Turno</div>
                            <div class="text-xs font-black">
                                {{ $modalTurno === 'M' ? 'Mañana' : ($modalTurno === 'T' ? 'Tarde' : 'Noche') }}
                            </div>
                        </div>
                    </div>

                    {{-- Selector de Empleado (wire:model.live para sincronización inmediata) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Empleado Asignado
                        </label>
                        <select
                            wire:model.live="modalEmpleadoId"
                            class="w-full text-xs bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 font-bold"
                        >
                            <option value="">-- Sin asignar / Dejar libre --</option>
                            @foreach($this->empleadosDisponibles as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->nombre }} {{ $emp->apellidos }} ({{ $emp->puesto ?? 'Expendedor' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ámbito de la Modificación / Sustitución --}}
                    <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-white/5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            Ámbito de la modificación
                        </label>
                        <div class="space-y-1.5">
                            <label class="flex items-start gap-2.5 p-2 rounded-lg border cursor-pointer transition text-xs {{ $modalAlcance === 'puntual' ? 'bg-primary-50/50 border-primary-500 dark:bg-primary-950/20' : 'border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                <input
                                    type="radio"
                                    wire:model.live="modalAlcance"
                                    value="puntual"
                                    class="mt-0.5 text-primary-600 focus:ring-primary-500"
                                />
                                <div>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Solo este día puntual</span>
                                    <p class="text-[10.5px] text-gray-500 dark:text-gray-400">Modifica exclusivamente el turno de esta fecha.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-2 rounded-lg border cursor-pointer transition text-xs {{ $modalAlcance === 'en_adelante' ? 'bg-amber-50 border-amber-500 dark:bg-amber-950/30' : 'border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                <input
                                    type="radio"
                                    wire:model.live="modalAlcance"
                                    value="en_adelante"
                                    class="mt-0.5 text-amber-600 focus:ring-amber-500"
                                />
                                <div>
                                    <span class="font-black text-amber-900 dark:text-amber-300">Sustitución permanente (desde esta fecha en adelante)</span>
                                    <p class="text-[10.5px] text-amber-700 dark:text-amber-400">
                                        Reemplaza automáticamente todos los turnos del titular desde el {{ \Carbon\Carbon::parse($modalFecha)->isoFormat('D [de] MMMM') }} hasta fin de mes.
                                    </p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-2 rounded-lg border cursor-pointer transition text-xs {{ $modalAlcance === 'todo_el_mes' ? 'bg-primary-50/50 border-primary-500 dark:bg-primary-950/20' : 'border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5' }}">
                                <input
                                    type="radio"
                                    wire:model.live="modalAlcance"
                                    value="todo_el_mes"
                                    class="mt-0.5 text-primary-600 focus:ring-primary-500"
                                />
                                <div>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">Reemplazar en todo el mes completo</span>
                                    <p class="text-[10.5px] text-gray-500 dark:text-gray-400">Reemplaza al titular en todos los turnos que tuviera en este mes.</p>
                                </div>
                            </label>
                        </div>
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
                                Marcar explícitamente como sustitución / suplencia
                            </span>
                        </label>
                    </div>

                    {{-- Campos condicionales de sustitución --}}
                    @if($modalEsSustitucion || $modalAlcance === 'en_adelante')
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-800/40 space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-amber-900 dark:text-amber-200 mb-1">
                                    Motivo de la sustitución
                                </label>
                                <select
                                    wire:model.live="modalMotivo"
                                    class="w-full text-xs bg-white dark:bg-gray-800 border border-amber-300 dark:border-amber-700 rounded-lg p-2 text-gray-900 dark:text-white focus:ring-amber-500 focus:border-amber-500"
                                >
                                    <option value="">-- Seleccionar motivo --</option>
                                    <option value="Sustitución permanente">Sustitución permanente</option>
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
                                    <strong>Titular teórico reemplazado:</strong> {{ $modalTitularNombre }}
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
                                class="px-3.5 py-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 text-xs font-bold transition border border-transparent hover:border-red-200"
                            >
                                Borrar Asignación
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button
                            type="button"
                            wire:click="cerrarModal"
                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-300 font-bold text-xs transition"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            wire:click="guardarTurnoModal"
                            class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-white font-black text-xs transition shadow-md hover:brightness-110 active:brightness-95 cursor-pointer"
                            style="background-color: #059669 !important; color: #ffffff !important;"
                        >
                            <x-filament::icon icon="heroicon-m-check" class="w-4 h-4 text-white" />
                            <span>Guardar Asignación</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>

