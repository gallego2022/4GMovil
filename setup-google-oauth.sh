#!/bin/bash

echo "========================================"
echo "    CONFIGURACION DE GOOGLE OAUTH"
echo "========================================"
echo

echo "1. Ve a https://console.developers.google.com/"
echo "2. Crea o selecciona un proyecto"
echo "3. Habilita Google+ API y Google OAuth2 API"
echo "4. Ve a 'Credenciales' > 'Crear credenciales' > 'ID de cliente OAuth 2.0'"
echo "5. Configura las URIs de redirección:"
echo "   - http://localhost:8000/auth/callback/google"
echo
echo "Presiona Enter cuando hayas completado estos pasos..."
read

echo
echo "Ahora necesitamos actualizar las variables de entorno."
echo

read -p "Ingresa tu GOOGLE_CLIENT_ID: " CLIENT_ID
read -p "Ingresa tu GOOGLE_CLIENT_SECRET: " CLIENT_SECRET

echo
echo "Actualizando archivo .env..."

# Actualizar el archivo .env del contenedor
docker-compose exec app bash -c "sed -i 's/GOOGLE_CLIENT_ID=.*/GOOGLE_CLIENT_ID=$CLIENT_ID/g' .env"
docker-compose exec app bash -c "sed -i 's/GOOGLE_CLIENT_SECRET=.*/GOOGLE_CLIENT_SECRET=$CLIENT_SECRET/g' .env"

echo
echo "Limpiando caché de configuración..."
docker-compose exec app php artisan config:clear

echo
echo "Verificando configuración..."
docker-compose exec app php artisan google:check

echo
echo "========================================"
echo "    CONFIGURACION COMPLETADA"
echo "========================================"
echo
echo "Ahora puedes probar el login con Google en:"
echo "http://localhost:8000/login"
echo




