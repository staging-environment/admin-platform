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
            margin-bottom: 12px;
            border-bottom: 2px solid #d97706;
            padding-bottom: 8px;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .header-subtitle {
            font-size: 9pt;
            color: #d97706;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
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
            margin-bottom: 12px;
            font-size: 7.5pt;
        }
        .filters-table {
            width: 100%;
            border-collapse: collapse;
        }
        .filters-table td {
            padding: 2px 6px;
            vertical-align: top;
        }
        .filter-label {
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
        }
        .filter-value {
            color: #0f172a;
            font-weight: 600;
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
            padding: 6px 6px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        table.data-table td {
            padding: 5px 6px;
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
            border-radius: 4px;
            border: 1px solid #a7f3d0;
            display: inline-block;
        }
        .badge-salida {
            background-color: #fff7ed;
            color: #c2410c;
            font-weight: bold;
            font-family: monospace;
            padding: 2px 4px;
            border-radius: 4px;
            border: 1px solid #fed7aa;
            display: inline-block;
        }
        .badge-en-curso {
            background-color: #fef3c7;
            color: #b45309;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #fde68a;
            display: inline-block;
        }
        .real-time {
            color: #64748b;
            font-size: 6.5pt;
            font-family: monospace;
            display: block;
            margin-top: 2px;
            white-space: nowrap;
        }
        .badge-retro {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-size: 6.5pt;
            padding: 1px 3px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            margin-top: 2px;
        }
        .badge-edit {
            background-color: #fef3c7;
            color: #b45309;
            font-size: 6.5pt;
            padding: 1px 3px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            margin-top: 2px;
        }
        .total-time {
            font-weight: bold;
            color: #0f172a;
        }
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            height: 6mm;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <!-- Encabezado corporativo -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="header-subtitle">Utrecar - Active Network</div>
                <div class="header-title">Control General de Fichajes de Empleados</div>
            </td>
            <td class="header-meta" style="vertical-align: middle;">
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
                <td style="width: 35%;">
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
                    <span class="filter-value" style="color: #d97706; font-size: 9pt;">
                        {{ count($fichajes) }} fichajes
                    </span>
                </td>
            </tr>
        </table>
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
                        @endif
                    </td>
                    <td>
                        @if($fichaje->hora_salida)
                            <span class="badge-salida">{{ \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') }}</span>
                            @if($fichaje->server_checkout_at)
                                <span class="real-time">Real: {{ $fichaje->server_checkout_at->timezone('Europe/Madrid')->format('d/m/Y H:i:s') }}</span>
                            @endif
                        @else
                            <span class="badge-en-curso">En curso</span>
                        @endif
                    </td>
                    <td style="text-align: right;" class="total-time">
                        @php
                            if ($fichaje->hora_salida) {
                                $t1 = \Carbon\Carbon::parse($fichaje->hora_entrada);
                                $t2 = \Carbon\Carbon::parse($fichaje->hora_salida);
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
    </table>

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
