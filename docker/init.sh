#!/bin/bash

# Script de inicialización para el contenedor Laravel

echo "🚀 Iniciando 4GMovil..."

# Esperar un momento para que la base de datos esté lista
echo "⏳ Esperando que la base de datos esté lista..."
sleep 10

echo "✅ Iniciando aplicación..."

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --force
fi

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders si es la primera vez
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force

# Limpiar y optimizar caché
echo "🧹 Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Establecer permisos correctos
echo "🔐 Estableciendo permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Aplicación lista!"
echo "🌐 Servidor iniciando en puerto 80..."

# Iniciar Apache
exec apache2-foreground
