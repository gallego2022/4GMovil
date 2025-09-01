@echo off
REM Script para ejecutar tareas programadas de 4GMovil en Windows
REM Este archivo debe estar en la raíz del proyecto

REM Cambiar al directorio del proyecto
cd /d "C:\xampp\htdocs\Proyecto V11.3\4GMovil"

REM Ejecutar el programador de tareas de Laravel
php artisan schedule:run >> nul 2>&1

REM Log de ejecución
echo %date% %time%: Tareas programadas ejecutadas >> storage\logs\cron.log
