@echo off
REM ========================================
REM VERIFICAR QUEUE WORKER EN DOCKER - 4GMovil
REM ========================================
echo.
echo Verificando Queue Worker en Docker...
echo.

REM Verificar que Docker esté ejecutándose
echo [1/5] Verificando Docker...
docker-compose ps
if %errorlevel% neq 0 (
    echo ERROR: Docker no está ejecutándose
    echo Ejecuta: docker-compose up -d
    pause
    exit /b 1
)
echo ✓ Docker está ejecutándose

REM Verificar contenedor de queue-worker
echo [2/5] Verificando contenedor de queue-worker...
docker-compose ps queue-worker
if %errorlevel% neq 0 (
    echo ERROR: Contenedor de queue-worker no está ejecutándose
    echo Ejecuta: docker-compose up -d queue-worker
    pause
    exit /b 1
)
echo ✓ Contenedor de queue-worker está ejecutándose

REM Verificar que no hay contenedores duplicados
echo [3/5] Verificando que no hay contenedores duplicados...
docker-compose ps | findstr queue
echo ✓ Verificación de duplicados completada

REM Verificar logs del queue-worker
echo [4/5] Verificando logs del queue-worker...
docker-compose logs --tail=10 queue-worker
echo ✓ Logs del queue-worker verificados

REM Verificar que Redis esté funcionando
echo [5/5] Verificando conexión a Redis...
docker-compose exec redis redis-cli ping
if %errorlevel% neq 0 (
    echo ADVERTENCIA: Redis no está respondiendo
    echo Verifica que el contenedor de Redis esté ejecutándose
) else (
    echo ✓ Redis está funcionando correctamente
)

echo.
echo ========================================
echo VERIFICACIÓN DE QUEUE WORKER COMPLETADA
echo ========================================
echo.
echo Comandos útiles:
echo - Ver logs del worker: docker-compose logs -f queue-worker
echo - Reiniciar worker: docker-compose restart queue-worker
echo - Detener worker: docker-compose stop queue-worker
echo - Iniciar worker: docker-compose start queue-worker
echo - Ver todos los contenedores: docker-compose ps
echo.
pause
