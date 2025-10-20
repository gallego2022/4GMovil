@echo off
echo ========================================
echo   4GMovil - Sincronizacion de Cambios
echo ========================================
echo.

echo Obteniendo cambios desde GitHub...
git pull origin main

if %errorlevel% neq 0 (
    echo ERROR: No se pudieron obtener los cambios.
    pause
    exit /b 1
)

echo Cambios obtenidos correctamente.
echo.

REM Verificar si esta en Docker
if exist "docker-compose.yml" (
    echo Aplicando cambios en Docker...
    echo.
    
    echo Deteniendo contenedores...
    docker compose down
    
    echo Reconstruyendo y levantando contenedores...
    docker compose up --build -d
    
    echo Esperando que los contenedores esten listos...
    timeout /t 10 /nobreak >nul
    
    echo Ejecutando migraciones...
    docker exec 4gmovil_app php artisan migrate
    
    echo Construyendo assets...
    docker exec 4gmovil_app npm run build
    
    echo.
    echo Sincronizacion completada en Docker!
    echo URLs de acceso:
    echo - Aplicacion: http://localhost:8000
    echo - Admin: http://localhost:8000/admin
    echo - phpMyAdmin: http://localhost:8080
) else (
    echo Aplicando cambios en instalacion tradicional...
    echo.
    
    echo Instalando dependencias PHP...
    composer install
    
    echo Instalando dependencias JavaScript...
    npm install
    
    echo Ejecutando migraciones...
    php artisan migrate
    
    echo Construyendo assets...
    npm run build
    
    echo.
    echo Sincronizacion completada en instalacion tradicional!
    echo URLs de acceso:
    echo - Aplicacion: http://127.0.0.1:8000
    echo - Admin: http://127.0.0.1:8000/admin
    echo.
    echo Para iniciar el servidor: php artisan serve
)

echo.
echo ========================================
echo   Sincronizacion completada!
echo ========================================
echo.
pause
