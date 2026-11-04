# PRD: Arquitectura Multiempresa, Proveedores e Inventario (Utrecar & Trieste)

Este documento define la arquitectura estratégica, la asignación de roles entre sistemas y el modelo de datos para la operativa conjunta de **Utrecar S.L.** y **Trieste S.L.**

---

## 1. Visión General del Grupo Empresarial

El grupo empresarial opera con dos sociedades mercantiles con CIFs independientes bajo el mismo accionariado:
1. **Utrecar S.L.**: Titular de las estaciones de servicio **Vistalegre**, **Ronda Norte**, **Rodalabota** y **Atenas**, del portal operativo `admin-platform`, del portal del empleado, fichajes y de la marca pública `utrecar.com`.
2. **Trieste S.L.**: Titular de la estación de servicio **Repsol Trieste** (SE-30 Calonge), del ERP transaccional Odoo 17 y de la pista con TPV propio (`pos_gas_station`).

---

## 2. Decisión Estratégica: Modelo Híbrido de Proveedores

### A. Ficha del Proveedor (Contacto) &rarr; UNIFICADA
* Proveedores de carburante (Repsol, Cepsa, Disa, Exolum/CLH) y distribuidores de tienda (Coca-Cola, Bimbo, Matutano, tabaco, lubricantes) son personas jurídicas únicas con el mismo CIF y cuenta bancaria.
* En **Odoo 17**, los proveedores se registran como contactos globales (`res.partner` con `company_id = False`).
* **Ventajas**: Cero duplicidad de fichas, actualización de cuentas bancarias en un solo punto y mayor fuerza de negociación conjunta de rappels y precios por volumen para las 5 estaciones.

### B. Operativa Mercantil, Contable y de Stock &rarr; ESTRICTAMENTE SEPARADA
* Cada pedido de compra (`purchase.order`) y factura recibida (`account.move`) lleva asignado el CIF legal correspondiente (`company_id = Utrecar S.L.` o `company_id = Trieste S.L.`).
* **Ventajas**: Cumplimiento normativo estricto con la Agencia Tributaria (libros de IVA independientes), trazabilidad contable limpia y pagos ordenados desde las cuentas bancarias de la sociedad titular.

---

## 3. Reparto de Responsabilidades entre Sistemas

```text
[ Odoo 17 ERP (odoo.utrecar.com) ]
  ├── Maestro Centralizado de Proveedores (Global)
  ├── Catálogo General de Artículos (Carburantes y Tienda)
  ├── Compras: Pedidos, Albaranes de Cisterna y Facturas de Compra
  ├── Inventario Multialmacén: 14 Tanques de Carburante + 5 Tiendas
  └── Contabilidad Oficial y TPV de Pista (Trieste)

[ Admin-Platform (Laravel + Filament / utrecar.com/admin) ]
  ├── Gestión de Recursos Humanos, Fichajes y Portal del Empleado
  ├── Planing de Turnos 4x2 (Vistalegre, Ronda Norte, Rodalabota, Atenas)
  ├── Monitorización y Analítica de Precios de Competencia (MITECO)
  └── Web Corporativa y Portal de Empleo

[ Aseproda / VirtusGesNet (En retirada) ]
  └── Sistema legacy transitorio en surtidores Utrecar (1 a 4).
      Sus 483 proveedores e inventario se migran a Odoo.
```

---

## 4. Estructura de Almacenes y Tanques en Odoo

```text
Grupo Empresarial
  ├── Utrecar S.L.
  │     ├── Almacén 1: E.S. Vistalegre (Tanques Gasóleo A, SP95 + Tienda)
  │     ├── Almacén 2: E.S. Ronda Norte (Tanques Carburantes 24H + Tienda)
  │     ├── Almacén 3: E.S. Rodalabota (Tanques Carburantes + Tienda)
  │     └── Almacén 4: E.S. Atenas (Tanques Carburantes + Tienda)
  └── Trieste S.L.
        └── Almacén 5: E.S. Repsol Trieste (Tanques 51, 52, 54 + Tienda)
```

---

## 5. Hoja de Ruta de Implantación
1. **Fase 1 (Odoo)**: Configuración de `res.company` para Utrecar S.L. y Trieste S.L. eliminando las compañías de demo.
2. **Fase 2 (Proveedores)**: Migración y saneamiento de los 483 proveedores de `virtusgesnet.proveedores` a Odoo como contactos compartidos.
3. **Fase 3 (Inventario)**: Modelado de tanques y almacenes con control de aforos y mermas en Odoo.
4. **Fase 4 (Circuito de Compras)**: Puesta en marcha del flujo oficial de pedidos de compra y facturación en Odoo, independizándose de Aseproda.

