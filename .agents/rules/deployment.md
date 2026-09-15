# Deployment & Git Workflow

Siempre que se finalicen cambios en el código, base de datos o estilos:
1. Realizar git add y git commit con un mensaje descriptivo.
2. Hacer git push origin main.
3. Desplegar automáticamente en el servidor remoto (164.68.101.69) ejecutando:
   ssh developer@164.68.101.69 "cd /home/developer/Projects/admin-platform && git pull && ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear"

Este flujo debe ejecutarse SIEMPRE al completar cualquier requerimiento o cambio solicitado por el usuario, sin esperar a que lo pida explícitamente.
