#!/bin/bash

# Script para ejecutar tareas programadas de 4GMovil
# Este archivo debe estar en la raíz del proyecto

# Cambiar al directorio del proyecto
cd /c/xampp/htdocs/Proyecto\ V11.3/4GMovil

# Ejecutar el programador de tareas de Laravel
php artisan schedule:run >> /dev/null 2>&1

# Log de ejecución
echo "$(date): Tareas programadas ejecutadas" >> storage/logs/cron.log
