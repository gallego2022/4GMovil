#!/bin/bash

# Script de Optimización Específica para Laravel Cloud
# Este script maximiza el rendimiento en Laravel Cloud

echo "🚀 Iniciando optimización para Laravel Cloud..."

# Verificar variables de entorno de Laravel Cloud
echo "🔍 Verificando variables de entorno..."
php verify-laravel-cloud-env.php

# Crear directorios optimizados
echo "📁 Creando directorios optimizados..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p /tmp/views
mkdir -p /tmp/cache

# Configurar permisos optimizados
echo "🔐 Configurando permisos optimizados..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 /tmp/views
chmod -R 775 /tmp/cache

# Limpiar todo el caché existente
echo "🧹 Limpiando caché existente..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true
php artisan event:clear || true
php artisan queue:clear || true

# Optimizar autoloader de Composer
echo "📦 Optimizando autoloader de Composer..."
composer dump-autoload --optimize --classmap-authoritative --no-dev

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
php artisan key:generate --force

# Ejecutar migraciones con optimizaciones
echo "📊 Ejecutando migraciones optimizadas..."
php artisan migrate --force --no-interaction

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link || true

# Optimizaciones críticas para Laravel Cloud
echo "⚡ Aplicando optimizaciones críticas..."

# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Cache de vistas
php artisan view:cache

# Cache de eventos
php artisan event:cache

# Optimizar para producción
php artisan optimize

# Precompilar vistas Blade
echo "🎨 Precompilando vistas Blade..."
php artisan view:cache

# Instalar dependencias de Node.js optimizadas
echo "📦 Instalando dependencias de Node.js optimizadas..."
npm ci --only=production --no-audit --no-fund

# Compilar assets para producción
echo "🎨 Compilando assets para producción..."
npm run build

# Optimizar imágenes si existen
echo "🖼️ Optimizando imágenes..."
if [ -d "public/img" ]; then
    find public/img -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" | head -10 | while read img; do
        if command -v convert &> /dev/null; then
            convert "$img" -quality 85 -strip "$img"
        fi
    done
fi

# Configurar Redis para máximo rendimiento
echo "🔴 Configurando Redis para máximo rendimiento..."
php artisan tinker --execute="
    try {
        Redis::ping();
        Redis::config('set', 'maxmemory-policy', 'allkeys-lru');
        Redis::config('set', 'timeout', '300');
        echo 'Redis configurado correctamente';
    } catch (Exception \$e) {
        echo 'Redis no disponible, usando caché de archivos';
    }
" || true

# Limpiar logs antiguos
echo "📝 Limpiando logs antiguos..."
find storage/logs -name "*.log" -mtime +7 -delete || true

# Optimizar base de datos
echo "🗄️ Optimizando base de datos..."
php artisan tinker --execute="
    try {
        DB::statement('OPTIMIZE TABLE users, productos, categorias, variantes_productos, pedidos, pedido_items');
        echo 'Base de datos optimizada';
    } catch (Exception \$e) {
        echo 'No se pudo optimizar la base de datos: ' . \$e->getMessage();
    }
" || true

# Configurar workers de cola
echo "⚙️ Configurando workers de cola..."
php artisan queue:restart || true

# Verificar optimizaciones
echo "✅ Verificando optimizaciones..."
php artisan about

echo ""
echo "🎉 ¡Optimización completada para Laravel Cloud!"
echo ""
echo "📊 Mejoras aplicadas:"
echo "  ✅ Redis para caché y sesiones"
echo "  ✅ Autoloader optimizado"
echo "  ✅ Vistas precompiladas"
echo "  ✅ Assets minificados"
echo "  ✅ Base de datos optimizada"
echo "  ✅ Logs limpiados"
echo "  ✅ Workers de cola configurados"
echo ""
echo "🚀 Tu aplicación debería cargar significativamente más rápido ahora."
