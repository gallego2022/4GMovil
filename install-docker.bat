@echo off
setlocal enabledelayedexpansion

echo ========================================
echo   4GMovil - Instalacion Docker
echo ========================================
echo.

REM Verificar si Docker esta instalado y funcionando
echo Verificando Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no esta instalado.
    echo Por favor instala Docker Desktop desde: https://www.docker.com/products/docker-desktop
    echo Y asegurate de que Docker Desktop este ejecutandose.
    pause
    exit /b 1
)

REM Verificar que Docker este ejecutandose
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no esta ejecutandose.
    echo Por favor inicia Docker Desktop y vuelve a intentar.
    pause
    exit /b 1
)

echo Docker encontrado y funcionando. Continuando...
echo.

REM Verificar si Git esta instalado
echo Verificando Git...
git --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Git no esta instalado.
    echo Por favor instala Git desde: https://git-scm.com/downloads
    pause
    exit /b 1
)

echo Git encontrado. Continuando...
echo.

REM Verificar si el proyecto ya existe
if exist "4gmovil" (
    echo El directorio 4gmovil ya existe.
    set /p choice="¿Deseas continuar y actualizar el proyecto? (s/n): "
    if /i "%choice%" neq "s" (
        echo Instalacion cancelada.
        pause
        exit /b 0
    )
    echo Actualizando proyecto...
    cd 4gmovil
    git pull origin main
    if %errorlevel% neq 0 (
        echo ERROR: No se pudo actualizar el proyecto desde Git.
        echo Verifica tu conexion a internet y los permisos del repositorio.
        pause
        exit /b 1
    )
) else (
    echo Clonando proyecto desde GitHub...
    git clone https://github.com/tu-usuario/4gmovil.git
    if %errorlevel% neq 0 (
        echo ERROR: No se pudo clonar el proyecto desde GitHub.
        echo Verifica tu conexion a internet y los permisos del repositorio.
        pause
        exit /b 1
    )
    cd 4gmovil
)

echo.
echo Configurando variables de entorno...
if not exist ".env" (
    if exist "env.docker.example" (
        copy env.docker.example .env
        echo Archivo .env creado desde env.docker.example
    ) else (
        echo ERROR: No se encontro el archivo env.docker.example
        echo Asegurate de que el proyecto este completo.
        pause
        exit /b 1
    )
) else (
    echo El archivo .env ya existe.
)

echo.
echo Verificando archivos necesarios...
if not exist "docker-compose.yml" (
    echo ERROR: No se encontro docker-compose.yml
    echo Asegurate de que el proyecto este completo.
    pause
    exit /b 1
)

if not exist "Dockerfile" (
    echo ERROR: No se encontro Dockerfile
    echo Asegurate de que el proyecto este completo.
    pause
    exit /b 1
)

echo.
echo Construyendo y levantando contenedores Docker...
echo Esto puede tomar varios minutos en la primera ejecucion...
docker compose up --build -d

if %errorlevel% neq 0 (
    echo ERROR: No se pudieron levantar los contenedores Docker.
    echo Revisa los logs con: docker compose logs
    pause
    exit /b 1
)

echo.
echo Esperando que los contenedores esten listos...
echo Esto puede tomar hasta 2 minutos...
timeout /t 30 /nobreak >nul

echo.
echo Verificando estado de contenedores...
docker compose ps

echo.
echo Verificando que la aplicacion este funcionando...
timeout /t 10 /nobreak >nul

REM Verificar que los contenedores esten corriendo
docker compose ps --format "table {{.Name}}\t{{.Status}}" | findstr "Up" >nul
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Algunos contenedores no estan funcionando correctamente.
    echo Revisa los logs con: docker compose logs
    echo.
)

echo.
echo ========================================
echo   Instalando dependencias y configurando base de datos
echo ========================================
echo.

REM Esperar un poco mas para asegurar que la base de datos este completamente lista
echo Esperando que la base de datos este completamente lista...
timeout /t 15 /nobreak >nul

REM Crear carpetas necesarias para Laravel
echo Creando carpetas necesarias para Laravel...
docker compose exec -T app mkdir -p /var/www/html/storage/framework/cache
docker compose exec -T app mkdir -p /var/www/html/storage/framework/sessions
docker compose exec -T app mkdir -p /var/www/html/storage/framework/views
docker compose exec -T app mkdir -p /var/www/html/storage/logs
docker compose exec -T app mkdir -p /var/www/html/bootstrap/cache
docker compose exec -T app touch /var/www/html/storage/logs/laravel.log

REM Establecer permisos iniciales
echo Estableciendo permisos iniciales...
docker compose exec -T app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec -T app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

REM Instalar dependencias de Composer
echo Instalando dependencias de PHP con Composer...
docker compose exec -T app composer install --no-dev --optimize-autoloader
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Error al instalar dependencias de Composer.
    echo Continuando con la instalacion...
    echo.
)

REM Generar clave de aplicacion
echo Generando clave de aplicacion...
docker compose exec -T app php artisan key:generate --force
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Error al generar clave de aplicacion.
    echo.
)

REM Limpiar cache
echo Limpiando cache de la aplicacion...
docker compose exec -T app php artisan config:clear
docker compose exec -T app php artisan cache:clear
docker compose exec -T app php artisan view:clear

REM Ejecutar migraciones
echo Ejecutando migraciones de base de datos...
docker compose exec -T app php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: No se pudieron ejecutar las migraciones.
    echo Revisa la conexion a la base de datos.
    echo.
)

REM Ejecutar seeders
echo Ejecutando seeders para poblar la base de datos...
docker compose exec -T app php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Error al ejecutar los seeders.
    echo La aplicacion funcionara pero sin datos de prueba.
    echo.
)

REM Crear enlace simbolico de storage
echo Creando enlace simbolico de storage...
docker compose exec -T app php artisan storage:link
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Error al crear enlace simbolico de storage.
    echo.
)

REM Sincronizar storage para evitar problemas de enlaces
echo Sincronizando storage para evitar problemas de acceso a imagenes...
docker compose exec -T app php artisan storage:sync --force
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Error al sincronizar storage.
    echo Continuando con la instalacion...
    echo.
)

REM Verificar permisos finales
echo Verificando permisos finales...
docker compose exec -T app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec -T app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo.
echo Verificando instalacion final...
docker compose exec -T app php artisan --version
if %errorlevel% neq 0 (
    echo ADVERTENCIA: No se pudo verificar la instalacion de Laravel.
    echo.
)

echo.
echo ========================================
echo   Instalacion completada!
echo ========================================
echo.
echo URLs de acceso:
echo - Aplicacion: http://localhost:8000
echo - Admin Panel: http://localhost:8000/admin
echo - phpMyAdmin: http://localhost:8080
echo.
echo Credenciales por defecto:
echo - Admin: 4gmoviltest@gmail.com / Admin123!
echo - Base de datos: laraveluser / laravelpass
echo.
echo Comandos utiles:
echo - Ver logs: docker compose logs -f
echo - Ver logs de un servicio: docker compose logs -f app
echo - Detener: docker compose down
echo - Reiniciar: docker compose restart
echo - Reconstruir: docker compose up --build -d
echo.
echo IMPORTANTE: Configura tus credenciales de Google OAuth y Stripe en el archivo .env
echo.
echo Si tienes problemas, revisa los logs con: docker compose logs
echo.
pause
