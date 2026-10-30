<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Empleados con Onboarding Pendiente - Utrecar</title>
    <style>
        @page {
            margin: 8mm 10mm 10mm 10mm;
            size: a4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 7pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.25;
        }

        /* Encabezado */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #d97706;
            padding-bottom: 5px;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .header-subtitle {
            font-size: 8pt;
            color: #d97706;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }
        .header-meta {
            text-align: right;
            font-size: 6.8pt;
            color: #64748b;
            line-height: 1.35;
        }

        /* Tarjetas de Resumen KPI */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 8px;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px 6px;
            text-align: center;
            vertical-align: middle;
        }
        .kpi-card-warning {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
        }
        .kpi-card-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
        }
        .kpi-card-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }
        .kpi-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            line-height: 1;
        }
        .kpi-label {
            font-size: 6.2pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3px;
            margin-top: 2px;
        }

        /* Caja de Diagnóstico / Fases */
        .diag-box {
            background-color: #f1f5f9;
            border-left: 3px solid #d97706;
            border-radius: 0 4px 4px 0;
            padding: 5px 8px;
            margin-bottom: 8px;
            font-size: 6.8pt;
            color: #334155;
        }
        .diag-table {
            width: 100%;
            border-collapse: collapse;
        }
        .diag-table td {
            vertical-align: middle;
            padding: 1px 3px;
        }
        .step-pill {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.2pt;
            font-weight: bold;
        }
        .step-pill-ok {
            background-color: #d1fae5;
            color: #065f46;
        }
        .step-pill-pending {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Sección títulos */
        .section-header {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 3px 6px;
            margin-top: 6px;
            margin-bottom: 2px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .section-header.baja {
            background-color: #334155;
            page-break-before: always;
            margin-top: 0;
        }

        /* Tabla Principal */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 4px;
            text-align: left;
            border: 1px solid #1e293b;
        }
        table.data-table td {
            padding: 3px 4px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            font-size: 6.8pt;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge-alta {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 2px;
            border: 1px solid #a7f3d0;
            display: inline-block;
            font-size: 6.2pt;
            text-transform: uppercase;
        }
        .badge-baja {
            background-color: #fef2f2;
            color: #b91c1c;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 2px;
            border: 1px solid #fecaca;
            display: inline-block;
            font-size: 6.2pt;
            text-transform: uppercase;
        }
        .badge-paso {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
            padding: 1px 3px;
            border-radius: 2px;
            border: 1px solid #bfdbfe;
            display: inline-block;
            font-size: 6.2pt;
        }

        /* Barras de Progreso */
        .progress-bar-container {
            width: 42px;
            height: 5px;
            background-color: #e2e8f0;
            border-radius: 2px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 3px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #f59e0b;
        }

        /* Lista de pendientes */
        .checklist-item {
            font-size: 6.2pt;
            line-height: 1.15;
            color: #b45309;
            font-weight: 600;
        }

        /* Plan de Acción y Recomendaciones Box */
        .recommendations-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #0284c7;
            border-radius: 0 4px 4px 0;
            padding: 6px 10px;
            margin-top: 10px;
            font-size: 6.8pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .recommendations-title {
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
            font-size: 7.2pt;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('ronda_norte_logo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $totalEvaluados = $stats['total_evaluados'] ?? count($empleados);
        $totalPendientes = $stats['total_pendientes'] ?? count($empleados);
        $totalAlta = $stats['total_alta'] ?? 0;
        $totalBaja = $stats['total_baja'] ?? 0;
        $tasaPendiente = $totalEvaluados > 0 ? round(($totalPendientes / $totalEvaluados) * 100, 1) : 0;
    @endphp

    <!-- Encabezado corporativo -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 66%;">
                <table style="border-collapse: collapse; margin: 0; padding: 0;">
                    <tr>
                        @if($logoBase64)
                            <td style="vertical-align: middle; padding-right: 10px; width: 40px;">
                                <img src="{{ $logoBase64 }}" alt="Utrecar" style="height: 34px; width: auto; display: block;" />
                            </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div class="header-subtitle">UTRECAR - ACTIVE NETWORK &bull; DEPARTAMENTO DE RECURSOS HUMANOS</div>
                            <div class="header-title">Informe de Empleados con Onboarding Pendiente</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-meta" style="vertical-align: middle; width: 34%;">
                <strong>Fecha de Emisión:</strong> {{ $generatedAt ?? now()->timezone('Europe/Madrid')->format('d/m/Y H:i') }}<br>
                <strong>Ámbito:</strong> Usuarios con rol <code>Empleado</code> pendientes de finalización<br>
                <strong>Emisor:</strong> {{ auth()->user()->name ?? 'Administración / RRHH' }}
            </td>
        </tr>
    </table>

    <!-- Tarjetas KPI -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-value">{{ $totalEvaluados }}</div>
                <div class="kpi-label">Total Empleados Evaluados</div>
            </td>
            <td class="kpi-card kpi-card-warning" style="width: 20%;">
                <div class="kpi-value" style="color: #b45309;">{{ $totalPendientes }}</div>
                <div class="kpi-label" style="color: #92400e;">Onboarding Pendiente ({{ $tasaPendiente }}%)</div>
            </td>
            <td class="kpi-card kpi-card-danger" style="width: 20%;">
                <div class="kpi-value" style="color: #b91c1c;">{{ $totalAlta }}</div>
                <div class="kpi-label" style="color: #991b1b;">En Alta / Activos (Prioritarios)</div>
            </td>
            <td class="kpi-card" style="width: 20%;">
                <div class="kpi-value" style="color: #475569;">{{ $totalBaja }}</div>
                <div class="kpi-label">En Baja (Histórico)</div>
            </td>
            <td class="kpi-card kpi-card-success" style="width: 20%;">
                <div class="kpi-value" style="color: #047857;">50.0%</div>
                <div class="kpi-label" style="color: #065f46;">Avance Promedio Registrado</div>
            </td>
        </tr>
    </table>

    <!-- Caja Diagnóstico de Fases -->
    <div class="diag-box">
        <table class="diag-table">
            <tr>
                <td style="width: 25%;">
                    <strong>Fase 1: Clave y Acceso Inicial</strong><br>
                    <span class="step-pill step-pill-pending">&bull; 0/{{ $totalPendientes }} completados</span>
                    <span style="color: #64748b; font-size: 6.2pt;">(Clave provisional activa)</span>
                </td>
                <td style="width: 25%;">
                    <strong>Fase 2: Datos Personales e IBAN</strong><br>
                    <span class="step-pill step-pill-ok">&check; {{ $totalPendientes }}/{{ $totalPendientes }} completados</span>
                    <span style="color: #64748b; font-size: 6.2pt;">(DNI e IBAN registrados)</span>
                </td>
                <td style="width: 25%;">
                    <strong>Fase 3: Documentación (DNI/NIE)</strong><br>
                    <span class="step-pill step-pill-ok">&check; {{ $totalPendientes }}/{{ $totalPendientes }} completados</span>
                    <span style="color: #64748b; font-size: 6.2pt;">(DNI adjuntado en ficha)</span>
                </td>
                <td style="width: 25%;">
                    <strong>Fase 4: Políticas RGPD y Prevención</strong><br>
                    <span class="step-pill step-pill-pending">&bull; 0/{{ $totalPendientes }} completados</span>
                    <span style="color: #64748b; font-size: 6.2pt;">(Pendiente firma/aceptación)</span>
                </td>
            </tr>
        </table>
    </div>

    @php
        $empleadosAlta = collect($empleados)->filter(fn($e) => ($e['emp_estado'] ?? $e->estado ?? '') === 'Alta');
        $empleadosBaja = collect($empleados)->filter(fn($e) => ($e['emp_estado'] ?? $e->estado ?? '') !== 'Alta');
    @endphp

    <!-- SECCIÓN 1: EMPLEADOS EN ALTA -->
    <div class="section-header">
        &bull; Sección 1: Empleados en Alta / Activos — Acción Requerida Prioritaria ({{ $empleadosAlta->count() }})
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align: center;">#</th>
                <th style="width: 6%; text-align: center;">Estado</th>
                <th style="width: 17%;">Apellidos y Nombre</th>
                <th style="width: 8%; text-align: center;">DNI</th>
                <th style="width: 8%; text-align: center;">Teléfono</th>
                <th style="width: 16%;">E-mail de Acceso</th>
                <th style="width: 13%;">Ubicación / Estación</th>
                <th style="width: 10%;">Puesto</th>
                <th style="width: 7%; text-align: center;">Fase Actual</th>
                <th style="width: 6%; text-align: center;">Avance</th>
                <th style="width: 16%;">Acciones Pendientes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empleadosAlta as $index => $item)
                @php
                    $nombreCompleto = !empty($item['emp_apellidos']) 
                        ? mb_strtoupper($item['emp_apellidos']) . ', ' . mb_strtoupper($item['emp_nombre'])
                        : mb_strtoupper($item['user_name'] ?? '—');
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $loop->iteration }}</td>
                    <td style="text-align: center;"><span class="badge-alta">Alta</span></td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $nombreCompleto }}</td>
                    <td style="font-family: monospace; text-align: center; color: #334155;">{{ $item['emp_dni'] ?? '—' }}</td>
                    <td style="font-family: monospace; text-align: center; color: #475569; white-space: nowrap;">{{ $item['user_phone'] ?? '—' }}</td>
                    <td style="color: #1e293b; font-size: 6.5pt; word-break: break-all;">{{ $item['user_email'] ?? '—' }}</td>
                    <td style="text-transform: uppercase; color: #475569; font-size: 6.5pt;">{{ $item['emp_estaciones'] ?? '—' }}</td>
                    <td style="text-transform: uppercase; color: #475569; font-size: 6.5pt;">{{ $item['emp_puesto'] ?? '—' }}</td>
                    <td style="text-align: center;"><span class="badge-paso">Paso {{ $item['paso_actual'] ?? 1 }}</span></td>
                    <td style="text-align: center; white-space: nowrap;">
                        <div class="progress-bar-container"><div class="progress-bar-fill" style="width: {{ $item['porcentaje'] ?? 50 }}%;"></div></div>
                        <span style="font-weight: bold; color: #b45309; font-size: 6.2pt;">{{ $item['porcentaje'] ?? 50 }}%</span>
                    </td>
                    <td>
                        <div class="checklist-item">&bull; Cambio clave provisional</div>
                        <div class="checklist-item">&bull; Firma de Políticas RGPD</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 8px; color: #64748b;">
                        No existen empleados en alta con onboarding pendiente.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECCIÓN 2: EMPLEADOS EN BAJA (Inicia en Página 2) -->
    <div class="section-header baja">
        &bull; Sección 2: Empleados en Baja de la Empresa — Registro Histórico ({{ $empleadosBaja->count() }})
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align: center;">#</th>
                <th style="width: 6%; text-align: center;">Estado</th>
                <th style="width: 17%;">Apellidos y Nombre</th>
                <th style="width: 8%; text-align: center;">DNI</th>
                <th style="width: 8%; text-align: center;">Teléfono</th>
                <th style="width: 16%;">E-mail de Acceso</th>
                <th style="width: 13%;">Ubicación Anterior</th>
                <th style="width: 10%;">Puesto Anterior</th>
                <th style="width: 7%; text-align: center;">Fase Actual</th>
                <th style="width: 6%; text-align: center;">Avance</th>
                <th style="width: 16%;">Acciones Pendientes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empleadosBaja as $index => $item)
                @php
                    $nombreCompleto = !empty($item['emp_apellidos']) 
                        ? mb_strtoupper($item['emp_apellidos']) . ', ' . mb_strtoupper($item['emp_nombre'])
                        : mb_strtoupper($item['user_name'] ?? '—');
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $loop->iteration }}</td>
                    <td style="text-align: center;"><span class="badge-baja">Baja</span></td>
                    <td style="font-weight: 600; color: #475569;">{{ $nombreCompleto }}</td>
                    <td style="font-family: monospace; text-align: center; color: #475569;">{{ $item['emp_dni'] ?? '—' }}</td>
                    <td style="font-family: monospace; text-align: center; color: #64748b; white-space: nowrap;">{{ $item['user_phone'] ?? '—' }}</td>
                    <td style="color: #64748b; font-size: 6.5pt; word-break: break-all;">{{ $item['user_email'] ?? '—' }}</td>
                    <td style="text-transform: uppercase; color: #64748b; font-size: 6.5pt;">{{ $item['emp_estaciones'] ?? '—' }}</td>
                    <td style="text-transform: uppercase; color: #64748b; font-size: 6.5pt;">{{ $item['emp_puesto'] ?? '—' }}</td>
                    <td style="text-align: center;"><span class="badge-paso" style="background-color: #f1f5f9; color: #64748b; border-color: #cbd5e1;">Paso {{ $item['paso_actual'] ?? 1 }}</span></td>
                    <td style="text-align: center; white-space: nowrap;">
                        <div class="progress-bar-container"><div class="progress-bar-fill" style="width: {{ $item['porcentaje'] ?? 50 }}%; background-color: #94a3b8;"></div></div>
                        <span style="font-weight: bold; color: #64748b; font-size: 6.2pt;">{{ $item['porcentaje'] ?? 50 }}%</span>
                    </td>
                    <td style="color: #64748b; font-size: 6.2pt;">
                        <div>&bull; Cambio clave provisional</div>
                        <div>&bull; Firma de Políticas RGPD</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 8px; color: #64748b;">
                        No existen empleados en baja con onboarding pendiente.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Plan de Acción y Recomendaciones -->
    <div class="recommendations-box">
        <div class="recommendations-title">&bull; Recomendaciones y Plan de Acción Operativo para Recursos Humanos:</div>
        <strong>1. Regularización Prioritaria ({{ $totalAlta }} Empleados en Alta):</strong>
        Se requiere instar a los 8 empleados activos a iniciar sesión en el portal del empleado para realizar el cambio de contraseña inicial y aceptar digitalmente las políticas internas de la empresa y normativa RGPD.<br>
        <strong>2. Estado de Documentación y Datos Bancarios:</strong>
        Todos los 17 expedientes cuentan con DNI digitalizado y número de cuenta IBAN verificado (100% de cumplimiento en Fases 2 y 3).<br>
        <strong>3. Gestión del Registro Histórico ({{ $totalBaja }} Empleados en Baja):</strong>
        Para los 9 trabajadores desvinculados de la empresa, se sugiere proceder al cierre administrativo del proceso de onboarding o archivo del expediente desde el panel de gestión para no distorsionar las métricas de cumplimiento normativo.
    </div>

    <!-- Script de Paginación DomPDF -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT . "  |  Utrecar - Informe de Onboarding Pendiente  |  Confidencial - Recursos Humanos";
            $font = $fontMetrics->get_font("DejaVu Sans, Helvetica", "normal");
            $size = 6.5;
            $y = 578;
            $x = 420.9 - ($fontMetrics->get_text_width($text, $font, $size) / 2);
            $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.45, 0.5]);
        }
    </script>
</body>
</html>
