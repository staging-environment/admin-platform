# Arquitectura Multiempresa: Utrecar, Trieste, Odoo y Admin-Platform

Este documento establece las reglas de arquitectura y reparto de responsabilidades entre las empresas del grupo y los sistemas en producción.

---

## 1. Estructura Societaria y Sistemas
* **Utrecar S.L.**: Titular de las estaciones Vistalegre, Ronda Norte, Rodalabota y Atenas.
  * **Sistema Principal**: `admin-platform` (Laravel + Filament en `/home/developer/Projects/admin-platform`).
  * **Funciones de admin-platform**: Recursos Humanos, portal del empleado, control horario/fichajes, planing de turnos 4x2, monitorización de precios de competencia (MITECO) y web pública.
* **Trieste S.L.**: Titular de la estación Repsol Trieste (SE-30 Calonge).
  * **Sistema Principal**: `odoo` (Odoo 17 ERP en `/home/developer/Projects/odoo`).
  * **Funciones de Odoo**: Compras, Inventario multialmacén (tanques de combustible y tiendas), Facturación oficial de proveedores, Contabilidad y TPV de pista (`pos_gas_station`).

---

## 2. Regla Crítica: Modelo Híbrido de Proveedores e Inventario
* **NO programar módulos de compras, inventario o facturas de proveedor en `admin-platform`**: Odoo 17 es el ERP maestro de compras, stock y contabilidad.
* **Proveedores Compartidos (`res.partner`)**: Los proveedores se unifican como contactos globales en Odoo (`company_id = False`) para evitar duplicidades de CIFs o cuentas bancarias.
* **Operativa Aislada por Empresa**: Los pedidos de compra (`purchase.order`), facturas de compra (`account.move`) y movimientos de stock (`stock.picking`) están estrictamente asignados a la compañía legal correspondiente (`Utrecar S.L.` o `Trieste S.L.`).
* **Almacenes y Tanques**: Los tanques de las 4 estaciones de Utrecar y los tanques de Trieste se configuran como almacenes independientes dentro de Odoo.

---

## 3. Desconexión de Aseproda (VirtusGesNet)
* Aseproda se mantiene únicamente como pasarela provisional de pista en las estaciones de Utrecar hasta su migración al TPV de Odoo.
* Todos los datos de proveedores e inventario histórico de `virtusgesnet` se centralizan en Odoo.
