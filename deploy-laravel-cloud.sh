#!/bin/bash

# Script de despliegue para Laravel Cloud
# Este script configura las rutas de caché correctamente

echo "🚀 Iniciando despliegue en Laravel Cloud..."

# Crear directorios necesarios si no existen
echo "📁 Creando directorios de caché..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Configurar permisos
echo "🔐 Configurando permisos..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Limpiar caché existente
echo "🧹 Limpiando caché existente..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

# Crear enlaces simbólicos si es necesario
echo "🔗 Creando enlaces simbólicos..."
php artisan storage:link

echo "✅ Despliegue completado exitosamente!"
