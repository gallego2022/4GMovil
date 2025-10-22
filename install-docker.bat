@echo off
REM ========================================
REM INSTALACIÓN DOCKER - 4GMovil
REM Sistema: Windows
REM Entorno: Docker con Redis
REM ========================================

echo.
echo ========================================
echo INSTALACIÓN DOCKER - 4GMovil
echo ========================================
echo.

REM Verificar si Docker está instalado
echo [1/12] Verificando Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no está instalado
    echo Por favor instala Docker Desktop desde: https://docs.docker.com/desktop/windows/install/
    pause
    exit /b 1
)
echo ✓ Docker encontrado

REM Verificar si Docker Compose está instalado
echo [2/12] Verificando Docker Compose...
docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker Compose no está instalado
    echo Por favor instala Docker Compose desde: https://docs.docker.com/compose/install/
    pause
    exit /b 1
)
echo ✓ Docker Compose encontrado

REM Verificar si Docker está ejecutándose
echo [3/12] Verificando que Docker esté ejecutándose...
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no está ejecutándose
    echo Por favor inicia Docker Desktop
    pause
    exit /b 1
)
echo ✓ Docker está ejecutándose

REM Crear archivo .env para Docker
echo [4/12] Configurando archivo de entorno para Docker...
if not exist .env (
    copy .env.example .env
    echo ✓ Archivo .env creado
) else (
    echo ✓ Archivo .env ya existe
)

REM Configurar variables específicas para Docker
echo Configurando variables para Docker...
echo. >> .env
echo # Configuración específica para Docker >> .env
echo DB_CONNECTION=mysql >> .env
echo DB_HOST=mysql >> .env
echo DB_PORT=3306 >> .env
echo DB_DATABASE=4gmovil >> .env
echo DB_USERNAME=root >> .env
echo DB_PASSWORD=password >> .env
echo. >> .env
echo CACHE_DRIVER=redis >> .env
echo REDIS_HOST=redis >> .env
echo REDIS_PORT=6379 >> .env
echo REDIS_PASSWORD= >> .env
echo. >> .env
echo APP_ENV=local >> .env
echo APP_DEBUG=true >> .env
echo ✓ Variables de entorno configuradas para Docker

REM Crear docker-compose.yml si no existe
echo [5/12] Configurando Docker Compose...
if not exist docker-compose.yml (
    echo Creando docker-compose.yml...
    (
    echo version: '3.8'
    echo.
    echo services:
    echo   app:
    echo     build:
    echo       context: .
    echo       dockerfile: Dockerfile
    echo     container_name: 4gmovil-app
    echo     restart: unless-stopped
    echo     working_dir: /var/www
    echo     volumes:
    echo       - ./:/var/www
    echo       - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    echo     ports:
    echo       - "8000:8000"
    echo     depends_on:
    echo       - mysql
    echo       - redis
    echo     environment:
    echo       - DB_CONNECTION=mysql
    echo       - DB_HOST=mysql
    echo       - DB_PORT=3306
    echo       - DB_DATABASE=4gmovil
    echo       - DB_USERNAME=root
    echo       - DB_PASSWORD=password
    echo       - CACHE_DRIVER=redis
    echo       - REDIS_HOST=redis
    echo       - REDIS_PORT=6379
    echo.
    echo   mysql:
    echo     image: mysql:8.0
    echo     container_name: 4gmovil-mysql
    echo     restart: unless-stopped
    echo     environment:
    echo       MYSQL_DATABASE: 4gmovil
    echo       MYSQL_ROOT_PASSWORD: password
    echo     ports:
    echo       - "3306:3306"
    echo     volumes:
    echo       - mysql_data:/var/lib/mysql
    echo.
    echo   redis:
    echo     image: redis:7-alpine
    echo     container_name: 4gmovil-redis
    echo     restart: unless-stopped
    echo     ports:
    echo       - "6379:6379"
    echo     volumes:
    echo       - redis_data:/data
    echo     command: redis-server --appendonly yes --maxmemory 256mb --maxmemory-policy allkeys-lru
    echo.
    echo   redis-commander:
    echo     image: rediscommander/redis-commander:latest
    echo     container_name: 4gmovil-redis-commander
    echo     restart: unless-stopped
    echo     ports:
    echo       - "8081:8081"
    echo     environment:
    echo       - REDIS_HOSTS=local:redis:6379
    echo       - HTTP_USER=admin
    echo       - HTTP_PASSWORD=admin
    echo     depends_on:
    echo       - redis
    echo.
    echo volumes:
    echo   mysql_data:
    echo     driver: local
    echo   redis_data:
    echo     driver: local
    ) > docker-compose.yml
    echo ✓ Docker Compose configurado
) else (
    echo ✓ Docker Compose ya existe
)

