#!/bin/bash

# Script de restauración de configuración Redis para Laravel Cloud
# Este script se ejecuta después del despliegue para configurar Redis

set -e

echo "🔄 Restaurando configuración de Redis para Laravel Cloud..."

# Verificar que el archivo .env existe
if [ ! -f ".env" ]; then
    echo "❌ Error: El archivo .env no existe"
    exit 1
fi

echo "✅ Archivo .env encontrado"

# Verificar configuración de Redis en .env
echo "🔍 Verificando configuración de Redis..."

# Configurar Redis si no está configurado
if ! grep -q "CACHE_DRIVER=redis" .env; then
    echo "📝 Configurando CACHE_DRIVER=redis..."
    sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=redis/' .env || echo "CACHE_DRIVER=redis" >> .env
fi

if ! grep -q "QUEUE_CONNECTION=redis" .env; then
    echo "📝 Configurando QUEUE_CONNECTION=redis..."
    sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env || echo "QUEUE_CONNECTION=redis" >> .env
fi

if ! grep -q "SESSION_DRIVER=database" .env; then
    echo "📝 Configurando SESSION_DRIVER=database..."
    sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=database/' .env || echo "SESSION_DRIVER=database" >> .env
fi

# Configurar Redis host
if ! grep -q "REDIS_HOST=" .env; then
    echo "📝 Configurando REDIS_HOST=redis..."
    echo "REDIS_HOST=redis" >> .env
fi

# Configurar Redis port
if ! grep -q "REDIS_PORT=" .env; then
    echo "📝 Configurando REDIS_PORT=6379..."
    echo "REDIS_PORT=6379" >> .env
fi

# Configurar Redis DB
if ! grep -q "REDIS_DB=" .env; then
    echo "📝 Configurando REDIS_DB=0..."
    echo "REDIS_DB=0" >> .env
fi

# Limpiar caché de configuración
echo "🧹 Limpiando caché de configuración..."
php artisan config:clear || true

# Recargar configuración
echo "🔄 Recargando configuración..."
php artisan config:cache || true

# Verificar conexión a Redis
echo "🔍 Verificando conexión a Redis..."
if php artisan tinker --execute="Redis::ping();" 2>/dev/null; then
    echo "✅ Conexión a Redis exitosa"
else
    echo "⚠️  Advertencia: No se pudo conectar a Redis"
    echo "   Esto es normal si Redis no está disponible aún"
fi

# Ejecutar migraciones si es necesario
echo "🗄️  Verificando migraciones..."
php artisan migrate --force || echo "⚠️  Las migraciones se ejecutarán cuando la base de datos esté disponible"

echo "✅ Configuración de Redis restaurada exitosamente"
echo "🎉 La aplicación está configurada para usar Redis"
