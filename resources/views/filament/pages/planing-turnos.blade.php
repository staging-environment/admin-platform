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

        {{-- Acciones principales y Selector de Vista --}}
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Selector de Vista (Cuadrícula vs Tabla Horizontal) --}}
            <div class="flex items-center bg-gray-100 dark:bg-white/10 rounded-lg p-1 border border-gray-200 dark:border-white/10">
                <button
                    type="button"
                    wire:click="cambiarVista('cuadricula')"
                    title="Vista Cuadrícula en Recuadros (Sin scroll horizontal)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-md transition {{ $vista === 'cuadricula' ? 'bg-white dark:bg-white/20 text-primary-600 dark:text-primary-400 shadow-sm border border-gray-200/80 dark:border-white/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    <x-filament::icon icon="heroicon-m-squares-2x2" class="w-4 h-4" />
                    <span>Cuadrícula</span>
                </button>
                <button
                    type="button"
                    wire:click="cambiarVista('horizontal')"
                    title="Vista Tabla Horizontal Tradicional"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-md transition {{ $vista === 'horizontal' ? 'bg-white dark:bg-white/20 text-primary-600 dark:text-primary-400 shadow-sm border border-gray-200/80 dark:border-white/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                >
                    <x-filament::icon icon="heroicon-m-table-cells" class="w-4 h-4" />
                    <span>Tabla Horizontal</span>
                </button>
            </div>

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

    {{-- 3. Visualización del Planing (Modo Cuadrícula en Recuadros o Tabla Horizontal) --}}
    <x-filament::section>
        @if($vista === 'cuadricula')
            {{-- VISTA CUADRÍCULA EN RECUADROS (100% Pantalla, Sin Scroll Horizontal) --}}
            <div class="space-y-2">
                {{-- Encabezado con los 7 días de la semana --}}
                <div class="grid grid-cols-7 gap-2">
                    @foreach([
                        ['corto' => 'LUN', 'largo' => 'Lunes', 'finde' => false],
                        ['corto' => 'MAR', 'largo' => 'Martes', 'finde' => false],
                        ['corto' => 'MIÉ', 'largo' => 'Miércoles', 'finde' => false],
                        ['corto' => 'JUE', 'largo' => 'Jueves', 'finde' => false],
                        ['corto' => 'VIE', 'largo' => 'Viernes', 'finde' => false],
                        ['corto' => 'SÁB', 'largo' => 'Sábado', 'finde' => true],
                        ['corto' => 'DOM', 'largo' => 'Domingo', 'finde' => true],
                    ] as $col)
                        <div class="py-2 px-1 text-center font-extrabold text-[11px] uppercase tracking-wider rounded-lg border {{ $col['finde'] ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-300/40 dark:border-amber-500/20' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200/70 dark:border-white/10' }}">
                            <span class="hidden md:inline">{{ $col['largo'] }}</span>
                            <span class="md:hidden">{{ $col['corto'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Matriz de Semanas (Recuadros de días) --}}
                @foreach($this->semanasMes as $semana)
                    <div class="grid grid-cols-7 gap-2">
                        @foreach($semana['dias'] as $dia)
                            @if(!$dia['esMesActual'])
                                {{-- Día de otro mes (atenuado para alinear la semana) --}}
                                <div class="min-h-[110px] rounded-xl border border-dashed border-gray-200/50 dark:border-white/5 bg-gray-50/30 dark:bg-white/[0.01] p-2 opacity-30 select-none flex flex-col justify-between">
                                    <div class="text-right text-[11px] font-bold text-gray-400">
                                        {{ $dia['numero'] }}
                                    </div>
                                    <div class="text-[9px] text-center text-gray-400 italic">
                                        &nbsp;
                                    </div>
                                </div>
                            @else
                                {{-- Día del Mes Actual: Recuadro Interactivo --}}
                                <div class="card-dia-cuadricula min-h-[110px] rounded-xl border {{ $dia['esHoy'] ? 'border-primary-500 ring-2 ring-primary-500/20 bg-primary-50/[0.05]' : ($dia['esFinDeSemana'] ? 'border-amber-200/90 dark:border-amber-500/20 bg-amber-500/[0.02]' : 'border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900') }} p-2 shadow-2xs flex flex-col justify-between transition-all duration-150">
                                    
                                    {{-- Encabezado del Recuadro --}}
                                    <div class="flex items-center justify-between pb-1 mb-1.5 border-b border-gray-100 dark:border-white/5">
                                        <span class="text-[10px] font-bold uppercase {{ $dia['esFinDeSemana'] ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500' }}">
                                            {{ $dia['diaSemana'] }}
                                        </span>
                                        <div class="flex items-center gap-1">
                                            @if($dia['esHoy'])
                                                <span class="px-1 py-0.2 text-[8.5px] font-black uppercase rounded bg-primary-600 text-white tracking-tight">
                                                    Hoy
                                                </span>
                                            @endif
                                            <span class="w-5 h-5 flex items-center justify-center rounded-md text-[11px] font-black {{ $dia['esFinDeSemana'] ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' : 'bg-gray-100 dark:bg-white/10 text-gray-800 dark:text-gray-200' }}">
                                                {{ $dia['numero'] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Turnos asignados dentro del Recuadro --}}
                                    @if($gasolineraCodigo === 2)
                                        {{-- RONDA NORTE: 5 Turnos (P1-M, P2-M / P1-T, P2-T / Noche) --}}
                                        <div class="space-y-1 flex-1 flex flex-col justify-center">
                                            <div class="grid grid-cols-2 gap-1">
                                                @foreach([
                                                    ['puesto' => 'Puesto 1', 'turno' => 'M', 'label' => 'P1-M'],
                                                    ['puesto' => 'Puesto 2', 'turno' => 'M', 'label' => 'P2-M'],
                                                    ['puesto' => 'Puesto 1', 'turno' => 'T', 'label' => 'P1-T'],
                                                    ['puesto' => 'Puesto 2', 'turno' => 'T', 'label' => 'P2-T'],
                                                ] as $cfg)
                                                    @php
                                                        $k = $dia['fecha'] . '_' . $cfg['puesto'] . '_' . $cfg['turno'];
                                                        $tReg = $this->turnosMap[$k] ?? null;
                                                        $emp = $tReg?->empleado;
                                                        $esSust = $tReg?->es_sustitucion;
                                                        $c = $emp?->color ?? '#64748b';
                                                    @endphp
                                                    <div
                                                        wire:click="abrirModalTurno('{{ $dia['fecha'] }}', '{{ $cfg['puesto'] }}', '{{ $cfg['turno'] }}')"
                                                        class="cursor-pointer badge-turno-cuadricula"
                                                        title="{{ $tReg ? ($emp ? $emp->nombre . ' ' . $emp->apellidos : 'Sin asignar') . ($esSust ? ' (Sustitución)' : '') : 'Asignar ' . $cfg['label'] }}"
                                                    >
                                                        @if($emp)
                                                            <div
                                                                class="rounded px-1 py-0.5 flex items-center justify-between text-left text-[10px] {{ $esSust ? 'border border-dashed' : 'border' }}"
                                                                style="background-color: {{ $c }}18; border-color: {{ $c }}; color: {{ $c }};"
                                                            >
                                                                <span class="font-extrabold truncate max-w-[85%]">
                                                                    {{ mb_strtoupper(explode(' ', trim($emp->nombre))[0]) }}
                                                                </span>
                                                                <span class="text-[8px] opacity-75 font-bold">{{ $cfg['turno'] }}</span>
                                                            </div>
                                                        @else
                                                            <div class="rounded border border-dashed border-gray-200 dark:border-white/10 px-1 py-0.5 text-[9px] text-gray-400 text-center hover:border-primary-400 hover:text-primary-600 bg-gray-50/50">
                                                                {{ $cfg['label'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Turno Noche --}}
                                            @php
                                                $kNoche = $dia['fecha'] . '_Noche_N';
                                                $tNoche = $this->turnosMap[$kNoche] ?? null;
                                                $empNoche = $tNoche?->empleado;
                                                $esSustNoche = $tNoche?->es_sustitucion;
                                                $cNoche = $empNoche?->color ?? '#8b5cf6';
                                            @endphp
                                            <div
                                                wire:click="abrirModalTurno('{{ $dia['fecha'] }}', 'Noche', 'N')"
                                                class="cursor-pointer badge-turno-cuadricula"
                                                title="{{ $tNoche ? ($empNoche ? $empNoche->nombre . ' ' . $empNoche->apellidos : 'Sin asignar') . ($esSustNoche ? ' (Sustitución)' : '') : 'Asignar Turno Noche' }}"
                                            >
                                                @if($empNoche)
                                                    <div
                                                        class="rounded px-1.5 py-0.5 flex items-center justify-between text-left text-[10px] {{ $esSustNoche ? 'border border-dashed' : 'border' }}"
                                                        style="background-color: {{ $cNoche }}18; border-color: {{ $cNoche }}; color: {{ $cNoche }};"
                                                    >
                                                        <span class="font-extrabold truncate max-w-[85%]">
                                                            🌙 {{ mb_strtoupper(explode(' ', trim($empNoche->nombre))[0]) }}
                                                        </span>
                                                        <span class="text-[8px] opacity-75 font-bold">N</span>
                                                    </div>
                                                @else
                                                    <div class="rounded border border-dashed border-purple-200/60 dark:border-purple-900/30 px-1 py-0.5 text-[9px] text-purple-400 text-center hover:border-purple-400 hover:text-purple-600 bg-purple-50/30">
                                                        🌙 Noche Libre
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        {{-- ESTACIONES DIURNAS (Vistalegre, Rodalabota, Atenas): Mañana y Tarde --}}
                                        <div class="space-y-1.5 flex-1 flex flex-col justify-center">
                                            @foreach($this->filasTurnos as $fila)
                                                @php
                                                    $key = $dia['fecha'] . '_' . $fila['puesto'] . '_' . $fila['turno'];
                                                    $turnoReg = $this->turnosMap[$key] ?? null;
                                                    $empleado = $turnoReg?->empleado;
                                                    $esSustitucion = $turnoReg?->es_sustitucion;
                                                    $color = $empleado?->color ?? '#64748b';
                                                @endphp

                                                <div
                                                    wire:click="abrirModalTurno('{{ $dia['fecha'] }}', '{{ $fila['puesto'] }}', '{{ $fila['turno'] }}')"
                                                    class="cursor-pointer badge-turno-cuadricula"
                                                    title="{{ $turnoReg ? ($empleado ? $empleado->nombre . ' ' . $empleado->apellidos : 'Sin asignar') . ($esSustitucion ? ' (Sustitución: ' . ($turnoReg->motivo_sustitucion ?? '') . ')' : '') : 'Asignar turno ' . $fila['etiqueta'] }}"
                                                >
                                                    @if($empleado)
                                                        <div
                                                            class="w-full rounded-lg px-2 py-1 flex items-center justify-between text-left shadow-2xs {{ $esSustitucion ? 'border-2 border-dashed' : 'border' }}"
                                                            style="background-color: {{ $color }}18; border-color: {{ $color }}; color: {{ $color }};"
                                                        >
                                                            <div class="flex items-center gap-1.5 min-w-0">
                                                                <span class="text-[9px] font-black uppercase px-1 py-0.5 rounded bg-white/80 dark:bg-black/50 shrink-0 leading-none">
                                                                    {{ $fila['turno'] }}
                                                                </span>
                                                                <span class="font-extrabold text-[11px] leading-tight truncate">
                                                                    {{ mb_strtoupper(explode(' ', trim($empleado->nombre))[0]) }}
                                                                </span>
                                                            </div>

                                                            @if($esSustitucion)
                                                                <span class="text-red-500 font-black text-xs shrink-0" title="Sustitución: {{ $turnoReg->motivo_sustitucion ?? 'Suplente' }}">*</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="w-full rounded-lg border border-dashed border-gray-200 dark:border-white/10 hover:border-primary-400 dark:hover:border-primary-500 py-1 px-2 flex items-center justify-between text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 bg-gray-50/50 dark:bg-white/[0.02]">
                                                            <span class="text-[9px] font-bold uppercase text-gray-400">
                                                                {{ $fila['turno'] }}
                                                            </span>
                                                            <span class="text-[10px] font-medium">+ Asignar</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            {{-- VISTA TABLA HORIZONTAL (Tradicional) --}}
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
        @endif

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

