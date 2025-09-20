#!/bin/bash

echo "========================================"
echo "  4GMovil - Sincronización de Cambios"
echo "========================================"
echo

echo "Obteniendo cambios desde GitHub..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "ERROR: No se pudieron obtener los cambios."
    exit 1
fi

echo "Cambios obtenidos correctamente."
echo

# Verificar si está en Docker
if [ -f "docker-compose.yml" ]; then
    echo "Aplicando cambios en Docker..."
    echo
    
    echo "Deteniendo contenedores..."
    docker-compose down
    
    echo "Reconstruyendo y levantando contenedores..."
    docker-compose up --build -d
    
    echo "Esperando que los contenedores estén listos..."
    sleep 10
    
    echo "Ejecutando migraciones..."
    docker exec 4gmovil_app php artisan migrate
    
    echo "Construyendo assets..."
    docker exec 4gmovil_app npm run build
    
    echo
    echo "Sincronización completada en Docker!"
    echo "URLs de acceso:"
    echo "- Aplicación: http://localhost:8000"
    echo "- Admin: http://localhost:8000/admin"
    echo "- phpMyAdmin: http://localhost:8080"
else
    echo "Aplicando cambios en instalación tradicional..."
    echo
    
    echo "Instalando dependencias PHP..."
    composer install
    
    echo "Instalando dependencias JavaScript..."
    npm install
    
    echo "Ejecutando migraciones..."
    php artisan migrate
    
    echo "Construyendo assets..."
    npm run build
    
    echo
    echo "Sincronización completada en instalación tradicional!"
    echo "URLs de acceso:"
    echo "- Aplicación: http://127.0.0.1:8000"
    echo "- Admin: http://127.0.0.1:8000/admin"
    echo
    echo "Para iniciar el servidor: php artisan serve"
fi

echo
echo "========================================"
echo "  ¡Sincronización completada!"
echo "========================================"
echo
