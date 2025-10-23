@echo off
echo ========================================
echo DESPLIEGUE DOCKER DESARROLLO - 4GMovil
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
echo 🔨 Reconstruyendo imágenes para DESARROLLO...
docker-compose build --no-cache

echo.
echo 🚀 Iniciando servicios en modo DESARROLLO...
docker-compose up -d

echo.
echo ⏳ Esperando a que los servicios estén listos...
timeout /t 30 /nobreak > nul

echo.
echo 📊 Verificando estado de los contenedores...
docker-compose ps

echo.
echo 🔍 Verificando logs de la aplicación...
docker-compose logs app --tail=20

echo.
echo 🛠️ Herramientas de desarrollo disponibles:
echo    - Laravel Tinker: docker-compose exec app php artisan tinker
echo    - Laravel Pail: docker-compose exec app php artisan pail
echo    - Laravel Boost: Incluido para desarrollo
echo    - Debug Bar: Habilitado en modo desarrollo
echo.
echo ✅ Despliegue de DESARROLLO completado!
echo.
echo 🌐 Aplicación disponible en: http://localhost:8000
echo 🗄️  phpMyAdmin disponible en: http://localhost:8080
echo 📊 Redis Commander disponible en: http://localhost:8081
echo.
echo Para ver logs en tiempo real: docker-compose logs -f
echo Para detener servicios: docker-compose down
echo.
pause
