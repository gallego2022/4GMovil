#!/bin/bash

# ========================================
# INSTALACIÓN DOCKER - 4GMovil
# Sistema: Linux/macOS/Windows (WSL)
# Entorno: Docker con Redis
# ========================================

set -e  # Salir si hay errores

echo ""
echo "========================================"
echo "INSTALACIÓN DOCKER - 4GMovil"
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

# [1/12] Verificar Docker
echo "[1/12] Verificando Docker..."
check_command docker
DOCKER_VERSION=$(docker --version)
echo "✓ Docker encontrado ($DOCKER_VERSION)"

# [2/12] Verificar Docker Compose
echo "[2/12] Verificando Docker Compose..."
check_command docker-compose
COMPOSE_VERSION=$(docker-compose --version)
echo "✓ Docker Compose encontrado ($COMPOSE_VERSION)"

# [3/12] Verificar si Docker está ejecutándose
echo "[3/12] Verificando que Docker esté ejecutándose..."
if ! docker info &> /dev/null; then
    echo "ERROR: Docker no está ejecutándose"
    echo "Por favor inicia Docker Desktop o el daemon de Docker"
    exit 1
fi
echo "✓ Docker está ejecutándose"

# [4/12] Crear archivo .env para Docker
echo "[4/12] Configurando archivo de entorno para Docker..."
if [ ! -f .env ]; then
    cp env.docker.example .env
    echo "✓ Archivo .env creado desde env.docker.example"
    echo "✓ Variables de entorno configuradas para Docker"
else
    echo "✓ Archivo .env ya existe"
    echo "ADVERTENCIA: Si necesitas actualizar la configuración, elimina el archivo .env y ejecuta nuevamente"
fi

# [5/12] Crear docker-compose.yml si no existe
echo "[5/12] Configurando Docker Compose..."
if [ ! -f docker-compose.yml ]; then
    cat > docker-compose.yml << 'EOF'
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: 4gmovil-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    ports:
      - "8000:8000"
    depends_on:
      - mysql
      - redis
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=4gmovil
      - DB_USERNAME=root
      - DB_PASSWORD=password
      - CACHE_DRIVER=redis
      - REDIS_HOST=redis
      - REDIS_PORT=6379

  mysql:
    image: mysql:8.0
    container_name: 4gmovil-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: 4gmovil
      MYSQL_ROOT_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    container_name: 4gmovil-redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    command: redis-server --appendonly yes --maxmemory 256mb --maxmemory-policy allkeys-lru

  redis-commander:
    image: rediscommander/redis-commander:latest
    container_name: 4gmovil-redis-commander
    restart: unless-stopped
    ports:
      - "8081:8081"
    environment:
      - REDIS_HOSTS=local:redis:6379
      - HTTP_USER=admin
      - HTTP_PASSWORD=admin
    depends_on:
      - redis

volumes:
  mysql_data:
    driver: local
  redis_data:
    driver: local
EOF
    echo "✓ Docker Compose configurado"
else
    echo "✓ Docker Compose ya existe"
fi

# [6/12] Crear Dockerfile si no existe
echo "[6/12] Configurando Dockerfile..."
if [ ! -f Dockerfile ]; then
    cat > Dockerfile << 'EOF'
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario para la aplicación
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copiar código de la aplicación
COPY . /var/www
COPY --chown=www:www . /var/www

# Cambiar al directorio de trabajo
WORKDIR /var/www

# Cambiar al usuario www
USER www

# Exponer puerto 8000
EXPOSE 8000

# Comando por defecto
CMD php artisan serve --host=0.0.0.0 --port=8000
EOF
    echo "✓ Dockerfile configurado"
else
    echo "✓ Dockerfile ya existe"
fi

# [7/12] Crear directorio docker si no existe
echo "[7/12] Configurando directorio docker..."
mkdir -p docker/php
cat > docker/php/local.ini << 'EOF'
upload_max_filesize=40M
post_max_size=40M
memory_limit=512M
max_execution_time=300
EOF
echo "✓ Configuración PHP creada"

# [8/12] Construir imágenes Docker
echo "[8/12] Construyendo imágenes Docker..."
docker-compose build --no-cache
echo "✓ Imágenes Docker construidas"

# [9/12] Iniciar contenedores
echo "[9/12] Iniciando contenedores..."
docker-compose up -d
echo "✓ Contenedores iniciados"

# [10/12] Esperar a que los servicios estén listos
echo "[10/12] Esperando a que los servicios estén listos..."
sleep 10

# Verificar que los contenedores estén ejecutándose
echo "Verificando contenedores..."
docker-compose ps

# [11/12] Crear directorios necesarios antes de instalar dependencias
echo "[11/12] Creando directorios necesarios..."
docker-compose exec app mkdir -p storage/framework/cache/data
docker-compose exec app mkdir -p storage/framework/sessions
docker-compose exec app mkdir -p storage/framework/views
docker-compose exec app mkdir -p storage/logs
docker-compose exec app mkdir -p bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
echo "✓ Directorios necesarios creados"

# Instalar dependencias dentro del contenedor
echo "Instalando dependencias dentro del contenedor..."
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader
docker-compose exec app npm install --silent
echo "✓ Dependencias instaladas"

# [12/12] Configurar aplicación
echo "[12/12] Configurando aplicación..."
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan cache:table
docker-compose exec app php artisan cache:setup-cloud --driver=redis
docker-compose exec app php artisan queue:table
echo "✓ Aplicación configurada"

# Ejecutar migraciones y seeders
echo "Ejecutando migraciones y seeders..."
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
echo "✓ Base de datos configurada"

# Compilar assets
echo "Compilando assets..."
docker-compose exec app npm run build
echo "✓ Assets compilados"

echo ""
echo "========================================"
echo "INSTALACIÓN DOCKER COMPLETADA"
echo "========================================"
echo ""
echo "Servicios disponibles:"
echo "- Aplicación: http://localhost:8000"
echo "- Redis Commander: http://localhost:8081 (admin/admin)"
echo "- MySQL: localhost:3306 (root/password)"
echo "- Redis: localhost:6379"
echo ""
echo "Comandos útiles:"
echo "- Ver logs: docker-compose logs -f"
echo "- Detener: docker-compose down"
echo "- Reiniciar: docker-compose restart"
echo "- Ejecutar comandos: docker-compose exec app php artisan [comando]"
echo ""
echo "Comandos de caché:"
echo "- docker-compose exec app php artisan cache:clear"
echo "- docker-compose exec app php artisan test:cache-performance-fallback"
echo "- docker-compose exec app php artisan cache:configure-environment"
echo ""
echo "Comandos de cola:"
echo "- Ver worker: docker-compose logs -f queue-worker"
echo "- Probar cola: docker-compose exec app php artisan queue:work --once"
echo "- Limpiar cola: docker-compose exec app php artisan queue:clear"
echo ""