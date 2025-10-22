# 🔧 Configuración de Entornos - 4GMovil

Esta guía explica cómo configurar correctamente los archivos de entorno para cada tipo de instalación.

## 📋 Archivos de Configuración Disponibles

| Archivo | Entorno | Descripción |
|---------|---------|-------------|
| `env.local.example` | Desarrollo Local | Configuración para desarrollo en máquina local |
| `env.docker.example` | Docker | Configuración para contenedores Docker |
| `env.production.example` | Laravel Cloud | Configuración para producción en Laravel Cloud |

## 🏠 Configuración Local (Desarrollo)

### Archivo: `env.local.example`

**Características:**
- ✅ Caché con archivos (file cache)
- ✅ Base de datos MySQL local
- ✅ Sesiones con archivos
- ✅ Logs detallados para desarrollo
- ✅ Debug habilitado

**Configuración Principal:**
```bash
# Copiar archivo de ejemplo
cp env.local.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar base de datos en .env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=tu_password
```

**Variables Importantes:**
- `CACHE_DRIVER=file` - Caché con archivos para desarrollo
- `SESSION_DRIVER=file` - Sesiones con archivos
- `APP_DEBUG=true` - Debug habilitado
- `LOG_LEVEL=debug` - Logs detallados

## 🐳 Configuración Docker

### Archivo: `env.docker.example`

**Características:**
- ✅ Caché con Redis
- ✅ Base de datos MySQL en contenedor
- ✅ Sesiones con base de datos
- ✅ Cola de trabajos con Redis
- ✅ Broadcasting con Redis

**Configuración Principal:**
```bash
# Copiar archivo de ejemplo
cp env.docker.example .env

# Configuración automática en Docker
DB_HOST=mysql
REDIS_HOST=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

**Variables Importantes:**
- `CACHE_DRIVER=redis` - Caché con Redis
- `REDIS_HOST=redis` - Host de Redis en Docker
- `DB_HOST=mysql` - Host de MySQL en Docker
- `SESSION_DRIVER=database` - Sesiones con base de datos

## ☁️ Configuración Producción (Laravel Cloud)

### Archivo: `env.production.example`

**Características:**
- ✅ Caché con base de datos
- ✅ Base de datos MySQL en Laravel Cloud
- ✅ Sesiones con base de datos
- ✅ Cola de trabajos con base de datos
- ✅ Seguridad optimizada

**Configuración Principal:**
```bash
# Copiar archivo de ejemplo
cp env.production.example .env

# Configuración para producción
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=database
SESSION_DRIVER=database
```

**Variables Importantes:**
- `CACHE_DRIVER=database` - Caché con base de datos
- `APP_DEBUG=false` - Debug deshabilitado
- `SESSION_ENCRYPT=true` - Sesiones encriptadas
- `LOG_LEVEL=error` - Solo logs de error

## 🔑 Configuración de Servicios Externos

### Stripe (Pagos)

**Desarrollo:**
```bash
STRIPE_KEY=pk_test_51S2h8II7KkRE0HkwUeOAL1PLAXgpM8llkoL8pccIrOyLu2Rq3SuiZIsVhCX5yUeVp9uVaAKxeFTTUhvzuFpTAXDn00Hen0m0eU
STRIPE_SECRET=sk_test_51S2h8II7KkRE0HkwoTs8c75KSWErEupH06bQtk8M9VnOktjmXH2BqdPGFd3AovrzPtS9UBPhJeQhTW8vYhmwOd8n003DuWlG2G
STRIPE_WEBHOOK_SECRET=whsec_1zaCrHKuXbohbZDI8mmm9WSkzBcChqfl
```

**Producción:**
```bash
# Configurar en Laravel Cloud
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Google OAuth

**Desarrollo:**
```bash
GOOGLE_CLIENT_ID=1034347236850-aiuq6vgevrc9dmsuvi92lpv9nm1qrl13.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/callback/google
```

