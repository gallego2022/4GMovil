@echo off
echo ========================================
echo VERIFICACION DOCKER - 4GMovil
echo ========================================

echo.
echo 📊 Estado de los contenedores:
docker-compose ps

echo.
echo 🔍 Verificando logs de la aplicación:
docker-compose logs app --tail=10

echo.
echo 🗄️ Verificando conexión a la base de datos:
docker-compose exec app php artisan migrate:status

echo.
echo 🔑 Verificando clave de aplicación:
docker-compose exec app php artisan key:show

echo.
echo 📊 Verificando configuración de Redis:
docker-compose exec app php artisan tinker --execute="echo 'Redis: ' . (Redis::ping() ? 'Conectado' : 'Desconectado');"

echo.
echo 🌐 Verificando acceso web:
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost:8000 || echo "Error al conectar con la aplicación"

echo.
echo ✅ Verificación completada!
echo.
pause
