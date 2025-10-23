#!/bin/bash

echo "========================================"
echo "SOLUCIONANDO PROBLEMA DE CONEXIÓN REDIS"
echo "========================================"

# Verificar si Docker está ejecutándose
echo "Verificando Docker..."
if ! command -v docker &> /dev/null; then
    echo "ERROR: Docker no está instalado o no está en el PATH"
    exit 1
fi

# Verificar si los contenedores están ejecutándose
echo "Verificando contenedores..."
docker-compose ps

# Parar contenedores si están ejecutándose
echo "Deteniendo contenedores..."
docker-compose down

# Limpiar volúmenes de Redis si es necesario
echo "Limpiando volúmenes de Redis..."
docker volume rm 4gmovil_redis_data 2>/dev/null || true

# Iniciar contenedores en orden correcto
echo "Iniciando base de datos..."
docker-compose up -d db

# Esperar a que la base de datos esté lista
echo "Esperando a que la base de datos esté lista..."
sleep 10

echo "Iniciando Redis..."
docker-compose up -d redis

# Esperar a que Redis esté listo
echo "Esperando a que Redis esté listo..."
sleep 5

echo "Iniciando aplicación..."
docker-compose up -d app

# Esperar a que la aplicación esté lista
echo "Esperando a que la aplicación esté lista..."
sleep 10

echo "Iniciando worker de colas..."
docker-compose up -d queue-worker

# Verificar conexión a Redis
echo "Verificando conexión a Redis..."
docker-compose exec app php artisan tinker --execute="echo 'Redis connection: '; try { Redis::ping(); echo 'SUCCESS'; } catch (Exception \$e) { echo 'FAILED: ' . \$e->getMessage(); }"

echo "========================================"
echo "VERIFICACIÓN COMPLETADA"
echo "========================================"
echo ""
echo "Para verificar manualmente:"
echo "  docker-compose exec app php artisan tinker"
echo "  Redis::ping()"
echo ""
echo "Para ver logs de Redis:"
echo "  docker-compose logs redis"
echo ""
echo "Para ver logs de la aplicación:"
echo "  docker-compose logs app"
echo ""
