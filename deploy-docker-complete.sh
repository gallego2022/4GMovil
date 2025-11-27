#!/bin/bash

echo "========================================"
echo "DESPLIEGUE COMPLETO DOCKER - 4GMovil"
echo "========================================"

echo ""
echo "🛑 Deteniendo contenedores existentes..."
docker-compose down

echo ""
echo "🗑️ Eliminando contenedores y volúmenes antiguos..."
docker-compose down -v --remove-orphans

echo ""
echo "🧹 Limpiando imágenes no utilizadas..."
docker system prune -f

echo ""
echo "🔨 Reconstruyendo imágenes..."
docker-compose build --no-cache

echo ""
echo "🚀 Iniciando servicios..."
docker-compose up -d

echo ""
echo "⏳ Esperando a que los servicios estén listos..."
sleep 30

echo ""
echo "📊 Verificando estado de los contenedores..."
docker-compose ps

echo ""
echo "🔍 Verificando logs de la aplicación..."
docker-compose logs app --tail=20

echo ""
echo "✅ Despliegue completado!"
echo ""
echo "🌐 Aplicación disponible en: http://localhost:8000"
echo "🗄️  phpMyAdmin disponible en: http://localhost:8083"
echo "📊 Redis Commander disponible en: http://localhost:8081"
echo "📈 Microservicio de Reportes: http://localhost:8082"
echo ""
echo "Para ver logs en tiempo real: docker-compose logs -f"
echo "Para detener servicios: docker-compose down"
echo ""
