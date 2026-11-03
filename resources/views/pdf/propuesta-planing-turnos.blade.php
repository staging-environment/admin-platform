<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Propuesta de Módulo de Planificación de Turnos - Utrecar</title>
    <style>
        @page {
            margin: 10mm 12mm 10mm 12mm;
            size: a4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.3;
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
            font-size: 13.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            line-height: 1.15;
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
            font-size: 6.8pt;
            color: #64748b;
            line-height: 1.35;
            vertical-align: middle;
        }

        /* Resumen Ejecutivo Destacado */
        .executive-box {
            background-color: #f8fafc;
            border-left: 3.5px solid #d97706;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 7px 10px;
            margin-bottom: 8px;
        }
        .executive-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 2px;
        }

        /* Tarjetas de Beneficios / Claves */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 8px;
        }
        .kpi-card {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 6px;
            text-align: center;
            vertical-align: top;
        }
        .kpi-card-highlight {
            border-color: #f59e0b;
            background-color: #fffbeb;
        }
        .kpi-value {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .kpi-label {
            font-size: 6.2pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: bold;
        }

        /* Títulos de Sección */
        .section-header {
            font-size: 9pt;
            font-weight: bold;
            color: #0f172a;
            background-color: #f1f5f9;
            padding: 4px 7px;
            border-left: 3px solid #0284c7;
            margin-top: 8px;
            margin-bottom: 6px;
            border-radius: 0 3px 3px 0;
        }

        /* Tablas Comparativas y de Datos */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .content-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 6.8pt;
            text-transform: uppercase;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
        }
        .content-table td {
            font-size: 7.2pt;
            padding: 3.5px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            line-height: 1.25;
        }
        .content-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Píldoras y Badges */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-green { background-color: #dcfce7; color: #166534; border: 0.5px solid #86efac; }
        .badge-amber { background-color: #fef3c7; color: #92400e; border: 0.5px solid #fcd34d; }
        .badge-blue { background-color: #e0f2fe; color: #075985; border: 0.5px solid #7dd3fc; }
        .badge-purple { background-color: #f3e8ff; color: #6b21a8; border: 0.5px solid #d8b4fe; }

        /* Simulación del Cuadrante Visual */
        .mockup-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.5pt;
            margin: 6px 0;
            text-align: center;
        }
        .mockup-table th {
            background-color: #334155;
            color: white;
            padding: 3.5px;
            font-size: 6.2pt;
            border: 1px solid #475569;
        }
        .mockup-table td {
            padding: 3.5px 2px;
            border: 1px solid #cbd5e1;
            font-weight: 600;
        }
        .mockup-header-col {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
            padding-left: 5px;
            color: #334155;
            width: 80px;
        }
        .cell-manana { background-color: #dbeafe; color: #1e40af; }
        .cell-tarde { background-color: #fef3c7; color: #92400e; }
        .cell-noche { background-color: #ede9fe; color: #5b21b6; }
        .cell-descanso { background-color: #f1f5f9; color: #94a3b8; font-style: italic; }
        .cell-sustituto { background-color: #dcfce7; color: #166534; font-weight: bold; border: 1.5px dashed #15803d !important; }

        .callout {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 6px 8px;
            font-size: 7.2pt;
            color: #1e3a8a;
            border-radius: 0 3px 3px 0;
            margin: 6px 0;
            line-height: 1.25;
        }

        .checklist-item {
            margin-bottom: 3px;
            font-size: 7.2pt;
            line-height: 1.25;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- ==================== PÁGINA 1: MOTIVACIÓN Y MODELO OPERATIVO ==================== -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 65%;">
                <div class="header-title">PLANIFICACIÓN MENSUAL DE TURNOS (PLANING)</div>
                <div class="header-subtitle">Propuesta Ejecutiva de Digitalización para Estaciones de Servicio</div>
            </td>
            <td class="header-meta" style="width: 35%;">
                <strong style="color: #0f172a; font-size: 7.5pt;">UTRECAR S.L. &bull; GRUPO EMPRESARIAL</strong><br>
                <strong>Destinatario:</strong> Dirección General / Gerencia<br>
                <strong>Área:</strong> Recursos Humanos y Operaciones<br>
                <strong>Fecha:</strong> Noviembre 2026 &bull; Versión 1.0
            </td>
        </tr>
    </table>

    <!-- Resumen Ejecutivo -->
    <div class="executive-box">
        <div class="executive-title">&bull; Resumen Ejecutivo para Dirección</div>
        Propuesta para sustituir los archivos manuales de Excel por un <strong>módulo visual de cuadrantes</strong> integrado directamente en la plataforma web de Utrecar. Conserva exactamente el método de trabajo habitual pero <strong>ahorra horas de gestión cada mes, evita errores en descansos obligatorios y permite sustituir turnos y exportar cuadrantes con un solo clic</strong>.
    </div>

    <!-- Indicadores Clave del Proyecto -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card kpi-card-highlight" style="width: 25%;">
                <div class="kpi-value" style="color: #b45309;">4 Estaciones</div>
                <div class="kpi-label">Cobertura Integral</div>
                <div style="font-size: 5.8pt; color: #78350f; margin-top: 1px;">Vistalegre, Ronda Norte, Rodalabota y Atenas</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-value" style="color: #0369a1;">Ciclo 4x2</div>
                <div class="kpi-label">Rotación Automática</div>
                <div style="font-size: 5.8pt; color: #64748b; margin-top: 1px;">4 días trabajo &bull; 2 días descanso</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-value" style="color: #047857;">100% Visual</div>
                <div class="kpi-label">Colores por Empleado</div>
                <div style="font-size: 5.8pt; color: #64748b; margin-top: 1px;">Reconocimiento inmediato del turno</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-value" style="color: #475569;">1 Clic</div>
                <div class="kpi-label">Generación Mensual</div>
                <div style="font-size: 5.8pt; color: #64748b; margin-top: 1px;">Cálculo automático del mes siguiente</div>
            </td>
        </tr>
    </table>

    <!-- Sección 1: Comparativa -->
    <div class="section-header">&bull; 1. ¿Por qué modernizar el sistema actual de Excel?</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 44%;">Situación Actual (Archivos Excel)</th>
                <th style="width: 56%;">Nuevo Módulo Integrado en Utrecar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Hojas de cálculo aisladas:</strong> Modificaciones en archivos locales que provocan versiones desactualizadas y confusiones.</td>
                <td><strong>Plataforma única y segura:</strong> Dirección, RRHH y encargados acceden a la versión oficial en tiempo real desde PC o móvil.</td>
            </tr>
            <tr>
                <td><strong>Cálculo manual del mes:</strong> Hay que cuadrar a mano la rotación 4x2 mes a mes para no descompensar turnos ni descansos.</td>
                <td><strong>Generador automático en 1 clic:</strong> El sistema calcula el mes respetando el ciclo del mes anterior de forma instantánea.</td>
            </tr>
            <tr>
                <td><strong>Sustituciones complejas:</strong> Anotaciones sueltas en texto al pie de hoja cuando alguien tiene vacaciones o baja.</td>
                <td><strong>Gestión visual de suplencias:</strong> Se asigna el suplente en 2 clics y la celda refleja quién cubre el puesto y por qué motivo.</td>
            </tr>
            <tr>
                <td><strong>Riesgo de errores:</strong> Ninguna alerta si se asigna a un empleado en dos sitios o en sus días de descanso legal.</td>
                <td><strong>Control de disponibilidad:</strong> Verificación automática de solapamientos, descansos mínimos y vacaciones aprobadas.</td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 2: El Modelo Operativo de Utrecar -->
    <div class="section-header">&bull; 2. El Modelo Operativo por Gasolinera</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 24%;">Estación</th>
                <th style="width: 18%;">Horario</th>
                <th style="width: 26%;">Estructura de Turnos</th>
                <th style="width: 32%;">Plantilla Asignada</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Vistalegre (Utrera)</strong></td>
                <td>Diurno (Sin noche)</td>
                <td><span class="badge badge-blue">Mañana</span> &bull; <span class="badge badge-amber">Tarde</span> (1 puesto)</td>
                <td>Equipo base de 3 personas rotativas (Ant. Jesús, Felisa, Miguel Ángel / Conso).</td>
            </tr>
            <tr>
                <td><strong>Rodalabota (El Cuervo)</strong></td>
                <td>Diurno (Sin noche)</td>
                <td><span class="badge badge-blue">Mañana</span> &bull; <span class="badge badge-amber">Tarde</span> (1 puesto)</td>
                <td>Equipo base de 3 personas rotativas (Marrufo, Borja, Emilio).</td>
            </tr>
            <tr>
                <td><strong>Atenas (Lebrija)</strong></td>
                <td>Diurno (Sin noche)</td>
                <td><span class="badge badge-blue">Mañana</span> &bull; <span class="badge badge-amber">Tarde</span> (1 puesto)</td>
                <td>Equipo base de 3 personas rotativas (Toni, Zambrano, Joaquín).</td>
            </tr>
            <tr style="background-color: #fffbeb;">
                <td><strong>Ronda Norte (Utrera)</strong></td>
                <td><strong>24 Horas</strong></td>
                <td>
                    <span class="badge badge-blue">Puesto 1 (M/T)</span> &bull;
                    <span class="badge badge-amber">Puesto 2 (M/T)</span><br>
                    <span class="badge badge-purple">Turno Noche</span>
                </td>
                <td>
                    <strong>Doble línea diurna:</strong> 2 equipos de 3 personas rotando en 4x2.<br>
                    <strong>Noche:</strong> Equipo permanente de 2 personas (Fran y Romero).
                </td>
            </tr>
        </tbody>
    </table>

    <div class="callout">
        <strong>Lógica del Ciclo 4x2:</strong> En las estaciones estándar, con 3 empleados siempre hay <strong>1 persona en Mañana, 1 persona en Tarde y 1 persona en Descanso</strong>. Cada trabajador cumple 4 días de mañana, 2 de descanso, 4 de tarde y 2 de descanso. Toda la semana queda 100% cubierta sin huecos.
    </div>

    <!-- SALTO A PÁGINA 2 -->
    <div class="page-break"></div>

    <!-- ==================== PÁGINA 2: INTERFAZ Y FUNCIONALIDADES ==================== -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 65%;">
                <div class="header-title">PLANIFICACIÓN MENSUAL DE TURNOS (PLANING)</div>
                <div class="header-subtitle">Diseño Visual &bull; Experiencia de Usuario en Plataforma</div>
            </td>
            <td class="header-meta" style="width: 35%;">
                <strong style="color: #0f172a; font-size: 7.5pt;">UTRECAR S.L. &bull; PÁGINA 2</strong><br>
                <strong>Módulo:</strong> Gestión de Recursos Humanos
            </td>
        </tr>
    </table>

    <!-- Sección 3: Cómo lo verá el usuario -->
    <div class="section-header">&bull; 3. ¿Cómo se verá en pantalla? (Ejemplo Visual del Cuadrante)</div>
    <div style="font-size: 7.5pt; margin-bottom: 5px;">
        La interfaz reproduce la vista familiar del Excel mensual pero en un entorno digital interactivo con el color corporativo de cada empleado:
    </div>

    <!-- Ejemplo Visual del Cuadrante -->
    <table class="mockup-table">
        <thead>
            <tr>
                <th style="width: 16%;">Turno / Puesto</th>
                <th style="width: 12%;">Lun 02</th>
                <th style="width: 12%;">Mar 03</th>
                <th style="width: 12%;">Mié 04</th>
                <th style="width: 12%;">Jue 05</th>
                <th style="width: 12%;">Vie 06</th>
                <th style="width: 12%;">Sáb 07</th>
                <th style="width: 12%;">Dom 08</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="mockup-header-col">MAÑANA</td>
                <td class="cell-manana">TONI</td>
                <td class="cell-manana">TONI</td>
                <td class="cell-manana">TONI</td>
                <td class="cell-manana">TONI</td>
                <td class="cell-sustituto">JOSE MANUEL*</td>
                <td class="cell-sustituto">JOSE MANUEL*</td>
                <td class="cell-sustituto">JOSE MANUEL*</td>
            </tr>
            <tr>
                <td class="mockup-header-col">TARDE</td>
                <td class="cell-sustituto">JOSE MANUEL*</td>
                <td class="cell-sustituto">JOSE MANUEL*</td>
                <td class="cell-tarde">JOAQUIN</td>
                <td class="cell-tarde">JOAQUIN</td>
                <td class="cell-tarde">JOAQUIN</td>
                <td class="cell-tarde">JOAQUIN</td>
                <td class="cell-tarde">TONI</td>
            </tr>
            <tr>
                <td class="mockup-header-col">DESCANSO (Libranza)</td>
                <td class="cell-descanso">JOAQUIN</td>
                <td class="cell-descanso">JOAQUIN</td>
                <td class="cell-descanso">ZAMBRANO (Vac.)</td>
                <td class="cell-descanso">ZAMBRANO (Vac.)</td>
                <td class="cell-descanso">TONI</td>
                <td class="cell-descanso">TONI</td>
                <td class="cell-descanso">JOAQUIN</td>
            </tr>
        </tbody>
    </table>
    <div style="font-size: 6.8pt; color: #64748b; margin-top: 2px; margin-bottom: 8px;">
        <em>* Píldora verde con borde punteado: el sistema muestra con claridad que <strong>Zambrano está de vacaciones y entra José Manuel en sustitución</strong>.</em>
    </div>

    <!-- Sección 4: Ubicación en el Menú de la Plataforma -->
    <div class="section-header">&bull; 4. Acceso Directo desde el Menú de Utrecar</div>
    <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 7px 10px; margin-bottom: 8px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <strong style="color: #0f172a; font-size: 8pt;">Estructura del Menú Principal:</strong><br>
                    <div style="margin-top: 4px; font-size: 7.5pt; color: #334155; line-height: 1.35;">
                        <span style="color: #6366f1; font-weight: bold;">Recursos humanos</span><br>
                        &nbsp;&nbsp;&bull; Fichajes<br>
                        &nbsp;&nbsp;&bull; Empleados<br>
                        &nbsp;&nbsp;&bull; Vacaciones<br>
                        &nbsp;&nbsp;<strong style="color: #d97706;">&bull; Planing &nbsp;▶ (Desplegable)</strong><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── <strong>Vistalegre</strong><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── <strong>Ronda Norte</strong><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── <strong>Rodalabota</strong><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── <strong>Atenas</strong>
                    </div>
                </td>
                <td style="width: 52%; vertical-align: top; border-left: 1px solid #e2e8f0; padding-left: 10px;">
                    <strong style="color: #0f172a; font-size: 8pt;">Control y Seguridad:</strong>
                    <div style="margin-top: 4px;">
                        <div class="checklist-item">&check; <strong>Permiso específico:</strong> Solo accesible para usuarios autorizados por Dirección o RRHH.</div>
                        <div class="checklist-item">&check; <strong>Fácil conmutación:</strong> Cambio de una gasolinera a otra en 1 clic.</div>
                        <div class="checklist-item">&check; <strong>Navegación mensual:</strong> Botones <em>"Mes Anterior"</em> y <em>"Mes Siguiente"</em> para revisar históricos o adelantar cuadrantes.</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Sección 5: Funcionalidades Especiales y Prácticas -->
    <div class="section-header">&bull; 5. Funcionalidades Prácticas Diseñadas para el Día a Día</div>
    <table class="content-table">
        <tbody>
            <tr>
                <td style="width: 28%;"><strong>1. Generación Automática 4x2</strong></td>
                <td>Con pulsar un botón al inicio de cada mes, el sistema calcula y dibuja la rotación completa respetando la cadencia del mes anterior. <strong>Ahorro inmediato de tiempo de redacción.</strong></td>
            </tr>
            <tr>
                <td><strong>2. Gestión Ágil de Suplencias</strong></td>
                <td>Al hacer clic sobre cualquier turno, un selector permite cambiar el titular por un sustituto (ej. José Manuel o Javier), indicando el motivo (vacaciones, baja, permiso o cambio de turno).</td>
            </tr>
            <tr>
                <td><strong>3. Registro de Observaciones</strong></td>
                <td>Cuadro de texto al pie de cada mes para reflejar acuerdos de gerencia, adaptaciones horarias temporales o apoyos en cafetería, quedando todo documentado formalmente.</td>
            </tr>
            <tr>
                <td><strong>4. Impresión en PDF (A4 Tablón)</strong></td>
                <td>Generación con un solo clic de la hoja mensual en PDF con formato limpio e idéntico al Excel, listo para imprimir y colgar en la cabina o tablón de cada gasolinera.</td>
            </tr>
        </tbody>
    </table>

    <div class="callout" style="background-color: #f8fafc; border-left-color: #d97706; color: #78350f;">
        <strong>Conclusión de Usabilidad:</strong> No se requiere formación técnica. Cualquier encargado o responsable que sepa manejar el panel actual de Utrecar sabrá gestionar el cuadrante desde el primer minuto.
    </div>

    <!-- SALTO A PÁGINA 3 -->
    <div class="page-break"></div>

    <!-- ==================== PÁGINA 3: PLAN DE ACCIÓN Y BENEFICIOS PARA DIRECCIÓN ==================== -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 65%;">
                <div class="header-title">PLANIFICACIÓN MENSUAL DE TURNOS (PLANING)</div>
                <div class="header-subtitle">Plan de Implantación &bull; Beneficios para la Dirección</div>
            </td>
            <td class="header-meta" style="width: 35%;">
                <strong style="color: #0f172a; font-size: 7.5pt;">UTRECAR S.L. &bull; PÁGINA 3</strong><br>
                <strong>Aprobación:</strong> Dirección General / Operaciones
            </td>
        </tr>
    </table>

    <!-- Sección 6: Plan de Implantación -->
    <div class="section-header">&bull; 6. Plan de Implantación por Fases (Sin Interrupción Operativa)</div>
    <div style="font-size: 7.5pt; margin-bottom: 5px;">
        La puesta en marcha se realizará de forma escalonada, garantizando que el servicio de las 4 estaciones continúe sin la menor alteración:
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 18%;">Fase</th>
                <th style="width: 52%;">Alcance y Tareas Principales</th>
                <th style="width: 30%;">Entregable Visible para Utrecar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Fase 1: Estructura y Menú</strong></td>
                <td>Creación del menú desplegable *Planing* con las 4 estaciones y modelo de datos relacional para cuadrantes mensuales.</td>
                <td>Menú accesible en la plataforma web.</td>
            </tr>
            <tr>
                <td><strong>Fase 2: Cuadrante y Motor 4x2</strong></td>
                <td>Diseño de la cuadrícula mensual interactiva con colores corporativos y botón de cálculo automático de rotación 4x2.</td>
                <td>Planificador mensual operativo en pantalla.</td>
            </tr>
            <tr>
                <td><strong>Fase 3: Suplencias e Impresión</strong></td>
                <td>Ventana modal para asignar suplentes por vacaciones/bajas y botón de exportación en PDF para imprimir en el tablón.</td>
                <td><strong>Sustitución total y definitiva del Excel.</strong></td>
            </tr>
            <tr>
                <td><strong>Fase 4 (Opcional a Futuro)</strong></td>
                <td>Consulta individual del turno por parte de cada trabajador desde su teléfono móvil en el Portal del Empleado.</td>
                <td>Mayor autonomía y comunicación interna.</td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 7: Beneficios Tangibles -->
    <div class="section-header">&bull; 7. Beneficios Tangibles para Dirección y Gerencia</div>
    <table class="content-table">
        <tbody>
            <tr>
                <td style="width: 30%; background-color: #f8fafc;"><strong>Ahorro de Tiempo de Gestión</strong></td>
                <td>Elimina el tiempo dedicado a cuadrar días a mano a final de cada mes y reenviar archivos por correo o WhatsApp. El cuadrante se crea en segundos.</td>
            </tr>
            <tr>
                <td style="background-color: #f8fafc;"><strong>Seguridad y Cumplimiento Normativo</strong></td>
                <td>Garantiza el respeto estricto de descansos entre jornadas (ciclo 4x2), evitando reclamaciones laborales o descompensación de horas entre compañeros.</td>
            </tr>
            <tr>
                <td style="background-color: #f8fafc;"><strong>Transparencia y Trazabilidad</strong></td>
                <td>Queda constancia exacta de quién hizo cada turno, qué sustitución se realizó y qué acuerdo de gerencia se aplicó, sin hojas extraviadas.</td>
            </tr>
            <tr>
                <td style="background-color: #f8fafc;"><strong>Disponibilidad Total 24/7</strong></td>
                <td>Acceso instantáneo desde cualquier ordenador de oficina, tablet o teléfono móvil de la dirección o de los encargados.</td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 8: Cuadro de Aprobación y Cierre -->
    <div style="background-color: #ffffff; border: 1.5px solid #d97706; border-radius: 5px; padding: 10px 14px; margin-top: 14px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 65%; vertical-align: middle;">
                    <div style="font-size: 9pt; font-weight: bold; color: #92400e; margin-bottom: 2px;">
                        &bull; Conclusión y Solicitud de Conformidad
                    </div>
                    <div style="font-size: 7.3pt; color: #334155; line-height: 1.35;">
                        Esta propuesta digitaliza un proceso clave de la empresa sin cambiar las reglas que ya funcionan. Aporta orden, rapidez y una imagen moderna y profesional acorde al crecimiento de <strong>Utrecar</strong>.
                    </div>
                </td>
                <td style="width: 35%; text-align: center; vertical-align: middle; border-left: 1px solid #e2e8f0; padding-left: 10px;">
                    <div style="font-size: 7pt; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 20px;">
                        Conforme Dirección General
                    </div>
                    <div style="border-bottom: 1px dashed #94a3b8; width: 80%; margin: 0 auto 5px auto;"></div>
                    <div style="font-size: 6.5pt; color: #94a3b8;">Firma / Aprobación</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Script de Paginación DomPDF -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT . "  |  Utrecar - Propuesta de Digitalización de Cuadrantes de Turnos (Planing)";
            $font = $fontMetrics->get_font("DejaVu Sans, sans-serif", "normal");
            $size = 6.5;
            $y = 820;
            $x = 297.5 - ($fontMetrics->get_text_width($text, $font, $size) / 2);
            $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.45, 0.5]);
        }
    </script>
</body>
</html>
