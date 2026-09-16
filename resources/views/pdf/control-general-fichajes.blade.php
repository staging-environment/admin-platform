<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control General de Fichajes - Utrecar</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: a4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2px solid #d97706;
            padding-bottom: 6px;
        }
        .header-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .header-subtitle {
            font-size: 8.5pt;
            color: #d97706;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }
        .header-meta {
            text-align: right;
            font-size: 7.5pt;
            color: #64748b;
            line-height: 1.4;
        }
        .filters-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 10px;
            font-size: 7.5pt;
        }
        .filters-table {
            width: 100%;
            border-collapse: collapse;
        }
        .filters-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .filter-label {
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 6.5pt;
        }
        .filter-value {
            color: #0f172a;
            font-weight: 600;
        }
        .single-employee-box {
            margin-top: 6px;
            padding: 5px 8px;
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 5px;
            font-size: 8pt;
            color: #92400e;
        }
        .multi-employee-box {
            margin-top: 6px;
            padding: 4px 8px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-size: 7.5pt;
            color: #334155;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        table.data-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            font-size: 7.5pt;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge-entrada {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: bold;
            font-family: monospace;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #a7f3d0;
            display: inline-block;
        }
        .badge-salida {
            background-color: #fff7ed;
            color: #c2410c;
            font-weight: bold;
            font-family: monospace;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #fed7aa;
            display: inline-block;
        }
        .badge-en-curso {
            background-color: #fef3c7;
            color: #b45309;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 3px;
            display: inline-block;
            font-size: 6.5pt;
            text-transform: uppercase;
        }
        .badge-retro {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
            display: inline-block;
            font-size: 6.5pt;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .badge-alerta {
            display: inline-block;
            font-size: 6.5pt;
            font-weight: bold;
            color: #b91c1c;
            background-color: #fee2e2;
            padding: 1px 4px;
            border-radius: 3px;
            margin-top: 1px;
            border: 0.5px solid #f87171;
        }
        .badge-edit {
            background-color: #fffbeb;
            color: #b45309;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
            display: inline-block;
            font-size: 6.5pt;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .real-time {
            color: #64748b;
            font-size: 6.5pt;
            display: block;
            margin-top: 1px;
        }
        .total-time {
            font-weight: bold;
            color: #0f172a;
        }
        .section-title {
            background-color: #f1f5f9;
            border-left: 4px solid #d97706;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            color: #0f172a;
            margin-top: 16px;
            margin-bottom: 6px;
        }
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.summary-table th {
            background-color: #334155;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 8px;
            text-align: left;
            border: 1px solid #334155;
        }
        table.summary-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 7.5pt;
            vertical-align: middle;
        }
        table.summary-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .summary-total-row td {
            background-color: #fef3c7 !important;
            border-top: 2px solid #d97706 !important;
            font-weight: bold !important;
        }
    </style>
</head>
<body>
    <!-- Encabezado Principal -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 68%;">
                <table style="border-collapse: collapse;">
                    <tr>
                        @if(file_exists(public_path('images/utrecar.png')))
                            <td style="padding-right: 12px; vertical-align: middle;">
                                <img src="{{ public_path('images/utrecar.png') }}" style="max-height: 38px; width: auto;" alt="Logo Utrecar">
                            </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div class="header-subtitle">UTRECAR - ACTIVE NETWORK</div>
                            <div class="header-title">Control General de Fichajes de Empleados</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-meta" style="vertical-align: middle; width: 32%;">
                <strong>Fecha de Emisión:</strong> {{ $generatedAt }}<br>
                <strong>Total Registros:</strong> {{ count($fichajes) }}<br>
                <strong>Usuario Emisor:</strong> {{ auth()->user()->name ?? 'Administrador' }}
            </td>
        </tr>
    </table>

    <!-- Resumen de Filtros Aplicados -->
    <div class="filters-box">
        <table class="filters-table">
            <tr>
                <td style="width: 25%;">
                    <span class="filter-label">Rango de Fechas:</span><br>
                    <span class="filter-value">
                        @if($filterDateFrom && $filterDateTo)
                            Del {{ \Carbon\Carbon::parse($filterDateFrom)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($filterDateTo)->format('d/m/Y') }}
                        @elseif($filterDateFrom)
                            Desde {{ \Carbon\Carbon::parse($filterDateFrom)->format('d/m/Y') }}
                        @elseif($filterDateTo)
                            Hasta {{ \Carbon\Carbon::parse($filterDateTo)->format('d/m/Y') }}
                        @else
                            Histórico Completo (Sin filtro de fecha)
                        @endif
                    </span>
                </td>
                <td style="width: 20%;">
                    <span class="filter-label">Ubicación:</span><br>
                    <span class="filter-value">
                        {{ $filterGasolineraNombre ? $filterGasolineraNombre : 'Todas' }}
                    </span>
                </td>
                <td style="width: 25%;">
                    <span class="filter-label">Filtro de Empleado:</span><br>
                    <span class="filter-value">
                        {{ $filterSearch ? $filterSearch : 'Todos los empleados' }}
                    </span>
                </td>
                @php
                    $criterioOrden = match($sortField) {
                        'fecha' => ($sortDirection === 'desc') ? 'Fecha (más recientes primero)' : 'Fecha (más antiguos primero)',
                        'apellidos' => ($sortDirection === 'asc') ? 'Apellidos (de la A a la Z)' : 'Apellidos (de la Z a la A)',
                        'nombre' => ($sortDirection === 'asc') ? 'Nombre (de la A a la Z)' : 'Nombre (de la Z a la A)',
                        default => ucfirst($sortField) . ' (' . ($sortDirection === 'asc' ? 'Ascendente' : 'Descendente') . ')'
                    };
                @endphp
                <td style="width: 28%;">
                    <span class="filter-label">Criterio de Orden:</span><br>
                    <span class="filter-value">
                        {{ $criterioOrden }}
                    </span>
                </td>
                <td style="width: 17%; text-align: right;">
                    <span class="filter-label">Total Exportado:</span><br>
                    <span class="filter-value" style="color: #d97706; font-size: 8.5pt;">
                        {{ count($fichajes) }} registros
                    </span>
                </td>
            </tr>
        </table>

        @if($isSingleEmployee && $singleEmpleado)
            <!-- Bloque Destacado de Horas para Empleado Concreto -->
            <div class="single-employee-box">
                @php
                    $hEmp = floor($totalMinutosGlobal / 60);
                    $mEmp = $totalMinutosGlobal % 60;
                    $decEmp = number_format($totalMinutosGlobal / 60, 2, ',', '.');
                @endphp
                <strong>Empleado:</strong> {{ mb_strtoupper($singleEmpleado['apellidos']) }}, {{ mb_strtoupper($singleEmpleado['nombre']) }} &nbsp;|&nbsp; 
                <strong>Centro:</strong> {{ $singleEmpleado['ubicacion'] }} &nbsp;|&nbsp; 
                <strong style="color: #b45309; font-size: 9pt;">Suma Total de Horas: {{ $hEmp }} h {{ $mEmp }} m ({{ $decEmp }} horas)</strong> &nbsp;|&nbsp; 
                <strong>Jornadas:</strong> {{ $singleEmpleado['total_fichajes'] }} ({{ $singleEmpleado['fichajes_completos'] }} completadas, {{ $singleEmpleado['fichajes_en_curso'] }} en curso)
            </div>
        @else
            <!-- Resumen Consolidado de Horas del Periodo -->
            <div class="multi-employee-box">
                @php
                    $hTot = floor($totalMinutosGlobal / 60);
                    $mTot = $totalMinutosGlobal % 60;
                    $decTot = number_format($totalMinutosGlobal / 60, 2, ',', '.');
                @endphp
                <strong>Total Trabajadores en Informe:</strong> {{ count($desglosePorEmpleado) }} &nbsp;|&nbsp; 
                <strong>Jornadas Totales:</strong> {{ count($fichajes) }} ({{ $totalFichajesCompletosGlobal }} completadas, {{ $totalFichajesEnCursoGlobal }} en curso) &nbsp;|&nbsp; 
                <strong style="color: #b45309;">Suma Total de Horas Registradas: {{ $hTot }} h {{ $mTot }} m ({{ $decTot }} horas)</strong>
            </div>
        @endif
    </div>

    <!-- Tabla Principal de Fichajes -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 17%;">Apellidos</th>
                <th style="width: 14%;">Nombre</th>
                <th style="width: 17%;">Ubicación</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 17%;">Hora Entrada</th>
                <th style="width: 17%;">Hora Salida</th>
                <th style="width: 8%; text-align: right;">Tiempo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fichajes as $fichaje)
                <tr>
                    <td style="font-weight: bold; text-transform: uppercase;">
                        {{ $fichaje->empleado ? mb_strtoupper($fichaje->empleado->apellidos ?? '') : 'N/A' }}
                    </td>
                    <td style="font-weight: bold; text-transform: uppercase;">
                        {{ $fichaje->empleado ? mb_strtoupper($fichaje->empleado->nombre ?? '') : '—' }}
                    </td>
                    <td style="text-transform: uppercase; color: #475569;">
                        {{ $fichaje->empleado?->gasolinera?->Nombre ?? '—' }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($fichaje->fecha)->format('d/m/Y') }}
                        @if($fichaje->is_retroactive)
                            <br><span class="badge-retro">Retroactivo</span>
                        @endif
                        @if($fichaje->is_edited)
                            <br><span class="badge-edit">Modificado</span>
                        @endif
                    </td>
                    <td>
                        @if($fichaje->hora_entrada)
                            <span class="badge-entrada">{{ \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') }}</span>
                        @else
                            -
                        @endif
                        @if($fichaje->server_checkin_at)
                            <span class="real-time">Real: {{ $fichaje->server_checkin_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</span>
                            @if($fichaje->isEntradaDescuadre())
                                <br><span class="badge-alerta">⚠️ Descuadre &gt; 5m</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($fichaje->hora_salida)
                            <span class="badge-salida">{{ \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') }}</span>
                            @if($fichaje->server_checkout_at)
                                <span class="real-time">Real: {{ $fichaje->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</span>
                                @if($fichaje->isSalidaDescuadre())
                                    <br><span class="badge-alerta">⚠️ Descuadre &gt; 5m</span>
                                @endif
                            @endif
                        @else
                            <span class="badge-en-curso">En curso</span>
                        @endif
                    </td>
                    <td style="text-align: right;" class="total-time">
                        @php
                            if ($fichaje->hora_entrada && $fichaje->hora_salida) {
                                $fechaStr = $fichaje->fecha ? ($fichaje->fecha instanceof \Carbon\Carbon ? $fichaje->fecha->format('Y-m-d') : substr((string)$fichaje->fecha, 0, 10)) : '2000-01-01';
                                $t1 = \Carbon\Carbon::parse($fechaStr . ' ' . substr((string)$fichaje->hora_entrada, 0, 5));
                                $t2 = \Carbon\Carbon::parse($fechaStr . ' ' . substr((string)$fichaje->hora_salida, 0, 5));
                                if ($t2->lessThan($t1)) {
                                    $t2->addDay();
                                }
                                $diff = $t1->diff($t2);
                                echo $diff->format('%hh %im');
                            } else {
                                echo '-';
                            }
                        @endphp
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">
                        No se encontraron registros de fichajes con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #cbd5e1;">
                <td colspan="6" style="text-align: right; text-transform: uppercase; font-size: 7.5pt; color: #475569; padding: 5px 8px;">
                    Suma Total de Horas Computadas:
                </td>
                <td style="text-align: right; font-size: 8pt; color: #b45309; font-weight: bold; padding: 5px 8px;">
                    @php
                        $hG = floor($totalMinutosGlobal / 60);
                        $mG = $totalMinutosGlobal % 60;
                    @endphp
                    {{ $hG }}h {{ $mG }}m
                </td>
            </tr>
        </tfoot>
    </table>

    @if(!$isSingleEmployee && count($desglosePorEmpleado) > 0)
        <!-- Sección de Desglose de Horas por Empleado (al final del informe) -->
        <div style="page-break-inside: avoid;">
            <div class="section-title">
                Resumen y Desglose de Horas por Empleado
            </div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Empleado (Apellidos, Nombre)</th>
                        <th style="width: 14%;">DNI</th>
                        <th style="width: 22%;">Ubicación de Trabajo</th>
                        <th style="width: 14%; text-align: center;">Jornadas (Comp. / Curso)</th>
                        <th style="width: 18%; text-align: right;">Total Horas Registradas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($desglosePorEmpleado as $item)
                        <tr>
                            <td style="font-weight: bold; text-transform: uppercase;">
                                {{ mb_strtoupper($item['apellidos']) }}, {{ mb_strtoupper($item['nombre']) }}
                            </td>
                            <td style="font-family: monospace; color: #475569;">
                                {{ $item['dni'] ?: '—' }}
                            </td>
                            <td style="text-transform: uppercase; color: #475569;">
                                {{ $item['ubicacion'] }}
                            </td>
                            <td style="text-align: center;">
                                <strong>{{ $item['total_fichajes'] }}</strong> 
                                <span style="color: #64748b; font-size: 7pt;">({{ $item['fichajes_completos'] }} comp. @if($item['fichajes_en_curso'] > 0)<span style="color: #d97706;">+ {{ $item['fichajes_en_curso'] }} curso</span>@endif)</span>
                            </td>
                            <td style="text-align: right; font-weight: bold; color: #0f172a;">
                                @php
                                    $h = floor($item['total_minutos'] / 60);
                                    $m = $item['total_minutos'] % 60;
                                    $dec = number_format($item['total_minutos'] / 60, 2, ',', '.');
                                @endphp
                                {{ $h }}h {{ $m }}m <span style="font-size: 7pt; color: #64748b;">({{ $dec }}h)</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="summary-total-row">
                        <td colspan="3" style="font-weight: bold; text-transform: uppercase; font-size: 8pt;">
                            TOTAL GENERAL CONSOLIDADO ({{ count($desglosePorEmpleado) }} EMPLEADOS)
                        </td>
                        <td style="text-align: center; font-weight: bold; font-size: 8pt;">
                            {{ count($fichajes) }} jornadas
                        </td>
                        <td style="text-align: right; font-weight: bold; color: #b45309; font-size: 8.5pt;">
                            @php
                                $hGlobal = floor($totalMinutosGlobal / 60);
                                $mGlobal = $totalMinutosGlobal % 60;
                                $decGlobal = number_format($totalMinutosGlobal / 60, 2, ',', '.');
                            @endphp
                            {{ $hGlobal }}h {{ $mGlobal }}m ({{ $decGlobal }}h)
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT . "  |  Utrecar - Control General de Fichajes de Empleados";
            $font = $fontMetrics->get_font("DejaVu Sans, Helvetica", "normal");
            $size = 7;
            $y = 565;
            $x = 420 - ($fontMetrics->get_text_width($text, $font, $size) / 2);
            $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.45, 0.5]);
        }
    </script>
</body>
</html>
