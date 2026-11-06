<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Planing {{ $nombreEstacion }} - {{ $mesNombre }}</title>
    <style>
        @page {
            margin: 8mm 10mm 8mm 10mm;
            size: a4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 7pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        /* Encabezado */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border-bottom: 2px solid #d97706;
            padding-bottom: 4px;
        }
        .header-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.2px;
        }
        .header-subtitle {
            font-size: 8pt;
            color: #d97706;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        .header-meta {
            text-align: right;
            font-size: 7.5pt;
            color: #475569;
            vertical-align: middle;
        }

        /* Cuadrícula Mensual */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        .grid-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 6pt;
            text-align: center;
            padding: 3px 1px;
            border: 0.5px solid #334155;
            font-weight: bold;
        }

        .grid-table th.col-turno-header {
            width: 85px;
            text-align: left;
            padding-left: 5px;
            background-color: #0f172a;
        }

        .grid-table th.weekend-header {
            background-color: #b45309;
            color: #ffffff;
        }

        .grid-table td {
            border: 0.5px solid #cbd5e1;
            padding: 2.5px 1px;
            text-align: center;
            vertical-align: middle;
            font-size: 5.8pt;
            height: 28px;
        }

        .grid-table td.col-turno {
            text-align: left;
            font-weight: bold;
            background-color: #f8fafc;
            color: #0f172a;
            padding-left: 5px;
            font-size: 6.2pt;
        }

        .grid-table td.weekend-cell {
            background-color: #fffbeb;
        }

        .badge-empleado {
            display: block;
            border-radius: 2.5px;
            padding: 2px 1px;
            font-weight: bold;
            font-size: 5.6pt;
            text-transform: uppercase;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sustitucion-badge {
            border: 1px dashed #dc2626 !important;
            font-weight: 800;
        }

        /* Sección Inferior: Observaciones y Firmas */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .observaciones-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 5px 7px;
            font-size: 6.5pt;
            min-height: 48px;
        }

        .legend-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 4px 6px;
            font-size: 6pt;
        }

        .signature-box {
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 5px 8px;
            text-align: center;
            height: 48px;
            vertical-align: middle;
            font-size: 6.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- Encabezado del Cuadrante --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <div class="header-title">{{ mb_strtoupper($nombreEstacion) }}</div>
                <div class="header-subtitle">Cuadrante Oficial de Turnos de Trabajo &bull; Grupo Utrecar</div>
            </td>
            <td class="header-meta" style="width: 40%;">
                <strong style="font-size: 11pt; color: #0f172a;">{{ $mesNombre }}</strong><br>
                <span>Generado el {{ now()->format('d/m/Y H:i') }} &bull; Versión Oficial</span>
            </td>
        </tr>
    </table>

    {{-- Tabla Cuadrícula Mensual --}}
    <table class="grid-table">
        <thead>
            <tr>
                <th class="col-turno-header">PUESTO / TURNO</th>
                @foreach($dias as $dia)
                    <th class="{{ $dia['esFinDeSemana'] ? 'weekend-header' : '' }}">
                        {{ $dia['diaSemana'] }}<br>{{ $dia['numero'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($filasTurnos as $fila)
                <tr>
                    <td class="col-turno">
                        {{ $fila['etiqueta'] }}
                    </td>

                    @foreach($dias as $dia)
                        @php
                            $key = $dia['fecha'] . '_' . $fila['puesto'] . '_' . $fila['turno'];
                            $turnoReg = $turnosMap[$key] ?? null;
                            $empleado = $turnoReg?->empleado;
                            $esSustitucion = $turnoReg?->es_sustitucion;
                        @endphp

                        <td class="{{ $dia['esFinDeSemana'] ? 'weekend-cell' : '' }}">
                            @if($empleado)
                                <div
                                    class="badge-empleado {{ $esSustitucion ? 'sustitucion-badge' : '' }}"
                                    style="background-color: {{ $empleado->color_bg }}; border: 0.8px solid {{ $empleado->color_border }}; color: {{ $empleado->color_text }};"
                                >
                                    {{ $empleado->nombre_corto }}{{ $esSustitucion ? '*' : '' }}
                                </div>
                            @else
                                <span style="color: #cbd5e1;">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pie: Observaciones, Leyenda y Firmas --}}
    <table class="footer-table">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                <div class="observaciones-box">
                    <strong style="color: #0f172a; text-transform: uppercase;">Observaciones y Acuerdos de Mes:</strong><br>
                    <div style="margin-top: 2px; color: #334155; line-height: 1.3;">
                        {{ $observaciones ?: 'Sin observaciones especiales registradas para este periodo.' }}
                    </div>
                    <div style="margin-top: 4px; font-size: 5.5pt; color: #94a3b8;">
                        * Asterisco (*): Turno cubierto mediante sustitución/suplencia por vacaciones, baja o refuerzo.
                    </div>
                </div>
            </td>

            <td style="width: 25%; vertical-align: top; padding-right: 6px;">
                <div class="legend-box">
                    <strong style="color: #0f172a; text-transform: uppercase; font-size: 6.2pt;">Plantilla:</strong><br>
                    <div style="margin-top: 3px;">
                        @foreach($empleadosLegenda as $emp)
                            <div style="margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <span style="display: inline-block; width: 6px; height: 6px; background-color: {{ $emp->color_bg }}; border: 1px solid {{ $emp->color_border }}; border-radius: 50%; margin-right: 3px;"></span>
                                <strong>{{ $emp->nombre_corto }}</strong> ({{ $emp->nombre }} {{ $emp->apellidos }})
                            </div>
                        @endforeach
                    </div>
                </div>
            </td>

            <td style="width: 25%; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="signature-box" style="width: 50%; border-right: none;">
                            Vº Bº Responsable RRHH<br><br>
                            <span style="font-size: 5pt; color: #94a3b8;">Firma / Fecha</span>
                        </td>
                        <td class="signature-box" style="width: 50%;">
                            Conforme Encargado/a E.S.<br><br>
                            <span style="font-size: 5pt; color: #94a3b8;">Firma / Fecha</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Script de Paginación DomPDF --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT . "  |  Utrecar S.L. - Módulo de Planing de Turnos";
            $font = $fontMetrics->get_font("DejaVu Sans, sans-serif", "normal");
            $size = 6;
            $y = 575;
            $x = 420 - ($fontMetrics->get_text_width($text, $font, $size) / 2);
            $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.45, 0.5]);
        }
    </script>
</body>
</html>

