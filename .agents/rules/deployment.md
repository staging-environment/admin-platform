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
   ssh utrecar-dev "cd /home/developer/Projects/pre-admin-platform && git pull origin pre && ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear"
   ```
4. Notificar al usuario para que realice las pruebas y validación en **`https://pre.utrecar.com`**.

---

## 2. Fase 2: Pase a PRODUCCIÓN (Rama `main`)
**ÚNICAMENTE cuando el usuario indique explícitamente pasar a producción** (ej: "pasa a producción", "pasa todos los cambios de pre a pro", "sube a producción", "haz el pase"):

**El proceso se realiza en la máquina virtual de producción (`utrecar-dev` / `164.68.101.69`)**:
1. Entrar al directorio de producción `/home/developer/Projects/admin-platform`.
2. Si hay cambios pendientes o configuraciones en producción, commitearlos en `main`.
3. Actualizar y hacer merge de los cambios de `pre` hacia `main`:
   ```bash
   git fetch origin
   git merge origin/pre --no-edit
   git push origin main
   ```
4. Ejecutar migraciones y refrescar cachés en el contenedor DDEV de producción:
   ```bash
   ddev exec php artisan migrate --force && ddev exec npm run build && ddev exec php artisan optimize:clear
   ```
5. Sincronizar el repositorio local:
   ```bash
   git fetch origin && git checkout main && git pull origin main && git checkout pre && git merge main && git push origin pre
   ```
6. Confirmar al usuario que el pase a producción se ha completado con éxito en la máquina virtual de producción y los entornos quedan alineados.
