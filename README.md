# Admin Platform - VirtusGesNet

Plataforma administrativa con Laravel 13, autenticación de usuarios, sistema de roles y permisos, conectada a las bases de datos de VirtusGesNet para consultar y filtrar información.

## 🚀 Stack Tecnológico

- **Laravel 13** - Framework PHP
- **PHP 8.3** - Versión de PHP
- **MySQL/MariaDB** - Base de datos local (DDEV)
- **Sanctum** - API autenticación
- **Spatie/Laravel-Permission** - Roles y permisos
- **Breeze** - Autenticación scaffolding
- **Tailwind CSS** - Estilos
- **Alpine.js** - Interactividad frontend
- **DDEV** - Entorno de desarrollo Docker

## 🔧 Estructura del Proyecto

```
admin-platform/
├── .ddev/                    # Configuración DDEV
│   └── config.yaml           # Configuración contenedores
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── DataQueryController.php   # Consultas a BD remotas
│   │   │   └── ReportController.php      # Reportes filtrados
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php          # Usuario con roles/permisos
│   │   ├── RemoteData.php    # Caché de datos remotos
│   │   └── Filter.php        # Filtros guardados
│   └── Services/
│       ├── AdministracionCorporativaService.php
│       └── VirtusgesnetService.php
├── database/
│   └── migrations/
├── routes/
│   ├── web.php               # Rutas web
│   └── api.php               # Rutas API con Sanctum
└── config/
    ├── database.php          # Conexiones locales y remotas
    ├── permission.php        # Spatie config
    └── sanctum.php           # Sanctum config
```

## 🗄️ Bases de Datos Configuradas

### Base de Datos Local (DDEV)
- **Conexión:** MySQL en DDEV
- **Propósito:** Datos de usuarios, roles, permisos, filtros guardados, caché
- **Env:** `DB_HOST=db, DB_DATABASE=admin_platform`

### Database 1: administracioncorporativa (Remota)
- **Conexión:** `administracioncorporativa`
- **Host:** 127.0.0.1 (túnel SSH)
- **Puerto:** 32828
- **Base de datos:** administracioncorporativa
- **Usuario:** db
- **Password:** (vacía)

### Database 2: virtusgesnet (Remota)
- **Conexión:** `virtusgesnet`
- **Host:** 127.0.0.1 (túnel SSH)
- **Puerto:** 32828
- **Base de datos:** virtusgesnet
- **Usuario:** db
- **Password:** (vacía)

## 🏃 Inicio Rápido

### 1. Iniciar DDEV
```bash
cd ~/Projects/admin-platform
ddev start
```

### 2. Instalar dependencias y ejecutar migraciones
```bash
ddev composer install
ddev exec php artisan migrate
```

### 3. Acceder a la aplicación
- **Frontend:** https://admin-platform.ddev.site
- **API:** https://admin-platform.ddev.site/api/

## 🛡️ Seguridad

- ✅ Autenticación con Laravel Breeze
- ✅ API con Sanctum (tokens)
- ✅ Validación de roles/permisos
- ✅ Solo lectura en BD remotas
- ✅ CSRF protection

## 📝 Notas Importantes

- Las BD remotas son **SOLO LECTURA** para este proyecto
- Los datos se cachean localmente según sea necesario
- Las credenciales están en el archivo `.env`
- Usar DDEV para desarrollo local consistente

---

**Proyecto creado:** 28/04/2026  
**Versión:** 1.0.0  
**Estado:** ✨ En construcción

