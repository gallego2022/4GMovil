#!/bin/bash

# Script para generar clave de aplicación en Laravel Cloud
# Este script se ejecuta durante el build para generar una clave válida

set -e

echo "🔑 Generando clave de aplicación para Laravel Cloud..."

# Verificar que el archivo .env existe
if [ ! -f ".env" ]; then
    echo "❌ Error: El archivo .env no existe"
    exit 1
fi

# Generar clave de aplicación
echo "📝 Generando nueva clave de aplicación..."
php artisan key:generate --force

# Verificar que la clave se generó correctamente
if grep -q "APP_KEY=base64:" .env; then
    echo "✅ Clave de aplicación generada correctamente"
else
    echo "❌ Error: No se pudo generar la clave de aplicación"
    exit 1
fi

echo "🎉 Clave de aplicación configurada exitosamente"