REM Crear Dockerfile si no existe
echo [6/12] Configurando Dockerfile...
if not exist Dockerfile (
    echo Creando Dockerfile...
    (
    echo FROM php:8.2-fpm
    echo.
    echo # Instalar dependencias del sistema
    echo RUN apt-get update ^&^& apt-get install -y \
    echo     git \
    echo     curl \
    echo     libpng-dev \
    echo     libonig-dev \
    echo     libxml2-dev \
    echo     zip \
    echo     unzip \
    echo     nodejs \
    echo     npm
    echo.
    echo # Limpiar caché
    echo RUN apt-get clean ^&^& rm -rf /var/lib/apt/lists/*
    echo.
    echo # Instalar extensiones de PHP
    echo RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
    echo.
    echo # Instalar Composer
    echo COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
    echo.
    echo # Crear usuario para la aplicación
    echo RUN groupadd -g 1000 www
    echo RUN useradd -u 1000 -ms /bin/bash -g www www
    echo.
    echo # Copiar código de la aplicación
    echo COPY . /var/www
    echo COPY --chown=www:www . /var/www
    echo.
    echo # Cambiar al directorio de trabajo
    echo WORKDIR /var/www
    echo.
    echo # Cambiar al usuario www
    echo USER www
    echo.
    echo # Exponer puerto 8000
    echo EXPOSE 8000
    echo.
    echo # Comando por defecto
    echo CMD php artisan serve --host=0.0.0.0 --port=8000
    ) > Dockerfile
    echo ✓ Dockerfile configurado
) else (
    echo ✓ Dockerfile ya existe
)

REM Crear directorio docker si no existe
echo [7/12] Configurando directorio docker...
if not exist docker\php mkdir docker\php
echo upload_max_filesize=40M > docker\php\local.ini
echo post_max_size=40M >> docker\php\local.ini
echo memory_limit=512M >> docker\php\local.ini
echo max_execution_time=300 >> docker\php\local.ini
echo ✓ Configuración PHP creada

REM Construir imágenes Docker
echo [8/12] Construyendo imágenes Docker...
docker-compose build --no-cache
if %errorlevel% neq 0 (
    echo ERROR: Falló la construcción de imágenes Docker
    pause
    exit /b 1
)
echo ✓ Imágenes Docker construidas

REM Iniciar contenedores
echo [9/12] Iniciando contenedores...
docker-compose up -d
if %errorlevel% neq 0 (
    echo ERROR: Falló el inicio de contenedores
    pause
    exit /b 1
)
echo ✓ Contenedores iniciados

REM Esperar a que los servicios estén listos
echo [10/12] Esperando a que los servicios estén listos...
timeout /t 10 /nobreak >nul

REM Verificar que los contenedores estén ejecutándose
echo Verificando contenedores...
docker-compose ps

REM Instalar dependencias dentro del contenedor
echo [11/12] Instalando dependencias dentro del contenedor...
docker-compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo ERROR: Falló la instalación de dependencias PHP
    pause
    exit /b 1
)
echo ✓ Dependencias PHP instaladas

docker-compose exec app npm install --silent
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Falló la instalación de dependencias Node.js
    echo Continuando...
) else (
    echo ✓ Dependencias Node.js instaladas
)

REM Configurar aplicación
echo [12/12] Configurando aplicación...
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan cache:table
docker-compose exec app php artisan cache:setup-cloud --driver=redis
echo ✓ Aplicación configurada

REM Ejecutar migraciones y seeders
echo Ejecutando migraciones y seeders...
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
echo ✓ Base de datos configurada

REM Compilar assets
echo Compilando assets...
docker-compose exec app npm run build
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Falló la compilación de assets
    echo Puedes compilar manualmente con: docker-compose exec app npm run dev
) else (
    echo ✓ Assets compilados
)

echo.
echo ========================================
echo INSTALACIÓN DOCKER COMPLETADA
echo ========================================
echo.
echo Servicios disponibles:
echo - Aplicación: http://localhost:8000
echo - Redis Commander: http://localhost:8081 (admin/admin)
echo - MySQL: localhost:3306 (root/password)
echo - Redis: localhost:6379
echo.
echo Comandos útiles:
echo - Ver logs: docker-compose logs -f
echo - Detener: docker-compose down
echo - Reiniciar: docker-compose restart
echo - Ejecutar comandos: docker-compose exec app php artisan [comando]
echo.
echo Comandos de caché:
echo - docker-compose exec app php artisan cache:clear
echo - docker-compose exec app php artisan test:cache-performance-fallback
echo - docker-compose exec app php artisan cache:configure-environment
echo.
pause