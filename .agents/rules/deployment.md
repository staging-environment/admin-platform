# Deployment & Git Workflow (REGLA CRÍTICA DE DESPLIEGUE EN DOS FASES)

A partir de ahora, todo el desarrollo y despliegue sigue estrictamente un flujo en dos fases (**Preproducción -> Producción**):

---

## 1. Fase 1: Desarrollo y Despliegue en PREPRODUCCIÓN (Rama `pre`)
Siempre que se completen cambios en código, vistas, estilos, configuración o migraciones:
1. Realizar los commits en la rama **`pre`**.
2. Subir los cambios a GitHub en la rama `pre`:
   ```bash
   git push origin pre
   ```
3. Desplegar automáticamente en el entorno de **Preproducción** (`pre.utrecar.com`):
   ```bash
   ssh developer@164.68.101.69 "cd /home/developer/Projects/pre-admin-platform && git pull origin pre && ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear"
   ```
4. Notificar al usuario para que realice las pruebas y validación en **`https://pre.utrecar.com`**.

---

## 2. Fase 2: Pase a PRODUCCIÓN (Rama `main`)
**ÚNICAMENTE cuando el usuario indique explícitamente pasar a producción** (ej: "pasa a producción", "sube a producción", "haz el pase"):
1. Cambiar a la rama `main`, sincronizar y hacer merge de la rama `pre`:
   ```bash
   git checkout main
   git pull origin main
   git merge pre
   git push origin main
   ```
2. Desplegar en el entorno de **Producción** (`utrecar.com`):
   ```bash
   ssh developer@164.68.101.69 "cd /home/developer/Projects/admin-platform && git pull origin main && ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear"
   ```
3. Volver a la rama `pre` para continuar con los siguientes desarrollos:
   ```bash
   git checkout pre
   ```
4. Confirmar al usuario que el pase a producción se ha completado con éxito.
