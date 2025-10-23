@echo off
echo ========================================
echo SOLUCIONANDO PROBLEMA DE CONEXIÓN REDIS
echo ========================================

REM Verificar si Docker está ejecutándose
echo Verificando Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker no está instalado o no está en el PATH
    pause
    exit /b 1
)

REM Verificar si los contenedores están ejecutándose
echo Verificando contenedores...
docker-compose ps

REM Parar contenedores si están ejecutándose
echo Deteniendo contenedores...
docker-compose down

REM Limpiar volúmenes de Redis si es necesario
echo Limpiando volúmenes de Redis...
docker volume rm 4gmovil_redis_data 2>nul

REM Iniciar contenedores en orden correcto
echo Iniciando base de datos...
docker-compose up -d db

REM Esperar a que la base de datos esté lista
echo Esperando a que la base de datos esté lista...
timeout /t 10 /nobreak >nul

echo Iniciando Redis...
docker-compose up -d redis

REM Esperar a que Redis esté listo
echo Esperando a que Redis esté listo...
timeout /t 5 /nobreak >nul

echo Iniciando aplicación...
docker-compose up -d app

REM Esperar a que la aplicación esté lista
echo Esperando a que la aplicación esté lista...
timeout /t 10 /nobreak >nul

echo Iniciando worker de colas...
docker-compose up -d queue-worker

REM Verificar conexión a Redis
echo Verificando conexión a Redis...
docker-compose exec app php artisan tinker --execute="echo 'Redis connection: '; try { Redis::ping(); echo 'SUCCESS'; } catch (Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }"

echo ========================================
echo VERIFICACIÓN COMPLETADA
echo ========================================
echo.
echo Para verificar manualmente:
echo   docker-compose exec app php artisan tinker
echo   Redis::ping()
echo.
echo Para ver logs de Redis:
echo   docker-compose logs redis
echo.
echo Para ver logs de la aplicación:
echo   docker-compose logs app
echo.
pause
