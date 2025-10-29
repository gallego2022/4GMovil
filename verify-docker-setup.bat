@echo off
setlocal EnableDelayedExpansion
chcp 65001 > nul
echo ========================================
echo VERIFICACION DOCKER - 4GMovil
echo ========================================

echo.
echo 📊 Estado de los contenedores:
docker compose ps

echo.
echo 🔍 Verificando logs de la aplicación:
docker compose logs app --tail=10

echo.
echo 🗄️ Verificando conexión a la base de datos:
docker compose exec app php artisan migrate:status

echo.
echo 🔑 Verificando clave de aplicación:
docker compose exec app php artisan config:show app.key

echo.
echo 📊 Verificando configuración de Redis:
docker compose exec app php artisan tinker --execute="echo 'Redis: ' . (\Illuminate\Support\Facades\Redis::ping() ? 'Conectado' : 'Desconectado');"

echo.
echo 🌐 Verificando acceso web:
set "URL=http://localhost:8000"
set /a RETRIES=30
set /a DELAY=2
for /l %%i in (1,1,%RETRIES%) do (
  set "STATUS="
  for /f "usebackq delims=" %%S in (`curl -s -o nul -w "%%{http_code}" %URL% 2^>nul`) do set "STATUS=%%S"
  echo HTTP Status: !STATUS!
  set "STATUS_FIRST=!STATUS:~0,1!"
  if "!STATUS_FIRST!"=="2" goto :WEB_OK
  if "!STATUS_FIRST!"=="3" goto :WEB_OK
  echo Esperando a que la aplicación esté lista... (%%i/%RETRIES%)
  timeout /t %DELAY% /nobreak > nul
)
echo Error al conectar con la aplicación
goto :WEB_DONE

:WEB_OK
echo Aplicación responde correctamente.

:WEB_DONE

echo.
echo ✅ Verificación completada!
echo.
pause
