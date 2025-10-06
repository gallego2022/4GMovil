# Build Command Corregido para Laravel Cloud

## Problema Actual
```
PHP Fatal error: Failed opening required '/var/www/html/vendor/autoload.php'
```

**Causa**: No se han instalado las dependencias de Composer antes de ejecutar comandos de Artisan.

## Solución: Build Command Corregido

Reemplaza tu Build Command actual con este:

```bash
# Eliminar archivos de Docker que causan conflictos
rm -f .env env.docker.example docker-compose.yml Dockerfile
rm -rf docker/

# Crear directorios necesarios
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p /tmp/views

# Configurar permisos
chmod -R 775 storage bootstrap/cache /tmp/views

# Copiar archivo de configuración específico para Laravel Cloud
cp laravel-cloud.env .env

# INSTALAR DEPENDENCIAS DE COMPOSER PRIMERO
composer install --no-dev --optimize-autoloader

# Limpiar caché existente (después de instalar dependencias)
php artisan cache:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Generar clave de aplicación
php artisan key:generate --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Instalar dependencias de Node.js
npm ci --audit false

# Compilar assets
npm run build
```

## Orden Correcto de Comandos

1. **Eliminar archivos de Docker** ✅
2. **Crear directorios** ✅
3. **Configurar permisos** ✅
4. **Copiar archivo de configuración** ✅
5. **INSTALAR DEPENDENCIAS DE COMPOSER** ⚠️ **CRÍTICO**
6. **Ejecutar comandos de Artisan** ✅
7. **Optimizar aplicación** ✅
8. **Instalar dependencias de Node.js** ✅
9. **Compilar assets** ✅

## ¿Por qué falló antes?

El Build Command anterior intentaba ejecutar:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

**Sin haber instalado las dependencias de Composer primero**, por eso el error:
```
Failed opening required '/var/www/html/vendor/autoload.php'
```

## Build Command Alternativo (Más Simple)

Si prefieres algo más simple:

```bash
# Eliminar archivos de Docker
rm -f .env env.docker.example docker-compose.yml Dockerfile
rm -rf docker/

# Crear directorios y permisos
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache /tmp/views
chmod -R 775 storage bootstrap/cache /tmp/views

# Copiar configuración
cp laravel-cloud.env .env

# Instalar dependencias de Composer
composer install --no-dev --optimize-autoloader

# Limpiar y optimizar
php artisan cache:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Instalar dependencias de Node.js y compilar
npm ci --audit false
npm run build
```

## Variables de Entorno Críticas

Asegúrate de configurar estas variables en Laravel Cloud:

```bash
VIEW_COMPILED_PATH=/tmp/views
VIEW_DEBUG=false
VIEW_CACHE_ENABLED=true
VIEW_CACHE_PATH=/tmp/views
CACHE_DRIVER=file
CACHE_STORE=file
CACHE_PREFIX=4gmovil_cache
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laravel
DB_PASSWORD=password
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.laravel.cloud
```

## Resultado Esperado

Con este Build Command corregido:
- ✅ Se instalan las dependencias de Composer primero
- ✅ Se eliminan los conflictos con Docker
- ✅ Se usa tu archivo `laravel-cloud.env`
- ✅ Se configuran las rutas de caché correctamente
- ✅ El error de `vendor/autoload.php` se resuelve
- ✅ La aplicación se desplega correctamente
