@echo off
echo ========================================
echo RESETEANDO Y MIGRANDO BASE DE DATOS
echo ========================================
echo.

echo Limpiando cache...
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan cache:clear

echo.
echo Reseteando base de datos...
php artisan migrate:reset

echo.
echo Ejecutando migraciones limpias...
php artisan migrate

echo.
echo Ejecutando seeders...
php artisan db:seed

echo.
echo ========================================
echo PROCESO COMPLETADO
echo ========================================
echo.
echo Base de datos reseteada y migrada exitosamente
echo Usuario admin: 4gmoviltest@gmail.com / Admin123!
echo.
pause
