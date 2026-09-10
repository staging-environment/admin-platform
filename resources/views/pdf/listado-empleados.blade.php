<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Empleados - Recursos Humanos - Utrecar</title>
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
        .badge-alta {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #a7f3d0;
            display: inline-block;
        }
        .badge-baja {
            background-color: #fef2f2;
            color: #b91c1c;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #fecaca;
            display: inline-block;
        }
        .badge-baja-medica {
            background-color: #fff1f2;
            color: #e11d48;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #fecdd3;
            display: inline-block;
        }
        .badge-alerta {
            background-color: #fee2e2;
            color: #b91c1c;
            font-size: 6.5pt;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            margin-top: 2px;
            border: 1px solid #fca5a5;
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
    @php
        $logoPath = public_path('ronda_norte_logo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    @endphp
    <!-- Encabezado corporativo -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 68%;">
                <table style="border-collapse: collapse; margin: 0; padding: 0;">
                    <tr>
                        @if($logoBase64)
                            <td style="vertical-align: middle; padding-right: 12px; width: 42px;">
                                <img src="{{ $logoBase64 }}" alt="Utrecar" style="height: 38px; width: auto; display: block;" />
                            </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div class="header-subtitle">UTRECAR - ACTIVE NETWORK</div>
                            <div class="header-title">Listado de Empleados - Recursos Humanos</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-meta" style="vertical-align: middle; width: 32%;">
                <strong>Fecha de Emisión:</strong> {{ $generatedAt }}<br>
                <strong>Total Registros:</strong> {{ count($empleados) }}<br>
                <strong>Usuario Emisor:</strong> {{ auth()->user()->name ?? 'Administrador' }}
            </td>
        </tr>
    </table>

    <!-- Resumen de Filtros Aplicados -->
    <div class="filters-box">
        <table class="filters-table">
            <tr>
                <td style="width: 25%;">
                    <span class="filter-label">Ubicación de trabajo:</span><br>
                    <span class="filter-value">
                        {{ $centroTrabajo ? $centroTrabajo : 'Todas las ubicaciones' }}
                    </span>
                </td>
                <td style="width: 22%;">
                    <span class="filter-label">Estado:</span><br>
                    <span class="filter-value">
                        @php
                            $estadoText = match($estado) {
                                'Alta' => 'Alta / Activo',
                                'Baja_Empresa', 'Baja' => 'Baja en la empresa',
                                'Baja_Medica' => 'Baja médica',
                                default => $estado ?: 'Todos los estados'
                            };
                        @endphp
                        {{ $estadoText }}
                    </span>
                </td>
                <td style="width: 25%;">
                    <span class="filter-label">Filtro de búsqueda:</span><br>
                    <span class="filter-value">
                        {{ $search ? $search : 'Todos los registros' }}
                    </span>
                </td>
                @php
                    $criterioOrden = match($sortColumn) {
                        'apellidos' => ($sortDirection === 'asc') ? 'Apellidos (de la A a la Z)' : 'Apellidos (de la Z a la A)',
                        'nombre' => ($sortDirection === 'asc') ? 'Nombre (de la A a la Z)' : 'Nombre (de la Z a la A)',
                        'puesto' => ($sortDirection === 'asc') ? 'Puesto (de la A a la Z)' : 'Puesto (de la Z a la A)',
                        'gasolinera.Nombre', 'gasolinera' => ($sortDirection === 'asc') ? 'Ubicación (Ascendente)' : 'Ubicación (Descendente)',
                        'estado' => ($sortDirection === 'asc') ? 'Estado (Ascendente)' : 'Estado (Descendente)',
                        default => ucfirst(str_replace('_', ' ', $sortColumn)) . ' (' . ($sortDirection === 'asc' ? 'Ascendente' : 'Descendente') . ')'
                    };
                @endphp
                <td style="width: 28%;">
                    <span class="filter-label">Criterio de Orden:</span><br>
                    <span class="filter-value">
                        {{ $criterioOrden }}
                    </span>
                </td>
                <td style="width: 15%; text-align: right;">
                    <span class="filter-label">Total Exportado:</span><br>
                    <span class="filter-value" style="color: #d97706; font-size: 9pt;">
                        {{ count($empleados) }} empleados
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabla Principal de Empleados -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 11%;">Situación</th>
                <th style="width: 21%;">Apellidos</th>
                <th style="width: 16%;">Nombre</th>
                <th style="width: 11%;">DNI</th>
                <th style="width: 11%;">Teléfono</th>
                <th style="width: 15%;">Ubicación</th>
                <th style="width: 15%;">Puesto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empleados as $emp)
                @php
                    $isOnBajaMedica = $emp->ausencias ? $emp->ausencias->where('tipo', 'Bajas médicas')->whereNull('fecha_fin')->count() > 0 : false;
                    $alertasCount = $emp->alertas ? $emp->alertas->count() : 0;
                @endphp
                <tr>
                    <td>
                        @if($emp->estado === 'Baja')
                            <span class="badge-baja">Baja</span>
                        @elseif($isOnBajaMedica)
                            <span class="badge-baja-medica">Baja médica</span>
                        @else
                            <span class="badge-alta">Alta</span>
                        @endif

                        @if($alertasCount > 0)
                            <br><span class="badge-alerta">{{ $alertasCount }} {{ $alertasCount === 1 ? 'alerta' : 'alertas' }}</span>
                        @endif
                    </td>
                    <td style="font-weight: bold; text-transform: uppercase;">
                        {{ mb_strtoupper(trim($emp->apellidos ?? '—')) }}
                    </td>
                    <td style="font-weight: bold; text-transform: uppercase;">
                        {{ mb_strtoupper(trim($emp->nombre ?? '—')) }}
                    </td>
                    <td style="font-family: monospace; color: #334155;">
                        {{ $emp->dni ?: '—' }}
                    </td>
                    <td style="font-family: monospace; color: #475569; white-space: nowrap;">
                        {{ $emp->telefono_principal ?: ($emp->telefono_secundario ?: '—') }}
                    </td>
                    <td style="text-transform: uppercase; color: #475569;">
                        {{ $emp->gasolinera?->Nombre ?? '—' }}
                    </td>
                    <td style="text-transform: uppercase; color: #475569;">
                        {{ $emp->puesto ?: '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">
                        No se encontraron empleados con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT . "  |  Utrecar - Listado de Empleados";
            $font = $fontMetrics->get_font("DejaVu Sans, Helvetica", "normal");
            $size = 7;
            $y = 565;
            $x = 420 - ($fontMetrics->get_text_width($text, $font, $size) / 2);
            $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.45, 0.5]);
        }
    </script>
</body>
</html>
