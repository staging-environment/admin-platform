# Workspace Rules

## Control de Acceso y Autorización Basado Exclusivamente en Permisos (REGLA CRÍTICA)
- **NUNCA evaluar roles directamente con `hasRole(...)`** para restringir o condicionar accesos a secciones, páginas de Filament (`canAccess`), menús de navegación, middleware, controladores o componentes visuales.
- **SIEMPRE basarse en PERMISOS (`can(...)`)**: Los roles son dinámicos (se pueden crear, editar, renombrar o eliminar desde la interfaz), por lo que la única fuente de verdad para la autorización son los permisos de Spatie asignados al usuario/rol.
- Cada funcionalidad debe comprobar su permiso correspondiente (ej: `gestion_recursos_humanos`, `ver_dashboard`, `aprobacion_vacaciones_bajas`, `acceder_portal_fichajes`, `ver_ficha_empleado`, `ver_informes`, `ver_analiticas`, `gestion_gasolineras`, `gestion_portada`, `utilizar_explorador`, `gestion_usuarios`, `gestion_roles`).
- Se mantiene únicamente la excepción para superadministradores por identificador directo si aplica (`$user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1`).

## Flujo Git y Despliegue en Dos Fases: Preproducción (`pre`) y Producción (`main`) (REGLA OBLIGATORIA)
- Todo el trabajo activo se realiza y commitea en la rama **`pre`**.
- Al completar cualquier tarea:
  1. `git add` y `git commit` en la rama `pre`.
  2. `git push origin pre`.
  3. Despliegue automático en Preproducción (`pre.utrecar.com`):
     `ssh utrecar-dev "cd /home/developer/Projects/pre-admin-platform && git pull origin pre && ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear"`
- **Solo cuando el usuario solicite explícitamente pasar a producción**:
  1. **En la máquina virtual de producción** (`utrecar-dev` / `164.68.101.69`, carpeta `/home/developer/Projects/admin-platform`):
     - Si hay cambios pendientes en producción, commitearlos en `main`.
     - Mergear la rama `origin/pre` en `main`.
     - Subir a `origin main`: `git push origin main`.
     - Actualizar producción: `ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear`.
  2. Sincronizar repositorio local y ramas `main` y `pre`.

## Tareas Programadas y Sincronización MITECO (REGLA CRÍTICA)
- Las tareas programadas en producción residen en `/etc/cron.d/utrecar-scheduler` (propiedad `root:root`, permisos `644`) y ejecutan `php artisan schedule:run` cada minuto. **NUNCA** configurarlas vía `crontab -e` del usuario `developer`.
- MITECO sincroniza precios cada 5 minutos mediante `UploadPricesToMitecoJob` si hay cambios en `preciosdeproductos`.
- Para diagnósticos o forzar sincronización manual: `ddev exec php artisan miteco:sync {--force}`.
- Guía detallada y resolución de incidencias en [.agents/rules/miteco_and_scheduler.md](file:///home/developer/Projects/admin-platform/.agents/rules/miteco_and_scheduler.md).

## Planificación de Turnos (Planing) (REGLA DE DOMINIO)
- La gestión de turnos se organiza mensualmente para las 4 estaciones de servicio (Vistalegre, Ronda Norte, Rodalabota y Atenas).
- En estaciones de 1 puesto diurno (Vistalegre, Rodalabota y Atenas), se sigue un ciclo estricto de 12 días 4x2 entre 3 personas (4 Mañanas, 2 Descansos, 4 Tardes, 2 Descansos). En Ronda Norte (24h) existen Puesto 1 (M/T), Puesto 2 (M/T) y Turno Noche.
- La especificación completa de negocio, suplencias y arquitectura técnica está documentada en [.agents/rules/planing_turnos.md](file:///home/developer/Projects/admin-platform/.agents/rules/planing_turnos.md).

## Arquitectura Multiempresa (Utrecar y Trieste) (REGLA ESTRATÉGICA)
- El grupo opera con dos sociedades: **Utrecar S.L.** (estaciones 1 a 4) y **Trieste S.L.** (estación Repsol Trieste).
- **Admin-Platform (Laravel)** lidera RRHH, portal de empleados, fichajes, planing 4x2, precios competencia y web.
- **Odoo 17 ERP** lidera compras, inventario de combustible y tienda, facturación de proveedores y contabilidad multiempresa.
- Los proveedores se unifican como contactos globales en Odoo, pero los pedidos, facturas y tanques se segregan por CIF de empresa.
- Especificación completa documentada en [.agents/rules/arquitectura_multiempresa.md](file:///home/developer/Projects/admin-platform/.agents/rules/arquitectura_multiempresa.md) y [docs/PRD_ARQUITECTURA_MULTIEMPRESA_ODOO_UTRECAR_TRIESTE.md](file:///home/developer/Projects/admin-platform/docs/PRD_ARQUITECTURA_MULTIEMPRESA_ODOO_UTRECAR_TRIESTE.md).


