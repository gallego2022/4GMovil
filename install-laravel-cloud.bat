@echo off
REM ========================================
REM INSTALACIÓN LARAVEL CLOUD - 4GMovil
REM Sistema: Windows
REM Entorno: Producción
REM ========================================

echo.
echo ========================================
echo INSTALACIÓN LARAVEL CLOUD - 4GMovil
echo ========================================
echo.

REM Verificar si Laravel Cloud CLI está instalado
echo [1/15] Verificando Laravel Cloud CLI...
laravel-cloud --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Laravel Cloud CLI no está instalado
    echo Por favor instala Laravel Cloud CLI desde: https://laravel.com/docs/cloud
    pause
    exit /b 1
)
echo ✓ Laravel Cloud CLI encontrado

REM Verificar autenticación
echo [2/15] Verificando autenticación...
laravel-cloud auth:check >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: No estás autenticado en Laravel Cloud
    echo Ejecuta: laravel-cloud auth:login
    pause
    exit /b 1
)
echo ✓ Autenticado en Laravel Cloud

REM Crear archivo .env para producción
echo [3/15] Configurando archivo de entorno para producción...
if not exist .env.production (
    copy .env.example .env.production
    echo ✓ Archivo .env.production creado
) else (
    echo ✓ Archivo .env.production ya existe
)

REM Configurar variables específicas para Laravel Cloud
echo Configurando variables para Laravel Cloud...
(
echo APP_NAME="4GMovil"
echo APP_ENV=production
echo APP_KEY=
echo APP_DEBUG=false
echo APP_URL=https://tu-proyecto.laravel-cloud.com
echo.
echo LOG_CHANNEL=stack
echo LOG_DEPRECATIONS_CHANNEL=null
echo LOG_LEVEL=error
echo.
echo # Base de datos (configurar en Laravel Cloud)
echo DB_CONNECTION=mysql
echo DB_HOST=mysql
echo DB_PORT=3306
echo DB_DATABASE=4gmovil
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo # Caché optimizado para Laravel Cloud
echo CACHE_DRIVER=database
echo CACHE_PREFIX=4gmovil_cache_
echo.
echo # Sesiones
echo SESSION_DRIVER=database
echo SESSION_LIFETIME=120
echo.
echo # Cola de trabajos
echo QUEUE_CONNECTION=database
echo.
echo # Mail (configurar en Laravel Cloud)
echo MAIL_MAILER=smtp
echo MAIL_HOST=
echo MAIL_PORT=587
echo MAIL_USERNAME=
echo MAIL_PASSWORD=
echo MAIL_ENCRYPTION=tls
echo MAIL_FROM_ADDRESS=noreply@4gmovil.com
echo MAIL_FROM_NAME="4GMovil"
echo.
echo # Stripe (configurar en Laravel Cloud)
echo STRIPE_KEY=
echo STRIPE_SECRET=
echo STRIPE_WEBHOOK_SECRET=
echo.
echo # Google OAuth (configurar en Laravel Cloud)
echo GOOGLE_CLIENT_ID=
echo GOOGLE_CLIENT_SECRET=
echo GOOGLE_REDIRECT_URI=
echo.
echo # Configuración de caché específica
echo CACHE_TTL=3600
echo CACHE_CLEAR_ON_DEPLOY=true
) > .env.production
echo ✓ Variables de entorno configuradas para Laravel Cloud

REM Crear proyecto en Laravel Cloud
echo [4/15] Creando proyecto en Laravel Cloud...
set /p PROJECT_NAME="¿Nombre del proyecto en Laravel Cloud? (4gmovil): "
if "%PROJECT_NAME%"=="" set PROJECT_NAME=4gmovil

laravel-cloud project:list | findstr "%PROJECT_NAME%" >nul
if %errorlevel% neq 0 (
    laravel-cloud project:create "%PROJECT_NAME%"
    echo ✓ Proyecto '%PROJECT_NAME%' creado en Laravel Cloud
) else (
    echo ✓ Proyecto '%PROJECT_NAME%' ya existe en Laravel Cloud
)

REM Configurar base de datos
echo [5/15] Configurando base de datos...
laravel-cloud db:create "%PROJECT_NAME%"
echo ✓ Base de datos configurada

