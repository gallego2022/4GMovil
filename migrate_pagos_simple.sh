#!/bin/bash

echo "========================================"
echo "    MIGRACION PAGOS - VERSION SIMPLIFICADA"
echo "========================================"
echo

echo "[1/3] Verificando estado actual de migraciones..."
php artisan migrate:status
echo

echo "[2/3] Ejecutando migracion consolidada final..."
php artisan migrate --path=database/migrations/2025_09_01_191130_create_pagos_table_final.php
echo

echo "[3/3] Verificando estado final..."
php artisan migrate:status
echo

echo "========================================"
echo "    MIGRACION COMPLETADA"
echo "========================================"
echo
echo "La tabla pagos ha sido creada con todos los campos necesarios"
echo "en una sola migracion consolidada."
echo
echo "Presiona Enter para continuar..."
read
