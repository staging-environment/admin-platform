# 🚀 Setup: Admin Platform - VirtusGesNet

Instrucciones detalladas para configurar y ejecutar el proyecto **Admin Platform** de forma local.

## ✅ Requisitos Previos

- **DDEV** instalado y funcionando
- **Docker** instalado
- **Composer** instalado
- **PHP 8.3+**
- **Node.js 20+** (ya instalado)
- **Túnel SSH activo** a las máquinas virtuales en puerto 32828

## 🛠️ Instalación Paso a Paso

### Paso 1: Inicializar DDEV

```bash
cd ~/Projects/admin-platform
ddev start
```

Esto iniciará los contenedores con:
- PHP 8.3
- MariaDB 11.0
- Nginx

### Paso 2: Instalar Dependencias PHP

```bash
ddev composer install
```

### Paso 3: Generar Key de la Aplicación

```bash
ddev exec php artisan key:generate
```

### Paso 4: Ejecutar Migraciones

```bash
ddev exec php artisan migrate
```

Esta comando creará las tablas:
- `users` - Usuarios del sistema
- `permissions` - Permisos (Spatie)
- `roles` - Roles (Spatie)
- `role_has_permissions` - Relación roles-permisos
- `model_has_roles` - Relación usuarios-roles
- `filters` - Filtros guardados

### Paso 5: Ejecutar Seeders

```bash
ddev exec php artisan db:seed --class=PermissionSeeder
```

Esto creará:
- **Roles:** Admin, Manager, User
- **Permisos:** view-dashboard, view-reports, create-filter, edit-filter, delete-filter, view-all-data, export-data, manage-users, manage-roles

### Paso 6: Compilar Assets Frontend

```bash
ddev exec npm run build
```

## 🌐 Acceso a la Aplicación

Una vez completados los pasos, accede a:

- **Frontend:** https://admin-platform.ddev.site
- **API:** https://admin-platform.ddev.site/api/

## 🔓 Crear Primer Usuario (Admin)

```bash
ddev exec php artisan tinker
```

En la consola Tinker:

```php
> $user = User::factory()->create(['email' => 'admin@example.com', 'name' => 'Administrator']);
> $user->assignRole('Admin');
> $user->password = bcrypt('password123');
> $user->save();
> exit
```

**Credenciales:**
- Email: `admin@example.com`
- Password: `password123`

## 📡 Verificar Conexión a las BD Remotas

```bash
ddev exec php artisan tinker
```

```php
> DB::connection('administracioncorporativa')->select("SELECT VERSION()")
> DB::connection('virtusgesnet')->select("SELECT VERSION()")
> exit
```

Si funciona, debería retornar la versión del servidor MySQL.

## 📝 Rutas API Principales

### Autenticación
```
POST /api/login                    # Iniciar sesión
POST /api/logout                   # Cerrar sesión
GET  /api/user                     # Datos del usuario actual
```

###  Consultas a Datos
```
GET  /api/databases/tables         # Listar tablas disponibles
POST /api/data/query               # Consultar datos de una tabla
POST /api/data/custom-query        # Ejecutar consulta SELECT personalizada
GET  /api/data/schema              # Obtener esquema de una tabla
```

### Filtros
```
GET  /api/filters                  # Listar filtros del usuario
POST /api/filters                  # Crear nuevo filtro
GET  /api/filters/{id}             # Obtener un filtro específico
PUT  /api/filters/{id}             # Actualizar filtro
DELETE /api/filters/{id}           # Eliminar filtro
```

## 🛑 Detener y Reiniciar DDEV

```bash
# Detener
ddev stop

# Reiniciar
ddev restart

# Ver logs
ddev logs -f

# Acceder al contenedor
ddev ssh
```

## 🐛 Troubleshooting

### Error: "No application key found"
```bash
ddev exec php artisan key:generate
```

### Error: "Connection refused" en BD remotas
- Verificar que el túnel SSH está activo en puerto 32828
- Verificar que las variables de entorno `.env` están correctas
- Probar conexión: `ddev exec php artisan tinker`

### Error: "SQLSTATE[42S02]: Table not found"
```bash
ddev exec php artisan migrate
```

### Assets no se compilan
```bash
ddev exec npm install
ddev exec npm run build
```

## 🔧 Configuración Importante

### Variables de Entorno (.env)

```ini
# Conexión Local (DDEV)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=admin_platform
DB_USERNAME=root
DB_PASSWORD=

# Conexiones Remotas
DB_CORP_HOST=127.0.0.1
DB_CORP_PORT=32828
DB_CORP_DATABASE=administracioncorporativa
DB_CORP_USERNAME=db
DB_CORP_PASSWORD=

DB_VIRTUS_HOST=127.0.0.1
DB_VIRTUS_PORT=32828
DB_VIRTUS_DATABASE=virtusgesnet
DB_VIRTUS_USERNAME=db
DB_VIRTUS_PASSWORD=
```

## 📦 Estructura de Carpetas Creadas

```
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php      ✅ Dashboard principal
│       └── Api/
│           ├── DataQueryController.php  ✅ Consultas a BD remotas
│           └── FilterController.php     ✅ Gestión de filtros
├── Models/
│   ├── User.php                         ✅ Con Spatie Roles
│   └── Filter.php                       ✅ Filtros guardados
├── Services/
│   ├── AdministracionCorporativaService.php  ✅ Consultas BD #1
│   └── VirtusgesnetService.php               ✅ Consultas BD #2

database/
└── seeders/
    └── PermissionSeeder.php             ✅ Roles y permisos iniciales

config/
├── database.php                         ✅ Conexiones múltiples
├── sanctum.php                          ✅ API authentication
└── permission.php                       ✅ Spatie permission config
```

## 🚀 Próximos Pasos

1. ✅ Crear vistas Blade para dashboard
2. ✅ Implementar búsqueda de tablas en frontend
3. ✅ Crear widget de estadísticas
4. ✅ Agregar exportación de datos (CSV/PDF)
5. ✅ Implementar caching de datos frecuentes
6. ✅ Agregar auditoría de acciones
7. ✅ Crear tests automatizados

## 📞 Comandos Útiles de Artisan

```bash
# Listar rutas
ddev exec php artisan route:list

# Ver migraciones pendientes
ddev exec php artisan migrate:status

# Crear modelo con migración y factory
ddev exec php artisan make:model NombreModelo -mf

# Crear controlador
ddev exec php artisan make:controller NombreController

# Ejecutar tinker (consola interactiva)
ddev exec php artisan tinker

# Limpiar caché
ddev exec php artisan cache:clear
ddev exec php artisan config:clear
ddev exec php artisan view:clear
```

## 📖 Documentación de Referencia

- **Laravel:** https://laravel.com/docs
- **Spatie Permission:** https://spatie.be/docs/laravel-permission
- **Laravel Sanctum:** https://laravel.com/docs/sanctum
- **DDEV:** https://ddev.readthedocs.io

## 🤝 Notas Importantes

- ⚠️ Las BD remotas son **SOLO LECTURA** para este proyecto
- ⚠️ La autenticación es requerida para consumir la API
- ⚠️ Usar Sanctum tokens para APIs externas
- ⚠️ Los filtros se guardan localmente, no en BD remotas
- ⚠️ Mantener el túnel SSH activo durante el desarrollo

---

**Versión:** 1.0.0  
**Última actualización:** 28/04/2026  
**Estado:** ✨ Listo para desarrollo

