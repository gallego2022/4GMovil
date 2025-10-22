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
    copy env.docker.example .env
    echo ✓ Archivo .env creado desde env.docker.example
    echo ✓ Variables de entorno configuradas para Docker
) else (
    echo ✓ Archivo .env ya existe
    echo ADVERTENCIA: Si necesitas actualizar la configuración, elimina el archivo .env y ejecuta nuevamente
)

REM Crear docker-compose.yml si no existe
echo [5/12] Configurando Docker Compose...
if not exist docker-compose.yml (
    echo Creando docker-compose.yml...
    (
    echo services:
    echo   app:
    echo     build:
    echo       context: .
    echo       dockerfile: Dockerfile
    echo     container_name: 4gmovil_app
    echo     restart: unless-stopped
    echo     ports:
    echo       - "8000:80"
    echo     volumes:
    echo       - ./storage:/var/www/html/storage
    echo       - ./app:/var/www/html/app
    echo       - ./resources:/var/www/html/resources
    echo       - ./routes:/var/www/html/routes
    echo       - ./database:/var/www/html/database
    echo       - ./public:/var/www/html/public
    echo     environment:
    echo       - APP_ENV=production
    echo       - APP_DEBUG=false
    echo       - DB_HOST=db
    echo       - DB_DATABASE=4gmovil
    echo       - DB_USERNAME=laraveluser
    echo       - DB_PASSWORD=laravelpass
    echo       - REDIS_HOST=redis
    echo       - REDIS_PORT=6379
    echo       - CACHE_DRIVER=redis
    echo       - QUEUE_CONNECTION=redis
    echo     depends_on:
    echo       - db
    echo       - redis
    echo     networks:
    echo       - 4gmovil_network
    echo.
    echo   db:
    echo     image: mysql:8.0
    echo     container_name: 4gmovil_db
    echo     restart: unless-stopped
    echo     environment:
    echo       MYSQL_ROOT_PASSWORD: rootpassword
    echo       MYSQL_DATABASE: 4gmovil
    echo       MYSQL_USER: laraveluser
    echo       MYSQL_PASSWORD: laravelpass
    echo     ports:
    echo       - "3307:3306"
    echo     volumes:
    echo       - db_data:/var/lib/mysql
    echo     networks:
    echo       - 4gmovil_network
    echo.
    echo   phpmyadmin:
    echo     image: phpmyadmin/phpmyadmin:latest
    echo     container_name: 4gmovil_phpmyadmin
    echo     restart: unless-stopped
    echo     environment:
    echo       PMA_HOST: db
    echo       PMA_USER: laraveluser
    echo       PMA_PASSWORD: laravelpass
    echo       MYSQL_ROOT_PASSWORD: rootpassword
    echo     ports:
    echo       - "8080:80"
    echo     depends_on:
    echo       - db
    echo     networks:
    echo       - 4gmovil_network
    echo.
    echo   redis:
    echo     image: redis:7-alpine
    echo     container_name: 4gmovil_redis
    echo     restart: unless-stopped
    echo     ports:
    echo       - "6379:6379"
    echo     volumes:
    echo       - redis_data:/data
    echo     networks:
    echo       - 4gmovil_network
    echo.
    echo   queue:
    echo     build:
    echo       context: .
    echo       dockerfile: Dockerfile
    echo     container_name: 4gmovil_queue
    echo     restart: unless-stopped
    echo     depends_on:
    echo       - app
    echo       - db
    echo       - redis
    echo     command: bash -lc "chmod +x /var/www/html/docker/start-queue.sh && /var/www/html/docker/start-queue.sh"
    echo     volumes:
    echo       - ./storage:/var/www/html/storage
    echo       - ./app:/var/www/html/app
    echo       - ./resources:/var/www/html/resources
    echo       - ./routes:/var/www/html/routes
    echo       - ./database:/var/www/html/database
    echo       - ./public:/var/www/html/public
    echo     environment:
    echo       - APP_ENV=production
    echo       - APP_DEBUG=false
    echo       - DB_HOST=db
    echo       - DB_DATABASE=4gmovil
    echo       - DB_USERNAME=laraveluser
    echo       - DB_PASSWORD=laravelpass
    echo       - REDIS_HOST=redis
    echo       - REDIS_PORT=6379
    echo       - QUEUE_CONNECTION=redis
    echo     networks:
    echo       - 4gmovil_network
    echo.
    echo   queue-worker:
    echo     build:
    echo       context: .
    echo       dockerfile: Dockerfile
    echo     container_name: 4gmovil_queue_worker
    echo     restart: unless-stopped
    echo     depends_on:
    echo       - app
    echo       - db
    echo       - redis
    echo     command: bash -lc "chmod +x /var/www/html/docker/start-queue.sh && /var/www/html/docker/start-queue.sh"
    echo     volumes:
    echo       - ./storage:/var/www/html/storage
    echo       - ./app:/var/www/html/app
    echo       - ./resources:/var/www/html/resources
    echo       - ./routes:/var/www/html/routes
    echo       - ./database:/var/www/html/database
    echo       - ./public:/var/www/html/public
    echo     environment:
    echo       - APP_ENV=production
    echo       - APP_DEBUG=false
    echo       - DB_HOST=db
    echo       - DB_DATABASE=4gmovil
    echo       - DB_USERNAME=laraveluser
    echo       - DB_PASSWORD=laravelpass
    echo       - REDIS_HOST=redis
    echo       - REDIS_PORT=6379
    echo       - QUEUE_CONNECTION=redis
    echo     networks:
    echo       - 4gmovil_network
    echo.
    echo volumes:
    echo   db_data:
    echo   redis_data:
    echo.
    echo networks:
    echo   4gmovil_network:
    echo     driver: bridge
    ) > docker-compose.yml
    echo ✓ Docker Compose configurado con Redis y Workers
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

REM Crear directorios necesarios antes de instalar dependencias
echo [11/12] Creando directorios necesarios...
docker-compose exec app mkdir -p storage/framework/cache/data
docker-compose exec app mkdir -p storage/framework/sessions
docker-compose exec app mkdir -p storage/framework/views
docker-compose exec app mkdir -p storage/logs
docker-compose exec app mkdir -p bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
echo ✓ Directorios necesarios creados

REM Instalar dependencias dentro del contenedor
echo Instalando dependencias dentro del contenedor...
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
docker-compose exec app php artisan queue:table
echo ✓ Aplicación configurada

REM Verificar conexión a Redis
echo Verificando conexión a Redis...
docker-compose exec redis redis-cli ping
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Redis no está respondiendo correctamente
    echo Verifica que el contenedor de Redis esté ejecutándose
) else (
    echo ✓ Redis está funcionando correctamente
)

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
echo Comandos de cola:
echo - Ver worker: docker-compose logs -f queue-worker
echo - Probar cola: docker-compose exec app php artisan queue:work --once
echo - Limpiar cola: docker-compose exec app php artisan queue:clear
echo.
echo Comandos de Redis:
echo - Conectar a Redis: docker-compose exec redis redis-cli
echo - Ver estado de Redis: docker-compose exec redis redis-cli ping
echo - Limpiar Redis: docker-compose exec redis redis-cli flushall
echo - Ver claves: docker-compose exec redis redis-cli keys "*"
echo - Ver configuración Redis: docker-compose exec redis redis-cli config get "*"
echo.
echo Verificación de configuración:
echo - Probar caché: docker-compose exec app php artisan tinker
echo   Luego ejecuta: Cache::put('test', 'Redis funciona', 60)
echo   Y verifica: Cache::get('test')
echo.
pause