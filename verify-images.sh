#!/bin/bash

echo "========================================"
echo "   VERIFICACION DE IMAGENES DE PERFIL"
echo "========================================"

echo ""
echo "🔍 Verificando enlace simbólico de storage..."
docker exec 4gmovil_app ls -la /var/www/html/public/storage

echo ""
echo "📁 Verificando directorio de fotos de perfil..."
docker exec 4gmovil_app ls -la /var/www/html/public/storage/fotos_perfil/

echo ""
echo "🌐 Probando acceso a imagen de perfil..."
curl -I http://localhost:8000/storage/fotos_perfil/Wr36haodjiR2z699PW0PtSGRh2yQyFPKfWS9ZWVx.jpg

echo ""
echo "✅ Verificación completada!"
echo ""
echo "Si ves 'HTTP/1.1 200 OK' arriba, las imágenes están funcionando correctamente."
echo ""
