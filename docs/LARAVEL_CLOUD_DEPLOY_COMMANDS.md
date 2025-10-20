# Comandos de Despliegue para Laravel Cloud

## Problema Actual
```
SQLSTATE[HY000] [2002] Connection refused
Deploy commands failed!
```

**Causa**: Laravel Cloud está intentando ejecutar comandos que requieren conexión a la base de datos, pero la base de datos aún no está configurada.

## Solución: Configurar Deploy Commands

En Laravel Cloud, configura los **Deploy Commands** como:

```bash
# Comandos que NO requieren base de datos
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## Comandos que NO debes usar en Deploy Commands

❌ **NO uses estos comandos** (requieren base de datos):
```bash
php artisan migrate
php artisan db:seed
php artisan tinker
php artisan queue:work
php artisan schedule:run
```

## Comandos Seguros para Deploy Commands

✅ **Usa estos comandos** (no requieren base de datos):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan optimize
```

## Configuración Completa

### 1. Build Commands (ya configurado correctamente)
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

### 2. Deploy Commands (NUEVO)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
php artisan optimize
```

## Variables de Entorno Críticas

Asegúrate de configurar estas variables en Laravel Cloud:

```bash
# Base de datos (Laravel Cloud las proporciona automáticamente)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laravel
DB_PASSWORD=password

# Caché
VIEW_COMPILED_PATH=/tmp/views
VIEW_DEBUG=false
VIEW_CACHE_ENABLED=true
VIEW_CACHE_PATH=/tmp/views
CACHE_DRIVER=file
CACHE_STORE=file
CACHE_PREFIX=4gmovil_cache

# Aplicación
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.laravel.cloud
```

## Migraciones de Base de Datos

Para ejecutar migraciones, usa el **terminal de Laravel Cloud** después del despliegue:

```bash
# Conectar al terminal de Laravel Cloud
php artisan migrate --force
php artisan db:seed --force
```

## Advertencias de PHP Deprecated

Las advertencias de PHP deprecated no impiden el despliegue, pero puedes corregirlas:

```php
// En lugar de:
public function showStatus($phase = null)

// Usa:
public function showStatus(?string $phase = null)
```

## Resultado Esperado

Con esta configuración:
- ✅ Build Commands se ejecutan correctamente
- ✅ Deploy Commands se ejecutan sin errores de base de datos
- ✅ La aplicación se despliega correctamente
- ✅ Las migraciones se ejecutan manualmente después del despliegue

## Pasos Siguientes

1. **Configura los Deploy Commands** como se muestra arriba
2. **Ejecuta el despliegue** nuevamente
3. **Después del despliegue**, usa el terminal de Laravel Cloud para ejecutar migraciones
4. **Verifica** que la aplicación funcione correctamente
