#!/bin/bash

echo "========================================"
echo "  4GMovil - Instalación Docker"
echo "========================================"
echo

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker no está instalado."
    echo "Por favor instala Docker desde: https://www.docker.com/products/docker-desktop"
    exit 1
fi

echo "Docker encontrado. Continuando..."
echo

# Verificar si Git está instalado
if ! command -v git &> /dev/null; then
    echo "ERROR: Git no está instalado."
    echo "Por favor instala Git desde: https://git-scm.com/downloads"
    exit 1
fi

echo "Git encontrado. Continuando..."
echo

# Verificar si el proyecto ya existe
if [ -d "4gmovil" ]; then
    echo "El directorio 4gmovil ya existe."
    read -p "¿Deseas continuar y actualizar el proyecto? (s/n): " choice
    if [[ ! $choice =~ ^[Ss]$ ]]; then
        echo "Instalación cancelada."
        exit 0
    fi
    echo "Actualizando proyecto..."
    cd 4gmovil
    git pull origin main
else
    echo "Clonando proyecto desde GitHub..."
    git clone https://github.com/tu-usuario/4gmovil.git
    cd 4gmovil
fi

echo
echo "Configurando variables de entorno..."
if [ ! -f ".env" ]; then
    cp env.docker.example .env
    echo "Archivo .env creado desde env.docker.example"
else
    echo "El archivo .env ya existe."
fi

echo
echo "Construyendo y levantando contenedores Docker..."
docker compose up --build -d

echo
echo "Esperando que los contenedores estén listos..."
sleep 10

echo
echo "Verificando estado de contenedores..."
docker compose ps

echo
echo "========================================"
echo "  ¡Instalación completada!"
echo "========================================"
echo
echo "URLs de acceso:"
echo "- Aplicación: http://localhost:8000"
echo "- Admin Panel: http://localhost:8000/admin"
echo "- phpMyAdmin: http://localhost:8080"
echo
echo "Credenciales por defecto:"
echo "- Admin: 4gmoviltest@gmail.com / Admin123!"
echo "- Base de datos: laraveluser / laravelpass"
echo
echo "Comandos útiles:"
echo "- Ver logs: docker compose logs -f"
echo "- Detener: docker compose down"
echo "- Reiniciar: docker compose restart"
echo
echo "IMPORTANTE: Configura tus credenciales de Google OAuth y Stripe en el archivo .env"
echo
