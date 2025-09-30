@echo off
echo ========================================
echo   Sincronizacion de Storage - 4GMovil
echo ========================================
echo.

REM Verificar que Docker este ejecutandose
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no esta ejecutandose.
    echo Por favor inicia Docker Desktop y vuelve a intentar.
    pause
    exit /b 1
)

echo Sincronizando storage para corregir problemas de imagenes...
echo.

REM Sincronizar storage
docker compose exec -T app php artisan storage:sync --force
if %errorlevel% neq 0 (
    echo ERROR: No se pudo sincronizar el storage.
    echo Revisa los logs con: docker compose logs app
    pause
    exit /b 1
)

echo.
echo Verificando sincronizacion...
docker compose exec -T app ls -la /var/www/html/public/storage

echo.
echo ========================================
echo   Sincronizacion completada!
echo ========================================
echo.
echo Las imagenes ahora deberian cargarse correctamente.
echo Si el problema persiste, reinicia los contenedores:
echo   docker compose restart
echo.
pause
