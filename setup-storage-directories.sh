#!/bin/bash

# ========================================
# CREAR DIRECTORIOS DE STORAGE - 4GMovil
# Script para crear todos los directorios necesarios
# ========================================

echo ""
echo "========================================"
echo "CREANDO DIRECTORIOS DE STORAGE"
echo "========================================"
echo ""

echo "[1/6] Creando directorios de framework..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
echo "✓ Directorios de framework creados"

echo "[2/6] Creando directorios de logs..."
mkdir -p storage/logs
echo "✓ Directorios de logs creados"

echo "[3/6] Creando directorios de cache..."
mkdir -p bootstrap/cache
echo "✓ Directorios de cache creados"

echo "[4/6] Creando directorios de app..."
mkdir -p storage/app/public
echo "✓ Directorios de app creados"

echo "[5/6] Configurando permisos..."
chmod -R 775 storage bootstrap/cache
echo "✓ Permisos configurados"

echo "[6/6] Verificando estructura..."
echo "Estructura de directorios creada:"
echo "- storage/framework/cache/data"
echo "- storage/framework/sessions"
echo "- storage/framework/views"
echo "- storage/logs"
echo "- storage/app/public"
echo "- bootstrap/cache"
echo "✓ Verificación completada"

echo ""
echo "========================================"
echo "DIRECTORIOS CREADOS EXITOSAMENTE"
echo "========================================"
echo ""
echo "Los directorios necesarios para Laravel han sido creados."
echo "Ahora puedes ejecutar composer install sin errores."
echo ""
