#!/bin/bash

# Script de inicialización para el contenedor Laravel

echo "🚀 Iniciando 4GMovil..."

# Esperar a que la base de datos esté lista
echo "⏳ Esperando que la base de datos esté lista..."
until nc -z db 3306; do
  echo "Esperando conexión a la base de datos..."
  sleep 2
done
echo "✅ Base de datos conectada!"

# Generar clave de aplicación si no existe
echo "🔑 Generando clave de aplicación..."
php artisan key:generate --force

# Limpiar caché
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force

# Establecer permisos correctos
echo "🔐 Estableciendo permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Aplicación lista!"
echo "🌐 Servidor iniciando en puerto 80..."

# Iniciar Apache
exec apache2-foreground
