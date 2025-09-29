@echo off
echo ========================================
echo    REBUILDING 4GMOVIL DOCKER CONTAINER
echo ========================================

echo.
echo 🛑 Deteniendo contenedores existentes...
docker-compose down

echo.
echo 🗑️ Eliminando contenedores y volúmenes antiguos...
docker-compose down -v --remove-orphans

echo.
echo 🧹 Limpiando imágenes no utilizadas...
docker system prune -f

echo.
echo 🔨 Reconstruyendo imagen de la aplicación...
docker-compose build --no-cache

echo.
echo 🚀 Iniciando contenedores...
docker-compose up -d

echo.
echo ⏳ Esperando que los servicios estén listos...
timeout /t 10 /nobreak > nul

echo.
echo 📊 Verificando estado de los contenedores...
docker-compose ps

echo.
echo ✅ Proceso completado!
echo 🌐 La aplicación debería estar disponible en: http://localhost:8000
echo.
echo Para ver los logs en tiempo real, ejecuta:
echo docker-compose logs -f app
echo.
pause
