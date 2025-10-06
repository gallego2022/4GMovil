#!/bin/bash

# Script de construcción específico para Laravel Cloud
# Este script reemplaza la lógica de Docker

echo "🚀 Iniciando construcción para Laravel Cloud..."

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
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

# Copiar archivo de entorno específico para Laravel Cloud
echo "📋 Configurando variables de entorno..."
if [ -f "laravel-cloud.env" ]; then
    cp laravel-cloud.env .env
    echo "✅ Archivo de entorno copiado"
else
    echo "⚠️ Archivo laravel-cloud.env no encontrado, usando configuración por defecto"
fi

# Generar clave de aplicación si no existe
echo "🔑 Generando clave de aplicación..."
php artisan key:generate --force

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Instalar dependencias de Node.js y compilar assets
echo "📦 Instalando dependencias de Node.js..."
npm install

echo "🎨 Compilando assets..."
npm run build

echo "✅ Construcción completada para Laravel Cloud!"
