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

# Crear enlace simbólico de storage si no existe
echo "🔗 Creando enlace simbólico de storage..."
if [ ! -L /var/www/html/public/storage ] && [ ! -d /var/www/html/public/storage ]; then
    php artisan storage:link || echo "⚠️  Error creando enlace simbólico, continuando..."
    echo "✅ Enlace simbólico creado"
elif [ -L /var/www/html/public/storage ]; then
    echo "✅ Enlace simbólico ya existe"
else
    # Si existe como directorio, intentar sincronizar
    echo "📦 Sincronizando storage (enlace simbólico no disponible)..."
    php artisan storage:sync --force || echo "⚠️  Error sincronizando storage, continuando..."
    echo "✅ Storage sincronizado"
fi

# Compilar assets de Vite si no existen
echo "📦 Verificando assets de Vite..."
if [ ! -f /var/www/html/public/build/manifest.json ]; then
    echo "🔨 Compilando assets de Vite..."
    npm run build || echo "⚠️  Error compilando assets, continuando..."
    echo "✅ Assets compilados"
else
    echo "✅ Assets de Vite ya existen"
fi

# Asegurar permisos
echo "🔐 Configurando permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || echo "⚠️  Error configurando permisos de storage"
chmod -R 755 /var/www/html/storage || echo "⚠️  Error configurando permisos de storage"
chmod -R 777 /var/www/html/storage/framework || echo "⚠️  Error configurando permisos de framework"
chmod -R 777 /var/www/html/storage/logs || echo "⚠️  Error configurando permisos de logs"
chmod -R 777 /var/www/html/storage/app/public || echo "⚠️  Error configurando permisos de app/public"
chmod -R 777 /var/www/html/bootstrap/cache || echo "⚠️  Error configurando permisos de bootstrap/cache"

# Asegurar permisos del enlace simbólico si existe
if [ -L /var/www/html/public/storage ] || [ -d /var/www/html/public/storage ]; then
    chown -R www-data:www-data /var/www/html/public/storage || echo "⚠️  Error configurando permisos de public/storage"
    chmod -R 755 /var/www/html/public/storage || echo "⚠️  Error configurando permisos de public/storage"
fi

# Asegurar permisos del directorio build si existe
if [ -d /var/www/html/public/build ]; then
    chown -R www-data:www-data /var/www/html/public/build || echo "⚠️  Error configurando permisos de public/build"
    chmod -R 755 /var/www/html/public/build || echo "⚠️  Error configurando permisos de public/build"
fi
echo "✅ Permisos configurados"

echo "🎉 Configuración de Laravel completada!"

# Ejecutar el comando original
exec "$@"
