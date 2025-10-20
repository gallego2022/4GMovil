#!/bin/bash

# Script de despliegue para Laravel Cloud
# Este archivo se ejecuta automáticamente después del build

echo "🚀 Iniciando despliegue automático para Laravel Cloud..."

# Ejecutar el script de restauración de Redis
if [ -f "restore-redis-config.sh" ]; then
    chmod +x restore-redis-config.sh
    ./restore-redis-config.sh
else
    echo "⚠️ restore-redis-config.sh no encontrado, continuando..."
fi

# Optimizaciones adicionales para runtime
echo "⚡ Aplicando optimizaciones de runtime..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Despliegue completado!"
