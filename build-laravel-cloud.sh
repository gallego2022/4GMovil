#!/bin/bash

# Script de construcción específico para Laravel Cloud
# Este script FORZA las configuraciones correctas

echo "🚀 Iniciando construcción para Laravel Cloud..."

# Eliminar archivos de Docker que pueden causar conflictos
echo "🗑️ Eliminando archivos de Docker que causan conflictos..."
rm -f .env
rm -f env.docker.example
rm -f docker-compose.yml
rm -f Dockerfile
rm -rf docker/

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p /tmp/views

# Configurar permisos
echo "🔐 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 /tmp/views

# Crear archivo .env específico para Laravel Cloud
echo "📋 Creando archivo .env específico para Laravel Cloud..."
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

# Base de datos - Laravel Cloud
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

# Configuración de caché CRÍTICA para Laravel Cloud
CACHE_DRIVER=file
CACHE_STORE=file
CACHE_PREFIX=4gmovil_cache

# Configuración de vistas CRÍTICA para Laravel Cloud
VIEW_COMPILED_PATH=/tmp/views
VIEW_DEBUG=false
VIEW_CACHE_ENABLED=true
VIEW_CACHE_PATH=/tmp/views
VIEW_CACHE_TTL=3600

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=4gmoviltest@gmail.com
MAIL_PASSWORD=szgtcvuixtdrhepg
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="4gmoviltest@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

# AWS
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# Stripe
STRIPE_KEY=pk_test_TU_STRIPE_PUBLIC_KEY_AQUI
STRIPE_SECRET=sk_test_TU_STRIPE_SECRET_KEY_AQUI
STRIPE_WEBHOOK_SECRET=whsec_TU_WEBHOOK_SECRET_AQUI

# Google OAuth
GOOGLE_CLIENT_ID=TU_GOOGLE_CLIENT_ID_AQUI
GOOGLE_CLIENT_SECRET=TU_GOOGLE_CLIENT_SECRET_AQUI
GOOGLE_REDIRECT_URI=https://tu-dominio.laravel.cloud/auth/callback/google

# Inventory Configuration
INVENTORY_LOW_STOCK_THRESHOLD=10
INVENTORY_ALERT_EMAIL=4gmoviltest@gmail.com
EOF

# Limpiar caché existente
echo "🧹 Limpiando caché existente..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
php artisan key:generate --force

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force || true

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link || true

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Instalar dependencias de Node.js y compilar assets
echo "📦 Instalando dependencias de Node.js..."
npm install || echo "⚠️ Error en npm install, continuando..."

echo "🎨 Compilando assets..."
npm run build || echo "⚠️ Error en npm build, continuando..."

echo "✅ Construcción completada para Laravel Cloud!"
