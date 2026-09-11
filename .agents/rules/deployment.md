# Deployment & Git Workflow

Siempre que se realicen cambios en el código o estilos:
1. Realizar git add y git commit con un mensaje descriptivo.
2. Hacer git push origin main.
3. Desplegar automáticamente en el servidor remoto (164.68.101.69) ejecutando:
   ssh developer@164.68.101.69 "cd /home/developer/Projects/admin-platform && git pull && ddev exec npm run build && ddev exec php artisan optimize:clear"
No es necesario esperar a que el usuario lo solicite.
