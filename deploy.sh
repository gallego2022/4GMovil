#!/bin/bash

# Script de despliegue para 4GMovil con Docker

echo "🚀 Iniciando despliegue de 4GMovil..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
print_message() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    print_error "Docker no está instalado. Por favor instala Docker primero."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose no está instalado. Por favor instala Docker Compose primero."
    exit 1
fi

# Crear archivo .env si no existe
if [ ! -f .env ]; then
    print_message "Creando archivo .env desde env.docker.example..."
    cp env.docker.example .env
    print_warning "Recuerda configurar las variables de entorno en .env antes de continuar"
fi

# Parar contenedores existentes
print_message "Deteniendo contenedores existentes..."
docker-compose down

# Limpiar imágenes antiguas (opcional)
read -p "¿Deseas limpiar imágenes Docker antiguas? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_message "Limpiando imágenes Docker antiguas..."
    docker system prune -f
fi

# Construir y levantar los servicios
print_message "Construyendo y levantando servicios..."
docker-compose up --build -d

# Verificar que los contenedores estén corriendo
print_message "Verificando estado de los contenedores..."
sleep 10

if docker-compose ps | grep -q "Up"; then
    print_success "¡Despliegue completado exitosamente!"
    echo
    print_message "Servicios disponibles:"
    echo "  🌐 Aplicación: http://localhost:8000"
    echo "  🗄️  phpMyAdmin: http://localhost:8080"
    echo "  📊 Base de datos: localhost:3306"
    echo "  🔴 Redis: localhost:6379"
    echo
    print_message "Para ver los logs: docker-compose logs -f"
    print_message "Para detener: docker-compose down"
else
    print_error "Hubo un problema con el despliegue. Revisa los logs:"
    docker-compose logs
    exit 1
fi