REM Configurar variables de entorno en Laravel Cloud
echo [6/15] Configurando variables de entorno en Laravel Cloud...
echo.
echo IMPORTANTE: Configura manualmente las siguientes variables en el panel de Laravel Cloud:
echo.
echo Variables obligatorias:
echo - APP_KEY: (se generará automáticamente)
echo - DB_PASSWORD: (contraseña de la base de datos)
echo - STRIPE_KEY: (clave pública de Stripe)
echo - STRIPE_SECRET: (clave secreta de Stripe)
echo - GOOGLE_CLIENT_ID: (ID del cliente de Google OAuth)
echo - GOOGLE_CLIENT_SECRET: (secreto del cliente de Google OAuth)
echo.
echo Variables opcionales:
echo - MAIL_HOST: (servidor SMTP)
echo - MAIL_USERNAME: (usuario SMTP)
echo - MAIL_PASSWORD: (contraseña SMTP)
echo.

REM Preparar archivos para deploy
echo [7/15] Preparando archivos para deploy...

REM Crear .gitignore específico para producción
(
echo # Archivos de desarrollo
echo .env
echo .env.local
echo .env.docker
echo .env.testing
echo.
echo # Archivos de caché
echo storage/framework/cache/*
echo storage/framework/sessions/*
echo storage/framework/views/*
echo bootstrap/cache/*
echo.
echo # Logs
echo storage/logs/*
echo.
echo # Archivos de usuario
echo storage/app/public/*
echo public/storage
echo.
echo # Archivos de Node.js
echo node_modules/
echo npm-debug.log
echo yarn-error.log
echo.
echo # Archivos de IDE
echo .vscode/
echo .idea/
echo *.swp
echo *.swo
echo.
echo # Archivos del sistema
echo .DS_Store
echo Thumbs.db
) > .gitignore.production

REM Optimizar para producción
echo [8/15] Optimizando para producción...

REM Instalar dependencias de producción
composer install --no-dev --optimize-autoloader --no-interaction
if %errorlevel% neq 0 (
    echo ERROR: Falló la instalación de dependencias de producción
    pause
    exit /b 1
)
echo ✓ Dependencias de producción instaladas

REM Compilar assets
node --version >nul 2>&1
if %errorlevel% equ 0 (
    npm install --production --silent
    npm run build
    echo ✓ Assets compilados para producción
) else (
    echo ADVERTENCIA: Node.js no está disponible, saltando compilación de assets
)

REM Crear archivo de configuración específico para Laravel Cloud
echo [9/15] Creando configuración específica para Laravel Cloud...
(
echo ^<?php
echo.
echo use Illuminate\Support\Str;
echo.
echo return [
echo     'default' =^> env('CACHE_DRIVER', 'database'),
echo     'stores' =^> [
echo         'database' =^> [
echo             'driver' =^> 'database',
echo             'table' =^> 'cache',
echo             'connection' =^> null,
echo             'lock_connection' =^> null,
echo         ],
echo         'file' =^> [
echo             'driver' =^> 'file',
echo             'path' =^> storage_path('framework/cache/data'),
echo             'lock_path' =^> storage_path('framework/cache/data'),
echo         ],
echo     ],
echo     'prefix' =^> env('CACHE_PREFIX', '4gmovil_cache_'),
echo ];
) > config\cache-cloud.php
echo ✓ Configuración de caché específica creada

REM Crear script de post-deploy
echo [10/15] Creando script de post-deploy...
(
echo @echo off
echo REM Script de post-deploy para Laravel Cloud
echo echo Ejecutando post-deploy...
echo.
echo REM Limpiar caché
echo php artisan cache:clear
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo.
echo REM Optimizar para producción
echo php artisan config:cache
echo php artisan route:cache
echo php artisan view:cache
echo.
echo REM Configurar caché
echo php artisan cache:table
echo php artisan cache:setup-cloud --driver=database
echo.
echo REM Ejecutar migraciones
echo php artisan migrate --force
echo.
echo REM Ejecutar seeders
echo php artisan db:seed --force
echo.
echo REM Configurar storage
echo php artisan storage:link
echo.
echo echo Post-deploy completado
) > laravel-cloud-deploy.bat
echo ✓ Script de post-deploy creado

REM Crear archivo de configuración de Laravel Cloud
echo [11/15] Creando configuración de Laravel Cloud...
(
echo name: 4gmovil
echo services:
echo   - name: app
echo     build:
echo       context: .
echo       dockerfile: Dockerfile.cloud
echo     environment:
echo       - APP_ENV=production
echo       - CACHE_DRIVER=database
echo     deploy:
echo       replicas: 1
echo       resources:
echo         limits:
echo           memory: 512Mi
echo         requests:
echo           memory: 256Mi
echo     healthcheck:
echo       test: ["CMD", "php", "artisan", "health:check"]
echo       interval: 30s
echo       timeout: 10s
echo       retries: 3
echo       start_period: 40s
) > laravel-cloud.yml
echo ✓ Configuración de Laravel Cloud creada

REM Crear Dockerfile específico para Laravel Cloud
echo [12/15] Creando Dockerfile para Laravel Cloud...
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
echo     npm \
echo     supervisor
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
echo # Crear directorio de trabajo
echo WORKDIR /var/www
echo.
echo # Copiar archivos de la aplicación
echo COPY . /var/www
echo.
echo # Instalar dependencias
echo RUN composer install --no-dev --optimize-autoloader --no-interaction
echo RUN npm install --production --silent
echo RUN npm run build
echo.
echo # Configurar permisos
echo RUN chown -R www-data:www-data /var/www
echo RUN chmod -R 755 /var/www
echo.
echo # Configurar supervisor
echo COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
echo.
echo # Exponer puerto
echo EXPOSE 8000
echo.
echo # Comando por defecto
echo CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
) > Dockerfile.cloud
echo ✓ Dockerfile para Laravel Cloud creado

REM Crear configuración de supervisor
echo [13/15] Creando configuración de supervisor...
if not exist docker\supervisor mkdir docker\supervisor
(
echo [supervisord]
echo nodaemon=true
echo user=root
echo logfile=/var/log/supervisor/supervisord.log
echo pidfile=/var/run/supervisord.pid
echo.
echo [program:php-fpm]
echo command=php-fpm
echo autostart=true
echo autorestart=true
echo stderr_logfile=/var/log/supervisor/php-fpm.err.log
echo stdout_logfile=/var/log/supervisor/php-fpm.out.log
echo.
echo [program:laravel-worker]
echo command=php artisan queue:work --sleep=3 --tries=3 --max-time=3600
echo directory=/var/www
echo autostart=true
echo autorestart=true
echo stderr_logfile=/var/log/supervisor/laravel-worker.err.log
echo stdout_logfile=/var/log/supervisor/laravel-worker.out.log
echo user=www-data
echo numprocs=2
) > docker\supervisor\supervisord.conf
echo ✓ Configuración de supervisor creada

REM Crear script de monitoreo
echo [14/15] Creando script de monitoreo...
(
echo @echo off
echo REM Script de monitoreo para Laravel Cloud
echo echo Monitoreando aplicación en Laravel Cloud...
echo.
echo REM Verificar estado de la aplicación
echo echo Estado de la aplicación:
echo laravel-cloud app:status
echo.
echo REM Verificar logs
echo echo Últimos logs:
echo laravel-cloud logs --tail=50
echo.
echo REM Verificar caché
echo echo Estado del caché:
echo laravel-cloud artisan cache:configure-environment
echo.
echo REM Verificar base de datos
echo echo Estado de la base de datos:
echo laravel-cloud artisan migrate:status
echo.
echo echo Monitoreo completado
) > monitor-laravel-cloud.bat
echo ✓ Script de monitoreo creado

REM Deploy a Laravel Cloud
echo [15/15] Desplegando a Laravel Cloud...
echo IMPORTANTE: Asegúrate de haber configurado todas las variables de entorno en el panel de Laravel Cloud
echo.
set /p CONTINUE="¿Continuar con el deploy? (y/N): "
if /i "%CONTINUE%"=="y" (
    laravel-cloud deploy
    echo ✓ Deploy completado
    
    REM Ejecutar post-deploy
    echo Ejecutando post-deploy...
    laravel-cloud artisan cache:clear
    laravel-cloud artisan cache:table
    laravel-cloud artisan cache:setup-cloud --driver=database
    laravel-cloud artisan migrate --force
    laravel-cloud artisan db:seed --force
    echo ✓ Post-deploy completado
) else (
    echo Deploy cancelado. Ejecuta 'laravel-cloud deploy' cuando estés listo.
)

echo.
echo ========================================
echo INSTALACIÓN LARAVEL CLOUD COMPLETADA
echo ========================================
echo.
echo Configuración completada:
echo - Proyecto: %PROJECT_NAME%
echo - Base de datos: Configurada
echo - Variables de entorno: Configurar en el panel
echo - Caché: Database cache optimizado
echo.
echo Próximos pasos:
echo 1. Configura las variables de entorno en el panel de Laravel Cloud
echo 2. Ejecuta: laravel-cloud deploy
echo 3. Verifica: laravel-cloud app:status
echo.
echo Comandos útiles:
echo - Ver logs: laravel-cloud logs
echo - Ejecutar comandos: laravel-cloud artisan [comando]
echo - Monitorear: monitor-laravel-cloud.bat
echo.
pause
