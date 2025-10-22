#!/bin/bash

# ========================================
# INSTALACIÓN LOCAL - 4GMovil
# Sistema: Linux/macOS
# Entorno: Desarrollo Local
# ========================================

set -e  # Salir si hay errores

echo ""
echo "========================================"
echo "INSTALACIÓN LOCAL - 4GMovil"
echo "========================================"
echo ""

# Función para verificar comandos
check_command() {
    if ! command -v $1 &> /dev/null; then
        echo "ERROR: $1 no está instalado"
        echo "Por favor instala $1 antes de continuar"
        exit 1
    fi
}

# [1/10] Verificar PHP
echo "[1/10] Verificando PHP..."
check_command php
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP encontrado (versión: $PHP_VERSION)"

# Verificar versión mínima de PHP
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
if [ "$PHP_MAJOR" -lt 8 ]; then
    echo "ERROR: Se requiere PHP 8.0 o superior"
    echo "Versión actual: $PHP_VERSION"
    exit 1
fi

# [2/10] Verificar Composer
echo "[2/10] Verificando Composer..."
check_command composer
echo "✓ Composer encontrado"

# [3/10] Verificar Node.js (opcional)
echo "[3/10] Verificando Node.js..."
if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version)
    echo "✓ Node.js encontrado (versión: $NODE_VERSION)"
    SKIP_NODE=0
else
    echo "ADVERTENCIA: Node.js no está instalado"
    echo "Para compilar assets, instala Node.js desde: https://nodejs.org/"
    SKIP_NODE=1
fi

# [4/10] Crear directorios necesarios antes de instalar dependencias
echo "[4/10] Creando directorios necesarios..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo "✓ Directorios necesarios creados"

# Instalar dependencias PHP
echo "Instalando dependencias PHP..."
composer install --no-interaction --prefer-dist --optimize-autoloader
echo "✓ Dependencias PHP instaladas"

# [5/10] Instalar dependencias Node.js (si está disponible)
if [ $SKIP_NODE -eq 0 ]; then
    echo "[5/10] Instalando dependencias Node.js..."
    npm install --silent
    echo "✓ Dependencias Node.js instaladas"
else
    echo "[5/10] Saltando instalación de Node.js..."
fi

# [6/10] Configurar archivo de entorno
echo "[6/10] Configurando archivo de entorno..."
if [ ! -f .env ]; then
    cp env.local.example .env
    echo "✓ Archivo .env creado desde env.local.example"
else
    echo "✓ Archivo .env ya existe"
fi

# [7/10] Configurar variables de entorno
echo "[7/10] Configurando variables de entorno..."
echo ""
echo "Configurando para desarrollo local..."
echo "CACHE_DRIVER=file"
echo "CACHE_PREFIX=4gmovil_cache_"
echo "APP_ENV=local"
echo ""

# [8/10] Generar clave de aplicación
echo "[8/10] Generando clave de aplicación..."
php artisan key:generate --force
echo "✓ Clave de aplicación generada"

# [9/10] Configurar permisos
echo "[9/10] Configurando permisos..."
# Crear directorios si no existen
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Configurar permisos
chmod -R 775 storage bootstrap/cache
echo "✓ Permisos configurados"

# [10/10] Configurar caché
echo "[10/10] Configurando sistema de caché..."
php artisan cache:table
php artisan cache:setup-cloud --driver=file
echo "✓ Sistema de caché configurado"

# Compilar assets (si Node.js está disponible)
if [ $SKIP_NODE -eq 0 ]; then
    echo ""
    echo "Compilando assets..."
    npm run build
    if [ $? -eq 0 ]; then
        echo "✓ Assets compilados"
    else
        echo "ADVERTENCIA: Falló la compilación de assets"
        echo "Puedes compilar manualmente con: npm run dev"
    fi
fi

echo ""
echo "========================================"
echo "INSTALACIÓN COMPLETADA"
echo "========================================"
echo ""
echo "Próximos pasos:"
echo "1. Configura tu base de datos en .env"
echo "2. Ejecuta: php artisan migrate"
echo "3. Ejecuta: php artisan db:seed"
echo "4. Inicia el servidor: php artisan serve"
echo "5. Accede a: http://localhost:8000"
echo ""
echo "Comandos útiles:"
echo "- php artisan cache:clear"
echo "- php artisan test:cache-performance-fallback"
echo "- php artisan cache:configure-environment"
echo ""
