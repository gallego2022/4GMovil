@echo off
echo ========================================
echo   4GMovil - Instalacion Docker
echo ========================================
echo.

REM Verificar si Docker esta instalado
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no esta instalado.
    echo Por favor instala Docker Desktop desde: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

echo Docker encontrado. Continuando...
echo.

REM Verificar si Git esta instalado
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
) else (
    echo Clonando proyecto desde GitHub...
    git clone https://github.com/tu-usuario/4gmovil.git
    cd 4gmovil
)

echo.
echo Configurando variables de entorno...
if not exist ".env" (
    copy env.docker.example .env
    echo Archivo .env creado desde env.docker.example
) else (
    echo El archivo .env ya existe.
)

echo.
echo Construyendo y levantando contenedores Docker...
docker-compose up --build -d

echo.
echo Esperando que los contenedores esten listos...
timeout /t 10 /nobreak >nul

echo.
echo Verificando estado de contenedores...
docker-compose ps

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
echo - Ver logs: docker-compose logs -f
echo - Detener: docker-compose down
echo - Reiniciar: docker-compose restart
echo.
echo IMPORTANTE: Configura tus credenciales de Google OAuth y Stripe en el archivo .env
echo.
pause
