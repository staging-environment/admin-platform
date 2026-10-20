# Integración MITECO y Programador de Tareas (Scheduler & Cron)

## 1. Arquitectura de Sincronización MITECO
- **Servicio principal**: App\Services\MitecoService
- **Job programado**: App\Jobs\UploadPricesToMitecoJob (ejecutado cada 5 minutos en routes/console.php).
- **Base de datos de origen**: VirtusGesNet, tabla preciosdeproductos (Estaciones 1: Vistalegre, 2: Ronda Norte, 3: Rodalabota, 4: Atenas).
- **Lógica de envío**:
  1. Compara los precios actuales en BD con storage/app/miteco_last_uploaded_prices.json.
  2. Si han cambiado, realiza el envío agrupado a MITECO para todas las estaciones.
  3. Si no han cambiado, se omite salvo los lunes entre las 06:00 y las 06:04 AM.
  4. Los precios se envían con vigencia futura de +75 minutos (exigencia MITECO: min 1 hora, max 3 días).
  5. En caso de éxito, actualiza caché miteco_last_update_status y notifica por Telegram.
  6. En caso de fallo, dispara una alerta crítica a Telegram y guarda el error en caché y logs.

---

## 2. Configuración del Programador en el Servidor (Producción)
- **Servidor**: utrecar-dev (164.68.101.69)
- **Archivo de configuración protegido**: /etc/cron.d/utrecar-scheduler
- **Permisos**: root:root (chmod 644)
- **Contenido del cron del sistema**:
  ```cron
  SHELL=/bin/bash
  PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

  * * * * * developer cd /home/developer/Projects/admin-platform && /usr/local/bin/ddev exec php artisan schedule:run >> /home/developer/Projects/admin-platform/storage/logs/cron.log 2>&1
  */5 * * * * developer cd /home/developer/Projects/status-utrecar && ddev exec php artisan monitor:stations > /dev/null 2>&1
  ```

### Regla Crítica sobre el Crontab
- **NUNCA** configurar estas tareas mediante el comando crontab -e del usuario developer.
- El 16/10/2026 el crontab de usuario fue vaciado accidentalmente por una sesión interactiva/script, deteniendo todo el planificador de producción.
- Al residir en /etc/cron.d/utrecar-scheduler con propiedad de root, queda protegido frente a comandos accidentales de usuario como crontab -r.

---

## 3. Diagnóstico Rápido si los Precios no Sincronizan

1. **Comprobar si el cron está activo**:
   ssh utrecar-dev "tail -n 25 /home/developer/Projects/admin-platform/storage/logs/cron.log"
   (Debe mostrar ejecuciones continuas minuto a minuto).

2. **Comprobar el archivo en el sistema**:
   ssh utrecar-dev "cat /etc/cron.d/utrecar-scheduler"

3. **Ejecutar o forzar sincronización manual**:
   ssh utrecar-dev "cd /home/developer/Projects/admin-platform && ddev exec php artisan miteco:sync"
   # Para forzar:
   ssh utrecar-dev "cd /home/developer/Projects/admin-platform && ddev exec php artisan miteco:sync --force"

4. **Supervisión Visual en Dashboard**:
   Si el planificador lleva más de 20 minutos inactivo, la cabecera del Dashboard muestra:
   ?? PROGRAMADOR INACTIVO (hace X min)
