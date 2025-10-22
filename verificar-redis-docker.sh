#!/bin/bash
# ========================================
# VERIFICAR REDIS EN DOCKER - 4GMovil
# ========================================
set -e
echo ""
echo "Verificando configuración de Redis en Docker..."
echo ""

# Verificar que Docker esté ejecutándose
echo "[1/6] Verificando Docker..."
docker-compose ps
if [ $? -ne 0 ]; then
    echo "ERROR: Docker no está ejecutándose"
    echo "Ejecuta: docker-compose up -d"
    exit 1
fi
echo "✓ Docker está ejecutándose"

# Verificar contenedor de Redis
echo "[2/6] Verificando contenedor de Redis..."
docker-compose ps redis
if [ $? -ne 0 ]; then
    echo "ERROR: Contenedor de Redis no está ejecutándose"
    echo "Ejecuta: docker-compose up -d redis"
    exit 1
fi
echo "✓ Contenedor de Redis está ejecutándose"

# Verificar conexión a Redis
echo "[3/6] Verificando conexión a Redis..."
docker-compose exec redis redis-cli ping
if [ $? -ne 0 ]; then
    echo "ERROR: No se puede conectar a Redis"
    echo "Verifica la configuración de Redis"
    exit 1
fi
echo "✓ Redis está respondiendo"

# Verificar configuración de Redis
echo "[4/6] Verificando configuración de Redis..."
docker-compose exec redis redis-cli config get maxmemory
docker-compose exec redis redis-cli config get maxmemory-policy
echo "✓ Configuración de Redis verificada"

# Verificar variables de entorno de la aplicación
echo "[5/6] Verificando variables de entorno de la aplicación..."
docker-compose exec app printenv | grep -E "(REDIS|CACHE|QUEUE)"
echo "✓ Variables de entorno verificadas"

# Probar caché desde la aplicación
echo "[6/6] Probando caché desde la aplicación..."
docker-compose exec app php artisan tinker --execute="Cache::put('test_redis', 'Redis funciona correctamente', 60); echo 'Clave guardada: ' . Cache::get('test_redis');"
if [ $? -ne 0 ]; then
    echo "ADVERTENCIA: No se pudo probar el caché desde la aplicación"
    echo "Verifica la configuración de Redis en .env"
else
    echo "✓ Caché funcionando correctamente"
fi

echo ""
echo "========================================"
echo "VERIFICACIÓN DE REDIS COMPLETADA"
echo "========================================"
echo ""
echo "Comandos útiles:"
echo "- Ver logs de Redis: docker-compose logs -f redis"
echo "- Ver logs de Queue Worker: docker-compose logs -f queue-worker"
echo "- Conectar a Redis: docker-compose exec redis redis-cli"
echo "- Ver claves: docker-compose exec redis redis-cli keys \"*\""
echo "- Limpiar Redis: docker-compose exec redis redis-cli flushall"
echo "- Ver estadísticas: docker-compose exec redis redis-cli info"
echo ""
