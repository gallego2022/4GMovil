#!/bin/bash

# Script de inicialización para el contenedor Laravel

echo "🚀 Iniciando 4GMovil..."

# Instalar dependencias de Node.js si no están instaladas
echo "📦 Instalando dependencias de Node.js..."
if [ ! -d "node_modules" ]; then
    npm config set fetch-timeout 300000
    npm config set fetch-retry-mintimeout 20000
    npm config set fetch-retry-maxtimeout 120000
    npm install --timeout=300000 || echo "⚠️ Error en npm install, continuando..."
fi

# Compilar assets si no existen
echo "🎨 Compilando assets..."
if [ ! -d "public/build" ]; then
    npm run build || echo "⚠️ Error en npm build, continuando..."
fi

# Esperar a que la base de datos esté lista
echo "⏳ Esperando que la base de datos esté lista..."
# Usar variables de entorno para host y puerto
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
until nc -z $DB_HOST $DB_PORT; do
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

# Crear enlace simbólico para storage usando el comando Artisan
echo "🔗 Verificando y corrigiendo enlace simbólico de storage..."
# Eliminar enlace existente si está roto
rm -f /var/www/html/public/storage
# Crear nuevo enlace simbólico
php artisan storage:link

# Establecer permisos correctos
echo "🔐 Estableciendo permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Aplicación lista!"
echo "🌐 Servidor iniciando en puerto 80..."

# Iniciar Apache
exec apache2-foreground