**Producción:**
```bash
# Configurar en Laravel Cloud
GOOGLE_CLIENT_ID=tu-client-id
GOOGLE_CLIENT_SECRET=tu-client-secret
GOOGLE_REDIRECT_URI=https://tu-proyecto.laravel-cloud.com/auth/callback/google
```

### Correo Electrónico

**Desarrollo:**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
```

**Producción:**
```bash
# Configurar en Laravel Cloud
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@4gmovil.com
MAIL_PASSWORD=tu-password
MAIL_ENCRYPTION=tls
```

## 🚀 Configuración de Caché

### Desarrollo Local
```bash
CACHE_DRIVER=file
CACHE_PREFIX=4gmovil_cache_
CACHE_TTL=3600
```

### Docker
```bash
CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379
CACHE_PREFIX=4gmovil_cache_
CACHE_TTL=3600
```

### Producción
```bash
CACHE_DRIVER=database
CACHE_PREFIX=4gmovil_cache_
CACHE_TTL=3600
CACHE_CLEAR_ON_DEPLOY=true
```

## 🗄️ Configuración de Base de Datos

### Desarrollo Local
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### Docker
```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=password
```

### Producción
```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=root
DB_PASSWORD=configurar_en_laravel_cloud
```

## 🔒 Configuración de Seguridad

### Desarrollo
```bash
APP_DEBUG=true
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### Producción
```bash
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

## 📊 Configuración de Monitoreo

### Desarrollo
```bash
LOG_LEVEL=debug
PERFORMANCE_MONITORING=true
DEBUG_BAR_ENABLED=true
TELESCOPE_ENABLED=true
```

### Producción
```bash
LOG_LEVEL=error
PERFORMANCE_MONITORING=true
DEBUG_BAR_ENABLED=false
TELESCOPE_ENABLED=false
```

## 🛠️ Comandos de Configuración

### Configurar Caché
```bash
# Configurar tabla de caché
php artisan cache:table

# Configurar caché según entorno
php artisan cache:configure-environment

# Configurar caché para Laravel Cloud
php artisan cache:setup-cloud --driver=database
```

### Configurar Sesiones
```bash
# Configurar tabla de sesiones
php artisan session:table

# Limpiar sesiones
php artisan session:clear
```

### Configurar Cola de Trabajos
```bash
# Configurar tabla de trabajos
php artisan queue:table

# Procesar trabajos
php artisan queue:work
```

## 📝 Notas Importantes

### Variables Obligatorias
- `APP_KEY` - Clave de aplicación (se genera automáticamente)
- `DB_PASSWORD` - Contraseña de base de datos
- `STRIPE_SECRET` - Clave secreta de Stripe
- `GOOGLE_CLIENT_SECRET` - Secreto del cliente de Google

### Variables Opcionales
- `AWS_ACCESS_KEY_ID` - Para almacenamiento en AWS
- `MAIL_PASSWORD` - Para envío de correos
- `REDIS_PASSWORD` - Para Redis con contraseña

### Configuración por Entorno
1. **Local**: Usar `env.local.example`
2. **Docker**: Usar `env.docker.example`
3. **Producción**: Usar `env.production.example`

### Verificación de Configuración
```bash
# Verificar configuración
php artisan config:show

# Verificar caché
php artisan cache:clear

# Verificar base de datos
php artisan migrate:status
```

## 🚨 Solución de Problemas

### Error de Cache Path
```bash
# Ejecutar script de creación de directorios
./setup-storage-directories.sh
```

### Error de Base de Datos
```bash
# Verificar conexión
php artisan tinker
DB::connection()->getPdo();
```

### Error de Redis
```bash
# Usar file cache como fallback
php artisan cache:setup-cloud --driver=file
```

### Error de Permisos
```bash
# Configurar permisos
chmod -R 775 storage bootstrap/cache
```

## 📚 Documentación Adicional

- [Guía de Instalación](README-INSTALACION.md)
- [Configuración Docker](docker-compose.yml)
- [Configuración Laravel Cloud](laravel-cloud.yml)
- [Funcionalidades del Proyecto](FUNCIONALIDADES_COMPLETAS_4GMOVIL.md)
