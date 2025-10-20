# Build Command para Laravel Cloud

## Comando Actual (Problemático)
```bash
composer install --no-dev
npm ci --audit false
npm run build
```

## Problema
Este comando no resuelve el conflicto con Docker y no configura las rutas de caché correctamente.

## Solución: Nuevo Build Command

Reemplaza tu Build Command actual con este:

```bash
# Hacer el script ejecutable
chmod +x build-laravel-cloud.sh

# Ejecutar nuestro script personalizado
./build-laravel-cloud.sh

# Instalar dependencias de Node.js
npm ci --audit false

# Compilar assets
npm run build
```

## Alternativa: Build Command Simplificado

Si prefieres un comando más simple, usa este:

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

# Crear archivo .env específico para Laravel Cloud
cat > .env << 'EOF'
APP_NAME="4GMovil"
APP_ENV=production
APP_KEY=base64:gRO33MAV0Lza0BC8blZlvMHUzg8zMAoiO/kCmRyi+64=
APP_DEBUG=false
APP_TIMEZONE=America/Bogota
APP_URL=https://tu-dominio.laravel.cloud
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_ES
APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=4gmovil
DB_USERNAME=laravel
DB_PASSWORD=password
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_DRIVER=file
CACHE_STORE=file
CACHE_PREFIX=4gmovil_cache
VIEW_COMPILED_PATH=/tmp/views
VIEW_DEBUG=false
VIEW_CACHE_ENABLED=true
VIEW_CACHE_PATH=/tmp/views
VIEW_CACHE_TTL=3600
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=4gmoviltest@gmail.com
MAIL_PASSWORD=szgtcvuixtdrhepg
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="4gmoviltest@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
VITE_APP_NAME="${APP_NAME}"
STRIPE_KEY=pk_test_TU_STRIPE_PUBLIC_KEY_AQUI
STRIPE_SECRET=sk_test_TU_STRIPE_SECRET_KEY_AQUI
STRIPE_WEBHOOK_SECRET=whsec_TU_WEBHOOK_SECRET_AQUI
GOOGLE_CLIENT_ID=TU_GOOGLE_CLIENT_ID_AQUI
GOOGLE_CLIENT_SECRET=TU_GOOGLE_CLIENT_SECRET_AQUI
GOOGLE_REDIRECT_URI=https://tu-dominio.laravel.cloud/auth/callback/google
INVENTORY_LOW_STOCK_THRESHOLD=10
INVENTORY_ALERT_EMAIL=4gmoviltest@gmail.com
EOF

# Limpiar caché
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

## ¿Por qué funciona esta solución?

1. **Elimina archivos de Docker** que causan conflictos
2. **Crea directorios necesarios** con permisos correctos
3. **Fuerza la creación** de un archivo `.env` específico para Laravel Cloud
4. **Configura rutas de caché** que Laravel Cloud puede usar
5. **Optimiza la aplicación** para producción

## Resultado Esperado

Con este Build Command:
- ✅ Se eliminan los conflictos con Docker
- ✅ Se configuran las rutas de caché correctamente
- ✅ El error "Please provide a valid cache path" se resuelve
- ✅ La aplicación se desplega correctamente
