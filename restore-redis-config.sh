#!/bin/bash

# Script para restaurar configuración de Redis después del build
# Este script se ejecuta en runtime para asegurar que Redis esté configurado correctamente

echo "🔄 Restaurando configuración de Redis para runtime..."

# Verificar si el archivo .env existe
if [ -f ".env" ]; then
    # Restaurar configuración de Redis
    sed -i 's/CACHE_DRIVER=file/CACHE_DRIVER=redis/' .env
    sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env
    sed -i 's/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=redis/' .env
    
    echo "✅ Configuración de Redis restaurada"
    
    # Limpiar caché para aplicar nueva configuración
    php artisan config:clear
    php artisan cache:clear
    
    echo "✅ Caché limpiado para aplicar nueva configuración"
else
    echo "⚠️ Archivo .env no encontrado"
fi

echo "✅ Restauración de Redis completada!"
