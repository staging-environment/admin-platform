<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Propuesta de Arquitectura Multiempresa - Utrecar y Trieste</title>
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

        .diagram-box {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <!-- ==================== PÁGINA 1: EL DILEMA Y EL MODELO HÍBRIDO ==================== -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 65%;">
                <div class="header-title">ARQUITECTURA MULTIEMPRESA &bull; GRUPO EMPRESARIAL</div>
                <div class="header-subtitle">Plan Estratégico de Proveedores, Inventario y ERP (Utrecar & Trieste)</div>
            </td>
            <td class="header-meta" style="width: 35%;">
                <strong style="color: #0f172a; font-size: 7.5pt;">UTRECAR S.L. &bull; TRIESTE S.L.</strong><br>
                <strong>Destinatario:</strong> Dirección General / Gerencia<br>
                <strong>Área:</strong> Organización, Compras y Sistemas<br>
                <strong>Fecha:</strong> Noviembre 2026 &bull; Versión 1.0
            </td>
        </tr>
    </table>

    <!-- Resumen Ejecutivo -->
    <div class="executive-box">
        <div class="executive-title">&bull; Resumen Ejecutivo para la Propiedad</div>
        El crecimiento del grupo con dos sociedades diferenciadas (<strong>Utrecar S.L.</strong> y <strong>Trieste S.L.</strong>) requiere ordenar la gestión de proveedores, compras y existencias. Esta propuesta define un <strong>modelo híbrido inteligente</strong>: unificar la ficha de los proveedores para negociar juntos y no duplicar datos, pero <strong>separar de forma rigurosa los pedidos, facturas, tanques y contabilidad según el CIF de cada empresa</strong> dentro del ERP Odoo ya instalado en el servidor.
    </div>

    <!-- Indicadores Clave del Proyecto -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card kpi-card-highlight" style="width: 25%;">
                <div class="kpi-value" style="color: #b45309;">2 Sociedades</div>
                <div class="kpi-label">Utrecar y Trieste</div>
                <div style="font-size: 5.8pt; color: #78350f; margin-top: 1px;">Mismo grupo &bull; CIFs independientes</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-value" style="color: #0369a1;">Ficha Única</div>
                <div class="kpi-label">Proveedores Centralizados</div>
                <div style="font-size: 5.8pt; color: #64748b; margin-top: 1px;">483 proveedores sin duplicidades</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-value" style="color: #047857;">5 Estaciones</div>
                <div class="kpi-label">Tanques y Tiendas</div>
                <div style="font-size: 5.8pt; color: #64748b; margin-top: 1px;">Stock y aforos aislados por empresa</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-value" style="color: #475569;">1 Solo ERP</div>
                <div class="kpi-label">Odoo Multiempresa</div>
                <div style="font-size: 5.8pt; color: #64748b; margin-top: 1px;">Compras, inventario y facturación</div>
            </td>
        </tr>
    </table>

    <!-- Sección 1: La Respuesta al Dilema de Proveedores -->
    <div class="section-header">&bull; 1. ¿Cómo gestionar los Proveedores: Unificados o Separados?</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 50%;">Nivel de Contacto (Ficha del Proveedor) &rarr; UNIFICADO</th>
                <th style="width: 50%;">Nivel Operativo y Contable (Facturas/Stock) &rarr; SEPARADO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Misma entidad jurídica:</strong> Proveedores como Repsol, Cepsa, CLH/Exolum, Coca-Cola o Bimbo son la misma empresa independientemente de a qué gasolinera sirvan.</td>
                <td><strong>Aislamiento fiscal absoluto:</strong> Cada pedido de compra y cada factura va emitida formalmente al CIF de Utrecar S.L. o al de Trieste S.L.</td>
            </tr>
            <tr>
                <td><strong>Sin duplicar fichas:</strong> Si un proveedor cambia de cuenta bancaria o de teléfono comercial, se actualiza una sola vez para todo el grupo.</td>
                <td><strong>Libros de IVA independientes:</strong> Hacienda exige que cada sociedad presente sus modelos tributarios sin mezcla contable alguna.</td>
            </tr>
            <tr>
                <td><strong>Mayor fuerza de negociación:</strong> La gerencia conoce el volumen global de compra del grupo ante petroleras y distribuidores.</td>
                <td><strong>Control de cobros y pagos:</strong> Los pagos salen de la cuenta bancaria de la sociedad titular del suministro.</td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 2: El Reparto de Tareas entre Sistemas -->
    <div class="section-header">&bull; 2. Especialización de Sistemas: Cada Herramienta para su Función</div>
    <div style="font-size: 7.5pt; margin-bottom: 5px;">
        Para evitar desarrollos innecesarios y no duplicar costes, se asigna a cada software su función natural:
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 28%;">Software</th>
                <th style="width: 24%;">Entorno y Rol</th>
                <th style="width: 48%;">Responsabilidades Asignadas</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #fffbeb;">
                <td><strong>Odoo 17 ERP</strong><br><span style="font-size: 6.2pt; color: #92400e;">(odoo.utrecar.com)</span></td>
                <td><span class="badge badge-amber">ERP Central</span><br>Multiempresa Nativo</td>
                <td>
                    &bull; <strong>Maestro de Proveedores</strong> centralizado.<br>
                    &bull; <strong>Módulo de Compras:</strong> Pedidos de combustible y tienda.<br>
                    &bull; <strong>Inventario y Tanques:</strong> Aforos, mermas y existencias.<br>
                    &bull; <strong>Facturación y Contabilidad:</strong> Facturas de compra.<br>
                    &bull; <strong>TPV de Pista:</strong> Estación Repsol Trieste ( Wayne DART).
                </td>
            </tr>
            <tr>
                <td><strong>Admin-Platform</strong><br><span style="font-size: 6.2pt; color: #1e40af;">(utrecar.com/admin)</span></td>
                <td><span class="badge badge-blue">Portal Operativo</span><br>Laravel + Filament</td>
                <td>
                    &bull; <strong>Recursos Humanos y Fichajes:</strong> Control horario y portal.<br>
                    &bull; <strong>Planing de Turnos 4x2:</strong> Cuadrantes de estaciones Utrecar.<br>
                    &bull; <strong>Precios de Competencia:</strong> Sincronización diaria MITECO.<br>
                    &bull; <strong>Presencia Pública:</strong> Portal corporativo y empleo.
                </td>
            </tr>
            <tr>
                <td><strong>Aseproda / VirtusGesNet</strong><br><span style="font-size: 6.2pt; color: #475569;">(En retirada paulatina)</span></td>
                <td><span class="badge badge-purple">Software Legacy</span><br>Surtidores Utrecar</td>
                <td>
                    &bull; Transitoriamente operativo en las estaciones 1 a 4.<br>
                    &bull; <strong>Sus 483 proveedores e histórico se migran a Odoo</strong> para apagar VirtusGesNet como gestor de inventario.
                </td>
            </tr>
        </tbody>
    </table>

    <div class="callout">
        <strong>Decisión Estratégica Clave:</strong> No se debe programar un módulo de inventario ni de compras de proveedores en Admin-Platform (Laravel). Odoo ya está instalado, probado y diseñado específicamente para gestionar stock, pedidos y facturas de compra con respaldo multialmacén de primer nivel mundial.
    </div>

    <!-- SALTO A PÁGINA 2 -->
    <div class="page-break"></div>

    <!-- ==================== PÁGINA 2: ESTRUCTURA MULTIEMPRESA E INVENTARIO ==================== -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 65%;">
                <div class="header-title">ARQUITECTURA MULTIEMPRESA &bull; GRUPO EMPRESARIAL</div>
                <div class="header-subtitle">Estructura Operativa &bull; Gestión de Tanques y Tiendas</div>
            </td>
            <td class="header-meta" style="width: 35%;">
                <strong style="color: #0f172a; font-size: 7.5pt;">UTRECAR S.L. &bull; TRIESTE S.L.</strong><br>
                <strong>Página 2:</strong> Inventarios y Circuito de Compras
            </td>
        </tr>
    </table>

    <!-- Sección 3: Mapa Operativo de Empresas y Almacenes -->
    <div class="section-header">&bull; 3. Estructura de Empresas y Almacenes en Odoo</div>
    <div style="font-size: 7.5pt; margin-bottom: 5px;">
        Así queda organizada la información dentro del ERP Odoo para que la Dirección conmute entre empresas en un clic:
    </div>

    <div class="diagram-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                    <div style="background-color: #f1f5f9; padding: 5px 8px; border-radius: 3px; border-left: 3px solid #0284c7; margin-bottom: 6px;">
                        <strong style="color: #0f172a; font-size: 8pt;">EMPRESA 1: UTRECAR S.L. (CIF: B-...)</strong>
                    </div>
                    <div style="font-size: 7.2pt; line-height: 1.4; color: #334155;">
                        <strong>Almacén 1: E.S. Vistalegre (Utrera)</strong><br>
                        &nbsp;&nbsp;&bull; Tanques de Gasóleo A y Gasolina 95 &bull; Tienda<br>
                        <strong>Almacén 2: E.S. Ronda Norte (Utrera)</strong><br>
                        &nbsp;&nbsp;&bull; Tanques de Carburantes (24 Horas) &bull; Tienda<br>
                        <strong>Almacén 3: E.S. Rodalabota (El Cuervo)</strong><br>
                        &nbsp;&nbsp;&bull; Tanques de Carburantes &bull; Tienda<br>
                        <strong>Almacén 4: E.S. Atenas (Lebrija)</strong><br>
                        &nbsp;&nbsp;&bull; Tanques de Carburantes &bull; Tienda
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 8px; border-left: 1px solid #e2e8f0;">
                    <div style="background-color: #fffbeb; padding: 5px 8px; border-radius: 3px; border-left: 3px solid #d97706; margin-bottom: 6px;">
                        <strong style="color: #92400e; font-size: 8pt;">EMPRESA 2: TRIESTE S.L. (CIF: B-...)</strong>
                    </div>
                    <div style="font-size: 7.2pt; line-height: 1.4; color: #334155;">
                        <strong>Almacén 5: E.S. Repsol Trieste (SE-30 Calonge)</strong><br>
                        &nbsp;&nbsp;&bull; Tanque Gasóleo A (51)<br>
                        &nbsp;&nbsp;&bull; Tanque Sin Plomo 95 (52)<br>
                        &nbsp;&nbsp;&bull; Tanque Gasóleo Plus (54)<br>
                        &nbsp;&nbsp;&bull; Tienda de Conveniencia y Lubricantes<br>
                        &nbsp;&nbsp;&bull; <em>Control de pista directo por TPV Odoo</em>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Sección 4: Gestión de Inventarios -->
    <div class="section-header">&bull; 4. Tratamiento del Inventario: Combustibles vs. Tiendas</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tipo de Producto</th>
                <th style="width: 35%;">Particularidad Operativa</th>
                <th style="width: 40%;">Cómo lo gestiona Odoo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Carburantes Líquidos</strong><br><span style="font-size: 6.2pt; color: #64748b;">(Gasóleo, 95, Plus)</span></td>
                <td>
                    &bull; Recepción por camión cisterna.<br>
                    &bull; Medición a 15ºC vs litros ambiente.<br>
                    &bull; Necesidad de cuadre con aforos de tanque.
                </td>
                <td>
                    Ubicaciones de tipo *Tanque* con control de mermas por temperatura y registro de desajustes por sondeo en un clic.
                </td>
            </tr>
            <tr>
                <td><strong>Productos de Tienda</strong><br><span style="font-size: 6.2pt; color: #64748b;">(Bebidas, snacks, aceites)</span></td>
                <td>
                    &bull; Miles de referencias con código de barras.<br>
                    &bull; Caducidades y reposiciones semanales.<br>
                    &bull; Venta directa en mostrador.
                </td>
                <td>
                    Control multialmacén con alertas de stock mínimo: cuando quedan pocas unidades de un producto, Odoo propone el pedido al proveedor.
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 5: El Circuito de Compras Paso a Paso -->
    <div class="section-header">&bull; 5. El Circuito de Compras: Sencillo y Sin Errores</div>
    <div style="font-size: 7.5pt; margin-bottom: 5px;">
        Cualquier encargado o administrativo sigue el mismo flujo seguro:
    </div>

    <table class="content-table">
        <tbody>
            <tr>
                <td style="width: 20%; font-weight: bold; color: #0284c7;">Paso 1: Proveedor</td>
                <td>Se elige el proveedor del catálogo común (ej. <em>Repsol Comercial</em> o <em>Matutano</em>). No hay que buscar si está dado de alta en una empresa u otra.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #0284c7;">Paso 2: Empresa Titular</td>
                <td>El sistema vincula la compra a la empresa activa (<strong>Utrecar</strong> o <strong>Trieste</strong>). Si el pedido es para Vistalegre, la factura se registrará a Utrecar.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #0284c7;">Paso 3: Recepción</td>
                <td>Al llegar la mercancía o la cisterna, se valida el albarán y las unidades entran automáticamente en el stock del tanque o tienda destino.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #0284c7;">Paso 4: Factura Oficial</td>
                <td>Al recibir la factura del proveedor, Odoo comprueba que coincida con lo recibido y la deja lista para liquidación de IVA y pago bancario.</td>
            </tr>
        </tbody>
    </table>

    <div class="callout" style="background-color: #f8fafc; border-left-color: #d97706; color: #78350f;">
        <strong>Conclusión de Control:</strong> Se terminan los descuadres de combustible y las facturas extraviadas. Dirección sabe exactamente cuánto combustible hay en cada tanque de las 5 estaciones en tiempo real.
    </div>

    <!-- SALTO A PÁGINA 3 -->
    <div class="page-break"></div>

    <!-- ==================== PÁGINA 3: HOJA DE RUTA Y BENEFICIOS ==================== -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle; width: 65%;">
                <div class="header-title">ARQUITECTURA MULTIEMPRESA &bull; GRUPO EMPRESARIAL</div>
                <div class="header-subtitle">Plan de Implantación &bull; Beneficios para la Dirección</div>
            </td>
            <td class="header-meta" style="width: 35%;">
                <strong style="color: #0f172a; font-size: 7.5pt;">UTRECAR S.L. &bull; TRIESTE S.L.</strong><br>
                <strong>Página 3:</strong> Hoja de Ruta y Aprobación
            </td>
        </tr>
    </table>

    <!-- Sección 6: Plan de Implantación -->
    <div class="section-header">&bull; 6. Plan de Implantación por Fases (Sin Afectar a las Ventas)</div>
    <div style="font-size: 7.5pt; margin-bottom: 5px;">
        La transición se realiza sin interrumpir la operativa diaria de ninguna estación:
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 15%;">Fase</th>
                <th style="width: 55%;">Tareas Principales</th>
                <th style="width: 30%;">Resultado Tangible</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Fase 1: Configuración</strong></td>
                <td>Configurar en Odoo las dos compañías legales (Utrecar S.L. y Trieste S.L.) con sus CIFs, cuentas bancarias y series de compra oficiales.</td>
                <td>Odoo preparado para operar con ambas empresas.</td>
            </tr>
            <tr>
                <td><strong>Fase 2: Migración Proveedores</strong></td>
                <td>Traspasar los 483 proveedores existentes en VirtusGesNet a Odoo como contactos globales saneados (eliminando duplicados y clasificándolos por sector).</td>
                <td>Directorio unificado de proveedores del grupo.</td>
            </tr>
            <tr>
                <td><strong>Fase 3: Almacenes y Tanques</strong></td>
                <td>Modelar los tanques de las 4 estaciones de Utrecar y la de Trieste con sus capacidades y tipos de carburante en Odoo Inventario.</td>
                <td>Mapa de existencias de combustible en tiempo real.</td>
            </tr>
            <tr>
                <td><strong>Fase 4: Circuito de Compras</strong></td>
                <td>Poner en marcha la entrada de albaranes y facturas de combustible y tienda en Odoo, retirando esa función de VirtusGesNet.</td>
                <td><strong>Control económico total en un único sistema.</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Sección 7: Beneficios Tangibles -->
    <div class="section-header">&bull; 7. Beneficios Directos para Dirección y Propiedad</div>
    <table class="content-table">
        <tbody>
            <tr>
                <td style="width: 32%; background-color: #f8fafc;"><strong>Negociación Conjunta de Precios</strong></td>
                <td>Al tener los proveedores unificados, la gerencia puede negociar rappels y descuentos por volumen global para las 5 estaciones ante suministradores y petroleras.</td>
            </tr>
            <tr>
                <td style="background-color: #f8fafc;"><strong>Tranquilidad Fiscal ante Hacienda</strong></td>
                <td>Contabilidad, facturación e impuestos 100% aislados por CIF de empresa, cumpliendo con la normativa tributaria sin cruces indebidos.</td>
            </tr>
            <tr>
                <td style="background-color: #f8fafc;"><strong>Cero Inversión en Nuevo Software</strong></td>
                <td>No hay que comprar licencias adicionales: Odoo 17 ya está instalado en el servidor y Admin-Platform continúa liderando RRHH y turnos de forma coordinada.</td>
            </tr>
            <tr>
                <td style="background-color: #f8fafc;"><strong>Ahorro en Cuotas de Aseproda</strong></td>
                <td>Paso previo fundamental para independizarse de Aseproda y eliminar costes recurrentes de mantenimiento de software propietario.</td>
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
                        Esta arquitectura dota al grupo de una estructura sólida para crecer. <strong>Odoo asume el peso de las compras, el inventario y las dos empresas; Admin-Platform lidera las personas, turnos y la competitividad comercial</strong>.
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
            $text = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT . "  |  Utrecar & Trieste - Propuesta de Arquitectura Multiempresa, Proveedores e Inventario";
            $font = $fontMetrics->get_font("DejaVu Sans, sans-serif", "normal");
            $size = 6.5;
            $y = 820;
            $x = 297.5 - ($fontMetrics->get_text_width($text, $font, $size) / 2);
            $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.45, 0.5]);
        }
    </script>
</body>
</html>
