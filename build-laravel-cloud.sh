#!/bin/bash

# Script de build para Laravel Cloud
# Este script se ejecuta durante el proceso de build en Laravel Cloud

set -e

echo "🚀 Iniciando proceso de build para Laravel Cloud..."

# Verificar que el archivo laravel-cloud.env existe
if [ ! -f "laravel-cloud.env" ]; then
    echo "❌ Error: El archivo laravel-cloud.env no existe"
    echo "📁 Archivos disponibles en el directorio:"
    ls -la
    exit 1
fi

echo "✅ Archivo laravel-cloud.env encontrado"

# Copiar laravel-cloud.env a .env
echo "📋 Copiando laravel-cloud.env a .env..."
cp laravel-cloud.env .env

# Verificar que .env se creó correctamente
if [ ! -f ".env" ]; then
    echo "❌ Error: No se pudo crear el archivo .env"
    exit 1
fi

echo "✅ Archivo .env creado correctamente"

# Instalar dependencias de Node.js
echo "📦 Instalando dependencias de Node.js..."
npm install

# Compilar assets con Vite
echo "🎨 Compilando assets con Vite..."
npm run build

# Instalar dependencias de PHP
echo "📦 Instalando dependencias de PHP..."
composer install --no-dev --optimize-autoloader --no-scripts

# Ejecutar scripts de Composer
echo "🔧 Ejecutando scripts de Composer..."
composer run-script post-install-cmd || true

# Limpiar caché
echo "🧹 Limpiando caché..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Dar permisos correctos
echo "🔐 Configurando permisos..."
chmod -R 775 storage bootstrap/cache || true

# Generar clave de aplicación si no existe
echo "🔑 Verificando clave de aplicación..."
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    echo "Generando nueva clave de aplicación..."
    php artisan key:generate --force
fi

echo "✅ Build completado exitosamente"
echo "🎉 La aplicación está lista para el despliegue"
