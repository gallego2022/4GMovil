#!/bin/bash

# Script de inicialización para el contenedor Laravel

echo "🚀 Iniciando 4GMovil..."

# Instalar dependencias de Node.js si no están instaladas
echo "📦 Instalando dependencias de Node.js..."
if [ ! -d "node_modules" ]; then
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

# Sincronizar carpetas de storage al directorio público
echo "📁 Sincronizando carpetas de storage..."
# Crear directorio público de storage si no existe
mkdir -p /var/www/html/public/storage

# Sincronizar todas las carpetas de storage/app/public al directorio público
if [ -d "/var/www/html/storage/app/public" ]; then
    echo "🔄 Sincronizando contenido de storage/app/public..."
    # Copiar todas las carpetas y archivos
    cp -r /var/www/html/storage/app/public/* /var/www/html/public/storage/ 2>/dev/null || true
    
    # Asegurar que las carpetas principales existan
    mkdir -p /var/www/html/public/storage/productos
    mkdir -p /var/www/html/public/storage/fotos_perfil
    mkdir -p /var/www/html/public/storage/perfiles
    
    # Sincronizar contenido específico
    if [ -d "/var/www/html/storage/app/public/productos" ]; then
        cp -r /var/www/html/storage/app/public/productos/* /var/www/html/public/storage/productos/ 2>/dev/null || true
    fi
    
    if [ -d "/var/www/html/storage/app/public/fotos_perfil" ]; then
        cp -r /var/www/html/storage/app/public/fotos_perfil/* /var/www/html/public/storage/fotos_perfil/ 2>/dev/null || true
    fi
    
    if [ -d "/var/www/html/storage/app/public/perfiles" ]; then
        cp -r /var/www/html/storage/app/public/perfiles/* /var/www/html/public/storage/perfiles/ 2>/dev/null || true
    fi
fi

# Establecer permisos correctos
echo "🔐 Estableciendo permisos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

echo "✅ Aplicación lista!"
echo "🌐 Servidor iniciando en puerto 80..."

# Iniciar Apache
exec apache2-foreground
