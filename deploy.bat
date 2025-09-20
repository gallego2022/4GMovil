@echo off
REM Script de despliegue para 4GMovil con Docker (Windows)

echo 🚀 Iniciando despliegue de 4GMovil...

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker no está instalado. Por favor instala Docker Desktop primero.
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker Compose no está instalado. Por favor instala Docker Compose primero.
    pause
    exit /b 1
)

REM Crear archivo .env si no existe
if not exist .env (
    echo [INFO] Creando archivo .env desde env.docker.example...
    copy env.docker.example .env
    echo [WARNING] Recuerda configurar las variables de entorno en .env antes de continuar
)

REM Parar contenedores existentes
echo [INFO] Deteniendo contenedores existentes...
docker-compose down

REM Preguntar sobre limpieza
set /p cleanup="¿Deseas limpiar imágenes Docker antiguas? (y/N): "
if /i "%cleanup%"=="y" (
    echo [INFO] Limpiando imágenes Docker antiguas...
    docker system prune -f
)

REM Construir y levantar los servicios
echo [INFO] Construyendo y levantando servicios...
docker-compose up --build -d

REM Verificar que los contenedores estén corriendo
echo [INFO] Verificando estado de los contenedores...
timeout /t 10 /nobreak >nul

docker-compose ps | findstr "Up" >nul
if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] ¡Despliegue completado exitosamente!
    echo.
    echo [INFO] Servicios disponibles:
    echo   🌐 Aplicación: http://localhost:8000
    echo   🗄️  phpMyAdmin: http://localhost:8080
    echo   📊 Base de datos: localhost:3306
    echo   🔴 Redis: localhost:6379
    echo.
    echo [INFO] Para ver los logs: docker-compose logs -f
    echo [INFO] Para detener: docker-compose down
) else (
    echo [ERROR] Hubo un problema con el despliegue. Revisa los logs:
    docker-compose logs
    pause
    exit /b 1
)

pause
