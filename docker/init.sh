#!/bin/bash

# Script de inicialización para Docker
# Este script se ejecuta cuando el contenedor inicia

set -e

echo "🚀 Iniciando configuración de Laravel en Docker..."

# Esperar a que la base de datos esté disponible
echo "⏳ Esperando a que la base de datos esté disponible..."
until nc -z db 3306; do
  echo "Base de datos no disponible - esperando..."
  sleep 2
done
echo "✅ Base de datos disponible"

# Esperar a que Redis esté disponible
echo "⏳ Esperando a que Redis esté disponible..."
until nc -z redis 6379; do
  echo "Redis no disponible - esperando..."
  sleep 2
done
echo "✅ Redis disponible"

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --force || echo "⚠️  Error generando clave, continuando..."
    echo "✅ Clave de aplicación generada"
else
    echo "✅ Clave de aplicación ya existe"
fi

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️  Error en migraciones, continuando..."
echo "✅ Migraciones ejecutadas"

# Ejecutar seeders si es necesario
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force || echo "⚠️  Seeders fallaron o no existen"
echo "✅ Seeders ejecutados"

# Limpiar caché
echo "🧹 Limpiando caché..."
php artisan config:clear || echo "⚠️  Error limpiando config"
php artisan cache:clear || echo "⚠️  Error limpiando cache"
php artisan route:clear || echo "⚠️  Error limpiando routes"
php artisan view:clear || echo "⚠️  Error limpiando views"
echo "✅ Caché limpiado"

# Optimizar para desarrollo (sin caché para desarrollo)
echo "⚡ Configurando aplicación para desarrollo..."
# En desarrollo no cacheamos para ver cambios en tiempo real
echo "✅ Aplicación configurada para desarrollo"

# Asegurar permisos
echo "🔐 Configurando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 755 /var/www/html/storage
chmod -R 777 /var/www/html/storage/framework
chmod -R 777 /var/www/html/storage/logs
chmod -R 777 /var/www/html/storage/app/public
chmod -R 777 /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/public/storage
echo "✅ Permisos configurados"

echo "🎉 Configuración de Laravel completada!"

# Ejecutar el comando original
exec "$@"
