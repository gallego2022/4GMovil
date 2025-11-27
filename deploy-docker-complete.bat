@echo off
setlocal EnableDelayedExpansion
echo ========================================
echo DESPLIEGUE COMPLETO DOCKER - 4GMovil
echo ========================================

echo.
echo 🛑 Deteniendo contenedores existentes...
docker compose down

echo.
echo 🗑️ Eliminando contenedores y volúmenes antiguos...
docker compose down -v --remove-orphans

echo.
echo 🧹 Limpiando imágenes no utilizadas...
docker system prune -f

echo.
echo 🔨 Reconstruyendo imágenes...
docker compose build --no-cache

echo.
echo 🚀 Iniciando servicios...
docker compose up -d

echo.
echo ⏳ Esperando a que los servicios estén listos...
timeout /t 60 /nobreak > nul

echo.
echo 🔎 Verificando base de datos...
for /l %%i in (1,1,20) do (
  docker compose exec db mysql -uroot -prootpassword -e "SELECT 1" >nul 2>&1
  if not errorlevel 1 (
    echo ✅ Base de datos lista
    goto :DB_OK
  )
  echo ⏳ DB no lista (intento %%i), reintentando...
  timeout /t 3 > nul
)
:DB_OK

echo.
echo 🔎 Verificando aplicación Laravel...
for /l %%i in (1,1,20) do (
  for /f "delims=" %%s in ('powershell -Command "(Invoke-WebRequest -UseBasicParsing -Uri http://localhost:8000 -TimeoutSec 5).StatusCode"') do set STATUS=%%s
  if "!STATUS!"=="200" (
    echo ✅ Aplicación lista
    goto :APP_OK
  )
  echo ⏳ App no lista (intento %%i), reintentando...
  timeout /t 3 > nul
)
:APP_OK

echo.
echo 🔎 Verificando microservicio de reportes...
for /l %%i in (1,1,20) do (
  for /f "delims=" %%s in ('powershell -Command "(Invoke-WebRequest -UseBasicParsing -Uri http://localhost:8082/reportes/ventas/resumen?desde=2025-01-01&hasta=2025-01-02 -TimeoutSec 5).StatusCode"') do set RSTATUS=%%s
  if "!RSTATUS!"=="200" (
    echo ✅ Microservicio de reportes listo
    goto :REP_OK
  )
  echo ⏳ Reportes no listo (intento %%i), reintentando...
  timeout /t 3 > nul
)
:REP_OK

echo.
echo � Ejecutando migraciones...
docker compose exec app php artisan migrate --force

echo.
echo 🌱 Ejecutando seeders...
docker compose exec app php artisan db:seed --force

echo.
echo �� Verificando estado de los contenedores...
docker compose ps

echo.
echo 🔍 Verificando logs de la aplicación...
docker compose logs app --tail=20

echo.
echo 🔍 Verificando logs del microservicio de reportes...
docker compose logs reportes --tail=20

echo.
echo ✅ Despliegue completado!
echo.
echo 🌐 Aplicación disponible en: http://localhost:8000
echo 🗄️  phpMyAdmin disponible en: http://localhost:8083
echo 📊 Redis Commander disponible en: http://localhost:8081
echo 📈 Microservicio de Reportes: http://localhost:8082
echo.
echo Para ver logs en tiempo real: docker compose logs -f
echo Para detener servicios: docker compose down
echo.
pause
