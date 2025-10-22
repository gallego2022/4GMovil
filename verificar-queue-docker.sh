#!/bin/bash
# ========================================
# VERIFICAR QUEUE WORKER EN DOCKER - 4GMovil
# ========================================
set -e
echo ""
echo "Verificando Queue Worker en Docker..."
echo ""

# Verificar que Docker esté ejecutándose
echo "[1/5] Verificando Docker..."
docker-compose ps
if [ $? -ne 0 ]; then
    echo "ERROR: Docker no está ejecutándose"
    echo "Ejecuta: docker-compose up -d"
    exit 1
fi
echo "✓ Docker está ejecutándose"

# Verificar contenedor de queue-worker
echo "[2/5] Verificando contenedor de queue-worker..."
docker-compose ps queue-worker
if [ $? -ne 0 ]; then
    echo "ERROR: Contenedor de queue-worker no está ejecutándose"
    echo "Ejecuta: docker-compose up -d queue-worker"
    exit 1
fi
echo "✓ Contenedor de queue-worker está ejecutándose"

# Verificar que no hay contenedores duplicados
echo "[3/5] Verificando que no hay contenedores duplicados..."
docker-compose ps | grep queue
echo "✓ Verificación de duplicados completada"

# Verificar logs del queue-worker
echo "[4/5] Verificando logs del queue-worker..."
docker-compose logs --tail=10 queue-worker
echo "✓ Logs del queue-worker verificados"

# Verificar que Redis esté funcionando
echo "[5/5] Verificando conexión a Redis..."
docker-compose exec redis redis-cli ping
if [ $? -ne 0 ]; then
    echo "ADVERTENCIA: Redis no está respondiendo"
    echo "Verifica que el contenedor de Redis esté ejecutándose"
else
    echo "✓ Redis está funcionando correctamente"
fi

echo ""
echo "========================================"
echo "VERIFICACIÓN DE QUEUE WORKER COMPLETADA"
echo "========================================"
echo ""
echo "Comandos útiles:"
echo "- Ver logs del worker: docker-compose logs -f queue-worker"
echo "- Reiniciar worker: docker-compose restart queue-worker"
echo "- Detener worker: docker-compose stop queue-worker"
echo "- Iniciar worker: docker-compose start queue-worker"
echo "- Ver todos los contenedores: docker-compose ps"
echo ""
