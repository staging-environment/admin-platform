---
description: Regla de autorización basada estrictamente en permisos (Spatie), nunca en nombres de roles
globs: ["app/**/*.php", "resources/views/**/*.blade.php", "routes/**/*.php"]
---

# Regla de Autorización: Permisos en lugar de Roles

## Principio Fundamental
- **NUNCA utilices `$user->hasRole('...')` ni evalúes nombres de roles** (como `'Admin'`, `'Gestor'`, `'Empleado'`) para autorizar accesos a páginas de Filament (`canAccess`), métodos de recursos, menús de navegación (`navigation.blade.php`), middleware, controladores o botones de acción.
- **LOS ROLES SON DINÁMICOS**: En esta plataforma, los roles pueden crearse, renombrarse, reconfigurarse o eliminarse dinámicamente desde el panel de administración. Por tanto, vincular lógica de código a un rol específico es un antipatrón que rompe la flexibilidad.

## Directrices de Implementación
1. **Evaluar siempre permisos**:
   Usa `$user->can('nombre_permiso')` o `auth()->user()->can('nombre_permiso')`.
2. **Excepción de Superadministrador**:
   Se permite comprobar `$user->id === 1 || $user->email === 'jarodriguezbonilla@gmail.com'` como fallback de superadministrador en caso de emergencia.
3. **Catálogo de Permisos Principales**:
   - `ver_dashboard`: Acceso a la página principal / métricas de mercado.
   - `gestion_recursos_humanos`: Ver y gestionar empleados y ofertas.
   - `acceder_portal_fichajes`: Acceso del trabajador a registrar entrada/salida.
   - `ver_ficha_empleado`: Acceso para auditar y consultar fichas y fichajes de empleados.
   - `aprobacion_vacaciones_bajas`: Aprobar o denegar solicitudes de vacaciones y bajas médicas.
   - `ver_informes`: Acceso y exportación de informes de ventas/combustible.
   - `ver_analiticas`: Acceso a gráficas y analítica.
   - `gestion_gasolineras`: Configuración de estaciones de servicio.
   - `gestion_portada`: Edición de banners y contenido público.
   - `utilizar_explorador`: Explorador de archivos del sistema.
   - `gestion_usuarios`: Gestión de cuentas de usuario.
   - `gestion_eliminar_usuarios`: Permiso crítico para eliminar usuarios.
   - `gestion_roles`: Gestión de roles y matriz de permisos.
