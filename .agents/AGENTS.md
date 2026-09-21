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
