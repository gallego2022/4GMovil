@echo off
echo ========================================
echo    MIGRACIONES CONSOLIDADAS - SISTEMA COMPLETO
echo ========================================
echo.

echo [1/8] Verificando estado actual de migraciones...
php artisan migrate:status
echo.

echo [2/8] Ejecutando migracion de usuarios consolidada...
php artisan migrate --path=database/migrations/2025_09_01_191259_create_usuarios_table_consolidated.php
echo.

echo [3/8] Ejecutando migracion de categorias y marcas...
php artisan migrate --path=database/migrations/2025_09_01_191707_create_pedidos_system_consolidated.php
echo.

echo [4/8] Ejecutando migracion de productos consolidada...
php artisan migrate --path=database/migrations/2025_09_01_191458_create_productos_table_consolidated.php
echo.

echo [5/8] Ejecutando migracion de sistema de inventario consolidada...
php artisan migrate --path=database/migrations/2025_09_01_191559_create_inventario_system_consolidated.php
echo.

echo [6/8] Ejecutando migracion de sistema de pedidos consolidada...
php artisan migrate --path=database/migrations/2025_09_01_191707_create_pedidos_system_consolidated.php
echo.

echo [7/8] Ejecutando migracion de sistema Stripe consolidada...
php artisan migrate --path=database/migrations/2025_09_01_191759_create_stripe_system_consolidated.php
echo.

echo [8/8] Ejecutando migracion de tabla de pagos...
php artisan migrate --path=database/migrations/2025_09_01_191130_create_pagos_table_final.php
echo.

echo [9/9] Agregando claves foraneas y relaciones...
php artisan migrate --path=database/migrations/2025_09_01_192715_add_foreign_keys_after_tables_created.php
echo.

echo ========================================
echo    MIGRACIONES COMPLETADAS
echo ========================================
echo.
echo Todas las migraciones consolidadas han sido ejecutadas:
echo - Usuarios (con OAuth y Stripe)
echo - Categorias y Marcas
echo - Productos (con stock reservado)
echo - Sistema de Inventario (variantes, movimientos, reservas)
echo - Sistema de Pedidos (pedidos, detalles, direcciones, reseñas)
echo - Sistema Stripe (suscripciones)
echo - Tabla de Pagos
echo - Claves foraneas y relaciones
echo.
echo Presiona cualquier tecla para continuar...
pause >nul
