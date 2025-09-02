#!/bin/bash

echo "========================================"
echo "    RESET Y MIGRACION COMPLETA"
echo "========================================"
echo

echo "[1/4] Reseteando base de datos..."
php artisan migrate:reset --force
echo

echo "[2/4] Ejecutando migraciones por defecto de Laravel..."
php artisan migrate
echo

echo "[3/4] Ejecutando migraciones consolidadas..."
php artisan migrate --path=database/migrations/2025_09_01_191259_create_usuarios_table_consolidated.php
php artisan migrate --path=database/migrations/2025_09_01_191458_create_productos_table_consolidated.php
php artisan migrate --path=database/migrations/2025_09_01_191559_create_inventario_system_consolidated.php
php artisan migrate --path=database/migrations/2025_09_01_191707_create_pedidos_system_consolidated.php
php artisan migrate --path=database/migrations/2025_09_01_191759_create_stripe_system_consolidated.php
php artisan migrate --path=database/migrations/2025_09_01_191130_create_pagos_table_final.php
echo

echo "[4/4] Agregando claves foraneas..."
php artisan migrate --path=database/migrations/2025_09_01_192715_add_foreign_keys_after_tables_created.php
echo

echo "========================================"
echo "    MIGRACION COMPLETADA"
echo "========================================"
echo
echo "Todas las migraciones consolidadas han sido ejecutadas exitosamente."
echo
echo "Presiona Enter para continuar..."
read
