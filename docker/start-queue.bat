@echo off
REM ========================================
REM SCRIPT DE INICIO PARA WORKERS DE COLA
REM ========================================

echo Iniciando workers de cola...

REM Esperar a que la base de datos esté lista
echo Esperando a que la base de datos esté lista...
:wait_db
php artisan migrate:status >nul 2>&1
if %errorlevel% neq 0 (
    echo Esperando conexión a la base de datos...
    timeout /t 5 /nobreak >nul
    goto wait_db
)

echo Base de datos conectada. Iniciando workers...

REM Limpiar caché antes de iniciar
php artisan cache:clear
php artisan config:clear

REM Iniciar workers de cola
echo Iniciando workers de cola con Redis...
php artisan queue:work redis --tries=3 --sleep=1 --timeout=90 --memory=512 --verbose
