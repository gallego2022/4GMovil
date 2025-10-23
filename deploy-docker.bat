@echo off
echo ========================================
echo    DESPLIEGUE AUTOMATICO EN DOCKER
echo ========================================

echo.
echo 🔧 Configurando entorno...
echo S | copy env.docker.example .env

echo.
echo 🐳 Construyendo y desplegando contenedores...
docker-compose down
docker-compose up -d --build

echo.
echo ⏳ Esperando que los servicios estén listos...
timeout /t 10 /nobreak >nul

echo.
echo 🔧 Ejecutando corrección de permisos...
docker-compose exec app /usr/local/bin/fix-permissions.sh

echo.
echo ✅ Despliegue completado!
echo.
echo 🌐 Servicios disponibles:
echo    - Aplicación: http://localhost:8000
echo    - phpMyAdmin: http://localhost:8080
echo    - Redis Commander: http://localhost:8081
echo.
echo 📋 Para ver logs: docker-compose logs -f
echo 🛑 Para detener: docker-compose down
echo.
pause
