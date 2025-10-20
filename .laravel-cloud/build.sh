#!/bin/bash

# Script de construcción para Laravel Cloud
# Este archivo se ejecuta automáticamente si existe

echo "🚀 Iniciando construcción automática para Laravel Cloud..."

# Ejecutar el script principal de construcción
if [ -f "build-laravel-cloud.sh" ]; then
    chmod +x build-laravel-cloud.sh
    ./build-laravel-cloud.sh
else
    echo "❌ Error: build-laravel-cloud.sh no encontrado"
    exit 1
fi
