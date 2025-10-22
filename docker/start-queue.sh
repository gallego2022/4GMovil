#!/bin/bash

# ========================================
# SCRIPT DE INICIO PARA WORKERS DE COLA
# ========================================

echo "Iniciando workers de cola..."

# Esperar a que la base de datos esté lista
echo "Esperando a que la base de datos esté lista..."
while ! php artisan migrate:status > /dev/null 2>&1; do
    echo "Esperando conexión a la base de datos..."
    sleep 5
done

echo "Base de datos conectada. Iniciando workers..."

# Limpiar caché antes de iniciar
php artisan cache:clear
php artisan config:clear

# Iniciar workers de cola
echo "Iniciando workers de cola con Redis..."
php artisan queue:work redis \
    --tries=3 \
    --sleep=1 \
    --timeout=90 \
    --memory=512 \
    --verbose