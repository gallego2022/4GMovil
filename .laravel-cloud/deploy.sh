#!/bin/bash

# Script de despliegue para Laravel Cloud
# Este archivo se ejecuta automáticamente después del build

echo "🚀 Iniciando despliegue automático para Laravel Cloud..."

# Ejecutar el script de aplicación de Redis
if [ -f "apply-redis-config.sh" ]; then
    chmod +x apply-redis-config.sh
    ./apply-redis-config.sh
elif [ -f "restore-redis-config.sh" ]; then
    chmod +x restore-redis-config.sh
    ./restore-redis-config.sh
else
    echo "⚠️ Scripts de Redis no encontrados, continuando..."
fi

# Optimizaciones adicionales para runtime
echo "⚡ Aplicando optimizaciones de runtime..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Despliegue completado!"
