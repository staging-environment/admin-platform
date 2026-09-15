# Workspace Rules

## Control de Acceso y Autorización Basado Exclusivamente en Permisos (REGLA CRÍTICA)
- **NUNCA evaluar roles directamente con `hasRole(...)`** para restringir o condicionar accesos a secciones, páginas de Filament (`canAccess`), menús de navegación, middleware, controladores o componentes visuales.
- **SIEMPRE basarse en PERMISOS (`can(...)`)**: Los roles son dinámicos (se pueden crear, editar, renombrar o eliminar desde la interfaz), por lo que la única fuente de verdad para la autorización son los permisos de Spatie asignados al usuario/rol.
- Cada funcionalidad debe comprobar su permiso correspondiente (ej: `gestion_recursos_humanos`, `ver_dashboard`, `aprobacion_vacaciones_bajas`, `acceder_portal_fichajes`, `ver_ficha_empleado`, `ver_informes`, `ver_analiticas`, `gestion_gasolineras`, `gestion_portada`, `utilizar_explorador`, `gestion_usuarios`, `gestion_roles`).
- Se mantiene únicamente la excepción para superadministradores por identificador directo si aplica (`$user->email === 'jarodriguezbonilla@gmail.com' || $user->id === 1`).

## Deployment to Production (REGLA OBLIGATORIA)
- SIEMPRE que se complete una tarea o conjunto de cambios, realizar automáticamente:
  1. `git add` y `git commit`
  2. `git push origin main`
  3. Despliegue en producción: `ssh developer@164.68.101.69 "cd /home/developer/Projects/admin-platform && git pull && ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear"`
  No esperar confirmación adicional para este flujo tras finalizar los cambios.
