@echo off
echo ========================================
echo    MIGRACION DE PAGOS - AUTOMATIZADA
echo ========================================
echo.

echo [1/4] Verificando estado actual de migraciones...
php artisan migrate:status
echo.

echo [2/4] Ejecutando migracion de rollback...
php artisan migrate --path=database/migrations/2025_09_01_190350_rollback_pagos_migrations.php
echo.

echo [3/4] Ejecutando migracion consolidada...
php artisan migrate --path=database/migrations/2025_09_01_190421_create_pagos_table_consolidated.php
echo.

echo [4/4] Verificando estado final...
php artisan migrate:status
echo.

echo ========================================
echo    MIGRACION COMPLETADA
echo ========================================
echo.
echo Presiona cualquier tecla para continuar...
pause >nul
